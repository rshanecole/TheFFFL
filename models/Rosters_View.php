<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Rosters_View Model.
	 *
	 * contains business logic for displaying roster elements
	 *		
	 */
	
Class Rosters_View extends CI_Model 
{
	public function __construct() 
    {
		parent::__construct();
		//$ci = get_instance();

		$this->load->helper('string');		
		
	}

//**********************************************************************
	
	//supplements the data added to the roster page about an individual player for that specific team
  	public function add_all_player_roster_data($team_id,$fffl_player_id,$year,$week){
		
		$data = array();
		$salary = $this->Salaries->get_player_team_salary($team_id,$fffl_player_id);
		$data['current_salary'] = $salary; 
		$weeks_on_pup = $this->Rosters->get_player_team_weeks_on_pup($team_id,$fffl_player_id);
		$data['weeks_on_pup'] = $weeks_on_pup;
		if($week==0) {
			$week=16; $year=$year-1;	
		}
		
		$scores_array = $this->NFL_stats->get_player_scores_season($year,$fffl_player_id,1,1,$week);
		
		$data['score_average'] = $scores_array['average'];
		foreach($this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","first_name last_name current_team position is_rookie is_injured nfl_injury_game_status injury_text nfl_status nfl_esbid")
			as $key => $data_item){
			$data[$key]=$data_item;		
		}
		$data['bye_week'] = $this->NFL_Teams->get_team_bye_week($data['current_team']);
		
		$data['is_player_locked'] = $this->Players->is_player_locked($fffl_player_id);
		
		$data['next_game_status'] = $this->Players->get_player_game_status($fffl_player_id);
		
		$data['headlines'] = $this->Players->get_player_headlines($fffl_player_id,1);
		
		return $data;
	}

//***********************************************************
	
	//gets a player's current location on roster
  	public function get_team_player_area($team_id,$fffl_player_id){
		
		
		$query = $this->db->select('lineup_area')
						->where('team_id',$team_id)
						->where('fffl_player_id',$fffl_player_id)
						->limit(1)
						->get('Rosters');
		
			$data=$query->row('lineup_area');
		
		return $data;
	}
	
}
/*End of file Rosters_View.php*/
/*Location: ./application/models/Teams.php*/