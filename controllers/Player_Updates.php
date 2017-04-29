<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Player_Updates extends CI_Controller
{
	/**
	 * Player_Updates Controller.
	 * CLI-Cron job to run every 3 hours
	 * Updates for each player
	 */
	
	//Load the needed libraries.  
	public function __construct() 
    {
		parent::__construct();
		// this controller can only be called from the command line
        if (!$this->input->is_cli_request()) show_error('Direct access is not allowed');
		
		$this->load->model('Player_Updater');
		$this->load->model('NFL_Teams');
		$this->load->model('Players');
		
		//backup the database
			// Load the DB utility class
			$this->load->model('Database_Manager');
			
			$this->Database_Manager->database_backup(array('Players'));
			
			
		//end database backup
	}

	// Will cycle through all nfl players in nfl api
	// using nfl id, then access player details api and get all info
	// send to model to update or add player as rookie if not already in Db
	public function index()
	{
		//nfl fantasy api
		//using 3000 as the cut off should include all players. count required or it only includes limited number
		//returns id, esbid, gsisPlayerId,firstName,lastName,teamAbbr,opponentTeamAbbr,position,depthChartOrder
		$nfl_players_file = file_get_contents('http://api.fantasy.nfl.com/v1/players/researchinfo?count=3000&format=json');
		$nfl_players_json = json_decode($nfl_players_file,true);
		$all_nfl_players = $nfl_players_json['players'];
		//list of all the positions for each player position to be checked against
		//so as to not include defensive players or punters
		$positions_array = array('QB','RB','WR','TE','K');
		//remove nfl_status from all but RET players
		$this->Player_Updater->remove_nfl_status();
		foreach($all_nfl_players as $player) {
			//check if is a qb rb wr te or k
			if(in_array($player['position'],$positions_array) ) {
				$player_data = array();
				$id= $player['id'];
				
				if($old_team_array = $this->Players->get_player_info(array($id),"nfl_player_id","current_team"))
				{
					$old_team = $old_team_array['current_team'];
				}
				else
				{
					$old_team="FA";	
				}
				
				$player_data['first_name'] = str_replace("'","\'",$player['firstName']);
				$player_data['last_name'] = str_replace("'","\'",$player['lastName']);
				
				//some depthchartorders were coming up NULL. These are set to 0 instead
				//When working iwth depthchartorder it needs to be greater than 0 to be
				//used
				if(!$player['depthChartOrder'])
				{
					$player['depthChartOrder']=0;
				}
				$player_data['depth_chart_order'] = $player['depthChartOrder'];
				
				//get the more detailed json file from nfl api
				$details = $this->nfl_details_json($id);
				
				
				//this adds the data from the nfl details json file to the array passed.
				//adds current_team,position,nfl_esbid,nfl_gsis_player_id,nfl_status(UFA,ACT,SUS,RET, more?),
				//nfl_injury_game_status,nfl_jersey_number
				$player_data = $this->append_nfl_details_json_array($details,$player_data);
				
				//fidn out if the player exists in the DB
				$is_in_database = $this->Players->is_in_database($id);
				
				if($is_in_database) {
					//send to updater to update
					
					$update_status = $this->Player_Updater->update_player_info('nfl_player_id',$id,$player_data);
					//echo results to see changed teams
					if($old_team<>$player_data['current_team']) 
					{
						echo $player_data['first_name'].' '.$player_data['last_name'].' '.$old_team.'->'.$player_data['current_team'].'<br>'; 
					}
				} elseif($details) {
					//add nfl_player_id to data array since the add player function doesn't accept an id it must be in the array
					$player_data['nfl_player_id']=$id;
					//send to updater to add a rookie
					$update_status = $this->Player_Updater->add_player($player_data,1);
					//echo new rookies
					echo $player_data['first_name'].' '.$player_data['last_name'].' rookie->'.$player_data['current_team'].'<br>'; 
					
					
				}

			}//if in position array
		}//foreach every player
		
		//any player who does not have a status at this point is checked
		//individually by getting array of non-status players from Players model
		//then getting data on the player from nfl details json and updating status
		$players_without_status_array = $this->Players->get_all_player_ids("nfl_status='0'","nfl_player_id");
		
		foreach($players_without_status_array as $id)
		{
			if($id->nfl_player_id){
				//get first and last name and set depth chart order to 0 since the player
				//clearly isn't active
				$player_id_array = array($id->nfl_player_id);
				$player_data = $this->Players->get_player_info($player_id_array,"nfl_player_id","first_name last_name");
				$player_data['depth_chart_order']='0';
				$details = $this->nfl_details_json($id->nfl_player_id);
				//this adds the data from the nfl details json file to the array passed.
				//adds current_team,position,nfl_esbid,nfl_gsis_player_id,nfl_status(UFA,ACT,SUS,RET, more?),
				//nfl_injury_game_status,nfl_jersey_number
				$player_data = $this->append_nfl_details_json_array($details,$player_data);
				//send to updater to update player
				$update_status = $this->Player_Updater->update_player_info('nfl_player_id',$id->nfl_player_id,$player_data);
				//echo player retired
				
				echo $player_data['first_name'].' '.$player_data['last_name'].' '.$player_data['current_team'].'<br>'; 
				
			}
			
		}//foreach palyers_without_status_array
		
	}//end index function
	
	public function nfl_details_json($nfl_player_id)
	{
		$url = 'http://api.fantasy.nfl.com/v1/players/details?playerId='.$nfl_player_id.'&statType=seasonStats&format=json';
		$headers = get_headers($url);
    	$file_exists = substr($headers[0], 9, 3);
		
		if($file_exists === "200")
		{
			$details_file = file_get_contents($url);
			$details_json = json_decode($details_file,true);
			$details = $details_json['players']['0'];
			
			return $details;
		}
		else 
		{
			
			return false;	
		}
	}
	//adds the fields from the nfl_details json array to the 
	//player data array that's passed to it
	public function append_nfl_details_json_array($details,$player_data)
	{
		//if details returned false, then the player must be retired, else continue
		if(!$details)
		{
			$player_data['current_team']='RET';
			//status may be UFA, ACT, SUS, RET, UDF?, CUT maybe more
			$player_data['nfl_status']= 'RET';
		}
		else
		{
			$player_data['current_team']=$details['teamAbbr']; if($player_data['current_team']==='') { $player_data['current_team'] = 'FA'; }
			$player_data['position'] = $details['position'];
			$player_data['nfl_esbid'] = $details['esbid'];
			$player_data['nfl_gsis_player_id'] = $details['gsisPlayerId'];
			//status may be UFA, ACT, SUS, RET, UDF?, CUT maybe more
			$player_data['nfl_status']= $details['status'];
			//uncertain of injury status returns. Maybe keep a separate injury update from CBS?
			$player_data['nfl_injury_game_status'] = $details['injuryGameStatus'];
			$player_data['nfl_jersey_number'] = $details['jerseyNumber'];
		}
		
		return $player_data;	
		
	}
	
	
}//end Class Player_Updates 

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

