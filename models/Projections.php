<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Projections Model.
	 *
	 * ?????
	 *		
	 */
	
Class Projections extends CI_Model 
{
	public function __construct() 
    {
		$this->load->helper('string');
		$this->load->helper('links');
	
		$this->load->library('simple_html_dom');
		
		$this->load->model('Database_Manager');
		
		parent::__construct();
		//$ci = get_instance();

		
	}
	
//*****************************************************************

	//scrapes the data from espn, cbs, scout and fftoday
	
	public function import_espn_projections($year){
		$this->Database_Manager->database_backup(array('Projections','Players'));
		$players=array();
		//QB=0 RB=2 WR=4 TE=6 K=17
		$positions = array('0','2','4','6','17');
		foreach($positions as $slotCategoryId){
			if($slotCategoryId == '2') { $startIndexes = array('0','40'); }
			elseif($slotCategoryId == '4') { $startIndexes = array('0','40','80'); }
			else{ $startIndexes = array('0'); }
			foreach($startIndexes as $startIndex){
				$html = file_get_html('http://games.espn.go.com/ffl/tools/projections?slotCategoryId='.$slotCategoryId.'&startIndex='.$startIndex);
				
				
				foreach($html->find('table.playerTableTable') as $table) {
					foreach($table->find('tr.pncPlayerRow') as $row){
						$total_points = 0;
						//$player['rank']     = $row->find('td', 0)->plaintext;
						$player['player_name']     = $row->find('td', 1)->plaintext;
						/*
						$player['comp_att']  = $row->find('td', 2)->plaintext;
						$player['pass_yards']  = $row->find('td', 3)->plaintext;
						$player['pass_td']  = $row->find('td', 4)->plaintext;
						$player['interceptions']  = $row->find('td', 5)->plaintext;
						////$player['rush_att']  = $row->find('td', 6)->plaintext;
						$player['rush_yards']  = $row->find('td', 7)->plaintext;
						$player['rush_tds']  = $row->find('td', 8)->plaintext;
						$player['receptions']  = $row->find('td', 9)->plaintext;
						$player['rec_yards']  = $row->find('td', 10)->plaintext;
						$player['rec_tds']  = $row->find('td', 11)->plaintext;
						$player['espn_pts']  = $row->find('td', 12)->plaintext;*/
						
						//get the name td sliced
						$player_name_array    = explode(' ',str_replace(',','',$row->find('td', 1)->plaintext));
						//incase there's a jr. or sr. on eand of name remove the index 2 if it is
						$surnames = array('Jr.','Sr.','III');
						if(in_array($player_name_array['2'],$surnames)){
							array_splice($player_name_array,2,1);	
						}
						
						//slice the combination of team&nbsp;position to get just team
						$current_team_array = explode('&nbsp;',$player_name_array['2']);
						
						//replace espn's different names for teams
						if($current_team_array['0']=='Wsh') { $current_team_array['0']='WAS'; }
						
						//remove the extra text from the team name for error report purposes later
						$player_name_array['2']=$current_team_array['0'];
						
						//get the player's espn_id
						$espn_id_array = explode('_',$row->find('td', 1)->getAttribute('id'));
						$espn_id = $espn_id_array['1'];
						$player_name_array['3']= $espn_id;
						
						//find the player with this espn id, get the fffl_player_id for update in projections later
						$player_espn_query = $this->db->select('fffl_player_id')
														->where('espn_id',$espn_id)
														->get('Players');
						
						//found the player with espn id, add fffl_player_id to array						
						if($player_espn_query->num_rows()==1){
							foreach($player_espn_query->result_array() as $fffl_player_id){
								$player['fffl_player_id']=$fffl_player_id['fffl_player_id'];
								
							}
						}
						//didn't find by espn_id, attempt to add the espn_id to the player
						else{
							$player_id_query = $this->db->select('fffl_player_id')
														->where('first_name',$player_name_array['0'])
														->where('last_name',$player_name_array['1'])
														->where('current_team',$current_team_array['0'])
														->get('Players');
							//found the player by name and team, add the espn_id
							if($player_id_query->num_rows()==1){
								foreach($player_id_query->result_array() as $fffl_player_id){
									d($fffl_player_id);
									$this->db->where('fffl_player_id',$fffl_player_id['fffl_player_id'])
												->set('espn_id',$espn_id)
												->update('Players');
									
									echo 'ESPN ID added<br>';
									d($player_name_array,$espn_id);	
								}
							}
							//couldn't find the player
							else{
								echo 'Not found<br>';
								d($player_name_array,$espn_id);	
							}
						}
														
						
						//start calculating points

						$comp_array = explode('/',$row->find('td', 2)->plaintext);
						$total_points = $total_points + floor($comp_array['0']/3);
						
						$pass_yards = $row->find('td', 3)->plaintext;
						$total_points = $total_points + floor($pass_yards/25);
						
						$pass_tds = $row->find('td', 4)->plaintext;
						$total_points = $total_points + floor($pass_tds*6);
						
						$interceptions = $row->find('td', 5)->plaintext;
						$total_points = $total_points + floor($interceptions*(-3));
						
						$rush_yards = $row->find('td', 7)->plaintext;
						$total_points = $total_points + floor($rush_yards/10);
					
						$rush_tds = $row->find('td', 8)->plaintext;
						$total_points = $total_points + floor($rush_tds*6);
						
						$receptions = $row->find('td', 9)->plaintext;
						$total_points = $total_points + floor($receptions/2);
						
						$rec_yards = $row->find('td', 10)->plaintext;
						$total_points = $total_points + floor($rec_yards/10);
						
						$rec_tds = $row->find('td', 11)->plaintext;
						$total_points = $total_points + floor($rec_tds*6);
						
						//its a kicker, just take total points from espn
						if($slotCategoryId==17){
							$total_points= floor($row->find('td', 12)->plaintext);
						}

						
						$player['points'] = $total_points;
						$players[]=$player;
					}//each player row
					
				}//finding the table
			}//foreach start index (0, 40, 80 etc)
		}//foreach position
	
	
		//update the projections table
		$this->insert_projections($players,'ESPN',$year);
		

	}//import espn projections
	
//*****************************************************************

	//scrapes the data from espn, cbs, scout and fftoday
	
	public function import_fftoday_projections($year){
		$this->Database_Manager->database_backup(array('Projections','Players'));
		$players=array();
		//QB=10 RB=20 WR=30 TE=40 K=80
		$positions = array('10','20','30','40','80');
		foreach($positions as $position_id){
			if($position_id == '20') { $pages = array('0','1'); }
			elseif($position_id == '30') { $pages = array('0','1','2'); }
			else{ $pages = array('0'); }
			foreach($pages as $page){
				$path = 'http://www.fftoday.com/rankings/playerproj.php?Season='.$year.'&PosID='.$position_id.'&order_by=FFPts&sort_order=DESC&cur_page='.$page;
				$html = file_get_html($path);
				//tableclmhdr is the row before the players start. get the parent of that row, that is the 
				//entire table
				$header_row = $html->find('tr.tableclmhdr');
             	$table = $header_row['0']->parent();
					
					foreach($table->find('tr') as $row){
						
						//the first couple of rows are not players. Player rows have tds that have class sort1
						//if the first td isn't sort1, continue 
						$attributes = $row->find('td', 1)->attr;
						if(!isset($attributes['class'])){ continue; }
						
						$total_points = 0;
						//$player['chg']     = $row->find('td', 0)->plaintext;
	
						//get the name td sliced
						$player_name_array    = explode(' ',str_replace('&nbsp;','',$row->find('td', 1)->plaintext));
						//incase there's a jr. or sr. on eand of name remove the index 2 if it is
						$surnames = array('Jr.','Sr.','III');
						if(in_array($player_name_array['2'],$surnames)){
							array_splice($player_name_array,2,1);	
						}
						
						//team
						$current_team = $row->find('td', 2)->plaintext;
						$player_name_array['2']=$current_team;
						//replace fftoday's different names for teams
						if($current_team=='JAC') { $current_team='JAX'; }
						if($current_team=='LAR') { $current_team='LA'; }

						//get the player's fftoday id
						$link_td = $row->find('td', 1)->children(0);
						$link = $link_td->href;
						$id_array = explode('/',$link);
						$id = $id_array['3'];
						
						//find the player with this fft id, get the fffl_player_id for update in projections later
						$player_fft_query = $this->db->select('fffl_player_id')
														->where('fftoday_id',$id)
														->get('Players');
						
						//found the player with espn id, add fffl_player_id to array						
						if($player_fft_query->num_rows()==1){
							foreach($player_fft_query->result_array() as $fffl_player_id){
								$player['fffl_player_id']=$fffl_player_id['fffl_player_id'];
								
							}
						}
						//didn't find by fft_id, attempt to add the fft_id to the player
						else{
							$player_id_query = $this->db->select('fffl_player_id')
														->where('first_name',$player_name_array['0'])
														->where('last_name',$player_name_array['1'])
														->where('current_team',$current_team)
														->get('Players');
							//found the player by name and team, add the fft_id
							if($player_id_query->num_rows()==1){
								foreach($player_id_query->result_array() as $fffl_player_id){
									d($fffl_player_id);
									$this->db->where('fffl_player_id',$fffl_player_id['fffl_player_id'])
												->set('fftoday_id',$id)
												->update('Players');
									
									echo 'FFtoday ID added<br>';
									d($player_name_array,$id);	
								}
							}
							//couldn't find the player
							else{
								echo 'FFToday Not found<br>';
								d($player_name_array,$id);	
							}
						}
														
						$scores=array();
						//start calculating points
						if($position_id == 10){
							$comp = $row->find('td', 4)->plaintext;
							$total_points = $total_points + floor($comp/3);
							
							$pass_yards = str_replace(',','',$row->find('td', 6)->plaintext);
							$total_points = $total_points + floor($pass_yards/25);
							
							$pass_tds = $row->find('td', 7)->plaintext;
							$total_points = $total_points + floor($pass_tds*6);
							
							$interceptions = $row->find('td', 8)->plaintext;
							$total_points = $total_points + floor($interceptions*(-3));
						
							$rush_yards = $row->find('td', 10)->plaintext;
							$total_points = $total_points + floor($rush_yards/10);
						
							$rush_tds = $row->find('td', 11)->plaintext;
							$total_points = $total_points + floor($rush_tds*6);
							
						} 
						elseif($position_id==20){
							
							$rush_yards = str_replace(',','',$row->find('td', 5)->plaintext);
							$total_points = $total_points + floor($rush_yards/10);
						
							$rush_tds = $row->find('td', 6)->plaintext;
							$total_points = $total_points + floor($rush_tds*6);
						
							$receptions = $row->find('td', 7)->plaintext;
							$total_points = $total_points + floor($receptions/2);
							
							$rec_yards = str_replace(',','',$row->find('td', 8)->plaintext);
							$total_points = $total_points + floor($rec_yards/10);
							
							$rec_tds = $row->find('td', 9)->plaintext;
							$total_points = $total_points + floor($rec_tds*6);
							
						}
						elseif($position_id==30 || $position_id==40){
							
							$receptions = $row->find('td', 4)->plaintext;
							$total_points = $total_points + floor($receptions/2);
							
							$rec_yards = str_replace(',','',$row->find('td', 5)->plaintext);
							$total_points = $total_points + floor($rec_yards/10);
							
							$rec_tds = $row->find('td', 6)->plaintext;
							$total_points = $total_points + floor($rec_tds*6);
							
						}
						
						//its a kicker, just take total points from fftoday
						else{
							$total_points= floor($row->find('td', 9)->plaintext);
							
						}

						$player_name_array['3']=$total_points;
						$player['points'] = $total_points;
						
						$players[]=$player;
						
						
					}//each player row
					
				//}//finding the table
			}//foreach page (0, 40, 80 etc)
		}//foreach position
	
		//insert the projections
		$this->insert_projections($players,'FFToday',$year);
		
		

	}//import fftoday projections
	
//*****************************************************************

	//scrapes the data from espn, cbs, scout and fftoday
	
	public function import_cbs_projections($year){
		$this->Database_Manager->database_backup(array('Projections','Players'));
		$players=array();
		
		$positions = array('QB','RB','WR','TE','K');
		foreach($positions as $position_id){
			if($position_id == 'RB') { $pages = array('1','51'); }
			elseif($position_id == 'WR') { $pages = array('1','51','101'); }
			else{ $pages = array('1'); }
			foreach($pages as $page){
				$path = 'http://www.cbssports.com/fantasy/football/stats/sortable/points/'.$position_id.'/standard/projections?&start_row='.$page;
				$html = file_get_html($path);
				//tableclmhdr is the row before the players start. get the parent of that row, that is the 
				//entire table
				foreach($html->find('table.data') as $table) {
				
					foreach($table->find('tr') as $row){
						
						//the first couple of rows are not players. 
						$attributes = $row->attr;
						//d($attributes);
						if($attributes['class']=='footer pagination' || $attributes['class']=='title' || $attributes['class']=='label' || isset($attributes['id'])){ continue; }
						
						$total_points = 0;
						
	
						//get the name td sliced
						$player_name_array    = explode(' ',str_replace(',&nbsp;',' ',$row->find('td', 0)->plaintext));
						//incase there's a jr. or sr. on eand of name remove the index 2 if it is
						$surnames = array('Jr.','Sr.','III');
						if(in_array($player_name_array['2'],$surnames)){
							array_splice($player_name_array,2,1);	
						}
						
						//team
						$current_team = $player_name_array['2'];
						
						//replace fftoday's different names for teams
						if($current_team=='JAC') { $current_team='JAX'; }
						if($current_team=='LAR') { $current_team='LA'; }
	
						//get the player's cbs id
						$link_td = $row->find('td', 0)->children(0);
						$link = $link_td->href;
						$id_array = explode('/',$link);
						$id = $id_array['4'];
						
						
						//find the player with this cbs id, get the fffl_player_id for update in projections later
						$player_cbs_query = $this->db->select('fffl_player_id')
														->where('cbs_id',$id)
														->get('Players');
						
						//found the player with cbs id, add fffl_player_id to array						
						if($player_cbs_query->num_rows()==1){
							foreach($player_cbs_query->result_array() as $fffl_player_id){
								$player['fffl_player_id']=$fffl_player_id['fffl_player_id'];
								
							}
						}
						//didn't find by fft_id, attempt to add the fft_id to the player
						else{
							$player_id_query = $this->db->select('fffl_player_id')
														->where('first_name',$player_name_array['0'])
														->where('last_name',$player_name_array['1'])
														->where('current_team',$current_team)
														->get('Players');
							//found the player by name and team, add the cbs_id
							if($player_id_query->num_rows()==1){
								foreach($player_id_query->result_array() as $fffl_player_id){
									d($fffl_player_id);
									$this->db->where('fffl_player_id',$fffl_player_id['fffl_player_id'])
												->set('cbs_id',$id)
												->update('Players');
									
									echo 'CBS ID added<br>';
									d($player_name_array,$id);	
								}
							}
							//couldn't find the player
							else{
								echo 'CBS Not found<br>';
								d($player_name_array,$id);	
							}
						}
														
						$scores=array();
						//start calculating points
						if($position_id == 'QB'){
							$comp = $row->find('td', 2)->plaintext;
							$total_points = $total_points + floor($comp/3);
							
							$pass_yards = str_replace(',','',$row->find('td', 3)->plaintext);
							$total_points = $total_points + floor($pass_yards/25);
							
							$pass_tds = $row->find('td', 4)->plaintext;
							$total_points = $total_points + floor($pass_tds*6);
							
							$interceptions = $row->find('td', 5)->plaintext;
							$total_points = $total_points + floor($interceptions*(-3));
						
							$rush_yards = $row->find('td', 9)->plaintext;
							$total_points = $total_points + floor($rush_yards/10);
						
							$rush_tds = $row->find('td', 11)->plaintext;
							$total_points = $total_points + floor($rush_tds*6);
							
						} 
						elseif($position_id=='RB'){
							
							$rush_yards = str_replace(',','',$row->find('td', 2)->plaintext);
							$total_points = $total_points + floor($rush_yards/10);
						
							$rush_tds = $row->find('td', 4)->plaintext;
							$total_points = $total_points + floor($rush_tds*6);
						
							$receptions = $row->find('td', 5)->plaintext;
							$total_points = $total_points + floor($receptions/2);
							
							$rec_yards = str_replace(',','',$row->find('td', 6)->plaintext);
							$total_points = $total_points + floor($rec_yards/10);
							
							$rec_tds = $row->find('td', 8)->plaintext;
							$total_points = $total_points + floor($rec_tds*6);
						}
						elseif($position_id=='WR' || $position_id=='TE'){
							$receptions = $row->find('td', 1)->plaintext;
							$total_points = $total_points + floor($receptions/2);
							
							$rec_yards = str_replace(',','',$row->find('td', 2)->plaintext);
							$total_points = $total_points + floor($rec_yards/10);
							
							$rec_tds = $row->find('td', 4)->plaintext;
							$total_points = $total_points + floor($rec_tds*6);
						}
						
						//its a kicker, just take total points from fftoday
						else{
							$total_points= floor($row->find('td', 4)->plaintext);
							
						}
	
						$player_name_array['3']=$total_points;
						$player['points'] = $total_points;
						
						$players[]=$player;
						
						
					}//each player row
					
				}//each table
			}//foreach page (0, 40, 80 etc)
		}//foreach position
		
		
		//insert the projections
		$this->insert_projections($players,'CBS',$year);
		
		

	}//import cbs projections
//*************************************************************

	public function import_scout_projections($year){
			$f = file_get_contents('http://api.scout.com//fantasy/rankings?from=0&size=5000&sortBy=FantasyPoints&viewType=players&skipAggregations=&positions=%5B%22QB%22,%22RB%22,%22WR%22,%22TE%22,%22K%22%5D&ppr=PPR&qbTouchdowns=6&scoringMethodId=770177&seasonYear='.$year.'&week=0');
		$jsonContent = json_decode($f,true);
		$allplayers = $jsonContent['searchResults']['results'];
		
		foreach($allplayers as $player) {
			$points=0;
			
			
			
			//get the player's scout id
			$id = $player['entityId'];

			//find the player with this cbs id, get the fffl_player_id for update in projections later
			$player_scout_query = $this->db->select('fffl_player_id')
											->where('scout_id',$id)
											->get('Players');
			
			//found the player with scout id, add fffl_player_id to array						
			if($player_scout_query->num_rows()==1){
				foreach($player_scout_query->result_array() as $fffl_player_id){
					$player['fffl_player_id']=$fffl_player_id['fffl_player_id'];
					
				}
			}
			//didn't find by scout_id, attempt to add the scout_id to the player
			else{
				$firstName = str_replace("'","\'",$player['firstName']);
				$lastName = str_replace("'","\'",$player['lastName']);
				$team = $player['playerNflTeam'];
				//adjsut differences in scout nfl teams
				if($team=='LAR'){ $team='LA';}
				if($team=='---'){$team='FA';}
				//correct some differences in the names
				if($firstName.' '.$lastName=='Robert Griffin'){$lastName=$lastName.' III';}
				if($firstName.' '.$lastName=='E.J. Manuel'){$firstName='EJ';}
				if($firstName.' '.$lastName=='James O\'Shaugnessy^'){$lastName="O'Shaughnessy";}
				$player_name = $firstName.' '.$lastName.' '.$team;
				$pos = $player['position'];
				
				$player_id_query = $this->db->select('fffl_player_id')
											->where('first_name',$firstName)
											->where('last_name',$lastName)
											->where('current_team',$team)
											->get('Players');
				//found the player by name and team, add the scout_id
				if($player_id_query->num_rows()==1){
					foreach($player_id_query->result_array() as $fffl_player_id){
						d($fffl_player_id);
						$this->db->where('fffl_player_id',$fffl_player_id['fffl_player_id'])
									->set('scout_id',$id)
									->update('Players');
						
						echo 'Scout ID added<br>';
						d($player_name,$id);	
					}
				}
				//couldn't find the player
				else{
					echo 'Scout Not found<br>';
					d($player_name,$id);	
				}
			}

			$points =  $player['totalFantasyPoints'];
			$players[] = array('points'=>$points,'fffl_player_id'=>$fffl_player_id['fffl_player_id']);
		}
		
		$this->insert_projections($players,'Scout',$year);
		
	}


//************************************************************************

	public function import_adp($year){
	

		$players=array();
		
		$positions = array('QB','RB','WR','TE','K');
		foreach($positions as $position){
				
				$html = file_get_html('http://games.espn.go.com/ffl/livedraftresults?position='.$position);
				if($position == 'QB'){ $limit=100; }
				elseif($position == 'RB'){ $limit=120; }
				elseif($position == 'WR') { $limit=160; }
				else{ $limit = 60; }
				
				foreach($html->find('table.tableBody') as $table) {
					foreach($table->find('tr') as $row){
						//the first couple of rows are not players. 
						$attributes = $row->attr;
						//d($attributes);
						if(isset($attributes['class']) && ($attributes['class']=='tableSubHead' || $attributes['class']=='tableHead')){ continue; }
						//if we've hit the limit of players, move on to th next positon
						if($row->find('td', 0)->plaintext >  $limit) { break; }
						
						//get the player's espn_id
						$id_td = $row->find('td', 1);
						$espn_id = $id_td->find('a', 0)->getAttribute('playerid');
						
	
						//find the player with this espn id, get the fffl_player_id for update in projections later
						$player_espn_query = $this->db->select('fffl_player_id')
														->where('espn_id',$espn_id)
														->get('Players');
						
						//found the player with espn id, add fffl_player_id to array						
						if($player_espn_query->num_rows()==1){
							foreach($player_espn_query->result_array() as $fffl_player_id){
								$player['fffl_player_id']=$fffl_player_id['fffl_player_id'];
								
							}
							
							$player['points'] = $row->find('td', 3)->plaintext;
							$players[]=$player;
						}
						

					}//each player row
					
				}//finding the table
			
		}//foreach position
	
	
		//update the projections table
		$this->insert_projections($players,'adp',$year);
	}
	
//***********************************************************************

	public function insert_projections($players_array,$source,$year){
		//delete the projections for this source
		$this->db->set($source,'0')
				->update('Projections');
		if($source=='adp'){
			$this->db->set($source,'200.0')
				->update('Projections');
		}
		
		//update the projections table
		
		foreach($players_array as $player){
			//check if in the table yet
			
			$query = $this->db->where('fffl_player_id',$player['fffl_player_id'])
							->get('Projections');

			if($query->num_rows()==1){
				$this->db->set($source,$player['points'])
							->where('fffl_player_id',$player['fffl_player_id'])
							->update('Projections');	
			}
			elseif($query->num_rows()==0){
				$this->db->set($source,$player['points'])
							->set('fffl_player_id',$player['fffl_player_id'])
							->set('year',$year)
							->insert('Projections');	
			}
			else{
				echo 'error, in more than once<br>';
			}
			
		} 	
	}


//**********************************************************************

	public function average_projections($year){

		$this->db->query("UPDATE `Projections` SET `average`=((`CBS`+`ESPN`+`FFToday`+`Scout`) div 4) WHERE `year`=".$year);
			
	}


//************************************************************************
	//returns an array of fffl_player_id key with values of average, adp and position, ordered by average descinding
	public function get_projections($year){
		$projections_query = $this->db->where('year',$year)
										->select('average,adp,fffl_player_id')
										->order_by('average','DESC')
										->get('Projections');
		$return_array = array();
		//create vbd
		//starters1: qb 5 rb 5 wr 5 te 5 k 5
		//laststarter: qb 5 rb 15 wr 25 te 5 k 5
		//first backup qb 15 rb 25 wr 35 te 15 k 5
		//last backup: qb 25 rb 35 wr 55 te 15 k 5
		$QB = $RB = $WR = $TE = $K = 0; $year_based=$year-1;
		foreach($projections_query->result_array() as $data){
			//get the player's SD from last year
			
			$scores = $this->NFL_stats->get_player_scores_season($year_based,$data['fffl_player_id'],0,1,16,1);
			$return_array[$data['fffl_player_id']]['standard_deviation']=$scores['standard_deviation'];
			
			$return_array[$data['fffl_player_id']]['average'] = $data['average'];
			$return_array[$data['fffl_player_id']]['adp'] = $data['adp'];
			$player_data = $this->Players->get_player_info(array($data['fffl_player_id']),"fffl_player_id","position");
			$return_array[$data['fffl_player_id']]['position'] = $player_data['position'];
			$$player_data['position']++;
			if($$player_data['position']==5){
				$vbdstarters1 = $player_data['position'].'starters1';
				$$vbdstarters1=$data['average'];
				if($player_data['position']=='QB' || $player_data['position']=='TE' ){
					$vbdlaststarter = $player_data['position'].'laststarter';
					$$vbdlaststarter = $data['average'];
				}
				elseif($player_data['position']=='K'){
					$vbdlaststarter = $player_data['position'].'laststarter';
					$$vbdlaststarter = $data['average'];
					$vbdfirstbackup = $player_data['position'].'firstbackup';
					$$vbdfirstbackup = $data['average'];
					$vbdlastbackup = $player_data['position'].'lastbackup';
					$$vbdlastbackup = $data['average'];
				}
			}
			elseif($$player_data['position']==15){
				if($player_data['position']=='RB'  ){
					$vbdlaststarter = $player_data['position'].'laststarter';
					$$vbdlaststarter = $data['average'];
				}
				elseif($player_data['position']=='QB' ){
					
					$vbdfirstbackup = $player_data['position'].'firstbackup';
					$$vbdfirstbackup = $data['average'];
				}
				elseif($player_data['position']=='TE'){
					$vbdfirstbackup = $player_data['position'].'firstbackup';
					$$vbdfirstbackup = $data['average'];
					$vbdlastbackup = $player_data['position'].'lastbackup';
					$$vbdlastbackup = $data['average'];
				}
			}
			elseif($$player_data['position']==25){
				if($player_data['position']=='WR'  ){
					$vbdlaststarter = $player_data['position'].'laststarter';
					$$vbdlaststarter = $data['average'];
				}
				elseif($player_data['position']=='RB' ){
					
					$vbdfirstbackup = $player_data['position'].'firstbackup';
					$$vbdfirstbackup = $data['average'];
				}
				elseif($player_data['position']=='QB'){
					$vbdlastbackup = $player_data['position'].'lastbackup';
					$$vbdlastbackup = $data['average'];
				}
			}
			elseif($$player_data['position']==35){
				
				if($player_data['position']=='RB' ){
					
					$vbdlastbackup = $player_data['position'].'lastbackup';
					$$vbdlastbackup = $data['average'];
				}
				elseif($player_data['position']=='WR'){
					$vbdfirstbackup = $player_data['position'].'firstbackup';
					$$vbdfirstbackup = $data['average'];
				}
			}
			elseif($$player_data['position']==55){
				
				if($player_data['position']=='WR' ){
					
					$vbdlastbackup = $player_data['position'].'lastbackup';
					$$vbdlastbackup = $data['average'];
				}

			}
		}
		
		//go back trhough each player and set their vbd numbers
		foreach($return_array as $fffl_player_id => $data){
			$return_array[$fffl_player_id]['fffl_player_id']=$fffl_player_id;
			$vbdstarters1 = $data['position'].'starters1';
			$return_array[$fffl_player_id]['starters1']=$data['average']-$$vbdstarters1;
			
			$vbdlaststarter = $data['position'].'laststarter';
			$return_array[$fffl_player_id]['last_starter']=$data['average']-$$vbdlaststarter;
			
			$vbdfirstbackup = $data['position'].'firstbackup';
			$return_array[$fffl_player_id]['first_backup']=$data['average']-$$vbdfirstbackup;
			
			$vbdlastbackup = $data['position'].'lastbackup';
			$return_array[$fffl_player_id]['last_backup']=$data['average']-$$vbdlastbackup;
			
		}
		
		// Obtain a list of columns
		foreach ($return_array as $key => $row) {
			$adp[$key]  = $row['adp'];
			$average[$key] = $row['average'];
		}
		
		// Sort the data 
		array_multisort($adp, SORT_ASC, $average, SORT_DESC, $return_array);
		
		return $return_array;
		
	}

	
}//end model


/*End of file Projections.php*/
/*Location: ./application/models/Players.php*/