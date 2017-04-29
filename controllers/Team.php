<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Team extends MY_Controller
{
	/**
	 * Team controller.
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
		
		
		$this->load->model('Leagues');
		$this->load->model('Owners');
		$this->load->model('Teams');
		$this->load->model('Rosters');
		$this->load->model('Players');
		$this->load->model('NFL_Teams');
		$this->load->model('NFL_stats');
		$this->load->model('Rosters_View');
		$this->load->model('Franchise');
		$this->load->model('Salaries');
		$this->load->model('Drafts');
		$this->load->model('Schedules');
		$this->load->model('Standings');
		$this->load->model('Transactions');
		$this->load->model('Database_Manager');
		
		$this->current_year = $this->Leagues->get_current_season($this->league_id);
		$this->current_week = $this->Leagues->get_current_week($this->league_id);

	}

//******************************************************************
	// Loads the content for the team view, types of content area:
	// roster(starting lineup),schedule, set_franchise, franchise(history), set_inactive, franchise_stats
	// 
	public function index($team_id=NULL, $page_content='roster', $content_data=array()) 
    {
		
		//this is loading a view from one of the methods to display on the page
			//RESTRICTED TO LOGGED IN MEMBERS ONLY
			//Just in case not logged in we direct to login page
			if (!$this->session->userdata('logged_in') )
			{
				redirect("/Restricted");
			} 
			
			if(is_NULL($team_id)) 
			{
				$team_id = $this->session->team_id;
				$this->id($team_id,'roster');
			}
			
			$path = 'team/'.$page_content;
			
			$this->load_view($path, $content_data, false, false, false);

	}


//**************************************************************
  
	public function id($team_id=NULL,$page_content='roster') 
	{
		//RESTRICTED TO LOGGED IN MEMBERS ONLY
		//Just in case not logged in we direct to login page
		if (!$this->session->userdata('logged_in') )
		{
			redirect("/Restricted");
		} 
		
		if(is_NULL($team_id)) 
		{
			$team_id = $this->session->team_id;
		}
		$content_data['team_logo_path'] = $this->Teams->get_team_logo_path($team_id);
		$content_data['owner_picture_path'] = $this->Owners->get_user_picture_path($this->Teams->get_user_id($team_id));
		$content_data['owner_city'] = $this->Owners->get_owner_city($this->Teams->get_user_id($team_id));
		$content_data['owner_state'] = $this->Owners->get_owner_state($this->Teams->get_user_id($team_id));
		$content_data['owner_occupation'] = $this->Owners->get_owner_occupation($this->Teams->get_user_id($team_id));
		$content_data['owner_date_of_birth'] = $this->Owners->get_owner_date_of_birth($this->Teams->get_user_id($team_id));
		$content_data['owner_email'] = $this->Owners->get_owner_email($this->Teams->get_user_id($team_id));
		$content_data['team_first_year'] = $this->Teams->get_team_first_year($team_id);
		$content_data['team_non_consecutive_years'] = $this->Teams->get_team_non_consecutive_years($team_id);
		if($this->current_week==0){ $record_year = $this->current_year-1; } else { $record_year=$this->current_year; }
		$content_data['wins_losses']= $this->Standings->get_team_wins_losses_year($team_id,$record_year);
		$content_data['points']= $this->Standings->get_team_total_points_year($team_id,$record_year);
		$content_data['points_against']= $this->Standings->get_team_points_against_year($team_id,$record_year);
		$content_data['sos_array']=$this->Standings->sos_ranking($this->Teams->get_team_league_id($team_id),$record_year);
		
		$content_data['current_year'] = $this->current_year;
		//get the user id of the viewer to set permissions
		$user_id = $this->session->userdata('user_id');
		$content_data['security_level'] = $this->session->userdata('security_level');
		
		
		//content to initially display
		$content_data['display_page']=$page_content;
		$content_data['load_path'] = 'Team/'.$page_content.'/'.$team_id;
		//send a display for the dropdown. it should match what is in the content_selector array
		//should be the same as the page_content which is also the method. multiple words
		//can be separated by _ and it will take those out and capitalize each word
		$create_dropdown_title = explode('_',$page_content);
		$dropdown_title='';
		foreach($create_dropdown_title as $part) {
			$dropdown_title .= ucfirst($part).' '; 	
		}
		$content_data['dropdown_title'] = $dropdown_title;
		//content selector will be activated for these views
		//set the list of items for content selector
		$content_data['content_selector'] = array(
			//each key is the display in the dropdown, linked to the path to the method
			//in this class to run to get content to display
			'Roster' => base_url().'Team/roster/'.$team_id,
			'Schedule' => base_url().'Team/schedule/'.$team_id.'/'.$this->current_year,
			'Transactions' => base_url().'Team/transaction/'.$team_id.'/'.$this->current_year,
			'Draft' => base_url().'Team/draft/'.$team_id,
			'Franchise' => base_url().'Team/franchise/'.$team_id,
			'History' => base_url().'Team/history/'.$team_id
		);
		//titles of the pages will be upper cased
		$title = $this->Teams->get_team_name_first_nickname($team_id);
		$content_data['title']= ucwords($title);
		$path = 'team/team';
		
		
		$this->load_view($path, $content_data, true);
		
	}

//****************************************************************************************
	
	//request to view a team roster page
	public function roster($team_id=NULL,$year='current',$week='current',$validation=array())
	{
		$league_id=1;
		if($year=='current'){
			$year = $this->current_year;
			
			
		}
		if($week=='current'){
			$week =  $this->current_week;
			
			
		}
		if($week==0){$week=1; $scores_week=16; $scores_year = $year-1;} else { $scores_week=$week; $scores_year=$year;}
		
		//get the id of the session team if none was passed
		if(is_NULL($team_id))
		{
			$team_id = $this->session->userdata('team_id');
		}
		$content_data = array();
		$content_data['scores_year']=$scores_year;
		$content_data['team_id']=$team_id;
		$content_data['week']=$week;
		$content_data['year']=$year;
		$content_data['franchise_players'] = array();
		if($this->current_week==0){
			$content_data['franchise_players'] = $this->Franchise->get_simple_franchise_by_year($team_id,$this->current_year);
		}
		
		$content_data['league_weeks_on_pup']=$this->Leagues->get_league_weeks_on_pup($league_id);
		//collect data for team's roster
		$content_data['starters']=array();
		$players_array=$this->Rosters->get_team_starters($team_id,$week,$year);
		$active_roster_count=count($players_array);
		//this line simply to declare teh empty validation_array in case starters is actually empty. In which case the
		//array would never be delcared.
		$validation_array=array();
		foreach($players_array as $fffl_player_id) {
			
			$content_data['starters'][$fffl_player_id] = $this->Rosters_View->add_all_player_roster_data($team_id,$fffl_player_id,$scores_year,$scores_week);	
				
		}
		

		//if the request comes from the view the $validation array will be empty by default
		//so that it requires a validation to take place. Otherwise a validation occured already
		//and we don't do it again.
		if(empty($validation)){
			$validation_array=array();
			//create the array of the starting positions
			foreach($content_data['starters'] as $player){
				$validation_array[]=$player['position'];	
			}
			$validation = $this->Rosters->validate_starting_lineup($validation_array,$this->league_id);
			//not a valid lineup, reset the starting lineup 
			if($validation['valid']==FALSE){
				$this->Rosters->reset_starting_lineup($team_id,$year,$week);
				$validation['open_positions']=array();
			}
		}
		
		$content_data['open_positions']=$validation['open_positions'];
		//current bench and inactives don't make sense for past weeks and seasons. Only include them if current week or future week.
		if($week>=$this->current_week && $year===$this->current_year) {
			//check if ps deadline is open or not
			$content_data['ps_open']=FALSE;
			$ps_deadline = $this->NFL_Games->get_week_sunday_deadline(2,$this->current_year);
			if(now()<$ps_deadline) { $content_data['ps_open']=TRUE; }
			
			$players_array=$this->Rosters->get_team_bench($team_id,$week,$year);
			$active_roster_count=$active_roster_count+count($players_array);
			foreach($players_array as $fffl_player_id) {
				$content_data['bench'][$fffl_player_id] = $this->Rosters_View->add_all_player_roster_data($team_id,$fffl_player_id,$scores_year,$scores_week);
			}
			//function returns playerid and pup or ps in key
			$players_array=$this->Rosters->get_team_inactives($team_id);
			foreach($players_array as $inactive_position => $fffl_player_id) {
				$content_data['inactives'][$inactive_position][$fffl_player_id] = $this->Rosters_View->add_all_player_roster_data($team_id,$fffl_player_id,$scores_year,$scores_week);
			}
		}
		if($this->current_week>0){
			$content_data['number_to_release'] = $active_roster_count - $this->Rosters->get_league_active_roser_limit($league_id);
		}
		else {
			$content_data['number_to_release']=0;	
		}
		
		$this->index($team_id, 'roster', $content_data);
	}//roster function

//**************************************************************************

	public function release_players($team_id,$release_string){
		$this->Database_Manager->database_backup(array('Rosters','Starting_Lineups'));
		$this->Database_Manager->database_backup(array('Trades'));
		$this->Database_Manager->database_backup(array('Transactions'));
		$release_string = rtrim($release_string,"#");
		$release_array = explode(',',$release_string);
		foreach($release_array as $fffl_player_id){
			$this->Rosters->release_player($team_id,$fffl_player_id);
		}
		
		header('Location: '.base_url().'Team/id'); 
	}
	
	
	
//***************************************************************************
	
	//gets curent team starters for the given week then appends player to starting lineup array
	//passes off to update_roster method
	public function add_player_starting_lineup($team_id,$year,$week,$fffl_player_id){
		if($week==0){$week=1;}
		
		$current_starters = $this->Rosters->get_team_starters($team_id, $week, $year);
		$current_starters[] = $fffl_player_id;
		
		$this->update_roster($team_id,$year,$week,array_unique($current_starters));
	}
	
	//gets curent team starters for the given week then removes player from starting lineup array
	//passes off to update_roster method
	public function remove_player_starting_lineup($team_id,$year,$week,$fffl_player_id){
		if($week==0){$week=1;}
		
		$current_starters = $this->Rosters->get_team_starters($team_id, $week, $year);
		$key = array_search($fffl_player_id,$current_starters);
		unset($current_starters[$key]);
		
		$this->update_roster($team_id,$year,$week,array_unique($current_starters));
	}
	
	//*********************************************************************************
	
	//recieves a request to update the roster from the view. validates the roster,
	//then updates it in the model if valid.  Returns the open_positions to the roster method
	//which then resends the current roster to the view
	//starting_lineup_array should be an array of player ids
	public function update_roster($team_id,$year,$week,$starting_lineup_array){
		
		$validation = $this->Rosters->update_roster($team_id,$year,$week,$starting_lineup_array);
		
		
		$this->roster($team_id,$year,$week,$validation)	;
	}
//*********************************************************************
	
	//this function loads the release_list view and populates
	//the variables for it. The data is displayed in a modal for the
	//user to select players to release. Currently called from roster.php view
	public function load_release_list($team_id,$number_to_release,$error_message = ''){
		//collect data for selecting release modal if week > 0
		if($this->current_week>0){
			$data['team_id']=$team_id;
			$data['year']=$this->current_year;
			$data['error_message']=$error_message;
			$data['number_to_release']=$number_to_release;
			$data['active_roster']= $this->Rosters->get_team_active_roster($team_id);
			
			$this->index($team_id, 'release_list', $data);
		}
	}
	
//*********************************************************************
	
	//this function loads the subs_list view and populates
	//the variables for it. The data is displayed in a modal for the
	//user to order their players from 1st to last for sub priority. 
	//Currently called from roster.php view
	public function load_sub_list($team_id){
		
		
			$data['team_id']=$team_id;
			
			$data['active_roster']= $this->Rosters->sort_by_sub_prioity($team_id);
			
			$this->index($team_id, 'sub_list', $data);
		
	} 
	
//**************************************************************************

	public function update_subs($team_id){
		
		$update_list = $this->input->post('list_order');
		if(is_null($update_list)) { redirect("/Restricted");}
		$sub_array = explode(',',$update_list);
		
		$this->Rosters->update_subs($team_id,$sub_array);
		
		
	}
	
	
	
//************************************************************************	
	
	//activates pup player
	public function activate_pup($team_id,$fffl_player_id){
		$league_id = $this->Teams->get_team_league_id($team_id);
		//check if really available
		if(($this->Leagues->get_league_weeks_on_pup($league_id)-$this->Rosters->get_player_team_weeks_on_pup($team_id,$fffl_player_id))<=0){
			//he is available, send to model to move him to the bench
			$this->Rosters->activate_pup($team_id,$fffl_player_id);
		}
		//reload the roster
		$this->roster($team_id,'current','current',array())	;	
	}
  
  
//************************************************************************	
	
	//adds pup player
	public function add_pup($team_id,$fffl_player_id){
		$this->Rosters->add_pup($team_id,$fffl_player_id);
		//reload the roster
		$this->id($team_id,'roster');	
	}

//*********************************************************************
	
	//this function loads the pup_list view and populates
	//the variables for it. The data is displayed in a modal for the
	//user to select a pup player. Currently called from roster.php view
	public function load_pup_list($team_id,$error_message = ''){
		//collect data for selecting pup modal 
		
		$data['team_id']=$team_id;
		$data['year']=$this->current_year;
		$data['current_week']=$this->current_week;
		$data['error_message']=$error_message;
		
		$eligible_pup_players=$this->Rosters->get_eligible_pup_players($team_id);
		foreach($eligible_pup_players as $fffl_player_id){
			$data['eligible_pup_players'][$fffl_player_id]['salary']=	$this->Salaries->get_player_team_salary($team_id,$fffl_player_id);
			$data['eligible_pup_players'][$fffl_player_id]['display_data']= $this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","position first_name last_name");
		}
		$data['league_weeks_on_pup']=$this->Leagues->get_league_weeks_on_pup($this->league_id);

		$this->index($team_id, 'pup_list', $data);
	
	}//end load_pup_list 

//************************************************************************	
	
	//adds ps player
	public function add_ps($team_id,$fffl_player_id){
		$this->Rosters->add_ps($team_id,$fffl_player_id);
		//reload the roster
		$this->id($team_id,'roster');	
	}

//*********************************************************************
	
	//this function loads the ps_list view and populates
	//the variables for it. The data is displayed in a modal for the
	//user to select a ps player. Currently called from roster.php view
	public function load_ps_list($team_id,$error_message = ''){
		//collect data for selecting ps modal 
		
		$data['team_id']=$team_id;
		$data['year']=$this->current_year;
		$data['current_week']=$this->current_week;
		$data['error_message']=$error_message;
		
		$eligible_ps_players=$this->Rosters->get_eligible_ps_players($team_id);
		$data['eligible_ps_players']=array();
		foreach($eligible_ps_players as $fffl_player_id){
			$data['eligible_ps_players'][$fffl_player_id]['salary']=	$this->Salaries->get_player_team_salary($team_id,$fffl_player_id);
			$data['eligible_ps_players'][$fffl_player_id]['display_data']= $this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","position first_name last_name");
		}

		$this->index($team_id, 'ps_list', $data);
	
	}//end load_ps_list 
	
//*****************************************************************************	
	//This function is to create the page to view franchise players from past and a link to launch a modal to 
	//select current franchise players IF week is 0 (preseason)
	public function franchise($team_id){
		$all_franchise = $this->Franchise->get_all_franchise_by_year($team_id,'all');
		if(now()<$this->Calendars->get_calendar_time('franchise',$this->league_id) && $this->current_week==0){
			$data['franchise_open']=1;	
		}
		else{
			$data['franchise_open']=0;	
		}
		if($all_franchise){
			krsort($all_franchise);
			$data['franchise_history'] = $all_franchise;
		}
		else {
			$data['franchise_history']=array();	
		}
		//if it's preseason, send this data for the franchise selection
		if($this->current_week == 0){
			$data['league_salary_cap']=$this->Leagues->get_league_salary_cap($this->Teams->get_team_league_id($team_id));
		}
		$data['week']=$this->current_week;
		$data['year']=$this->current_year;
		$data['team_id']=$team_id;
		$this->index($team_id, 'franchise', $data);
		

	}//end franchise method

//*********************************************************************
	
	//this function loads the franchise_list view and populates
	//the variables for it. The data is displayed in a modal for the
	//user to select franchise players. Currently called from franchise.php view
	public function load_franchise_list($team_id,$error_message = ''){
		//collect data for selecting franchise modal if it is week 0 (preseason)
		if($this->current_week==0){
			$data['team_id']=$team_id;
			$data['year']=$this->current_year;
			$data['error_message']=$error_message;
			$data['salary_total'] = $this->Franchise->get_team_franchise_salary($team_id, $this->current_year);
			$data['league_salary_cap'] = $this->Leagues->get_league_salary_cap($this->Teams->get_team_league_id($team_id));
			$data['selected_franchise_players']=$this->Franchise->get_all_franchise_by_year($team_id,$this->current_year);
			
			$non_franchise_array=$this->Franchise->get_non_franchise_players($team_id,$this->current_year);
			foreach($non_franchise_array as $fffl_player_id){
				
				$data['unselected_franchise_players'][] = array(
						'fffl_player_id'=>$fffl_player_id,
						'display_data'=> $this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","position first_name last_name"),
						'salary' => $this->Salaries->get_player_team_salary($team_id,$fffl_player_id),
						'area'=>$this->Rosters_View->get_team_player_area($team_id,$fffl_player_id)
					);
			}
			
			$this->index($team_id, 'franchise_list', $data);
		}//end preseason franchise selection data
	}//end load_franchise_list 
	
//************************************************************************************
	//requests franchise model add a player to franchise list
	public function add_franchise_player($team_id,$fffl_player_id){
		$message = $this->Franchise->insert_franchise_player($team_id,$fffl_player_id,$this->current_year);
			
		$this->load_franchise_list($team_id,$message);
	}
	//**************************************************************************************
	//requests franchise model remove a player froom franchise list
	public function remove_franchise_player($team_id,$fffl_player_id){
		$this->Franchise->remove_franchise_player($team_id,$fffl_player_id,$this->current_year);
			
		$this->load_franchise_list($team_id);
	}

	
//*******************************************************************	
	
	//This function is to create the page to view a team's draft from past
	//if week is 0 show current year draft, otherwise so next year draft
	public function draft($team_id){
		$all_drafts = $this->Drafts->get_team_draft_by_year($team_id,'all');
		
		if($all_drafts){
			
			$data['draft_history'] = $all_drafts;
		}
		else {
			$data['draft_history']=array();	
		}

		$data['week']=$this->current_week;
		$data['year']=$this->current_year;
		$data['team_id']=$team_id;
		$this->index($team_id, 'drafts', $data);
		

	}//end draft method
//*******************************************************************	
	
	//This function is to create the page to view a team's schedule 

	public function transaction($team_id,$year){
		
		$data['team_id']=$team_id;
		$data['year']=$year;
		$data['current_year']=$this->current_year;
		$data['first_year']=$this->Teams->get_team_first_year($team_id);
      	
		$data['transactions_array']=$this->Transactions->get_transactions_year($this->league_id,$year,$team_id);
		$trades = $this->Trades->get_team_trades($team_id,$year);
		
		foreach($trades as $trade_id){
			$trade_details = $this->Trades->get_trade_details($trade_id,'All',FALSE);
			$given_text ='';
			$received_text ='';
			
			//other owner offers to this owner
			if($trade_details['offered_to']==$team_id){
				
				$partner = $trade_details['offered_by'];
				if(isset($trade_details['players_received'])){
					foreach($trade_details['players_received'] as $fffl_player_id){
						$given_text .= player_name_link($fffl_player_id,TRUE,FALSE).', ';
					}
					$given_text = chop($given_text,', ');
				}
				if(isset($trade_details['draft_picks_received'])){
					if($given_text!=''){ $given_text.=' and '; }
					foreach($trade_details['draft_picks_received'] as $pick_id => $pick_data){
						$given_text .= 'Rd. '.$pick_data['round'].'(#'.$pick_data['pick_number'].' '.date('D',$pick_data['start_time']).'), ';
					}
					$given_text = chop($given_text,', ');
				}
				if(isset($trade_details['players_offered'])){
					foreach($trade_details['players_offered'] as $fffl_player_id){
						$received_text .= player_name_link($fffl_player_id,TRUE,FALSE).', ';
					}
					$received_text = chop($received_text,', ');
				}
				if(isset($trade_details['draft_picks_offered'])){
					if($received_text!=''){ $received_text.=' and '; }
					foreach($trade_details['draft_picks_offered'] as $pick_id => $pick_data){
						$received_text .= 'Rd. '.$pick_data['round'].'(#'.$pick_data['pick_number'].' '.date('D',$pick_data['start_time']).'), ';					
					}
					$received_text = chop($received_text,', ');
				}
			}
			else{ //this owner offers to other owner
				$partner = $trade_details['offered_to'];
				
				if(isset($trade_details['players_offered'])){
					foreach($trade_details['players_offered'] as $fffl_player_id){
						$given_text .= player_name_link($fffl_player_id,TRUE,FALSE).', ';
					}
					$given_text = chop($given_text,', ');
				}
				if(isset($trade_details['draft_picks_offered'])){
					if($given_text!=''){ $given_text.=' and '; }
					foreach($trade_details['draft_picks_offered'] as $pick_id => $pick_data){
						
						$given_text .= 'Rd. '.$pick_data['round'].'(#'.$pick_data['pick_number'].' '.date('D',$pick_data['start_time']).'), ';
					}
					$given_text = chop($given_text,', ');
				}
				if(isset($trade_details['players_received'])){
					
					foreach($trade_details['players_received'] as $fffl_player_id){
						$received_text .= player_name_link($fffl_player_id,TRUE,FALSE).', ';
					}
					$received_text = chop($received_text,', ');
				}
				if(isset($trade_details['draft_picks_received'])){
					if($received_text!=''){ $received_text.=' and '; }
					foreach($trade_details['draft_picks_received'] as $pick_id => $pick_data){
						
						$received_text .= 'Rd. '.$pick_data['round'].'(#'.$pick_data['pick_number'].' '.date('D',$pick_data['start_time']).'), ';
					}
					$received_text = chop($received_text,', ');
				}
			}
			
			$trades_array=array(
				'team_id' => $team_id,
				'text' => 'Traded '.$given_text.' to '.team_name_link($partner).' for '.$received_text,
				'transaction_type' => 'Trade',
				'time' => $trade_details['time_approved']
			);
          	if(!is_array($data['transactions_array'])){
				
            	$data['transactions_array']=array();
            }
          	array_push($data['transactions_array'],$trades_array);
		}
		
		//sort the transactions by time
		// Obtain a list of columns
		if(isset($data['transactions_array']) && !empty($data['transactions_array'])){
			foreach ($data['transactions_array'] as $key => $row) {
				$time[$key]  = $row['time'];
				
			}
			
			// Sort the data with volume descending, edition ascending
			// Add $data as the last parameter, to sort by the common key
			array_multisort($time, SORT_DESC, $data['transactions_array']);
		}

		if($data['first_year']<2004){$data['first_year']=2004;}
		
		$this->index($team_id, 'transactions_list', $data);
		

	}//end transaction method	
	
//*******************************************************************	
	
	//This function is to create the page to view a team's schedule 

	public function schedule($team_id,$year){
		$data['team_schedule']=array();
		$data['team_id']=$team_id;
		$schedule = $this->Schedules->get_team_schedule($team_id,$year);
		//go through each week setting the data for the view
		foreach($schedule as $game){
			//set win or loss
			$data['team_schedule'][$game['week']]['wl']='';
			if($game['winner']>0 && $game['winner']==$team_id) { 
				$data['team_schedule'][$game['week']]['wl']='W';
			}
			elseif($game['winner']>0) { 
				$data['team_schedule'][$game['week']]['wl']='L';
			}
			
			//set opponent and score from team perspective
			if($game['opponent_a']==$team_id){
				$data['team_schedule'][$game['week']]['opponent']=$game['opponent_b'];
				$data['team_schedule'][$game['week']]['score']=$game['opponent_a_score'].'-'.$game['opponent_b_score'];
				//get record vs team
				
			}
			else{
				$data['team_schedule'][$game['week']]['opponent']=$game['opponent_a'];
				$data['team_schedule'][$game['week']]['score']=$game['opponent_b_score'].'-'.$game['opponent_a_score'];
			}
			$data['team_schedule'][$game['week']]['vs']=$this->Games->get_team_record_vs_team($team_id,$data['team_schedule'][$game['week']]['opponent']);

		}

		$data['year']=$year;
		$data['current_year']=$this->current_year;
		$data['first_year']=$this->Teams->get_team_first_year($team_id);
		$data['points']=$this->Standings->get_team_total_points_year($team_id,$year);
		$data['wins_losses']=$this->Standings->get_team_wins_losses_year($team_id,$year);
		
		if($data['first_year']<2004){$data['first_year']=2004;}
		$this->index($team_id, 'schedule', $data);
		

	}//end draft method	
	
//*****************************************************************************
	//returns array in format: team_id=>position=>Player_id=>average,salary,area
	public function depth(){
		$teams_and_rosters = array();
		//get all teams
		$all_teams = $this->Teams->get_all_team_id($this->league_id);
		if($this->current_week==0){$average_year=$this->current_year-1;} else { $average_year=$this->current_year; }
		//foreach get roster
		foreach($all_teams as $team_id){
			
			$all_players = $this->Rosters->get_team_complete_roster($team_id);
			//get position for index, returns playerid=>position->position
			$all_players = $this->Players->get_player_info($all_players,"fffl_player_id","position");
			
			//foreach player add to the array average, salary and area
			foreach($all_players as $fffl_player_id => $position){
				
				$get_average = $this->NFL_stats->get_player_scores_season($average_year,$fffl_player_id,1,1,16);

				$teams_and_rosters[$team_id][$position['position']][$fffl_player_id]['average']=$get_average['average'];
				$teams_and_rosters[$team_id][$position['position']][$fffl_player_id]['salary']=$this->Salaries->get_player_team_salary($team_id,$fffl_player_id);
				$teams_and_rosters[$team_id][$position['position']][$fffl_player_id]['area']=$this->Rosters->get_player_roster_area($team_id,$fffl_player_id);
				$teams_and_rosters[$team_id][$position['position']][$fffl_player_id]['fffl_player_id']=$fffl_player_id;
			}
			//sort each position by average descending
			foreach(array('QB','RB','WR','TE','K') as $position){
				// Obtain a list of columns
				if(isset($teams_and_rosters[$team_id][$position])){
					$sort_array = $teams_and_rosters[$team_id][$position];
				
				
					foreach ($sort_array as $key => $row) {
						$average[$key]  = $row['average'];
						$area[$key] = $row['area'];
					}
					
					// Sort the data with average descending, salary ascending
					// Add $data as the last parameter, to sort by the common key
					array_multisort($area, SORT_DESC, $average, SORT_DESC, $sort_array);
					$teams_and_rosters[$team_id][$position] = $sort_array;
					unset($area,$average);
				}
			}
		}//foreach all teams
		$data['teams_and_rosters']=$teams_and_rosters;
		$data['title'] = 'Depth Charts';
		$this->load_view('team/depth_chart', $data, true);
		
		
		
	}

	
}//end Class Team extends MY_Controller

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

