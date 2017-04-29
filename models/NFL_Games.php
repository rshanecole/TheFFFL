<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * NFL_Games Model.
	 *
	 * ?????
	 *		
	 */
	
Class NFL_Games extends CI_Model 
{
	public function __construct() 
   {
		parent::__construct();
		//$ci = get_instance();
		$this->NFL_db = $this->load->database('NFL',true);
		$this->load->helper('string');
		$this->load->model('Players');
		$this->load->model('NFL_stats');
		
		
	}
	
//*******************************************************************
	function search($array, $key, $value)
	{
		$results = array();
	
		if (is_array($array))
		{
			if (isset($array[$key]) && $array[$key] == $value)
				$results[] = $array;
	
			foreach ($array as $subarray)
				$results = array_merge($results, $this->search($subarray, $key, $value));
		}
	
		return $results;
	}
//******************************************************************
	//gets the game ids, status flag, home and away teams, start time for all games of a week
	//ordered by status flag field active, upcoming, final (1,0,2)
	public function get_week_nfl_games($year,$week){
		$query = $this->NFL_db->select('nfl_game_id,home_team,away_team,start_time,status_flag')
							->where('week',$week)
							->where('season',$year)
							->order_by("FIELD(status_flag,'1','0','2'),start_time ASC")
							->get('NFL_Schedule_'.$year);
		$return_array = array();
		foreach($query->result_array() as $game){
			$return_array[]=$game;	
			
		}
		return $return_array;
		
	}


//*****************************************************************
	public function update_NFL_games_status($week,$year){

		$file = file_get_contents('http://www.nfl.com/ajax/scorestrip?season='.$year.'&seasonType=REG&week='.$week);
		$parsed_file = xml_parser_create();
		xml_parse_into_struct($parsed_file, $file, $vals, $index);
		xml_parser_free($parsed_file);
		$now=now();
		$nfl_xml_file_week=$vals[1]['attributes']['W'];
		$games_array = array();
		
		if($nfl_xml_file_week==$week) {//the weeks match, just a check to make sure we are in the right file
			$games = $this->search($vals, 'tag', 'G'); //each game is indexd by g

			foreach($games as $game_data) {
				$game_query = $this->NFL_db->where('nfl_game_id',$game_data['attributes']['EID'])
									->select('status_flag')
									->get('NFL_Schedule_'.$year); 
				foreach($game_query->result_array() as $game_query_info){
					$game_status_flag =$game_query_info['status_flag'];
				}
				
				$status_flag=0;
				$game_id = $game_data['attributes']['EID']; //eid
				
				$games_array[$game_id]['possession'] = $game_data['attributes']['P'];
				$games_array[$game_id]['is_in_redzone'] = $game_data['attributes']['RZ'];
				//check if this file hasn't started the game but the stats json has
				if($game_data['attributes']['Q']=='P' || $game_status_flag==1){
					
					$f = @file_get_contents('http://www.nfl.com/liveupdate/game-center/'.$game_id.'/'.$game_id.'_gtd.json');
					if($f){
						$jsonContent = json_decode($f,true);
						$game_content = array_slice($jsonContent,0,1);
						if(isset($game_content)){
							foreach($game_content as $game){
								$games_array[$game_id]['possession']=$game['posteam'];
								$games_array[$game_id]['is_in_redzone']=$game['redzone'];
								if($game['qtr']=='Final') { $game['qtr']='F'; }
								$game_data['attributes']['Q']=$game['qtr'];
								$game_data['attributes']['K']=$game['clock'];
								
							}
						}
					}
				}
				//d($game_data['attributes']['Q']);
				//Q is quarter 5 is OT. if not pregame	
				if($game_data['attributes']['Q']>0 || $game_data['attributes']['Q']=='H' || $game_data['attributes']['Q']=='Halftime' || $game_data['attributes']['Q']=='F'  || $game_data['attributes']['Q']=='FO' || $game_data['attributes']['Q']=='final overtime') {//q = 1-5 or F
						//set the current clock situation as status
						$status_flag=1;
						if($game_data['attributes']['Q']==1){
							$games_array[$game_id]['status'] = '1st QTR, '.$game_data['attributes']['K'];
						} else if($game_data['attributes']['Q']==2){
							$games_array[$game_id]['status'] = '2nd QTR, '.$game_data['attributes']['K'];
						} else if($game_data['attributes']['Q']==3){
							$games_array[$game_id]['status'] = '3rd QTR, '.$game_data['attributes']['K'];
						} else if($game_data['attributes']['Q']==4){
							$games_array[$game_id]['status'] = '4th QTR, '.$game_data['attributes']['K'];
						} else if($game_data['attributes']['Q']==5){
							$games_array[$game_id]['status'] = 'OT, '.$game_data['attributes']['K'];
						} else if($game_data['attributes']['Q']=='H' || $game_data['attributes']['Q']=='Halftime'){
							$games_array[$game_id]['status'] = 'Half';
						} else if($game_data['attributes']['Q']=='F' || $game_data['attributes']['Q']=='FO' || $game_data['attributes']['Q']=='final overtime'){
							$games_array[$game_id]['status'] = 'Final';
							$status_flag=2;
						}
						
				} else {
					//should set games that haven't begun as status P presumably for "Pregame"
					$games_array[$game_id]['status']=$game_data['attributes']['Q'];

				} 
				//d($game_id,$games_array[$game_id]['status']);
				$games_array[$game_id]['home_team']= $game_data['attributes']['H'];
				$games_array[$game_id]['away_team']= $game_data['attributes']['V'];
				//gets the year date and time from the format of the EID
				$date = substr($game_data['attributes']['EID'],0,4).'-'.substr($game_data['attributes']['EID'],4,2).'-'.substr($game_data['attributes']['EID'],6,2).' '.$game_data['attributes']['T'];
				//d($date);	
				if($game_data['attributes']['T']=='12:30'){
					 $games_array[$game_id]['start_time'] = strtotime($date)-7200;//
				} else{
					 $games_array[$game_id]['start_time'] = strtotime($date.'pm')-3600;//here
					// d($games_array[$game_id]['start_time']);
				}
				
				
			
			
				//update the table. First to check if the game has been inserted, if not insert. Otherwise update				
				$data = array(
							'nfl_game_id' => $game_id,
							'season' => $year,
							'week' => $week,
							'home_team' => $games_array[$game_id]['home_team'],
							'away_team' => $games_array[$game_id]['away_team'],
							'start_time' => $games_array[$game_id]['start_time'],
							'status' => $games_array[$game_id]['status'],
							'status_flag' => $status_flag,
							'possession' => $games_array[$game_id]['possession'],
							'is_in_redzone' => $games_array[$game_id]['is_in_redzone'],
							'last_update' => $now
						);
								
				$check_game_query = $this->NFL_db->where('nfl_game_id',$game_id)
										->select('status_flag')
										->get('NFL_Schedule_'.$year);
				
				if($check_game_query->num_rows()==0){
					//insert the game
					$insert_game = $this->NFL_db->insert('NFL_Schedule_'.$year,$data);	
				}
				else{
					
					//update the game
					$update_game = $this->NFL_db->where('nfl_game_id',$game_id)
										->update('NFL_Schedule_'.$year,$data);
					
					//in case the NFL file hasn't started the game yet
					//d($week,$status_flag, $games_array[$game_id]['start_time'],now());
					if($status_flag>0 ||  $games_array[$game_id]['start_time']<now()){
						
						//lock the players for this game
						$this->Players->lock_players($games_array[$game_id]['home_team'],$games_array[$game_id]['away_team'],$year,$week);
						
					}
				}
				
				//check if 9 a.m. sunday deadline has passed and if this game should be locked
				if(now()>$this->get_week_sunday_deadline($week,$year) && $games_array[$game_id]['start_time']<$this->get_week_sunday_deadline_gametimes($week,$year)){
					
					//lock the players for this game
					$this->Players->lock_players($games_array[$game_id]['home_team'],$games_array[$game_id]['away_team'],$year,$week);
				}
				
				//for probowl check if deadline has passed and update teh probowl
				if(now()>$this->get_week_sunday_deadline($week,$year) && $week==17){
					$this->load->model('Rosters');
					$this->Rosters->update_all_pro_team(1,$year);	//NI first parameter should be league id
				}
					
			}// for each game

			return TRUE;
	
		}// if weeks match
		else {//the weeks didn't match up
			return 'error: weeks mismatch';
		
		}
	
	
	}//end update game status
  
 
  //******************************************************************************
  
  public function get_week_first_game($week,$season){
    
		$query=$this->NFL_db->where('week',$week)
						->where('season',$season)
						->order_by('start_time','ASC')
						->limit(1)
						->get('NFL_Schedule_'.$season);
		foreach($query->result_array() as $game){
		  return $game['start_time'];
		}
    
    
  }
  
    //******************************************************************************
  
  public function get_week_sunday_deadline($week,$season){
    
		$query=$this->NFL_db->where('week',$week)
						->where('season',$season)
						->order_by('start_time','ASC')
						->get('NFL_Schedule_'.$season);
		
		//echo $season.' ';
		foreach($query->result_array() as $game){
		  if(date('l',$game['start_time'])=='Sunday'){
			return strtotime(date('j F Y',$game['start_time']).' 9:00');
		  }
		}
    
    
  }
  
  public function get_week_sunday_deadline_gametimes($week,$season){
	  	$query=$this->NFL_db->where('week',$week)
						->where('season',$season)
						->order_by('start_time','ASC')
						->get('NFL_Schedule_'.$season);
		
		foreach($query->result_array() as $game){
		  if(date('l',$game['start_time'])=='Sunday'){
			return strtotime(date('j F Y',$game['start_time']).' 13:00');
		  }
		}
	  
  }
  
  //**********************************************************************************
  	//returns the game_ids of active games
	public function get_active_games(){
		
		$query = $this->NFL_db->where('week',$this->current_week)
								->where('season',$this->current_year)
								->where("status_flag",1)
								->get('NFL_Schedule_'.$this->current_year);
		$return_array=array();
		foreach($query->result_array() as $game_data){
			$return_array[]=$game_data['nfl_game_id'];
		}
		return $return_array;
	}

//*************************************************************************************

	//returns array of possesion and redzone teams
	public function get_possession_redzone($week,$year){
		$return_array=array();
		$return_array['possession']=array();
		$return_array['redzone']=array();
		$query=$this->NFL_db->where('week',$week)
					->where('season',$year)
					->where('status_flag',1)
					->get('NFL_Schedule_'.$year);
		foreach($query->result_array() as $data){
			if(isset($data['possession'])){
				$return_array['possession'][]=$data['possession'];
				if($data['is_in_redzone']==1){
					$return_array['redzone'][]=	$data['possession'];
				}
			
			}
		}
		
		return $return_array;
	}
 //***********************************************************************************
	 function search2($array, $key, $value, $prevkey, $prev2key)
		{
			$results = array();
		
			if (is_array($array))
			{	
				
				if (isset($array[$key]) && $array[$key] == $value) {
					$array['gsis']=$prev2key;
					$results[] = $array;
				}
				$prev2key = $prevkey;
				$prevkey = key($array);
				foreach ($array as $subarray) {
					$results = array_merge($results, $this->search2($subarray, $key, $value, $prevkey, $prev2key));
				}
			}
		
			return $results;
		}
 //***********************************************************************************
 	//LIVE SCORING.  Updates the stats for a given game
	
	public function update_nfl_stats_game($year,$week,$game_id){
	
		$f = file_get_contents('http://www.nfl.com/liveupdate/game-center/'.$game_id.'/'.$game_id.'_gtd.json');
		$jsonContent = json_decode($f,true);
		if(!is_null($jsonContent)){
			$game_content = array_slice($jsonContent,0,1);
		}
		else {
			$game_content = array();	
		}
		//d($game_content['0']['drives']['20']);
		$players_data=array();
		$players_list=array();

		foreach($game_content as $game) { //this is level one just the game, there's only one key here
		
			$home = $game['home']['abbr'];
			$away = $game['away']['abbr'];
			//d($game);
			//PASSING
			foreach(array('home','away') as $home_away){
				if(isset($game[$home_away]['stats']['passing'])){
					foreach($game[$home_away]['stats']['passing'] as $gsis=>$stats) {//home team passing stats each key is a different player
						if(!in_array($gsis,$players_list)) {
							$name = $stats['name'];
							$players_list[]=$gsis;
							$players_data[$gsis]=$this->add_to_players_list($gsis,$home_away,$name,$home,$away);
							
						}//if !in players_list
						
						//get the stats
						$players_data[$gsis]=array_merge($players_data[$gsis],$this->parse_passing($stats,$$home_away));
						$players_data[$gsis]['two_point_made']=$players_data[$gsis]['two_point_made']+$stats['twoptm'];	
						
					} //foreach passing player
				}
			}//foreach home_away
			
			//RUSHING
			foreach(array('home','away') as $home_away){
				if(isset($game[$home_away]['stats']['rushing'])){
					foreach($game[$home_away]['stats']['rushing'] as $gsis=>$stats) {//home team passing stats each key is a different player
						if(!in_array($gsis,$players_list)) {
							$name = $stats['name'];
							$players_list[]=$gsis;
							$players_data[$gsis]=$this->add_to_players_list($gsis,$home_away,$name,$home,$away);
							
						}//if !in players_list
						
						//get the stats
						$players_data[$gsis]=array_merge($players_data[$gsis],$this->parse_rushing($stats,$$home_away));
						$players_data[$gsis]['two_point_made']=$players_data[$gsis]['two_point_made']+$stats['twoptm'];
							
					} //foreach rushing player
				}
			}//foreach home_away
			
			//RECEIVING
			foreach(array('home','away') as $home_away){
				if(isset($game[$home_away]['stats']['receiving'])){
					foreach($game[$home_away]['stats']['receiving'] as $gsis=>$stats) {//home team receiving stats each key is a different player
						if(!in_array($gsis,$players_list)) {
							$name = $stats['name'];
							$players_list[]=$gsis;
							$players_data[$gsis]=$this->add_to_players_list($gsis,$home_away,$name,$home,$away);
							
						}//if !in players_list
						
						//get the stats
						$players_data[$gsis]=array_merge($players_data[$gsis],$this->parse_receiving($stats,$$home_away));
						$players_data[$gsis]['two_point_made']=$players_data[$gsis]['two_point_made']+$stats['twoptm'];
							
					} //foreach receiving player
				}
			}//foreach home_away
			
			//FUMBLES
			foreach(array('home','away') as $home_away){
				if(isset($game[$home_away]['stats']['fumbles'])){
					foreach($game[$home_away]['stats']['fumbles'] as $gsis=>$stats) {//home team fumbles stats each key is a different player
						if(!in_array($gsis,$players_list)) {
							$name = $stats['name'];
							$players_list[]=$gsis;
							$players_data[$gsis]=$this->add_to_players_list($gsis,$home_away,$name,$home,$away);
							
						}//if !in players_list
						
						//get the stats
						$players_data[$gsis]=array_merge($players_data[$gsis],$this->parse_fumbles($stats,$$home_away));
							
					} //foreach fumbles player
				}
			}//foreach home_away
			
			//EXTRA POINTS
			foreach(array('home','away') as $home_away){
				if(isset($game[$home_away]['stats']['kicking'])){
					foreach($game[$home_away]['stats']['kicking'] as $gsis=>$stats) {//home team xpt stats each key is a different player
						if(!in_array($gsis,$players_list)) {
							$name = $stats['name'];
							$players_list[]=$gsis;
							$players_data[$gsis]=$this->add_to_players_list($gsis,$home_away,$name,$home,$away);
							
						}//if !in players_list
						
						//get the stats
						$players_data[$gsis]=array_merge($players_data[$gsis],$this->parse_extra_points($stats,$$home_away));
							
					} //foreach extra points player
				}
			}//foreach home_away
			
			//Kick Returns
			foreach(array('home','away') as $home_away){
				if(isset($game[$home_away]['stats']['kickret'])){
					foreach($game[$home_away]['stats']['kickret'] as $gsis=>$stats) {//home team k ret stats each key is a different player
						if(!in_array($gsis,$players_list)) {
							$name = $stats['name'];
							$players_list[]=$gsis;
							$players_data[$gsis]=$this->add_to_players_list($gsis,$home_away,$name,$home,$away);
							
						}//if !in players_list
						
						//get the stats
						$players_data[$gsis]=array_merge($players_data[$gsis],$this->parse_kick_returns($stats,$$home_away));
							
					} //foreach kick returns player
				}
			}//foreach home_away
			
			//Punt Returns
			foreach(array('home','away') as $home_away){
				if(isset($game[$home_away]['stats']['puntret'])){
					foreach($game[$home_away]['stats']['puntret'] as $gsis=>$stats) {//home team punt ret stats each key is a different player
						if(!in_array($gsis,$players_list)) {
							$name = $stats['name'];
							$players_list[]=$gsis;
							$players_data[$gsis]=$this->add_to_players_list($gsis,$home_away,$name,$home,$away);
							
						}//if !in players_list
						
						//get the stats
						$players_data[$gsis]=array_merge($players_data[$gsis],$this->parse_punt_returns($stats,$$home_away));
							
					} //foreach punt return player
				}
			}//foreach home_away
			
			//Field Goals
			$missedFG = $this->search2($game['drives'], 'statId', '69','','');
			$missedFGBlock = $this->search2($game['drives'], 'statId', '71','','');
			$madeFG = $this->search2($game['drives'], 'statId', '70','','');
			
			foreach($missedFG as $stats) {//all team missed FGS stats each key is a different player
				
				$team = $stats['clubcode'];
				if($team == $home) { $home_away='home'; } else { $home_away='away';  }
				$gsis = $stats['gsis'];
				
					foreach(array('19','29','39','49','59','60') as $distance){
						if(!isset($players_data[$gsis]['fgs_missed_'.$distance])) { $players_data[$gsis]['fgs_missed_'.$distance]=0; }
						if(!isset($players_data[$gsis]['fgs_made_'.$distance])) { $players_data[$gsis]['fgs_made_'.$distance]=0; }
				
					}
				
				
				if(!in_array($gsis,$players_list)) {
						$name = $stats['playerName'];
						$players_list[]=$gsis;
						$players_data[$gsis]=$this->add_to_players_list($gsis,$home_away,$name,$home,$away);
						
				}//if !in players_list
				
				if($stats['yards']<20) {
					if(isset($players_data[$gsis]['fgs_missed_19'])){
						$players_data[$gsis]['fgs_missed_19']=$players_data[$gsis]['fgs_missed_19']+1;
					} 
					else {
						$players_data[$gsis]['fgs_missed_19']=1;
					}
				} else if($stats['yards']<30) {
					if(isset($players_data[$gsis]['fgs_missed_29'])){
						$players_data[$gsis]['fgs_missed_29']=$players_data[$gsis]['fgs_missed_29']+1;
					} 
					else {
						$players_data[$gsis]['fgs_missed_29']=1;
					}
				} else if($stats['yards']<40) {
					if(isset($players_data[$gsis]['fgs_missed_39'])){
						$players_data[$gsis]['fgs_missed_39']=$players_data[$gsis]['fgs_missed_39']+1;
					} 
					else {
						$players_data[$gsis]['fgs_missed_39']=1;
					}
				} else if($stats['yards']<50) {
					if(isset($players_data[$gsis]['fgs_missed_49'])){
						$players_data[$gsis]['fgs_missed_49']=$players_data[$gsis]['fgs_missed_49']+1;
					} 
					else {
						$players_data[$gsis]['fgs_missed_49']=1;
					}
				} else if($stats['yards']<60) {
					if(isset($players_data[$gsis]['fgs_missed_59'])){
						$players_data[$gsis]['fgs_missed_59']=$players_data[$gsis]['fgs_missed_59']+1;
					} 
					else {
						$players_data[$gsis]['fgs_missed_59']=1;
					}
				} else if($stats['yards']>59) {
					if(isset($players_data[$gsis]['fgs_missed_60plus'])){
						$players_data[$gsis]['fgs_missed_60plus']=$players_data[$gsis]['fgs_missed_60plus']+1;
					} 
					else {
						$players_data[$gsis]['fgs_missed_60plus']=1;
					}
				} 
				
			}//missed FGS STATS
			
			foreach($missedFGBlock as $stats) {//all team missed FGS stats each key is a different player
				
				$team = $stats['clubcode'];
				if($team == $home) { $home_away='home'; } else { $home_away='away';  }
				$gsis = $stats['gsis'];
					foreach(array('19','29','39','49','59','60') as $distance){
						if(!isset($players_data[$gsis]['fgs_missed_'.$distance])) { $players_data[$gsis]['fgs_missed_'.$distance]=0; }
						if(!isset($players_data[$gsis]['fgs_made_'.$distance])) { $players_data[$gsis]['fgs_made_'.$distance]=0; }
				
					}
				if(!in_array($gsis,$players_list)) {
						$name = $stats['playerName'];
						$players_list[]=$gsis;
						$players_data[$gsis]=$this->add_to_players_list($gsis,$home_away,$name,$home,$away);
						
				}//if !in players_list
				
				if($stats['yards']<20) {
					if(isset($players_data[$gsis]['fgs_missed_19'])){
						$players_data[$gsis]['fgs_missed_19']=$players_data[$gsis]['fgs_missed_19']+1;
					} 
					else {
						$players_data[$gsis]['fgs_missed_19']=1;
					}
				} else if($stats['yards']<30) {
					if(isset($players_data[$gsis]['fgs_missed_29'])){
						$players_data[$gsis]['fgs_missed_29']=$players_data[$gsis]['fgs_missed_29']+1;
					} 
					else {
						$players_data[$gsis]['fgs_missed_29']=1;
					}
				} else if($stats['yards']<40) {
					if(isset($players_data[$gsis]['fgs_missed_39'])){
						$players_data[$gsis]['fgs_missed_39']=$players_data[$gsis]['fgs_missed_39']+1;
					} 
					else {
						$players_data[$gsis]['fgs_missed_39']=1;
					}
				} else if($stats['yards']<50) {
					if(isset($players_data[$gsis]['fgs_missed_49'])){
						$players_data[$gsis]['fgs_missed_49']=$players_data[$gsis]['fgs_missed_49']+1;
					} 
					else {
						$players_data[$gsis]['fgs_missed_49']=1;
					}
				} else if($stats['yards']<60) {
					if(isset($players_data[$gsis]['fgs_missed_59'])){
						$players_data[$gsis]['fgs_missed_59']=$players_data[$gsis]['fgs_missed_59']+1;
					} 
					else {
						$players_data[$gsis]['fgs_missed_59']=1;
					}
				} else if($stats['yards']>59) {
					if(isset($players_data[$gsis]['fgs_missed_29'])){
						$players_data[$gsis]['fgs_missed_60plus']=$players_data[$gsis]['fgs_missed_60plus']+1;
					} 
					else {
						$players_data[$gsis]['fgs_missed_60plus']=1;
					}
				} 
				
			}//missed FGS block STATS
			
			//Made FGs
			foreach($madeFG as $stats) {//all team missed FGS stats each key is a different player
				
				$team = $stats['clubcode'];
				if($team == $home) { $home_away='home'; } else { $home_away='away';  }
				$gsis = $stats['gsis'];
					foreach(array('19','29','39','49','59','60') as $distance){
						if(!isset($players_data[$gsis]['fgs_missed_'.$distance])) { $players_data[$gsis]['fgs_missed_'.$distance]=0; }
						if(!isset($players_data[$gsis]['fgs_made_'.$distance])) { $players_data[$gsis]['fgs_made_'.$distance]=0; }
				
					}
				if(!in_array($gsis,$players_list)) {
						$name = $stats['playerName'];
						$players_list[]=$gsis;
						$players_data[$gsis]=$this->add_to_players_list($gsis,$home_away,$name,$home,$away);
						
				}//if !in players_list
				
				if($stats['yards']<20) {
					if(isset($players_data[$gsis]['fgs_made_19'])){
						$players_data[$gsis]['fgs_made_19']=$players_data[$gsis]['fgs_made_19']+1;
					} 
					else {
						$players_data[$gsis]['fgs_made_19']=1;
					}
				} else if($stats['yards']<30) {
					if(isset($players_data[$gsis]['fgs_made_29'])){
						$players_data[$gsis]['fgs_made_29']=$players_data[$gsis]['fgs_made_29']+1;
					} 
					else {
						$players_data[$gsis]['fgs_made_29']=1;
					}
				} else if($stats['yards']<40) {
					if(isset($players_data[$gsis]['fgs_made_39'])){
						$players_data[$gsis]['fgs_made_39']=$players_data[$gsis]['fgs_made_39']+1;
					} 
					else {
						$players_data[$gsis]['fgs_made_39']=1;
					}
				} else if($stats['yards']<50) {
					if(isset($players_data[$gsis]['fgs_made_49'])){
						$players_data[$gsis]['fgs_made_49']=$players_data[$gsis]['fgs_made_49']+1;
					} 
					else {
						$players_data[$gsis]['fgs_made_49']=1;
					}
				} else if($stats['yards']<60) {
					if(isset($players_data[$gsis]['fgs_made_59'])){
						$players_data[$gsis]['fgs_made_59']=$players_data[$gsis]['fgs_made_59']+1;
					} 
					else {
						$players_data[$gsis]['fgs_missed_59']=1;
					}
				} else if($stats['yards']>59) {
					if(isset($players_data[$gsis]['fgs_made_60plus'])){
						$players_data[$gsis]['fgs_made_60plus']=$players_data[$gsis]['fgs_made_60plus']+1;
					} 
					else {
						$players_data[$gsis]['fgs_made_60plus']=1;
					}
				} 
				
			}//made FGS STATS
		  
		  
          	//insert stats into table
			
			$this->NFL_stats->update_stats($players_data,$year,$week);
          
		}//foreach game_content as game  //end of the game
	
	}//end update stats method
		

//*********************************************************************

	public function parse_passing($stats,$team){ 
					
		$players_data['completions']=$stats['cmp'];
		$players_data['incompletions']=$stats['att']-$stats['cmp'];
		$players_data['pass_yards']=$stats['yds'];
		$players_data['interceptions']=$stats['ints'];
		$players_data['pass_tds']=$stats['tds'];
		
		return $players_data;
	}
	
	public function parse_rushing($stats,$team){
		$players_data['rushes']=$stats['att'];
		$players_data['rush_yards']=$stats['yds'];
		$players_data['rush_tds']=$stats['tds'];
			
		
		return $players_data;
	}
	
	public function parse_receiving($stats,$team){
		$players_data['receptions']=$stats['rec'];
		$players_data['receiving_yards']=$stats['yds'];
		$players_data['receiving_tds']=$stats['tds'];
			
		
		return $players_data;
	}
	
	public function parse_fumbles($stats,$team){
		$players_data['fumbles']=$stats['tot'];
		$players_data['fumbles_lost']=$stats['lost'];	
		
		return $players_data;
	}
	
	public function parse_extra_points($stats,$team){
		$players_data['xps_made']=$stats['xpmade'];
		$players_data['xps_missed']=$stats['xpmissed'];	
		
		return $players_data;	
	}
	
	public function parse_kick_returns($stats,$team){
		$players_data['kick_return_tds']=$stats['tds'];
		
		return $players_data;	
	}
	
	public function parse_punt_returns($stats,$team){
		$players_data['punt_return_tds']=$stats['tds'];
		
		return $players_data;	
	}
	

	public function add_to_players_list($gsis,$home_away,$name,$home,$away) {
		$fffl_player_id = $this->Players->convert_player_id($gsis, 'nfl_gsis_player_id', 'fffl_player_id');
						
		$players_data['gsisPlayerId']=$gsis;
		$players_data['playerteam']=$$home_away;
		$players_data['playername']= $name;
		if($home_away=='home'){
			$players_data['vs']=$away;
		}
		else{
			$players_data['vs']=$home;
		}
		$players_data['two_point_made']='0';	
		
		return $players_data;
	}
 
//******************************************************************	
	
}


/*End of file NFL_Games.php*/
/*Location: ./application/models/NFL_Games.php*/