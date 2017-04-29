<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Standings Model.
	 *
	 * ?????
	 *		
	 */
	
Class Standings extends CI_Model 
{
	public function __construct() 
    {
		parent::__construct();
		//$ci = get_instance();
		$this->load->helper('date');
		$this->load->model('Rosters');
		$this->load->model('Players');
		$this->load->model('Games');
	}


//*********************************************************************************	
	//will sort teams by given order array
	//current order options are wins, points, last_game
	//returned array will have keys matching sort order criteria and a key for team_id
	//'last_game','wins','points','streak'
	public function sort_teams_by($year,$league_id,$conference='',$division='',$sort_order_array=array('last_game','wins','points'),$playoffs=TRUE){
		
		//add all team's wins and points to array based on current week unless week is 0 go to previous year
		$all_teams_data_array = array();
		if($conference !=''){
			$conf = ' and Teams_Seasons.conference="'.$conference.'"';
		}
		else {
			$conf = '';	
		}
		if($division !=''){
			$div = ' and Teams_Seasons.division="'.$division.'"';
		}
		else{
			$div='';	
		}
		
		$all_teams_query = $this->db->query('SELECT DISTINCT Games.opponent_a FROM Games JOIN Teams_Seasons on Games.opponent_a = Teams_Seasons.team_id WHERE Teams_Seasons.year = '.$year.' and Games.year='.$year.$conf.$div);
		foreach($all_teams_query->result_array() as $team_id){
			
			$all_teams_array[]=$team_id['opponent_a'];
		}
		
		$all_teams_query = $this->db->query('SELECT DISTINCT Games.opponent_b FROM Games JOIN Teams_Seasons on Games.opponent_b = Teams_Seasons.team_id WHERE Games.opponent_b<>-1 and Teams_Seasons.year = '.$year.' and Games.year='.$year.$conf.$div);
		foreach($all_teams_query->result_array() as $team_id){
			if(!in_array($team_id['opponent_b'],$all_teams_array)){
				$all_teams_array[]=$team_id['opponent_b'];
			}
		}

		//go through each team and set an array to include points and wins
		
		foreach($all_teams_array as $team_id){
			//GET WINS : get count where team is opp a or b and winner is team
			if(in_array('wins',$sort_order_array)){
				$wins_and_losses = $this->get_team_wins_losses_year($team_id,$year,$playoffs);
				$total_wins = $wins_and_losses['wins'];
				
				//set  wins to array with key team_id
				$all_teams_data_array[$team_id]['wins']=$total_wins;
				if(in_array('losses',$sort_order_array)){
					$all_teams_data_array[$team_id]['losses']=$wins_and_losses['losses'];
				}
			}
			
			//get points for the team
			if(in_array('points',$sort_order_array)){
				$total_points = $this->get_team_total_points_year($team_id, $year,$playoffs);
				//set points  to array with key team_id
				$all_teams_data_array[$team_id]['points']=$total_points;
			}
			//create a key in the array for each team called "last game". get last week played with an opponent_b.
			//used primarily for sorting teams after playoffs for draft picks
			if(in_array('last_game',$sort_order_array) || in_array('streak',$sort_order_array)){
				$superbowl_week = $this->Leagues->get_superbowl_week(1); //***NI*** league
				$last_game = $this->Games->get_team_last_game($team_id,$year);
				//separate champ from runner up
				if($last_game==$superbowl_week){
					$conditions = "(opponent_a =".$team_id." or opponent_b =".$team_id.") and week =".$superbowl_week." and opponent_b>0 and year=".$year;
					$this->db->select('winner');
					$this->db->where($conditions);
					$get_champ = $this->db->get('Games');
					if($get_champ->row('winner') ==$team_id){
						$last_game = $superbowl_week +1;	
					}
				}
				//only add to array if actually requested. This function is used for part of the streak request as well.
				if(in_array('last_game',$sort_order_array)){
					$all_teams_data_array[$team_id]['last_game']=$last_game;
				}
				
			}
			
			
			//get streak for the team
			if(in_array('streak',$sort_order_array)){
				$streak = $this->Games->get_team_streak($team_id,$year);
				
				//set streak  to array with key team_id
				$all_teams_data_array[$team_id]['streak']=$streak;
			}
			
			
			
			//this is because multi sort loses the keys, but that's ok beacuse there's
			//a key of team_id
			$all_teams_data_array[$team_id]['team_id']=$team_id; 
		
		}
		
		//sort by given order
		//first create the columns, then use array_multisort
		//***NI*** if other sort options are added a new entry for each will be needed here
		foreach($all_teams_data_array as $team_id => $data){
			foreach($sort_order_array as $sort_criteria){
				if($sort_criteria=='wins'){$wins_array[$team_id] = $data['wins'];}
				if($sort_criteria=='losses'){$losses_array[$team_id] = $data['losses'];}
				if($sort_criteria=='points'){$points_array[$team_id] = $data['points'];}
				if($sort_criteria=='last_game'){$last_game_array[$team_id] = $data['last_game'];}
				if($sort_criteria=='streak'){$streak_array[$team_id] = $data['streak'];}
			}
		}
		
		//dynamically create the sort parameters based on the sort order passed
		$dynamic_sort = array();
		
		foreach($sort_order_array as $sort_criteria){
			$column = $sort_criteria.'_array';
			$dynamic_sort[] = $$column;
			$dynamic_sort[] = SORT_ASC;
		}
		 
		$param = array_merge($dynamic_sort, array(&$all_teams_data_array));
		call_user_func_array('array_multisort', $param);
		//should have created as example: (based on default sort order of last_game, wins, points)
		//array_multisort($last_game_array, SORT_ASC, $wins_array, SORT_ASC, $points_array, SORT_ASC, $all_teams_data_array);	
		return $all_teams_data_array;
	}//end sort method
	
	
//***************************************************************************

	public function get_team_total_points_year($team_id,$year, $include_playoffs=TRUE,$last_week=FALSE){
		
		//if team is in A slot
		$conditions = "opponent_a =".$team_id." and opponent_b<>0 and year=".$year;//0 is for toilet bowl
		$this->db->select_sum('opponent_a_score','sum');
		$this->db->where($conditions);
		if($include_playoffs==FALSE){
				$this->db->where('is_playoff',0);
		}
		if($last_week!=FALSE){
				$this->db->where('week<="'.$last_week.'"');
		}
		$get_sum_a = $this->db->get('Games');
		
		$sum_a = $get_sum_a->row('sum');
		//if team is in B slot
		$conditions = "opponent_b =".$team_id." and year=".$year;
		$this->db->select_sum('opponent_b_score','sum');
		$this->db->where($conditions);
		if($include_playoffs==FALSE){
				$this->db->where('is_playoff',0);
		}
		if($last_week!=FALSE){
				$this->db->where('week<="'.$last_week.'"');
		}
		$get_sum_b = $this->db->get('Games');
		$sum_b = $get_sum_b->row('sum');
		$total_points = $sum_b + $sum_a;	
		//d($team_id,$include_playoffs,$total_points);
		return $total_points;
		
	}


//***********************************************************************

	public function get_team_wins_losses_year($team_id,$year, $include_playoffs=TRUE,$last_week=FALSE){
		
		//wins
		$conditions = "(opponent_a =".$team_id." or opponent_b =".$team_id.") and winner = ".$team_id." and year=".$year;
		$this->db->where($conditions);
		if($include_playoffs===FALSE){ $this->db->where('is_playoff',0); }
		if($last_week!=FALSE){ $this->db->where('week<="'.$last_week.'"'); }
		$query =$this->db->get('Games');
		$total_wins = count($query->result_array());	
		
		//losses
		$conditions = "(opponent_a =".$team_id." or opponent_b =".$team_id.") and winner <> ".$team_id." and winner <> 0 and year=".$year;
		$this->db->where($conditions);
		if($include_playoffs===FALSE){ $this->db->where('is_playoff',0); }
		if($last_week!=FALSE){ $this->db->where('week<="'.$last_week.'"'); }
		$query2 =$this->db->get('Games');
		$total_losses = count($query2->result_array());	
		
		return array('wins'=>$total_wins, 'losses'=>$total_losses);
	}

//***************************************************************************

	public function get_team_points_against_year($team_id,$year, $include_playoffs=TRUE){
		
		//if team is in A slot
		$conditions = "opponent_a =".$team_id." and opponent_b>0 and year=".$year;
		$this->db->select_sum('opponent_b_score','sum');
		$this->db->where($conditions);
		if(!$include_playoffs){
				$this->db->where('week < 14');
		}
		$get_sum_a = $this->db->get('Games');
		$sum_a = $get_sum_a->row('sum');
		//if team is in B slot
		$conditions = "opponent_b =".$team_id." and year=".$year;
		$this->db->select_sum('opponent_a_score','sum');
		$this->db->where($conditions);
		if(!$include_playoffs){
				$this->db->where('week < 14');
		}
		$get_sum_b = $this->db->get('Games');
		$sum_b = $get_sum_b->row('sum');
		$total_points = $sum_b + $sum_a;	
		
		return $total_points;
		
	}
	
//***********************************************************************
	//determines the current playoff teams based on standings
	//only for current year's rules
	public function determine_playoff_teams($year,$standings=NULL){
		if(is_null($standings)){
			$standings= $this->sort_teams_by($year,$this->league_id,'','',array('wins','losses','points','streak'),FALSE);
		}
		rsort($standings);
		//add the divisions to the array
		$AFCEast=$AFCWest=$NFCEast=$NFCWest=$AFCWC=$NFCWC=$AFCpts=$NFCpts=$AFCPTSWC=$NFCPTSWC=0;
		foreach($standings as $key => $data){
			$conference_division = $this->Teams->get_team_conference_division($data['team_id']);
			$standings[$key]['conference'] = $conference_division['conference'];
			$standings[$key]['division'] = $conference_division['division'];
			$confdiv = $conference_division['conference'].$conference_division['division'];
			$conf=$conference_division['conference'].'WC';
			$confpts=$conference_division['conference'].'pts';
			$confptswc=$conference_division['conference'].'PTSWC';
			//if the divisionleader hasn't been assigned, add this team
			if($$confdiv == 0){
				$$confdiv = $data['team_id'];
				$standings[$key]['playoffs']=TRUE;
			}
			//if not the division leader, then conference WC
			elseif($$conf==0){
				$standings[$key]['playoffs']=TRUE;
				$$conf=$data['team_id'];
			}
			//not playoffs by record, add false and check for points
			else {
				//more points
				if($data['points']>$$confpts){
					$$confpts=$data['points'];
					$$confptswc = $key;
					
				}
				$standings[$key]['playoffs']=FALSE;
			}
		}

		//add points wildcard
		$standings[$AFCPTSWC]['playoffs']=TRUE;
		$standings[$NFCPTSWC]['playoffs']=TRUE;
		
		return $standings;	
	}


//**********************************************************************
	public function get_past_playoff_teams($year,$standings=NULL){
		if(is_null($standings)){
			$standings= $this->sort_teams_by($year,$this->league_id,'','',array('wins','losses','points','streak'),FALSE);
		}
		rsort($standings);
		foreach($standings as $key => $data){
			$seed_query = $this->db->where('team_id',$data['team_id'])
									->where('year',$year)
									->select('conference,division,seed')
									->get('Teams_Seasons');
			foreach($seed_query->result_array() as $team){
	
				if($team['seed']>0){
					$standings[$key]['playoffs']=$team['seed'];
				}
				else{
					$standings[$key]['playoffs']=FALSE;	
				}
				
				$standings[$key]['conference']=$team['conference'];
				$standings[$key]['division']=$team['division'];
				
			}
		}
		
		return $standings;
	}
//********************************************************************

	public function get_standings($year,$conference='',$division='',$wildcard=FALSE){
		
		//get standings
		$sort_array = array('wins','losses','points');
		if($year>2003){ $sort_array[] = 'streak'; }
		$standings= $this->sort_teams_by($year,$this->league_id,$conference,$division,$sort_array,FALSE);
		
		//add the playoff teams
		if($year==$this->current_year){
			
			if($division!=''){
				$standings_playoffs = $this->sort_teams_by($year,$this->league_id,$conference,'',$sort_array,FALSE);
				$standings_playoffs = $this->determine_playoff_teams($year,$standings_playoffs);
				foreach($standings_playoffs as $key=>$data){
					if($data['division']!=$division){
						unset($standings_playoffs[$key]);
					}
				}
				$standings=$standings_playoffs;
			}
			else {
				$standings = $this->determine_playoff_teams($year,$standings);	
			}
			
			
		} 
		else{
			$standings = $this->get_past_playoff_teams($year,$standings);	
		}
		
		return $standings;
		
	}



//********************************************************************

	public function sos_ranking($league_id,$year){
		$all_teams = $this->Teams->get_all_team_id($league_id);
		
		$points_against_array = array();
		foreach($all_teams as $team_id){
			$pa = $this->get_team_points_against_year($team_id,$year, FALSE);
			$points_against_array[$team_id]=$pa;
		}
		arsort($points_against_array);
		return $points_against_array;
		
	}

//************************************************************************

	public function set_regular_season_championships($league_id,$year,$standings){
		$afc_east=$nfc_east=$afc_west=$nfc_west=$scoring=$points_scored=$record=$afc_regular_season=$nfc_regular_season=0;
	
		foreach($standings as $team_info){
			$conference = strtolower($team_info['conference']);
			$division = strtolower($conference.'_'.$team_info['division']);
			$conference_regular = strtolower($conference.'_regular_season');
			
			if($team_info['points']>$points_scored){
				$scoring=$team_info['team_id'];
				$points_scored=$team_info['points'];	
			}
			if($$division==0){
				$$division=$team_info['team_id'];	
			}
			if($record == 0){
				$record = $team_info['team_id'];	
			}
			if($$conference_regular==0){
				$$conference_regular=$team_info['team_id'];
			}

		}
		//set the database
		$this->db->set('afc_east',$afc_east)
				->set('afc_west',$afc_west)
				->set('nfc_east',$nfc_east)
				->set('nfc_west',$nfc_west)
				->set('scoring',$scoring)
				->set('record',$record)
				->set('afc_regular_season',$afc_regular_season)
				->set('nfc_regular_season',$nfc_regular_season)
				->where('league_id',$league_id)
				->where('year',$year)
				->update('Championships');
		
	}


}//end model


/*End of file Database_Manager.php*/
/*Location: ./application/models/Database_Manager.php*/