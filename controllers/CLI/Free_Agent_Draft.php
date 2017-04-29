<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Free_Agent_Draft extends CI_Controller
{
	/**
	 * Free agent draft controller.
	 * CLI-Cron job to run wed and fri
	 *
	 */
	
	//Load the needed libraries.  
	public function __construct() 
    {
		parent::__construct();
		// this controller can only be called from the command line
      //  if (!$this->input->is_cli_request()) show_error('Direct access is not allowed');
		//$this->load->library('rssparser');
		
		//backup the database
			// Load the DB utility class
			$this->load->model('Database_Manager');	
			$this->load->model("Free_Agents");	
			$this->load->model("Calendars");
			$this->load->model("Rosters");	
			$this->load->Model('Facebook_Interact');		
		//end database backup
	}


	public function index()
	{
		$this->Database_Manager->database_backup(array('FA_drafts'));
		$this->Database_Manager->database_backup(array('FA_Draft_Order','FA_Lists','FA_Release'));
		$this->Database_Manager->database_backup(array('FA_Requests','Starting_Lineups'));
		$this->Database_Manager->database_backup(array('Rosters','Transactions'));
		
		$league_id = 1; //***NI*** remove when multiple leagues are in place
		$current_year = $this->Leagues->get_current_season($league_id);
		$current_week = $this->Leagues->get_current_week($league_id);

		$first_fa = $this->Calendars->get_calendar_time('firstFA',$league_id);
		$last_fa = $this->Calendars->get_calendar_time('lastFA',$league_id);
		
		//allows this to run within an hour before the first and hour after the last draft
		//ends the script otherwise
		if(($first_fa-3700)>now()){ echo 'It is not time for the first free agent draft'; exit; }
		elseif(($last_fa+3700)<now()) { echo 'The last free agent draft of the year has already occured'; exit; }


		//This continues the draft as long as it needs to go, when the draft is over the variable $drafting should
		//be set at 0 to end the while

		$drafting = 1;
		$pick = 1;
		$draft_order = $this->Free_Agents->get_FA_draft_order($league_id);
		
		$draft_list=array();
		$facebook_message = 'FA Draft Results for Week '.$current_week.', '.date('l',now()).'
		 
		 ';
		while ($drafting == 1) 
		{//			
			//this ends the draft if the pick doesn't exist
			if (!isset($draft_order[$pick]))
			{
				$drafting = 0;
			}
			//the pick exists
			else
			{
				//d($pick);
				//reset draft_list
				unset($draft_list);
				$draft_list = array();
				
				//This gets the team with the pick
				$team_id = $draft_order[$pick];
				//d($team_id);
				//get team's lists if there are submitted lists
				$submitted_lists = $this->Free_Agents->get_team_submitted_lists($team_id);
				//d($submitted_lists);
				if(empty($submitted_lists)) {//passing team, no active lists
					$pick++;
				}
				else { //continue checking lists and begin pick process
					//determine number of empty roster spots
					$roster = $this->Rosters->get_team_active_roster($team_id);
					$empty_spots = $this->Rosters->get_league_active_roser_limit($league_id) - count($roster);
					if($empty_spots<0){$empty_spots=0;}
					
					//go through each list
					foreach($submitted_lists as $list_id => $number_desired){
						//get draft list and release list for this list_id
						if($empty_spots==0){
							
							$release_data_array = $this->Free_Agents->get_list_release_player_data($team_id,$list_id);
							$release_player = array_shift($release_data_array);
							//d($release_player);
						} 
						else{
							$release_player=0;	
						}
						$list_player_data_array = $this->Free_Agents->get_list_player_data($team_id,$list_id);
						$pick_player = array_shift($list_player_data_array);
						//d($pick_player);
						//if there are releaseable players or an empty spot and draftable players, then this will
						//be the list picked from. Set the draft list
						if((isset($release_player) || $empty_spots>0) && isset($pick_player)){
							$draft_list=array('list_id'=>$list_id,'number_desired'=>$number_desired,'pick_player'=>$pick_player,'release_player'=>$release_player,'pick'=>$pick);
							
							//d($draft_list);
							break; //breaks the foreach loop looking at each list
						}
						else{ //this list should no longer be submitted
							//deactive this list and change number_desired to 0
							$this->Free_Agents->deactivate_list($list_id,$team_id);
							$this->Free_Agents->update_number_desired($team_id,$list_id,0);
							
						}

					}
					
					//make sure a draft list has been selected and make the pick, otherwise move on to next pick
					if(!empty($draft_list)){
						//makes the pick, sets all transactions, updates fa_salary, updates request lists and
						//release lists, etc.
						$this->Free_Agents->make_draft_pick($draft_list,$team_id); 
						//d($pick,team_name_no_link($team_id),$pick_player,$release_player);
						unset($release_player,$pick_player);
						$facebook_message .=$pick.' '.team_name_no_link($team_id).' selects '.player_name_no_link($draft_list['pick_player']).' releases ';
						if($draft_list['release_player']>0){
							$facebook_message .= player_name_no_link($draft_list['release_player']).'
						 ';
						}
						else{
							$facebook_message .= 'None
						 ';
						}
						//move team to bottom of draft order array
						unset($draft_order[$pick]);
						$draft_order[] = $team_id; 
					}
					
					$pick++; //move on to next pick

				}// end of begin pick process
		
			} //end else for the pick exists
					
		} //end while drafting
	
		//***draft over***
		
		//adjust the ladder
		$this->Free_Agents->update_FA_draft_order($draft_order);
		
		//set all lists to is_submitted = 0
		$this->Free_Agents->deactivate_all_lists();  
		
		//create string of picks and send to facebook
		$facebook = $this->Facebook_Interact->post_to_facebook($facebook_message,$league_id);
		
	} //end index
				

	
} //end Class Free_Agent_Draft 

/*End of file Free_Agent_Draft.php*/
/*Location: ./application/controllers/CLI/RSS_Updates.php*/

