<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Players Model.
	 *
	 * ?????
	 *		
	 */
	
Class Leagues extends CI_Model 
{
	public function __construct() 
    {
		parent::__construct();
		//$ci = get_instance();

		$this->load->helper('string');
	}


//*************************************************************************	
	//returns current season
	public function get_current_season($league_id)
	{
    $this->db->select("current_season");
		$this->db->from('Leagues');
		$this->db->where("league_id",$league_id);

		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				
				$data = $row->current_season;
			}
			return $data;
		}
		else
		{
			return false;
		}
		
	}//get current season

//**********************************************************************	
	public function get_current_week($league_id)
	{
    $this->db->select("current_week");
		$this->db->from('Leagues');
		$this->db->where("league_id",$league_id);

		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				
				$data = $row->current_week;
			}
			return $data;
		}
		else
		{
			return false;
		}
		
	}//get current week
	
//**********************************************************************	
	public function advance_week($league_id,$new_week)
	{
   	 	$this->db->set("current_week",$new_week)
				->where("league_id",$league_id)
				->update("Leagues");

		
		
	}
//********************************************************************************
	//returns the league's setting for nubmer of weeks a plyer has to stay on pup	
	public function get_league_weeks_on_pup($league_id){
		$this->db->select('weeks_on_pup');
		
		$this->db->where('league_id',$league_id);
		$this->db->limit(1);
		$query = $this->db->get('League_Settings');
		
		return $query->row('weeks_on_pup');	
	}
	
//*************************************************************************
	//get the current salary cap for a league
	public function get_league_salary_cap($league_id){
		$this->db->select('salary_cap');
		
		$this->db->where('league_id',$league_id);
		$this->db->limit(1);
		$query = $this->db->get('League_Settings');
		
		return $query->row('salary_cap');			
		
	}

//****************************************************************************	
	//get the first week of playoffs
	public function get_first_playoff_week($league_id){
		$this->db->select('first_playoff_week');
		
		$this->db->where('league_id',$league_id);
		$this->db->limit(1);
		$query = $this->db->get('League_Settings');
		
		return $query->row('first_playoff_week');			
		
	}

//*************************************************************************	
	//get the week used for trade deadline
	public function get_trade_deadline_week($league_id){
		$this->db->select('trade_deadline_week');
		
		$this->db->where('league_id',$league_id);
		$this->db->limit(1);
		$query = $this->db->get('League_Settings');
		
		return $query->row('trade_deadline_week');			
		
	}
	
//**************************************************************************
	//get the week in the current league settings of the superbowl
	public function get_superbowl_week($league_id){
		$this->db->select('superbowl_week');
		
		$this->db->where('league_id',$league_id);
		$this->db->limit(1);
		$query = $this->db->get('League_Settings');
		
		return $query->row('superbowl_week');			
		
	}
	

//**************************************************************************************
	
	public function get_league_calendar_dates($league_id){
		$this->db->where('league_id',$league_id);
		$query = $this->db->get('Calendar');
		//contstruct array with unix time as first key
		$calendar_array = array();
		foreach($query->result_array() as $event){
			$calendar_array[$event['time']]['long_name']= $event['long_name'];	
			$calendar_array[$event['time']]['short_name']= $event['short_name'];	
		}
		return $calendar_array;
	}
	
//*******************************************************************************************

	public function get_all_league_years($league_id = 1,$order='DESC'){
		$query = $this->db->select('year')
							->distinct()
							->where('league_id',$league_id)
							->order_by('year',$order)
							->get('Games');
		$return_array = array();
		
		foreach($query->result_array() as $year){
			$return_array[]=$year['year'];	
		}
		
		return $return_array;
	}
	
}//end model


/*End of file Players.php*/
/*Location: ./application/models/Players.php*/