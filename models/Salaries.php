<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Salaries Model.
	 *
	 * ?????
	 *		
	 */
	
Class Salaries extends CI_Model 
{
	public function __construct() 
    {
		parent::__construct();
		//$ci = get_instance();
		$this->load->helper('date');
		$this->load->model('Rosters');
		$this->load->model('Players');
	}

//*******************************************************************
	
	//returns a salary for a player specific to a team.
	public function get_player_team_salary($team_id,$fffl_player_id){
		$this->db->select('salary');
		$conditions = "team_id =".$team_id." and fffl_player_id=".$fffl_player_id;
		$this->db->where($conditions);
		$this->db->limit(1);
		$query = $this->db->get('Rosters');
		return $query->row('salary');
			
	}

//*********************************************************************
	
	//increases the salary of a player for a specific given team
	//determines if it should add base or annual increase
	public function increase_salary($team_id,$fffl_player_id){
		//get the current salary
		$current_salary = $this->get_player_team_salary($team_id,$fffl_player_id);
		//get position of player
		$pos = $this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","position");
		$position = $pos['position'];
		//get the base and increase for that position
		$league_id = $this->Teams->get_team_league_id($team_id);
		
		$query= $this->db->select('base,annual_increase')
				->where('league_id',$league_id)
				->where('position',$position)
				->limit(1)
				->get('League_Salary_Settings');
		$result = $query->row();
		$base = $result->base;
		$increase = $result->annual_increase;
		
		//if current less than base, then add base
		if($current_salary < $base){
			$new_salary = $current_salary+$base;
		}
		//else if current less than 100 add increase
		elseif(($current_salary+$increase)<100) {
			$new_salary = $current_salary+$increase;	
		}
		else {
			$new_salary = 100;
		}
	
		//update the salary
		$this->db->set('salary',$new_salary);
		$this->db->where('team_id='.$team_id.' and fffl_player_id='.$fffl_player_id);
		$this->db->update('Rosters');
		
	}
	
//**************************************************************************************

	public function set_free_agent_salary($fffl_player_id,$reset=0){
		if(is_object($fffl_player_id)){$fffl_player_id=$fffl_player_id->fffl_player_id;}
		$max_salary_query = $this->db->select_max('salary','max')
										->where('fffl_player_id',$fffl_player_id)
										->get('Rosters');
		$result = $max_salary_query->row();
		$max = $result->max;
		
		$fa_salary = $this->get_free_agent_salary($fffl_player_id); 
		
		if(($max && $max>$fa_salary) || $reset==1){
			$salary = $max;	
		}
		else {
			$salary = $fa_salary;	
		}
		
		$this->db->set('fa_salary',$salary)
				->where('fffl_player_id',$fffl_player_id)
				->update('Players');
	}
	
//**************************************************************************************

	public function get_free_agent_salary($fffl_player_id){
		$fa_salary_query = $this->db->select('fa_salary')
										->where('fffl_player_id',$fffl_player_id)
										->get('Players');
		$result = $fa_salary_query->row();
		$fa_salary = $result->fa_salary;
		
		return $fa_salary;
	}

//**************************************************************************************
	//resets all free agent salaries to highest paid by current owner
	public function reset_all_free_agent_salaries(){
		$all_players=$this->Players->get_all_player_ids('',"fffl_player_id","Players.last_name",'ASC',0);
		foreach($all_players['ids'] as $fffl_player_id){
			$this->set_free_agent_salary($fffl_player_id->fffl_player_id,1);
		}
	}
	
	
}//end model


/*End of file Database_Manager.php*/
/*Location: ./application/models/Database_Manager.php*/