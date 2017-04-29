<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Game extends MY_Controller
{
	/**
	 * Game controller.
	 *
	 * ???????
	 */
	 
	public $league_id = 1; //***NI*** remove when multiple leagues are in place
	public $current_year;	
	public $current_week;
	
	//Load the needed libraries.  
	public function __construct() 
    {
		parent::__construct();

		$this->load->helper('date');
		$this->load->helper('combinations');
		$this->load->helper('links');
		
		$this->load->model('Games');
		$this->load->model('Leagues');
		$this->load->model('NFL_stats');
		$this->load->model('NFL_Teams');
		$this->load->model('Standings');
		
		$this->current_year = $this->Leagues->get_current_season($this->league_id);
		$this->current_week = $this->Leagues->get_current_week($this->league_id);

	}


	// 
	// 
	public function index($page_content='games', $content_data=array()) 
    {
		
		//this is loading a view from one of the methods to display on the page
			//RESTRICTED TO LOGGED IN MEMBERS ONLY
			//Just in case not logged in we direct to login page
			if (!$this->session->userdata('logged_in') )
			{
				redirect("/Restricted");
			} 
			
			$team_id = $this->session->team_id;
			
			
			$path = '/'.$page_content;
			
			$this->load_view($path, $content_data, false, false, false);

	}



//**************************************************************
  
	public function scores($year=NULL,$week=NULL) 
	{
		//RESTRICTED TO LOGGED IN MEMBERS ONLY
		//Just in case not logged in we direct to login page
		if (!$this->session->userdata('logged_in') )
		{
			redirect("/Restricted");
		} 
		
		if(is_null($year)){ $year=$this->current_year; }
		if(is_null($week)){ $week=$this->current_week; }
		$team_id = $this->session->team_id;

		$content_data['current_year'] = $this->current_year;
		
		//content to initially display
		$content_data['display_page']='Games';
		$content_data['load_path'] = 'Game/week/'.$this->league_id.'/'.$year.'/'.$week;
		//send a display for the dropdown. it should match what is in the content_selector array
		//should be the same as the page_content which is also the method. multiple words
		//can be separated by _ and it will take those out and capitalize each word
		$content_data['dropdown_title'] = $year;
		//content selector will be activated for these views
		//set the list of items for content selector
		$content_data['content_selector'] = array();
			//each key is the display in the dropdown, linked to the path to the method
			$y=$this->current_year;
			while($y >=2004){
				$content_data['content_selector'][$y]= base_url().'Game/week/'.$this->league_id.'/'.$y.'/1';
				$y--;
			}
		
		//titles of the pages will be upper cased
		$title = 'Scoreboard';
		$content_data['title']= ucwords($title);
		$path = 'games/scoreboard_container';
		
		
		$this->load_view($path, $content_data, true);
		
	}


	
//**************************************************************
  
	public function week($league_id=1,$year,$week) 
	{
		$content_data['year']=$year;
		$content_data['current_year']=$this->current_year;
		$content_data['week']=$week;
		$content_data['league_id']=$league_id;
		if($week>=$this->Leagues->get_first_playoff_week($league_id) && $week<17){
			$content_data['is_playoffs']=TRUE;
		}
		else {
			$content_data['is_playoffs']=FALSE;	
		}
		$content_data['first_playoff']=$this->Leagues->get_first_playoff_week($league_id);
		$content_data['superbowl_week']=$this->Leagues->get_superbowl_week($league_id);
		
		
		 $this->index('games/scoreboard', $content_data);
	}

	//**************************************************************
  
	public function league_games_load($league_id=1,$year,$week) 
	{
		$content_data['year']=$year;
		$content_data['current_year']=$this->current_year;
		$content_data['week']=$week;
		$content_data['league_id']=$league_id;
		
		$league_roster = explode(",",$this->Rosters->get_league_lineup($league_id));
		
		$content_data['number_starters']=count($league_roster);
		$content_data['number_games']=count($this->Games->get_week_games($league_id,$year,$week));
		
		if($week>=$this->Leagues->get_first_playoff_week($league_id) && $week<17){
			$content_data['is_playoffs']=TRUE;
		}
		else {
			$content_data['is_playoffs']=FALSE;	
		}
		$content_data['first_playoff']=$this->Leagues->get_first_playoff_week($league_id);
		$content_data['superbowl_week']=$this->Leagues->get_superbowl_week($league_id);
		
		 $this->index('games/league_games', $content_data);
	}	
	
	//**************************************************************
  
	public function league_games($league_id=1,$year,$week) 
	{
		
		$content_data['year']=$year;
		$content_data['current_year']=$this->current_year;
		$content_data['week']=$week;
		
		$content_data['league_id']=$league_id;
		
		$content_data['toilet_bowl_standings']=array();
		
		$content_data['games_array'] = $this->Games->get_week_games($league_id,$year,$week);
		
		
		//d($content_data['games_array']);
		//add the rosters to to the games_array
		foreach($content_data['games_array'] as $game_index => $game_data){
			if($game_data['is_toilet']==1 || $week==17){
					$content_data['toilet_bowl_standings'][]=array("score"=>$game_data['opponent_a_score'],"team"=>team_name_link($game_data["opponent_a"],TRUE,TRUE));
					$content_data['toilet_bowl_standings'][]=array("score"=>$game_data['opponent_b_score'],"team"=>team_name_link($game_data["opponent_b"],TRUE,TRUE));
			}
			
			foreach(array("a","b") as $letter){
					$team_id = $game_data['opponent_'.$letter];
					
					if($team_id > 0){
						$content_data['games_array'][$game_index]['opponent_'.$letter] = team_name_link($team_id,TRUE,TRUE);
						$content_data['games_array'][$game_index]['opponent_'.$letter.'_small'] = team_name_link($team_id,TRUE,FALSE);
						$content_data['games_array'][$game_index]['logo_'.$letter] = $this->Teams->get_team_logo_path($team_id);
					} 
					else {
						$content_data['games_array'][$game_index]['opponent_'.$letter] = "All Pro Team";
						$content_data['games_array'][$game_index]['opponent_'.$letter.'_small'] = "All Pro Team";
						$content_data['games_array'][$game_index]['logo_'.$letter] = base_url()."assets/img/logos/probowl.jpg";
					}
					
					$wins_losses = $this->Standings->get_team_wins_losses_year($team_id,$year, TRUE,$week);
					$content_data['games_array'][$game_index]['record_'.$letter] = '('.$wins_losses['wins'].'-'.$wins_losses['losses'].', '.$this->Standings->get_team_total_points_year($team_id,$year, TRUE,$week).')';
					$content_data['games_array'][$game_index]['tiebreaker_'.$letter] = 0.0;
					//add the starters to the array
					
					if($week < 17){
						$starters=$this->Rosters->get_team_starters($game_data['opponent_'.$letter], $week, $year);
					}
					else { //pro bowl
						$probowl = $this->Rosters->get_probowl_roster($team_id,$year);
						$starters=array();
						$query_locked = $this->db->get("Locked_Players");
						if(count($query_locked->result_array()) > 0){
							foreach($probowl as $position){
								foreach($position as $fffl_player_id){
									$starters[]=$fffl_player_id;	
								}
							}
						}
						
					}
					
					if(empty($starters)){
						$content_data['games_array'][$game_index]['starters_'.$letter]=array();
					}
					else {
						
						foreach($starters as $fffl_player_id){
							
							$score = $this->NFL_stats->get_player_scores_season($year,$fffl_player_id,0,$week,$week,0);
							if(is_array($score['team'])){ $score['team']=$score['team']['current_team']; }
	
							$status=explode(' ',$this->Players->get_player_game_status($fffl_player_id,$year,$week),2);
							
							//players on bye have no status, so fill status with blank
							if(!isset($status['1'])){
								$status['1']="";
								
							}
							$content_data['games_array'][$game_index]['tiebreaker_'.$letter] += $score['weeks'][$week]['decimal'];						
							
							$content_data['games_array'][$game_index]['starters_'.$letter][] = array(
								'score'=>$score['weeks'][$week]['points'],
								'decimal'=>$score['weeks'][$week]['decimal'],
								'team'=>$score['team'],
								'name'=> player_name_link($fffl_player_id,TRUE,TRUE),
								'status'=> $status['1'],
								'fffl_player_id'=> $fffl_player_id
							);
							
						}
					}
					//add the bench to the array
					if($year == $this->current_year){
						$bench=$this->Rosters->get_team_bench($game_data['opponent_'.$letter], $week, $year);
					} else {
						$bench = array();
					}
						$content_data['games_array'][$game_index]['bench_'.$letter.'_number']=count($bench);
					
					if(empty($bench)){
						$content_data['games_array'][$game_index]['bench_'.$letter]=array();
					}
					else {
						
						foreach($bench as $fffl_player_id){
							
							$score = $this->NFL_stats->get_player_scores_season($year,$fffl_player_id,0,$week,$week,0);
							if(is_array($score['team'])){ $score['team']=$score['team']['current_team']; }
							
							$status=explode(' ',$this->Players->get_player_game_status($fffl_player_id,$year,$week),2);
							
							//players on bye have no status, so fill status with blank
							if(!isset($status['1'])){
								$status['1']="";
								
							}
							$content_data['games_array'][$game_index]['tiebreaker_'.$letter] += $score['weeks'][$week]['decimal'];						
							
							$content_data['games_array'][$game_index]['bench_'.$letter][] = array(
								'score'=>$score['weeks'][$week]['points'],
								'decimal'=>$score['weeks'][$week]['decimal'],
								'team'=>$score['team'],
								'name'=> player_name_link($fffl_player_id,TRUE,TRUE),
								'status'=> $status['1'],
								'fffl_player_id'=> $fffl_player_id
							);
							
						}
					}
				
				
			}//foreach a and b
			
		}//foreach game
		
		if(!empty($content_data['toilet_bowl_standings'])){
			//sort toiletbowl
			// Obtain a list of columns
			
			foreach ($content_data['toilet_bowl_standings'] as $key => $row) {
				$team[$key]  = $row['team'];
				$tb_scores[$key] = $row['score'];
			}

			// Sort the data with score descending, team ascending
			array_multisort($tb_scores, SORT_DESC, $team, SORT_DESC, $content_data['toilet_bowl_standings']);
		}
		
		//get all the teams in possession and all the teams in the redzone
		$content_data = array_merge($content_data,$this->NFL_Games->get_possession_redzone($week,$year));
		header('Content-Type: application/json');
		echo json_encode($content_data);
	}	
	
	
	//**************************************************************
  
	public function nfl_games_load($year,$week) 
	{
		$content_data['year']=$year;
		$content_data['current_year']=$this->current_year;
		$content_data['week']=$week;
		
		//add number_games here
		//get all the nfl games orderd by status flag 1,0,2
		$content_data['number_games'] = count( $this->NFL_Games->get_week_nfl_games($year,$week));
		
		 $this->index('games/nfl_games', $content_data);	
	}	
	
	//**********************************************************************

	public function nfl_games($season,$week){
		
		//get all the nfl games orderd by status flag 1,0,2
		$nfl_games = $this->NFL_Games->get_week_nfl_games($season,$week);
		
		//foreach nfl game
		$data['games_array']=array();
		$game_index = 1;
		foreach($nfl_games as $game_data){
			
			
			$data['games_array'][$game_index]['nfl_game_id'] =$game_data['nfl_game_id'];
			$data['games_array'][$game_index]['home_team'] = $game_data['home_team'];
			$data['games_array'][$game_index]['away_team'] = $game_data['away_team'];
			$data['games_array'][$game_index]['start_time'] = $game_data['start_time'];
			$data['games_array'][$game_index]['status_flag'] = $game_data['status_flag'];
			$data['games_array'][$game_index]['home_team_score'] = $game_data['home_team'];
			$data['games_array'][$game_index]['away_team_score'] = $game_data['away_team'];
			$data['games_array'][$game_index]['time'] = date("D g:i A",$game_data['start_time']);
			//game is current
			if($game_data['status_flag']>0){
				$f = file_get_contents('http://www.nfl.com/liveupdate/game-center/'.$game_data['nfl_game_id'].'/'.$game_data['nfl_game_id'].'_gtd.json');
				$json_content = json_decode($f,true);
				$game_content = array_slice($json_content,0,1);
				
				foreach($game_content as $game) { //this is level one just the game, there's only one key here
					$to_bullets_home="";
					$to_bullets_away="";
					
					if($game_data['status_flag']==1){
					
						$quarter='';
						if($game['qtr']==1){
							$quarter='1st';
						}
						if($game['qtr']==2){
							$quarter='2nd';
						}
						if($game['qtr']==3){
							$quarter='3rd';
						}
						if($game['qtr']==4){
							$quarter='4th';
						}
						if($game['qtr']==5){
							$quarter='OT';
						}

						$data['games_array'][$game_index]['time'] = $game['clock'].' '.$quarter;
						if($game['qtr']=="Halftime"){
							$data['games_array'][$game_index]['time']="Halftime";
						}
						
						$data['games_array'][$game_index]['away_to']=$game['away']['to'];
						$data['games_array'][$game_index]['home_to']=$game['home']['to'];
						$down='';
						if($game['down']==1){
							$down = '<br>'.$game['posteam'].' at '.$game['yl'].' '.$game['down'].'st'.' & '.$game['togo'];
						}
						if($game['down']==2){
							$down = '<br>'.$game['posteam'].' at '.$game['yl'].' '.$game['down'].'nd'.' & '.$game['togo'];
						}
						if($game['down']==3){
							$down = '<br>'.$game['posteam'].' at '.$game['yl'].' '.$game['down'].'rd'.' & '.$game['togo'];
						}
						if($game['down']==4){
							$down = '<br>'.$game['posteam'].' at '.$game['yl'].' '.$game['down'].'th'.' & '.$game['togo'];
						}
						$data['games_array'][$game_index]['down'] = $down;
						
						for($to=1;$to<=$data['games_array'][$game_index]['home_to'];$to++){
							$to_bullets_home .= "&bull;";
						}
						for($to=1;$to<=$data['games_array'][$game_index]['away_to'];$to++){
							$to_bullets_away .= "&bull;";
						}
						$drives = $game['drives'];
						end($drives);
						$driveKey = key($drives);
						
						$plays= $drives[$driveKey]['plays'];
						if(!$plays) {
							prev($drives);
							$driveKey = key($drives);
							$plays= $drives[$driveKey]['plays'];
						}
						$i=0;
						$count=count($plays);
						end($plays);
						$data['games_array'][$game_index]['plays']='';
						while($i<$count && $i<3) {
							$key = key($plays);
							$time= $plays[$key]['time'];
							$yrdln = $plays[$key]['yrdln'];
							$data['games_array'][$game_index]['plays'] .= '&#149;'.$plays[$key]['down'].'&'.$plays[$key]['ydstogo'].': '.$plays[$key]['desc'].'<br>';
							
							$prev = prev($plays);
							$i++;
						}
						
					}
					else if($game_data['status_flag']==2){
						$data['games_array'][$game_index]['time']=$game['qtr'];
						$data['games_array'][$game_index]['down']="";
					}
					else{
						$data['games_array'][$game_index]['time'] = date('D g:ia',$game['start_time']);
						$data['games_array'][$game_index]['down']="";
					}
					
					$data['games_array'][$game_index]['home_team_score'] = $to_bullets_home.' '.$game['home']['score']['T'].' '.$data['games_array'][$game_index]['home_team'];	
					$data['games_array'][$game_index]['away_team_score'] = $data['games_array'][$game_index]['away_team'].' '.$game['away']['score']['T'].' '.$to_bullets_away;
					
					$data['games_array'][$game_index]['to_go'] = $game['togo'];
					$data['games_array'][$game_index]['yard_line'] = $game['yl'];
					$data['games_array'][$game_index]['clock'] = $game['clock'];
					$data['games_array'][$game_index]['quarter'] =  $game['qtr'];
					
					if($data['games_array'][$game_index]['quarter']=='Halftime'){$data['games_array'][$game_index]['quarter']='H';}
					$data['games_array'][$game_index]['redzone'] = $game['redzone'];
					$data['games_array'][$game_index]['possession'] = $game['posteam'];
					
					
					//home passing
					$data['games_array'][$game_index]['home_passing']=array();
					if(isset($game['home']['stats']['passing'])){
						foreach($game['home']['stats']['passing'] as $player){
                        	$data['games_array'][$game_index]['home_passing'][] = '
								<tr>
									<td class="text-left"><small>'.$player['name'].'</small></td>
                                    <td class="text-center"><small>'.$player['cmp'].'/'.($player['cmp']+$player['att']).'</small></td>
									<td class="text-center"><small>'.$player['yds'].'</small></td>
									<td class="text-center"><small>'.$player['tds'].'</small></td>
									<td class="text-center"><small>'.$player['ints'].'</small></td>
									<td class="text-center"><small>'.$player['twoptm'].'/'.($player['twoptm']+$player['twopta']).'</small></td>
								</tr>';
                        } 
					}
					//away passing
					$data['games_array'][$game_index]['away_passing']=array();
					if(isset($game['away']['stats']['passing'])){
						foreach($game['away']['stats']['passing'] as $player){
                        	$data['games_array'][$game_index]['away_passing'][] = '
								<tr>
									<td class="text-left"><small>'.$player['name'].'</small></td>
                                    <td class="text-center"><small>'.$player['cmp'].'/'.($player['cmp']+$player['att']).'</small></td>
									<td class="text-center"><small>'.$player['yds'].'</small></td>
									<td class="text-center"><small>'.$player['tds'].'</small></td>
									<td class="text-center"><small>'.$player['ints'].'</small></td>
									<td class="text-center"><small>'.$player['twoptm'].'/'.($player['twoptm']+$player['twopta']).'</small></td>
								</tr>';
                        }
					}
					//home rushing
					$data['games_array'][$game_index]['home_rushing']=array();
					if(isset($game['home']['stats']['rushing'])){
						foreach($game['home']['stats']['rushing'] as $player){
                        	$data['games_array'][$game_index]['home_rushing'][] = '
								<tr>
									<td class="text-left"><small>'.$player['name'].'</small></td>
                                    <td class="text-center"><small>'.$player['att'].'</small></td>
									<td class="text-center"><small>'.$player['yds'].'</small></td>
									<td class="text-center"><small>'.$player['tds'].'</small></td>
									<td class="text-center"><small>'.$player['lng'].'</small></td>
									<td class="text-center"><small>'.$player['twoptm'].'/'.($player['twoptm']+$player['twopta']).'</small></td>
								</tr>';
                        }
					}
					//away rushing
					$data['games_array'][$game_index]['away_rushing']=array();
					if(isset($game['away']['stats']['rushing'])){
						foreach($game['away']['stats']['rushing'] as $player){
                        	$data['games_array'][$game_index]['away_rushing'][] = '
								<tr>
									<td class="text-left"><small>'.$player['name'].'</small></td>
                                    <td class="text-center"><small>'.$player['att'].'</small></td>
									<td class="text-center"><small>'.$player['yds'].'</small></td>
									<td class="text-center"><small>'.$player['tds'].'</small></td>
									<td class="text-center"><small>'.$player['lng'].'</small></td>
									<td class="text-center"><small>'.$player['twoptm'].'/'.($player['twoptm']+$player['twopta']).'</small></td>
								</tr>';
                        }
					}
					//home receiving
					$data['games_array'][$game_index]['home_receiving']=array();
					if(isset($game['home']['stats']['receiving'])){
						foreach($game['home']['stats']['receiving'] as $player){
							//d($player);
                        	$data['games_array'][$game_index]['home_receiving'][] = '
								<tr>
									<td class="text-left"><small>'.$player['name'].'</small></td>
                                    <td class="text-center"><small>'.$player['rec'].'</small></td>
									<td class="text-center"><small>'.$player['yds'].'</small></td>
									<td class="text-center"><small>'.$player['tds'].'</small></td>
									<td class="text-center"><small>'.$player['lng'].'</small></td>
									<td class="text-center"><small>'.$player['twoptm'].'/'.($player['twoptm']+$player['twopta']).'</small></td>
								</tr>';
                        }
					}
					//away receiving
					$data['games_array'][$game_index]['away_receiving']=array();
					if(isset($game['away']['stats']['receiving'])){
						foreach($game['away']['stats']['receiving'] as $player){
                        	$data['games_array'][$game_index]['away_receiving'][] = '
								<tr>
									<td class="text-left"><small>'.$player['name'].'</small></td>
                                    <td class="text-center"><small>'.$player['rec'].'</small></td>
									<td class="text-center"><small>'.$player['yds'].'</small></td>
									<td class="text-center"><small>'.$player['tds'].'</small></td>
									<td class="text-center"><small>'.$player['lng'].'</small></td>
									<td class="text-center"><small>'.$player['twoptm'].'/'.($player['twoptm']+$player['twopta']).'</small></td>
								</tr>';
                        }
					}
					//home fumbles
					$data['games_array'][$game_index]['home_fumbles']=array();
					if(isset($game['home']['stats']['fumbles'])){
						foreach($game['home']['stats']['fumbles'] as $player){
                        	$data['games_array'][$game_index]['home_fumbles'][] = '
								<tr>
									<td class="text-left"><small>'.$player['name'].'</small></td>
                                    <td class="text-center"><small>'.$player['lost'].'/'.$player['tot'].'</small></td>
								</tr>';
                        }
					}
					//away fumbles
					$data['games_array'][$game_index]['away_fumbles']=array();
					if(isset($game['away']['stats']['fumbles'])){
						foreach($game['away']['stats']['fumbles'] as $player){
                        	$data['games_array'][$game_index]['away_fumbles'][] = '
								<tr>
									<td class="text-left"><small>'.$player['name'].'</small></td>
                                    <td class="text-center"><small>'.$player['lost'].'/'.$player['tot'].'</small></td>
								</tr>';
                        }
					}
					//home kicking
					$data['games_array'][$game_index]['home_kicking']=array();
					if(isset($game['home']['stats']['kicking'])){
						foreach($game['home']['stats']['kicking'] as $player){
							
                        	$data['games_array'][$game_index]['home_kicking'][] = '
								<tr>
									<td class="text-left"><small>'.$player['name'].'</small></td>
                                    <td class="text-center"><small>'.$player['fgm'].'/'.$player['fga'].'</small></td>
									<td class="text-center"><small>'.$player['xpmade'].'/'.$player['xpa'].'</small></td>
								</tr>';
                        }
					}
					//away kicking
					$data['games_array'][$game_index]['away_kicking']=array();
					if(isset($game['away']['stats']['kicking'])){
						foreach($game['away']['stats']['kicking'] as $player){
                        	$data['games_array'][$game_index]['away_kicking'][] = '
								<tr>
									<td class="text-left"><small>'.$player['name'].'</small></td>
                                    <td class="text-center"><small>'.$player['fgm'].'/'.$player['fga'].'</small></td>
									<td class="text-center"><small>'.$player['xpmade'].'/'.$player['xpa'].'</small></td>
								</tr>';
                        }
					}
					
					
					
				}
				
			}
			$game_index++;
		}
		
		
		header('Content-Type: application/json');
		echo json_encode($data);	
		
		
	}

	public function bracket($year=NULL,$week=NULL) 
	{
		//RESTRICTED TO LOGGED IN MEMBERS ONLY
		//Just in case not logged in we direct to login page
		if (!$this->session->userdata('logged_in') )
		{
			redirect("/Restricted");
		} 
		
		if(is_null($year)){ $year=$this->current_year; }
		if(is_null($week)){ $week=$this->current_week; }
		$team_id = $this->session->team_id;

		$content_data['current_year'] = $this->current_year;
		
		//content to initially display
		$content_data['display_page']='Games';
		$content_data['load_path'] = 'Game/playoff_bracket_load/'.$this->league_id.'/'.$year;
		//send a display for the dropdown. it should match what is in the content_selector array
		//should be the same as the page_content which is also the method. multiple words
		//can be separated by _ and it will take those out and capitalize each word
		$content_data['dropdown_title'] = $year;
		//content selector will be activated for these views
		//set the list of items for content selector
		$content_data['content_selector'] = array();
			//each key is the display in the dropdown, linked to the path to the method
			$y=$this->current_year;
			while($y >=2004){
				$content_data['content_selector'][$y]= base_url().'Game/week/'.$this->league_id.'/'.$y.'/1';
				$y--;
			}
		
		//titles of the pages will be upper cased
		$title = 'Scoreboard';
		$content_data['title']= ucwords($title);
		$path = 'games/bracket_container';
		
		
		$this->load_view($path, $content_data, true);
		
	}
	//**************************************************************
  
	public function playoff_bracket_load($league_id=1,$year) 
	{
		$content_data['year']=$year;
		$content_data['current_year']=$this->current_year;
		
		$content_data['league_id']=$league_id;		
		
		$this->index('games/playoff_bracket', $content_data);
	}	

}//end Class Game extends MY_Controller

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/