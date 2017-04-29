<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Substitutions extends CI_Controller
{
	/**
	 * Substitution controller.
	 * CLI-Cron job to run sunday mornings
	 *
	 */

	public $league_id = 1; //***NI*** remove when multiple leagues are in place
	public $current_year;	
	public $current_week;

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
			$this->load->model("Rosters");	
			$this->load->model("Teams");
			$this->load->model("Players");	
			$this->load->model('NFL_Teams');
                        $this->load->model('Leagues');
	                $league_id = 1;
		        $this->current_year = $this->Leagues->get_current_season($league_id);
		        $this->current_week = $this->Leagues->get_current_week($league_id);
			
			$this->load->helper('combinations');	
		//end database backup
	}


	public function index()
	{

		$this->Database_Manager->database_backup(array('Starting_Lineups','Rosters'));
		
	
	//check's each team's current starting lineup 
	//if a palyer is out it checks for a sub
	
		//get all teams
		$teams = $this->Teams->get_all_team_id($this->league_id);
		foreach($teams as $team_id){
			//get all the starters for a team
			$starters = $this->Rosters->get_team_starters($team_id, $this->current_week, $this->current_year);
			$player_info = $this->Players->get_player_info($starters,"fffl_player_id","fffl_player_id nfl_injury_game_status current_team");
				
			foreach($player_info as $fffl_player_id => $data){
				//if injury status is OUT
			
				if($data['nfl_injury_game_status']=='OUT' || $this->NFL_Teams->get_team_bye_week($data['current_team'])==$this->current_week ){
					
					if($data['nfl_injury_game_status']=='OUT'){
					//get player's kickoff time 
					$start_time = $this->NFL_Teams->get_team_kickoff($data['current_team'],$this->current_week,$this->current_year);
					
					//check if it's been less than 15 minutes since the game's scheduled start time.
					}
					else {
					    $start_time =0;
					}
					if((now()-$start_time)<=900 || $this->NFL_Teams->get_team_bye_week($data['current_team'])==$this->current_week ){
						//player is out and the game is less than 15 minutes in or hasn't begun
						//see if there's a suitable sub that meets the league's lineup validation
						//and is not out, and is less than 15 minutes past the game's start time
						
						//determine whhich positions would be open if the player is removed
						$validation_array = array();
						$edited_starters = $starters;
						if(($key = array_search($fffl_player_id, $edited_starters)) !== false) {
							unset($edited_starters[$key]);
						
							foreach($edited_starters as $fffl_player_id_temp){
								$position = $this->Players->get_player_info(array($fffl_player_id_temp),"fffl_player_id","position");
								$validation_array[]=$position['position'];
							}
							$lineup_validation = $this->Rosters->validate_starting_lineup($validation_array,$this->league_id);
							$open_positions_array = $lineup_validation['open_positions'];
							
							//get the list of substitutes who meet the positons requirements in priority order
							$this->db->where('team_id',$team_id);
							$this->db->group_start();
							foreach($open_positions_array as $position){
								$this->db->or_where('position',$position);	
							}
							$this->db->group_end();
							$this->db->where('lineup_area','Roster');
							$this->db->order_by('sub_priority','ASC');
							$subs_query = $this->db->get('Rosters');
							
							//foreach eligible player check if it's not more than 15 minutes
							//from scheduled start time and he isn't in the starting lineup already. If not
							//make the switch 
							foreach($subs_query->result_array() as $player_info){
								$sub_player_info = $this->Players->get_player_info(array($player_info['fffl_player_id']),"fffl_player_id","fffl_player_id nfl_injury_game_status current_team");
								$start_time = $this->NFL_Teams->get_team_kickoff($sub_player_info['current_team'],$this->current_week,$this->current_year);	
								//allt he final checks
								if($sub_player_info['nfl_injury_game_status']!='OUT' && (now()-$start_time)<900 && !in_array($sub_player_info['fffl_player_id'],$edited_starters)){
									//make the switch
									
									unset($starters[$key]);
									$starters[] = $sub_player_info['fffl_player_id'];
									
									//bypass validation because we've already validated everything to this point
									//and the validation method will not allow locked players to be added
									$update_success=$this->Rosters->update_starting_lineup($team_id,$this->current_year,$this->current_week,$starters);
									if($update_success){
										echo team_name_no_link($team_id).' - '.player_name_no_link($fffl_player_id).' removed '.player_name_no_link($sub_player_info['fffl_player_id']).' added.<br>';
									
									}
									else {
										echo 'There was an error trying to sub: '.team_name_no_link($team_id).' - '.player_name_no_link($fffl_player_id).' to be removed '.player_name_no_link($sub_player_info['fffl_player_id']).' to be added.<br>';	
									}
									break;
									
								}
							}
						
						}
						
						
						
						
						
						
											
					}
						
				}
			}
		}
	}

	
} //end Class Substitutions 

/*End of file Free_Agent_Draft.php*/
/*Location: ./application/controllers/CLI/RSS_Updates.php*/