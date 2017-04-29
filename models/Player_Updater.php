<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Players_Updater Model.
	 *
	 * ?????
	 *		
	 */
	
Class Player_Updater extends CI_Model 
{
	public $league_id = 1; //***NI*** remove when multiple leagues are in place
	public $current_year;	
	public $current_week;
	public function __construct() 
    {
		$this->load->helper('string');
		$this->load->model('Leagues');
		$this->load->model('NFL_Games');
		$this->current_year = $this->Leagues->get_current_season($this->league_id);
		$this->current_week = $this->Leagues->get_current_week($this->league_id);
		parent::__construct();
		//$ci = get_instance();

		
	}
//*****************************************************************************	
	//removes all nfl status for players not RET to prep for CLI updater 
	//that checks current team and status of every player
	//when status is empty and the updater is finished, the player is checked
	//for his status by the controller and status is set by set_player_status function
	public function remove_nfl_status()
	{

		$this->db->where("nfl_status<>'RET'");
		$this->db->update('Players',"nfl_status='0'");
	
		return true;	
	}
//********************************************************************************
	public function set_player_status($id, $id_type="fffl_player_id",$status)
	{
		$this->db->where($id_type,$id);
		$this->db->update('Players',"nfl_status=".$status);	
	}
	
	//player is already in the DB, update
	//id must be separate from the data array
	//data_array must be all the data to be updated
	public function update_player_info($id_type="fffl_player_id",$id, $data_array) {
		
		$this->db->where($id_type,$id);
		$this->db->update("Players",$data_array);
		
		
	}
//***********************************************************************************		
	//add a player to the database
	//nfl_id must be an index in the array
	public function add_player($data_array,$is_rookie="0")
	{
		$data_array['is_rookie']=$is_rookie;
		$this->db->set($data_array);
		$this->db->insert('Players');
		
		$week=$this->current_week;
		//d($week);
		//add stat table lines for the season
		if($this->current_week>0 && $this->current_week<17){
			
			$bye_week = $this->NFL_Teams->get_team_bye_week($data_array['current_team']);
			$fffl_player_id = $this->Players->convert_player_id($data_array['nfl_player_id'], 'nfl_player_id', 'fffl_player_id');
			while($week>0){
				if($week==$bye_week){ 
					$opponent='Bye'; 
				} 
				else {
					$opponent= $this->NFL_Teams->get_team_opponent($data_array['current_team'],$week,$this->current_year);
				}
				$this->NFL_stats->add_player_stats_table($fffl_player_id,$week,$this->current_year,$data_array['current_team'],$opponent);
				$week--;
			}
		}
		
	}
		
//************************************************************************************

	public function cbs_injury_details_json_array($current_week){
		
		$this->db->set('is_injured',0)
				->set('nfl_injury_game_status','')
				->set('injury_text','')
				->update('Players');
		$url = 'http://api.cbssports.com/fantasy/players/injuries?version=3.0&SPORT=football&response_format=json';
		$headers = get_headers($url);
    	$file_exists = substr($headers[0], 9, 3);
		
		if($file_exists === "200")
		{
			$details_file = file_get_contents($url);
			$details_json = json_decode($details_file,true);
			$injuries = $details_json['body']['injuries'];
			$positions = array('QB','RB','WR','TE','K');
			$injury_statuses = array('OUT','PROBABLE','DOUBTFUL','QUESTIONABLE','DL','IL','PUP','IR','LEFT');
			foreach($injuries as $injury){
				//reset fffl_player_id each time so the previous isn't mistaken for a player whose fffl_player_id
				//couldn't be found
				$fffl_player_id=0;
				if(in_array($injury['player']['position'],$positions)) {
					$cbs_id = $injury['player_id'];
					$status = strtoupper($injury['status']);
					$position = $injury['player']['position'];
					$first_name = $injury['player']['firstname'];
					$last_name = $injury['player']['lastname'];
					$last_name_array  = explode(' ',$last_name);
						//incase there's a jr. or sr. on eand of name remove the index 1 if it is
						//otherwise just keep the last name
						$surnames = array('Jr.','Sr.','III');
						if(isset($last_name_array['1'])){
							if(in_array($last_name_array['1'],$surnames)){
								$last_name=$last_name_array['0'];
							}
						}
					$current_team = $injury['player']['pro_team'];
					$description = $injury['injury_type'].': '.$injury['expected_return'];
					//these are real injuries that are eligible for PUP
					if(in_array($status,$injury_statuses)){
						
						//find the player with this cbs id, get the fffl_player_id for update in projections later
						$player_cbs_query = $this->db->select('fffl_player_id')
														->where('cbs_id',$cbs_id)
														->get('Players');
						//found him, do the update
						if($player_cbs_query->num_rows()==1){
							foreach($player_cbs_query->result_array() as $fffl_player_id){
								$this->db->set('is_injured',1)
										->set('injury_text',$description)
										->set('nfl_injury_game_status',$status)
										->where('fffl_player_id',$fffl_player_id['fffl_player_id'])
										->update('Players');
							}
						}
						//didn't find by cbs_id, attempt to add the cbs_id to the player
						else{
							$player_id_query = $this->db->select('fffl_player_id')
														->where('first_name',$first_name)
														->where('last_name',$last_name)
														->where('current_team',$current_team)
														->get('Players');
							//found the player by name and team, add the cbs_id
							if($player_id_query->num_rows()==1){
								foreach($player_id_query->result_array() as $fffl_player_id){	
									$this->db->where('fffl_player_id',$fffl_player_id['fffl_player_id'])
												->set('cbs_id',$cbs_id)
												->update('Players');

								}
								$this->db->set('is_injured',1)
										->set('injury_text',$description)
										->set('nfl_injury_game_status',$status)
										->where('fffl_player_id',$fffl_player_id['fffl_player_id'])
										->update('Players');
							}
							//couldn't find the player
							else{
                              //echo 'CBS Not found<br>'.$cbs_id;
									
							}
						}

					//not eligible for pup but will be entered anyway
					} else {
						
						//find the player with this cbs id, get the fffl_player_id for update in projections later
						$player_cbs_query = $this->db->select('fffl_player_id')
														->where('cbs_id',$cbs_id)
														->get('Players');
						//found him, do the update
						if($player_cbs_query->num_rows()==1){
							foreach($player_cbs_query->result_array() as $fffl_player_id){
								$this->db->set('is_injured',0)
										->set('injury_text',$description)
										->set('nfl_injury_game_status',$status)
										->where('fffl_player_id',$fffl_player_id['fffl_player_id'])
										->update('Players');
							}
						}
						//didn't find by cbs_id, attempt to add the cbs_id to the player
						else{
							$player_id_query = $this->db->select('fffl_player_id')
														->where('first_name',$first_name)
														->where('last_name',$last_name)
														->where('current_team',$current_team)
														->get('Players');
							//found the player by name and team, add the cbs_id then update
							if($player_id_query->num_rows()==1){
								foreach($player_id_query->result_array() as $fffl_player_id){	
									$this->db->where('fffl_player_id',$fffl_player_id['fffl_player_id'])
												->set('cbs_id',$cbs_id)
												->update('Players');

								}
								$this->db->set('is_injured',0)
										->set('injury_text',$description)
										->set('nfl_injury_game_status',$status)
										->where('fffl_player_id',$fffl_player_id['fffl_player_id'])
										->update('Players');
							}
							//couldn't find the player
							else{
                              //echo 'CBS Not found<br>'.$cbs_id.' '.$first_name.' '.$last_name;
									
							}
						}	
					}	
				}	
			}
		}
		else //file not found 
		{
			echo 'notfound';

		}
		
		//gameday inactives updates
		$url = 'http://api.cbssports.com/fantasy/players/inactives?version=3.0&period='.$current_week.'&response_format=json&SPORT=football';
		$headers = get_headers($url);
    	$file_exists = substr($headers[0], 9, 3);
		
		if($file_exists === "200")
		{
			$details_file = file_get_contents($url);
			$details_json = json_decode($details_file,true);
			$inactives = $details_json['body']['gameday-inactives']['games'];
			foreach($inactives as $game) {
				//home team
				foreach($game['home_team']['inactive_players'] as $player) {
					$cbs_id = $player['id'];
					$this->db->set("is_injured",1)
							->set("nfl_injury_game_status",'OUT')
							->where('cbs_id',$cbs_id)
							->update('Players');
				}
				//away team
				foreach($game['away_team']['inactive_players'] as $player) {
					$cbs_id = $player['id'];
					$this->db->set("is_injured",1)
							->set("nfl_injury_game_status",'OUT')
							->where('cbs_id',$cbs_id)
							->update('Players');
				}
			}
		}
	}
	
	
	
}//end model


/*End of file Players.php*/
/*Location: ./application/models/Players.php*/