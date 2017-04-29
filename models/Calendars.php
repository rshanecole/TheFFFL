<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Calendars Model.
	 *
	 * ?????
	 *		
	 */
	
Class Calendars extends CI_Model 
{

	
	public function __construct() 
    {
		parent::__construct();
		//$ci = get_instance();
		$this->load->helper('date');
		$this->load->model('Leagues');
		$this->load->model('NFL_Games');
		
		
	}
	
	
	//gets the time of an event by its id in the calendar table
	public function get_calendar_time($calendar_id,$league_id=1){
		$this->db->select('time');
		$this->db->where('calendar_id',$calendar_id);
		$this->db->where('league_id',$league_id);
		$query = $this->db->get('Calendar');
		$row = $query->row();

		return $row->time;
	}
//**************************************************************************	
	
	//gets the times of all drafts. returns [type]=>time
	public function get_draft_times($league_id,$year){
		$this->db->select('start_time, type');
		$this->db->where('year',$year);
		$this->db->where('league_id',$league_id);
		$this->db->order_by('start_time','ASC');
		$query = $this->db->get('Drafts');
		$return_array = array();
		foreach($query->result() as $row){
			$return_array[$row->type][]=$row->start_time;
		}
		
		return $return_array;
	}

//*********************************************************************
	public function get_next_event($league_id){
		
		$calendar_query = $this->db->where('time>'.now())
									->where('league_id',$league_id)
									->select('short_name,time')
									->order_by('time','ASC')
									->limit(1)
									->get('Calendar');
		foreach($calendar_query->result_array() as $event){
			return array('time'=>$event['time'],'short_name'=>$event['short_name']);
			
		}
		
	}
	
//*********************************************************************
	public function get_previous_event($league_id){
		
		$calendar_query = $this->db->where('time<'.now())
									->where('league_id',$league_id)
									->select('short_name,time')
									->order_by('time','DESC')
									->limit(1)
									->get('Calendar');
		foreach($calendar_query->result_array() as $event){
			return array('time'=>$event['time'],'short_name'=>$event['short_name']);
			
		}
		
	}

//*********************************************************************
	public function get_trade_deadline($league_id){
		
		//get the week of the trade deadline
		$week = $this->Leagues->get_trade_deadline_week($league_id);
		
		//get the sunday dadline for that week
		return $this->NFL_Games->get_week_sunday_deadline($week,$this->current_year);
		
	}

}//end model


/*End of file Database_Manager.php*/
/*Location: ./application/models/Database_Manager.php*/