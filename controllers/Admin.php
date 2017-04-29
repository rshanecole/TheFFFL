<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Admin extends MY_Controller
{
	/**
	 * Admin controller.
	 *
	 * 
	 */
	
	//Load the needed libraries.  
	public $league_id = 1; //***NI*** remove when multiple leagues are in place
	public $current_year;	
	public $current_week;
	public function __construct() 
    {
		parent::__construct();

		$this->load->helper('form');
		$this->load->helper('date');
		$this->load->helper('links');
		
		$this->load->library('form_validation');

		$this->load->model('Owners');
		$this->load->model('Teams');
		$this->load->model('Drafts');
		$this->load->model('Rosters');
		$this->load->model('Players');
		$this->load->model('Rosters_View');
		$this->load->model('Projections');
		$this->load->model('NFL_stats');
		$this->load->model('Trades');
		$this->load->model('Leagues');
		$this->load->model('Standings');
		$this->load->model('Database_Manager');
		$this->load->model('Games');
		$this->load->model('Schedules');

		
		$league_id=1; //***NI*** ELIMINATE THIS WHEN A MEANS TO ADD MORE LEAGUES IS CREATED
		$this->current_year = $this->Leagues->get_current_season($this->league_id);
		$this->current_week = $this->Leagues->get_current_week($this->league_id);
		
		if (!$this->session->userdata('logged_in') || $this->session->userdata('security_level')<2)
		{
			redirect();
		}
		
	}
  
  

	//
	// requests views from MY_Controller load_view function
	public function index($page='admin_panel', $content_data=array()) 
    {
		$content_data['year']=$this->current_year;
		
		//titles of the pages will be upper cased either Register Login or Update Profile
		$title = str_replace('_',' ',$page);
		$content_data['title']= ucwords($title);
		$path ='admin/'.$page;
		$this->load_view($path, $content_data, true);
	}
  
  
  //*********************************************************************
	//request to start a new season
	public function new_season($league_id,$new_year)
	{
		$content_data=array();
		//used to, one line at a time, display all actions completed or errors
		$content_data['action_messages']='';
		//***NI***  Needs more functionality to create new season
		
		//update player titles***
		
		
		
		//Add new draft picks for the NEXT season, $new_year + 1 will be added in the method
		$this->Drafts->create_next_season_drafts($league_id,$new_year);
		$content_data['action_messages'] .='<br>'.($new_year+1).' Drafts Created.';
		
		//back to admin_panel
		$this->index('admin_panel', $content_data);
		
	}//end function new_season
	
//*********************************************************************
	//request to start a new week
	public function new_week($league_id, $confirm=0)
	{
		if($confirm==1){
			$data=array();
			//one line at a time display all actions completed or errors
			$data['action_messages']='';
			$this->Database_Manager->database_backup('All');
			sleep(2);
			$this->Database_Manager->NFL_database_backup(array('NFL_Schedule_'.$this->current_year,'NFL_stats_'.$this->current_year));
			sleep(2);
			
			//Current week is always the week that is ending. The week isn't advanced
			//until the very end of the function
			
			//set all trades to reject
			$this->Trades->decline_all_open_trades($league_id);
			
			//week 0 to 1
			if($this->current_week==0){
				//create next season draft and add the draft picks
				$this->Drafts->create_next_season_drafts($league_id,$this->current_year);
				$next_year = $this->current_year+1;
				$this->Drafts->assign_draft_picks($league_id,$next_year);
				
			}
			else{ // week 1 through 18
				//remove all players from locked_players table
				$this->Players->remove_player_locks();
				
				//determine wins and losses, set winner in game table, set scores in games table
				$this->Games->finalize_games($this->current_week, $this->current_year);
				
								
				//add the bye week player's stats lines for next week
				$this->NFL_stats->add_bye_weeks($this->current_year,($this->current_week+1));
				
				//weeks 1to 2 through weeks 13 to 14
				if($this->current_week>0 && $this->current_week<14){
					//add one to all weeks on pups for players on pup
					$this->Rosters->add_week_to_pup();
					
					//set starting lineup if not already set, fill in the gaps? For now no. evaluate later  ***NI
					//foreach team, get the starting lineup for the current week and the 
					//upcoming week
					$team_ids = $this->Teams->get_all_team_id($league_id);
					
					foreach($team_ids as $team_id){
						$next_starters = $this->Rosters->get_team_starters($team_id, ($this->current_week+1), $this->current_year);
						if(empty($next_starters)){
							$current_starters=$this->Rosters->get_team_starters($team_id,$this->current_week, $this->current_year);
							$this->Rosters->update_starting_lineup($team_id,$this->current_year,($this->current_week+1),$current_starters);
						}
						
					}
						
					
				}
				
				//week13 to 14 update regular season titles, finalize seeding, put playoff games in table,set TB games
				if($this->current_week==13) {
					
					//get playoff standings
					$playoff_standings = $this->Standings->determine_playoff_teams($this->current_year,$standings=NULL);
					
					//update regular season titles
					$this->Standings->set_regular_season_championships($this->league_id,$this->current_year,$playoff_standings);
					//set playoff games and seeds,set TB participatns
					$this->Schedules->set_playoff_games($this->league_id,$playoff_standings,$this->current_year,($this->current_week+1));
				}
				//week14 to 15 set TB game set starting lineups for just the playoff teams
				if($this->current_week==14){
					//set playoff games
					$this->Schedules->update_playoff_games($this->league_id,$this->current_year,$this->current_week);
					//set tb game
					$this->Schedules->update_toilet_bowl($this->league_id,$this->current_year,$this->current_week);
					
					//set rosters
					$this->Rosters->set_playoff_lineups($this->league_id,$this->current_year,($this->current_week+1));
				}
				
				
				//week15 to 16 set SB and add TB winner to draft supplemental draft picks, update conf champtionships set starting lineups for just the superbowl teams
				if($this->current_week==15){
					//set superbowl in games
					$this->Schedules->update_playoff_games($this->league_id,$this->current_year,$this->current_week);
					//set tb winner
					$this->Schedules->update_toilet_bowl($this->league_id,$this->current_year,$this->current_week);
					//set rosters
					$this->Rosters->set_playoff_lineups($this->league_id,$this->current_year,($this->current_week+1));
					
				}
				//week16 to 17 set SB winner,update SB champtionships set starting lineups for just the superbowl teams
				if($this->current_week==16){
					//set SB winner
					$this->Schedules->update_playoff_games($this->league_id,$this->current_year,$this->current_week);
					
				}
				
				//update the draft picks for NEXT YEAR
				$this->Drafts->update_draft_order($league_id,$this->current_year,$this->current_week);
			}
			
			//update current week in leagues table
			$this->Leagues->advance_week($league_id,($this->current_week+1));
			
			
			//back to admin_panel
			$this->index('admin_panel');
		}
		else{//not confimred yet
			
			$data['message']='Finalize Week?';
			$data['confirm']=$confirm;
		}
		
		$path ='admin/finalize_week';

		$this->load_view($path, $data, false, false, false);
		
		
	}//end function new_season
	
//***********************************************************************
	//change characteristicts of a player for a specific team
	public function adjust_team_player($team_id=FALSE,$fffl_player_id=FALSE){
		$data = array();
		
		//this was simply a request to load the view. No data has been passed yet
		$data['team_id'] = $team_id;
		$data['fffl_player_id'] = $fffl_player_id;
		//an array of id => team name
		$data['all_teams'] = $this->Teams->get_all_teams_id_to_first_nickname($this->session->userdata('league_id'));
		
		
		//a team was selected, now send the players so a player can be selected
		if($team_id>0){
			//a team was selected, but not plwayr	
			$data['team_id'] = $team_id;
			$data['fffl_player_id'] = $fffl_player_id;
			//an array of id => player name
			$all_roster_players = $this->Rosters->get_team_complete_roster($team_id);
			
			foreach($all_roster_players as $player_id){
				$player_info = $this->Players->get_player_info(array($player_id),"fffl_player_id","first_name last_name position current_team");
				
				$data['all_players'][$player_id] = $player_info['position'].' '.$player_info['first_name'].' '.$player_info['last_name'].' '.$player_info['current_team'];
			}
			
			
		}
		$path ='admin/adjust_team_player';
		//a player has been passed, now send player_info
		if($fffl_player_id>0){
			$data['player_info']['area']= $this->Rosters_View->get_team_player_area($team_id,$fffl_player_id);
			$data['player_info']['weeks_on_pup']= $this->Rosters->get_player_team_weeks_on_pup($team_id,$fffl_player_id);
			$data['player_info']['salary']= $this->Salaries->get_player_team_salary($team_id,$fffl_player_id);
			$data['player_info']['fffl_player_id'] = $fffl_player_id;
		}
		
		
			$this->load_view($path, $data, false, false, false);		
		
	}

//*******************************************************************************************************	
	//updates it
	public function update_team_player_data(){
		$this->Database_Manager-> database_backup(array('Rosters'));
			$fffl_player_id = $this->input->post('fffl_player_id');
			$team_id = $this->input->post('team_id');
			$salary = $this->input->post('salary');
			$area = $this->input->post('area');
			$weeks_on_pup = $this->input->post('weeks_on_pup');
			$query = $this->db->set('salary',$salary)
					->set('lineup_area',$area)
					->set('weeks_on_pup',$weeks_on_pup)
					->where('team_id',$team_id)
					->where('fffl_player_id',$fffl_player_id)
					->update('Rosters');
			echo json_encode(array('player' => $fffl_player_id, 'salary' => $salary));

	}
	
	
//***************************************************************

	public function fb_token($league_id){
		
		$this->Facebook_Interact->update_facebook_token($league_id);
	}
	
	
//*****************************************************************

	public function import_projections(){
		
		$this->Projections->import_scout_projections($this->current_year);
		$this->Projections->import_espn_projections($this->current_year);
		$this->Projections->import_cbs_projections($this->current_year);
		$this->Projections->import_fftoday_projections($this->current_year);
		$this->Projections->import_adp($this->current_year);
		//average the projections
		$this->Projections->average_projections($this->current_year);

	}

//******************************************************************
	public function load_projections($year){
		
		if($this->session->userdata('security_level')<3){
			redirect();
		}
		$content_data = array();
		
		//get array of all players franchised by 2 teams
		$content_data['unavailable'] = $this->Franchise->get_franchise_players(1,$year,2);
		
		//get array of all players franchised by 1 team
		$content_data['supplemental'] = $this->Franchise->get_franchise_players(1,$year,1);
		
		//get draft that is in current status
		$draft_in_progress = $this->Drafts->get_in_progress_drafts($this->league_id, $year);
		
		//get array of players drafted in that draft if it is current
		if($draft_in_progress>0){
			$draft_picks_array = $this->Drafts->get_drafted_players($draft_in_progress);
			
			foreach($draft_picks_array as $index => $data){
				$drafted[] = $data['fffl_player_id'];	
				
			}
			$content_data['drafted']=$drafted;
		}
		else{
			$content_data['drafted']=array();	
		}
		
		//get array of all players with a projection value, order by projection descinding
		
		//each index is player_id with keys of average, position, vbd starters, vbd first backup, vbd last backup, available(2,1,0)
		//starters1: qb 5 rb 5 wr 5 te 5 k 5
		//laststarter: qb 5 rb 15 wr 25 te 5 k 5
		//first backup qb 15 rb 25 wr 35 te 15 k 5
		//last backup: qb 25 rb 35 wr 55 te 15 k 5
		$all_players = $this->Projections->get_projections($year);
		
		
		//available, number of players available 2 draftable, 1 supplemental, 0 not available
		$adp=1;
		foreach($all_players as  $data){
			$content_data['all_players'][$data['fffl_player_id']]['adp']=999;
			$content_data['all_players'][$data['fffl_player_id']]['average']=$data['average'];
			$content_data['all_players'][$data['fffl_player_id']]['standard_deviation']=$data['standard_deviation'];
			$content_data['all_players'][$data['fffl_player_id']]['position']=$data['position'];
			$content_data['all_players'][$data['fffl_player_id']]['starters1']=$data['starters1'];
			$content_data['all_players'][$data['fffl_player_id']]['last_starter']=$data['last_starter'];
			$content_data['all_players'][$data['fffl_player_id']]['first_backup']=$data['first_backup'];
			$content_data['all_players'][$data['fffl_player_id']]['last_backup']=$data['last_backup'];
			
			
			if(!in_array($data['fffl_player_id'],$content_data['drafted'])){
				if(in_array($data['fffl_player_id'],$content_data['unavailable'])){
					$content_data['all_players'][$data['fffl_player_id']]['available']=0;	
				}
				elseif(in_array($data['fffl_player_id'],$content_data['supplemental'])){
						$content_data['all_players'][$data['fffl_player_id']]['available']=1;
				}
				else{
					$content_data['all_players'][$data['fffl_player_id']]['available']=2;
					$content_data['all_players'][$data['fffl_player_id']]['adp']=$adp;
					$adp++;
				}
			}
			else{
				$content_data['all_players'][$data['fffl_player_id']]['available']=0;	
				$content_data['all_players'][$data['fffl_player_id']]['adp']=$adp;
				$adp++;
			}
			
		}
		//get team roster
		$content_data['team_roster'] = $this->Rosters->get_team_complete_roster($this->session->userdata('team_id'));
		
		$this->index('projections', $content_data);
		
	}
	
//******************************************************************
	public function load_audit(){
		
		if($this->session->userdata('security_level')<2){
			redirect();
		}
		$content_data = array();
      //get the list of players who were fa before the last fa draft
		$url = base_url().'assets/json/fa.json';
		$headers = get_headers($url);
    	$file_exists = substr($headers[0], 9, 3);
		
		if($file_exists === "200")
		{
			$fa_file = file_get_contents($url);
			$fa_array = json_decode($fa_file,true);
			reset($fa_array);
			$content_data['fa_time'] = key($fa_array);
			$content_data['fa_players'] = $fa_array[$content_data['fa_time']];
			
		}
		else{ echo 'error'; }
      
      	//get all rosters
      	$all_teams=$this->Teams->get_all_team_id(1);
      	$rosters_array=array();
      	foreach($all_teams as $team_id){
          get_team_active_roster($team_id);
          get_team_inactives($team_id);
          
          }
		
		
		
		
		$this->index('audit', $content_data);
		
	}	
	
	
	
//*************************************************************************
	public function swap_teams_draft_day($team_id_a=NULL,$team_id_b=NULL){
		$data = array();
		
		//this was simply a request to load the view. No data has been passed yet
		$data['team_id_a'] = $team_id_a;
		$data['team_id_b'] = $team_id_b;
		
		//an array of id => team name
		$data['all_teams'] = $this->Teams->get_all_teams_id_to_first_nickname($this->session->userdata('league_id'));
		
		
		//a team was selected, now send the other teams
		if($team_id_a>0){
			//a team_a was selected, but team_b	
			$data['all_teams'] = $this->Teams->get_all_teams_id_to_first_nickname($this->session->userdata('league_id'));
		}
		$path ='admin/swap_teams_draft';
		//a team_b has been passed, send data and confirmation
		if($team_id_b>0){
			$data['team_a_draft'] = $this->Drafts->get_team_original_draft($team_id_a,$this->current_year);
			$data['team_b_draft'] = $this->Drafts->get_team_original_draft($team_id_b,$this->current_year);
		}
		
		
			$this->load_view($path, $data, false, false, false);
		
	}
	
//************************************************************************

	public function confirm_swap_teams_draft_day(){
		$this->Database_Manager-> database_backup(array('Draft_Picks'));
		$team_id_a = $this->input->post('team_id_a');
		$team_id_b = $this->input->post('team_id_b');
		$draft_id_a = $this->input->post('draft_id_a');
		$draft_id_b = $this->input->post('draft_id_b');
		//swap team a first
		$query = $this->db->set('draft_id',$draft_id_b)
				->where('original_owner',$team_id_a)
				->where('draft_id',$draft_id_a)
				->update('Draft_Picks');
		//swap team b
		$query = $this->db->set('draft_id',$draft_id_a)
				->where('original_owner',$team_id_b)
				->where('draft_id',$draft_id_b)
				->update('Draft_Picks');
		echo json_encode(array('success' => 'success'));
		
	}

//*************************************************************************
	public function adjust_fa_draft_order(){
		$data = array();
		
		if(NULL == $this->input->post('list_order')){
			$data['order']= $this->Free_Agents->get_FA_draft_order($this->session->userdata('league_id'));
			$path ='admin/update_fa_draft_order';
			$this->load_view($path, $data, false, false, false);
		}
		else {
			$draft_order = explode(',', $this->input->post('list_order'));	
			$this->Free_Agents->update_FA_draft_order($draft_order);
		}
		
	}
	
//*************************************************************************
	public function set_gow($week=NULL,$year=NULL){
		if($week == NULL) {
			$week = $this->current_week;
			$year = $this->current_year;
		}
			
		$data = array();

		if(NULL == $this->input->post('gow_a')){
			$data['games']= $this->Games->get_week_games($this->session->userdata('league_id'),$this->current_year,$this->current_week);
			
			$path ='admin/set_gow';
			$this->load_view($path, $data, false, false, false);
		}
		else {
			$this->Games->update_gow($this->session->userdata('league_id'),$week,$year,$this->input->post('gow_a'),$this->input->post('gow_b'));	
			echo json_encode(array('success' => 'success'));
			//$this->Games->update_gow($this->session->userdata('league_id'),$week,$year,46,49);		
		}
		
	}


//*****************************************************************

	public function drop_non_franchise($confirm=0){
		
		if($confirm==1){
			//decline any open trades
			$this->Trades->decline_all_open_trades(1);//***NI***
			
			//drop the non-franchise
			$this->Franchise->drop_non_franchise($this->league_id);
			//set the fa salary for all players
			$all_players = $this->Players->get_all_player_ids("","fffl_player_id","Players.last_name",'ASC',0);
			foreach($all_players['ids'] as $fffl_player_id){
				$this->Salaries->set_free_agent_salary($fffl_player_id,1);
			}
		
			
			$data['message']='Complete';
			$data['confirm']=$confirm;
			
				
		}
		else{
			$data['message']='Drop all players from rosters who have not been designated as Franchise Players?';
			$data['confirm']=$confirm;
		}
		$path ='admin/drop_non_franchise';

			$this->load_view($path, $data, false, false, false);

	}

//*****************************************************************

	public function adjust_owner_profile(){
		
		$path ='admin/adjust_owner_profile';
		$user_ids= $this->Owners->get_all_user_id_league($this->league_id);
		foreach($user_ids as $user_id){
			$data['all_owners'][$user_id]=$this->Owners->get_owner_first_name($user_id).' '.$this->Owners->get_owner_last_name($user_id);
		}
		asort($data['all_owners']);
		$this->load_view($path, $data, false, false, false);
			
		

	}
	
//********************************************************************

	public function preseason_predictions(){
		//get each team
		$teams=$this->Teams->get_all_team_id(1);
		$all_teams=array();
		//foreach team get roster by position sorted by projection with points and bye week, position as key
		foreach($teams as $team_id){
			$all_teams[$team_id]=array();
			$all_teams[$team_id]['team_id']=$team_id;
			$all_teams[$team_id]=array_merge($all_teams[$team_id],$this->Teams->get_team_conference_division($team_id));
			
			foreach(array('QB','RB','WR','TE','K') as $position){
				$player = $this->db->select('Projections.average,Projections.fffl_player_id')
						->from('Projections')
						->join('Rosters','Projections.fffl_player_id=Rosters.fffl_player_id')
						->where('Rosters.position',$position)
						->where('Rosters.team_id',$team_id)
						->where('Rosters.lineup_area','Roster')
						->order_by('Projections.average','DESC')
						->get();
				$all_teams[$team_id][$position]=$player->result_array();
				foreach($all_teams[$team_id][$position] as $key => $player_data){
					
					$team_info = $this->Players->get_player_info(array($player_data['fffl_player_id']),"fffl_player_id","current_team");
					$bye = $this->NFL_Teams->get_team_bye_week($team_info['current_team']);
					$all_teams[$team_id][$position][$key]['bye']=$bye;
				}
			}
			$all_teams[$team_id]['weeks']=array();
			$all_teams[$team_id]['season']=0;
			$all_teams[$team_id]['wins']=0;
			$all_teams[$team_id]['losses']=0;
			
			$week=1;
			while($week<14){
				$all_teams[$team_id]['weeks'][$week]=0;
				$week_array=array();
				//qb
				foreach($all_teams[$team_id]['QB'] as $player){
					if($player['bye']!=$week && !in_array($player['fffl_player_id'],$week_array)){
						$all_teams[$team_id]['weeks'][$week]=$all_teams[$team_id]['weeks'][$week]+($player['average']/16);	
						$week_array[]=$player['fffl_player_id'];
						break;
					}
				}
				//rb
				foreach($all_teams[$team_id]['RB'] as $player){
					if($player['bye']!=$week && !in_array($player['fffl_player_id'],$week_array)){
						$all_teams[$team_id]['weeks'][$week]=$all_teams[$team_id]['weeks'][$week]+($player['average']/16);	
						$week_array[]=$player['fffl_player_id'];
						break;
					}
				}
				//wr
				$count=1;
				foreach($all_teams[$team_id]['WR'] as $player){
					if($player['bye']!=$week && !in_array($player['fffl_player_id'],$week_array)){
						$all_teams[$team_id]['weeks'][$week]=$all_teams[$team_id]['weeks'][$week]+($player['average']/16);	
						$week_array[]=$player['fffl_player_id'];
						if($count==2){
							break;
						}
						$count++;
					}
				}
				
				//te
				foreach($all_teams[$team_id]['TE'] as $player){
					if($player['bye']!=$week && !in_array($player['fffl_player_id'],$week_array)){
						$all_teams[$team_id]['weeks'][$week]=$all_teams[$team_id]['weeks'][$week]+($player['average']/16);	
						$week_array[]=$player['fffl_player_id'];
						break;
					}
				}
				
				//rb, wr
				foreach($all_teams[$team_id]['RB'] as $player){
								
					if($player['bye']!=$week && !in_array($player['fffl_player_id'],$week_array)){
						$best_rb_id = $player['fffl_player_id'];
						$best_rb_avg =$player['average'];
						break;
					}
				}
				foreach($all_teams[$team_id]['WR'] as $player){
					if($player['bye']!=$week && !in_array($player['fffl_player_id'],$week_array)){
						$best_wr_id = $player['fffl_player_id'];
						$best_wr_avg =$player['average'];
						break;
					}
					
				}
				if($best_rb_avg>$best_wr_avg){
					$all_teams[$team_id]['weeks'][$week]=$all_teams[$team_id]['weeks'][$week]+($best_rb_avg/16);	
					$week_array[]=$best_rb_id;
						
				}
				else{
					$all_teams[$team_id]['weeks'][$week]=$all_teams[$team_id]['weeks'][$week]+($best_wr_avg/16);	
					$week_array[]=$best_wr_id;
				}
					
				
				//wr,te
				foreach($all_teams[$team_id]['WR'] as $player){
								
					if($player['bye']!=$week && !in_array($player['fffl_player_id'],$week_array)){
						$best_wr_id = $player['fffl_player_id'];
						$best_wr_avg =$player['average'];
						break;
					}
				}
				foreach($all_teams[$team_id]['TE'] as $player){
					if($player['bye']!=$week && !in_array($player['fffl_player_id'],$week_array)){
						$best_te_id = $player['fffl_player_id'];
						$best_te_avg =$player['average'];
						break;
					}
					
				}
				if($best_wr_avg>$best_te_avg){
					$all_teams[$team_id]['weeks'][$week]=$all_teams[$team_id]['weeks'][$week]+($best_wr_avg/16);	
					$week_array[]=$best_wr_id;
						
				}
				else{
					$all_teams[$team_id]['weeks'][$week]=$all_teams[$team_id]['weeks'][$week]+($best_te_avg/16);	
					$week_array[]=$best_te_id;
				}
					
				
				//k
				foreach($all_teams[$team_id]['K'] as $player){
					if($player['bye']!=$week && !in_array($player['fffl_player_id'],$week_array)){
						$all_teams[$team_id]['weeks'][$week]=$all_teams[$team_id]['weeks'][$week]+($player['average']/16);	
						$week_array[]=$player['fffl_player_id'];
						break;
					}
				}
				
				$all_teams[$team_id]['season']=$all_teams[$team_id]['season']+$all_teams[$team_id]['weeks'][$week];			
					
				
				$week++;	
				
			}
			
		}
		
		
		
		//foreach week, foreach game, determine winner, add to wins and losses and total points
		$week=1;
		while($week<14){
			$weeks_games = $this->Games->get_week_games(1,$this->current_year,$week);
			
			foreach($weeks_games as $game){
				$a_score = $all_teams[$game['opponent_a']]['weeks'][$week];
				$b_score = $all_teams[$game['opponent_b']]['weeks'][$week];
				if($a_score>$b_score){
					$all_teams[$game['opponent_a']]['wins']=$all_teams[$game['opponent_a']]['wins']+1;
					$all_teams[$game['opponent_b']]['losses']=$all_teams[$game['opponent_b']]['losses']+1;
				}
				else{
					$all_teams[$game['opponent_b']]['wins']=$all_teams[$game['opponent_b']]['wins']+1;
					$all_teams[$game['opponent_a']]['losses']=$all_teams[$game['opponent_a']]['losses']+1;
				}
				
			}
			$week++;
		}
		
		
		// Obtain a list of columns
			foreach ($all_teams as $key => $row) {
				$wins[$key]  = $row['wins'];
				$season[$key] = $row['season'];
			}
			
			// Sort the data with volume descending, edition ascending
			// Add $data as the last parameter, to sort by the common key
			array_multisort($wins, SORT_DESC, $season, SORT_DESC, $all_teams);
		
		
		//create_standings
		$standings=array();
		$standings['AFC']=array();
		$standings['NFC']=array();
		foreach($all_teams as $team_data){
			$standings[$team_data['conference']][$team_data['division']][$team_data['team_id']]['team_id']=$team_data['team_id'];
			$standings[$team_data['conference']][$team_data['division']][$team_data['team_id']]['wins']=$team_data['wins'];
			$standings[$team_data['conference']][$team_data['division']][$team_data['team_id']]['losses']=$team_data['losses'];
			$standings[$team_data['conference']][$team_data['division']][$team_data['team_id']]['points']=$team_data['season'];
			$standings[$team_data['conference']][$team_data['division']][$team_data['team_id']]['po']=$team_data['weeks'][1];
		}
		
		$po_scores='';
		foreach($standings as $conference=>$division){
			echo $conference.'<br>';
			foreach($division as $div => $standing){
				echo $div.'<br>';
				foreach($standing as $team){
					echo team_name_no_link($team['team_id']).' '.$team['wins'].'-'.$team['losses'].', '.floor($team['points']).'<br>';	
					$po_scores .= team_name_no_link($team['team_id']).': '.$team['po'].'<br>';
				}
				echo '<br>';
			}
			echo '<br>';
		}

		echo $po_scores;
		
		
		
	}
	
}//end Class Admin extends MY_Controller

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

