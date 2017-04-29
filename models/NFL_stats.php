<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Players Model.
	 *
	 * ?????
	 *		
	 */
	
Class NFL_stats extends CI_Model 
{
	public $current_year;	
	public $current_week;
	public $league_id=1;
	
	public function __construct() 
   {
		parent::__construct();
		//$ci = get_instance();
		$this->load->model('Leagues');
		$this->load->model('Players');
		$this->NFL_db = $this->load->database('NFL',true);
		$this->load->helper('string');
		
		$this->current_year = $this->Leagues->get_current_season($this->league_id);
		$this->current_week = $this->Leagues->get_current_week($this->league_id);
	}
	// Function to calculate square of value - mean
	public function sd_square($x, $mean) { return pow($x - $mean,2); }
	
	// Function to calculate standard deviation (uses sd_square)    
	public function sd($array) {
		
		if(count($array)<2) { return 0; }
		// square root of sum of squares devided by N-1
		return sqrt(array_sum(array_map(array($this,'sd_square'), $array, array_fill(0,count($array), (array_sum($array) / count($array)) ) ) ) / (count($array)-1) );
	}
//**********************************************************************************

	//adds a player to the stats table for the year and week
	function add_player_stats_table($fffl_player_id,$week,$year,$team,$opponent){
		//determine if the player is already in the stats table for this week
		$nfl_gsis_player_id = $this->Players->convert_player_id($fffl_player_id, 'fffl_player_id', 'nfl_gsis_player_id');
		$count = $this->NFL_db->where('week',$week)
								->where('season',$year)
								->group_start()
									->or_where('fffl_player_id',$fffl_player_id)
									->or_where('nfl_gsis_player_id',$nfl_gsis_player_id)
								->group_end()
								->get('NFL_stats_'.$year);
								
		if($count->num_rows()==0){					
			$this->NFL_db->set('fffl_player_id',$fffl_player_id)
						->set('nfl_gsis_player_id',$nfl_gsis_player_id)
						->set('season',$year)
						->set('week',$week)
						->set('player_name','')
						->set('player_team', $team)
						->set('player_opponent',$opponent)
						->insert('NFL_stats_'.$year);	
		}
	}

//************************************************************************************

	//adds the bye week palyers bye line to the stats table
	function add_bye_weeks($year,$week){
		//get teams on bye
		$byes_query = $this->NFL_db->where('bye_week',$week)
								->get('NFL_Teams');
		foreach($byes_query->result_array() as $team){
			$players= $this->Players->get_all_player_ids_no_objects("Players.current_team='".$team['team_abbr']."'","fffl_player_id","Players.last_name",'ASC',0);
			
			foreach($players['ids'] as $fffl_player_id){
				$this->add_player_stats_table($fffl_player_id,$week,$year,$team['team_abbr'],'Bye')	;
			}
			
		}
	}

//***********************************************************************************
	public function get_score_average($scores_array,$number_of_weeks=15)
	{
		$total_points=0;
		foreach($scores_array as $score)
		{
			$total_points = $total_points + $score;
		}
		$average = number_format($total_points/$number_of_weeks,1);
		
		return $average;
	}//get score average
	
//**********************************************************************************
	
	public function get_score_standard_deviation($scores_array,$number_of_weeks=15)
	{	
		return $this->sd($scores_array);
	}//get score standard deviation
	
//**************************************************************************
	
	/*Gets all scores for a season for a specific player.*/
	
	public function get_player_scores_season($season,$fffl_player_id,$include_average=0,$start_week=1,$end_week=16,$include_SD=0)
	{
		
		//create scores array in case include_average = 1, also byes set to subtract from total weeks in order to get average
		if($include_average==1 || $include_SD==1)
		{
			$scores_array = array();
		}
		$byes=0;
		//if it's the current season only go as afar as current week no matter
		//what the end week passed was. Then if the player's game isn't final then don't 
		//count this week
		$check_status = strpos($this->Players->get_player_game_status($fffl_player_id),'Final');
		if($season==$this->current_year){ 
			$end_week=$this->current_week; 
			
			if($check_status==FALSE){
				$end_week--;	
				if($end_week<$start_week){ $end_week=$start_week; }
			}		
		}
		
		//query includes plalyer_opponent because that is where 'Bye' is stored
    	$this->NFL_db->select("points, decimal, week, player_opponent,player_team");
		$this->NFL_db->where("fffl_player_id=".$fffl_player_id." AND week>=".$start_week." AND week<=".$end_week);
		$this->NFL_db->order_by('week','ASC');
			//d($fffl_player_id,$start_week,$end_week);
		$query = $this->NFL_db->get('NFL_stats_'.$season);
		
		if($query->num_rows() > 0)
		{
			$scores = array();
			$player_team='';
			
			//d($query->result_array());
			foreach($query->result_array() as $row){
				//sets the player's team for the first week of stats
				if($player_team==''){ $player_team=$row['player_team']; }
				//d($row['points']);
				$scores[$row['week']]['points']=$row['points'];
				
				$scores[$row['week']]['decimal']=$row['decimal'];
				$scores[$row['week']]['player_opponent']=$row['player_opponent'];
				
				$scores[$row['week']]['player_team']=$row['player_team'];
			}
		
			$week = $start_week;
			while($week<=$end_week)
			{
				
				//the data returned will be an array that matches the opponent with the score.
				if(!isset($scores[$week])){ //if that week doesn't have stats for the player
					$data['weeks'][$week]['points']=0;
					$data['weeks'][$week]['decimal']=0;
					$teams_query = $this->NFL_db->select('home_team,away_team')
							->or_group_start()
								->or_where('home_team',$player_team)
								->or_where('away_team',$player_team)
							->group_end()
							->where('season',$season)
							->where('week',$week)
							->get('NFL_Schedule_'.$season);
							
					foreach($teams_query->result_array() as $teams){
						
						if($teams['home_team']==$player_team) { $opponent = $teams['away_team']; }
						else{ $opponent = $teams['home_team']; }
						
						
					}
					if(!isset($opponent)){ /*d($fffl_player_id,$season,$week,$player_team);*/ }
					$data['weeks'][$week]['player_opponent']=$opponent;
				}
				else {
					$player_team = $scores[$week]['player_team'];
					$data['weeks'][$week]['points'] = $scores[$week]['points'];
					$data['weeks'][$week]['decimal'] = $scores[$week]['decimal'];
					$data['weeks'][$week]['player_opponent'] = $scores[$week]['player_opponent'];
					//used in the total number of weeks to send to average function
					if($scores[$week]['player_opponent']=='Bye')
					{
						$byes++;
					}
					//compile a scores_array if the average is to be included
					if($include_average==1 || $include_SD==1)
					{
						$scores_array[]=$scores[$week]['points'];
					}
				}	
				$week++;
				
			}
			
			//get the averaage if include_average=1
			if($include_average==1)
			{
				if($end_week==1 && $start_week==1 && $check_status==FALSE && $this->current_week==1){
					$data['average']=0;
				}
				else{
					
					$data['average']=$this->get_score_average($scores_array,($end_week-$start_week+1-$byes));
				}
			}
			if($include_SD==1){
				
				$data['standard_deviation']=round($this->get_score_standard_deviation($scores_array,($end_week-$start_week+1-$byes)),1);
				
			}
			
			$data['start_week']=$start_week;
			$data['end_week']=$end_week;
			$data['team']=$scores[$row['week']]['player_team'];
			
			return $data;
			
		}
		else //the player wasn't in the stats table likely because this game hasn't started so add just the team
		{
			if($season==$this->current_year){
				$scores = array();
				$player_team='';
				$data['team']=$this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","current_team");
				if(!isset($data['start_week'])){
					$data['start_week']=$start_week;
				}
				if(!isset($data['end_week'])){
					$data['end_week']=$end_week;
				}
				
				$data['average']=0.0;
				$data['weeks']=FALSE;
				return $data;
			
			}
			
			return FALSE;
		}
		
		
	}//get player scores season
	
//**************************************************************************
	
	/*Gets all scores for a season for a specific player.
	*/
	public function get_player_stats_season($season,$fffl_player_id,$start_week=1,$end_week=16)
	{
		
		//get a sum for each category
		$stats_array = array('completions','incompletions','interceptions','rushes','receptions','pass_yards','rush_yards','receiving_yards','pass_tds','rush_tds','receiving_tds','punt_return_tds','kick_return_tds','fumbles','fumbles_lost','xps_made','xps_missed','two_point_made','targets','other_tds');
		
		foreach($stats_array as $category){
			$this->NFL_db->select_sum($category,'sum');
			$this->NFL_db->where("fffl_player_id=".$fffl_player_id." AND week>=".$start_week." AND week<=".$end_week);
			$query = $this->NFL_db->get('NFL_stats_'.$season);
			foreach($query->result() as $row)
			{
				$data[$category] = $row->sum;
			}
			
		}
		//combine FGs
		$fg_made_array=array('fgs_made_19','fgs_made_29','fgs_made_39','fgs_made_49','fgs_made_59','fgs_made_60plus');
		$data['fgs_made']=0;
		foreach($fg_made_array as $fg_made){
			//d($start_week,$end_week);
			$this->NFL_db->select_sum($fg_made,'sum');
			$this->NFL_db->where("fffl_player_id=".$fffl_player_id." AND week>=".$start_week." AND week<=".$end_week);
			$query = $this->NFL_db->get('NFL_stats_'.$season);
			foreach($query->result() as $row)
			{
				//d($row);
				$data['fgs_made']=$data['fgs_made']+$row->sum;
			}
		}
		
		$fg_missed_array=array('fgs_missed_19','fgs_missed_29','fgs_missed_39','fgs_missed_49','fgs_missed_59','fgs_missed_60plus');
		$data['fgs_missed']=0;
		foreach($fg_missed_array as $fg_missed){
			$this->NFL_db->select_sum($category,'sum');
			$this->NFL_db->where("fffl_player_id=".$fffl_player_id." AND week>=".$start_week." AND week<=".$end_week);
			$query = $this->NFL_db->get('NFL_stats_'.$season);
			foreach($query->result() as $row)
			{
				$data['fgs_missed']=$data['fgs_missed']+$fg_missed;
			}
		}


			return $data;

		
	}//get player stats season
	
	
//******************************************************************

	public function get_player_first_last_year($fffl_player_id){
		
		$year = 2003;
		$first=0;
		$last=0;
		while($first==0 && $year<=$this->current_year){
			$count= $this->NFL_db->where('fffl_player_id',$fffl_player_id)
					->from('NFL_stats_'.$year)
					->count_all_results();
					
			if($count>0){ $first=$year;}
			$year++;
		}
		
		$missed_years=0;

		while($missed_years<6 && $year<=$this->current_year){
			$count= $this->NFL_db->where('fffl_player_id',$fffl_player_id)
					->from('NFL_stats_'.$year)
					->count_all_results();
					
			if($count==0){ 
				$missed_years++; 
				if($last==0){$last=$year-1; }
			}
			else { $missed_years=0; $last=0;}
			
			$year++;
		}
		
		if($last==0){ $last=$this->current_year; if($first==0){$first=$this->current_year;} }
		
		return array('first'=>$first,'last'=>$last);
	}
//*********************************************************************
	//returns array of rankings for a year and position. rank #=>fffl_player_id, average, team for that year
	public function get_position_rankings($position,$year,$full=1){

	
		
		//get every distinct player id that scored that year. If they didn't score, they will be left out
		
		$fffl_player_id_query = $this->NFL_db->select('fffl_player_id')
				->distinct()
				->where('week>0 and week<17')
				->get('NFL_stats_'.$year);
		
		$rankings = array();
		
		
		foreach($fffl_player_id_query->result_array() as $player){
			$player_position = $this->Players->get_player_info(array($player['fffl_player_id']),"fffl_player_id","position");
			if(isset($player_position['position'])){ 
				if($player_position['position']==$position){
					if($full==1){
						$team_query= $this->NFL_db->select('player_team')
										->where('fffl_player_id',$player['fffl_player_id'])
										->order_by('week','DESC')
										->limit(1)
										->get('NFL_stats_'.$year);
						$team_row = $team_query->row();
						$team = $team_row->player_team;
					}
					
					$score_data = $this->get_player_scores_season($year,$player['fffl_player_id'],1,1,16,0);
				
					if($full==1){
						$owners = $this->Players->get_player_owners($player['fffl_player_id'], "fffl_player_id");
						$owners_array=array();
						
						if($owners){
							foreach($owners as $team_id){
								$owners_array[]=$team_id->team_id;
							}
						}
					}
					if($full==1){
						$rankings[] = array(
								'average' => $score_data['average'],
								'fffl_player_id' => $player['fffl_player_id'],
								'team' => $team,
								'fffl_teams' => $owners_array
						);
					}
					else {
						$rankings[] = array(
								'average' => $score_data['average'],
								'fffl_player_id' => $player['fffl_player_id']
						);
		
					}
				}
			}
		}
		
		
		//these definitions are due to problems caused by rookies having no average
		$average=array(); $team_ar=array();
		// Obtain a list of columns
		
		foreach ($rankings as $key => $row) {
			$average[$key]  = $row['average'];
			
			
		}
		
		// Sort the data with average descending, 
		// Add $rankings as the last parameter, to sort by the common key
		array_multisort($average, SORT_DESC, $rankings);
		
		
		return $rankings;
		
	}
	
//***********************************************
	public function get_player_ranking_year($fffl_player_id,$year){
		
		//get position
		$position_array = $this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","position");
		
		
		$position = $position_array['position'];
				
		$position_rankings_array = $this->get_position_rankings($position,$year,0);
				
		//search for the player

		   foreach($position_rankings_array as $rank => $data)
		   {
			  if ( $data['fffl_player_id'] == $fffl_player_id ){
				 return $rank;
			  }
		   }

		   return false;

	}
	
//********************************************************************************************
	//LIVE SCORING  send the stats to the table
	public function update_stats($data,$year,$week){
		 
		//go through each player and update stats ['gsis']=>all columns
		foreach($data as $nfl_gsis_player_id => $stats){
			
			//count entries for the player in the stats table
			$count = $this->NFL_db->where('nfl_gsis_player_id',$nfl_gsis_player_id)
								->where('week',$week)
								->where('season',$year)
								->count_all_results('NFL_stats_'.$year);
			if($count==0){
				//insert an entry into the stats table for the player
				$fffl_player_id = $this->Players->convert_player_id($nfl_gsis_player_id, 'nfl_gsis_player_id', 'fffl_player_id');
				if($fffl_player_id!=FALSE) { 
					$this->NFL_db->set('fffl_player_id',$fffl_player_id)
							->set('nfl_gsis_player_id',$nfl_gsis_player_id)
							->set('season',$year)
							->set('week',$week)
							->set('player_name','')
							->set('player_team', $stats['playerteam'])
							->set('player_opponent',$stats['vs'])
							->insert('NFL_stats_'.$year);
				}
			}
			
			$this->NFL_db->set('player_name',$stats['playername']);
			foreach(array('completions','incompletions','interceptions','rushes','receptions','pass_yards','rush_yards','receiving_yards','pass_tds','rush_tds','receiving_tds','punt_return_tds','kick_return_tds','fumbles','fumbles_lost','xps_made','xps_missed','two_point_made','fgs_made_19','fgs_made_29','fgs_made_39','fgs_made_49','fgs_made_59','fgs_made_60plus','fgs_missed_19','fgs_missed_29','fgs_missed_39','fgs_missed_49','fgs_missed_59','fgs_missed_60plus') as $key){
				if(array_key_exists($key, $stats)) {
					$this->NFL_db->set($key,$stats[$key]);
				}
				else{
					 
					$stats[$key]=0;	
				}
			}
			
			//$this->NFL_db->set('targets',$stats['?']);
			//$this->NFL_db->set('other_tds',$stats['?']);
			$this->NFL_db->where('nfl_gsis_player_id',$nfl_gsis_player_id);
			$this->NFL_db->where('week',$week);
			$this->NFL_db->update('NFL_stats_'.$this->current_year);
		
			//update the scores
			$this->calculate_scoring($stats,$nfl_gsis_player_id,$week,$year);
		}
		
		
		
	}

//******************************************************************************

	//LIVE SCOING
	//calculates a player's score
	public function calculate_scoring($stats,$nfl_gsis_player_id,$week,$year){
		
		$pts = 0; $decimal = 0;
		//touchdowns 6
		$pts=$pts+($stats['pass_tds']+$stats['rush_tds']+$stats['receiving_tds']+$stats['punt_return_tds']+$stats['kick_return_tds'])*6;
		//3 completions thrown 1
		$pts =$pts+ floor($stats['completions']/3);
		$decimal_total = ($stats['completions']/3)*10;
		$decicmal = $decimal + (((floor($decimal_total))/10)-floor($stats['completions']/3));
		//25 yards passing 1
		if ($stats['pass_yards']>=0){
			$pts =$pts+ floor($stats['pass_yards']/25);
			$decimal_total = $stats['pass_yards']/25;
			$decimal = $decimal + ((floor($decimal_total*10)/10)-floor($stats['pass_yards']/25));
			//300+ passing 1
			$pts =$pts+ floor($stats['pass_yards']/300);
		}else {
			$pts =$pts+ ceil($stats['pass_yards']/25);
			$decimal_total = $stats['pass_yards']/25;
			$decimal = $decimal + ((ceil($decimal_total*10)/10)-ceil($stats['pass_yards']/25));
		}
		//ints -3
		$pts =$pts-($stats['interceptions']*3);
		//fum lost -2
		$pts =$pts-($stats['fumbles_lost']*2);
		//10yd rush or rec 1
		if ($stats['rush_yards']>=0){
			$pts =$pts+ floor($stats['rush_yards']/10);
			$decimal_total = $stats['rush_yards']/10;
			$decimal = $decimal + ((floor($decimal_total*10)/10)-floor($stats['rush_yards']/10));
			
			$pts =$pts+ floor($stats['rush_yards']/100);//this gives the 100 yd bonus.
		}else {
			$pts =$pts+ ceil($stats['rush_yards']/10);
			$decimal_total = $stats['rush_yards']/10;
			$decimal = $decimal + ((ceil($decimal_total*10)/10)-ceil($stats['rush_yards']/10));
		}
		if ($stats['receiving_yards']>=0){
			$pts =$pts+ floor($stats['receiving_yards']/10);
			$decimal_total = $stats['receiving_yards']/10;
			$decimal = $decimal + ((floor($decimal_total*10)/10)-floor($stats['receiving_yards']/10));
			$pts =$pts+ floor($stats['receiving_yards']/100);//this is the 100 yd bonus
		}else {
			$pts =$pts+ ceil($stats['receiving_yards']/10);
			$decimal_total = $stats['receiving_yards']/10;
			$decimal = $decimal + ((ceil($decimal_total*10)/10)-ceil($stats['receiving_yards']/10));
		}
		//2 recepts 1
		$pts =$pts+ floor($stats['receptions']/2);
		$decimal_total = $stats['receptions']/2;
		$decimal = $decimal + ((floor($decimal_total*10)/10)-floor($stats['receptions']/2));
		//2 pt conversion 2
		$pts =$pts+ ($stats['two_point_made']*2);
		//pat 1
		$pts =$pts+ ($stats['xps_made']); 
		//fg 0-39 3
		$pts =$pts+ (($stats['fgs_made_19']+$stats['fgs_made_29']+$stats['fgs_made_39'])*3); 
		
			
		//fg 40-49 3
		$pts =$pts+ (($stats['fgs_made_49'])*3);
		//fg 50-59 4
		$pts =$pts+ (($stats['fgs_made_59'])*4);
		//fg 60+ 5
		$pts =$pts+ (($stats['fgs_made_60plus'])*5);
		//fg 0-39 missed -1
		$pts =$pts- $stats['fgs_missed_19']-$stats['fgs_missed_29']-$stats['fgs_missed_39'];
		//pat missed -1
		$pts =$pts- $stats['xps_missed'];
				
			
	
			if ($pts<0){

				$pts=0;
			}
			
			$this->NFL_db->where('nfl_gsis_player_id',$nfl_gsis_player_id)
							->where('week',$week)
							->where('season',$year)
							->set('points',$pts)
							->set('decimal',$decimal)
							->update('NFL_stats_'.$year);
		

	}


}//end model


/*End of file NFL_stats.php*/
/*Location: ./application/models/Players.php*/