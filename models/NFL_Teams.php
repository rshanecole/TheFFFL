<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * NFL_Teams Model.
	 *
	 * ?????
	 *		
	 */
	
Class NFL_Teams extends CI_Model 
{
	public function __construct() 
   {
		parent::__construct();
		//$ci = get_instance();
		$this->NFL_db = $this->load->database('NFL',true);
		$this->load->helper('string');
	}
	
//*******************************************************************

	public function get_team_bye_week($team_abbr)
	{
		
		$this->NFL_db->select('bye_week');
		$this->NFL_db->from('NFL_Teams');
		$this->NFL_db->where("`team_abbr`='".$team_abbr."'");
		$this->NFL_db->limit(1);
		$query = $this->NFL_db->get();
		if ($query->num_rows() === 1) 
		{
			return $query->row('bye_week');
		}
		else 
		{
			return NULL;
		}
	}

//******************************************************************

	public function get_game_status($team,$week,$year){
		
		if($week==0){$week=1;}
		$query = $this->NFL_db->where('week',$week)
					->group_start()
						->or_where('home_team',$team)
						->or_where('away_team',$team)
					->group_end()
					->select('start_time,status,status_flag,home_team,away_team')
					->get('NFL_Schedule_'.$year);
		
		$status = $query->result_array();
		if(!empty($status)){
			if($status['0']['status_flag']==0){//future game
				if($status['0']['home_team'] == $team) {	$return = $status['0']['away_team']; }
				else { $return = '@'.$status['0']['home_team']; }
				$return .= ' '.date('D g:ia',$status['0']['start_time']);
			}
			else {
				if($status['0']['home_team'] == $team) {	$return = $status['0']['away_team']; }
				else { $return = '@'.$status['0']['home_team']; }
				$return .= ' '.$status['0']['status'];	
			}
        }
      	else{
        	$return='';
        }
		
		return $return;
	}

//******************************************************************

	public function get_team_kickoff($team,$week,$year){
		
		if($week==0){$week=1;}
		$query = $this->NFL_db->where('week',$week)
					->group_start()
						->or_where('home_team',$team)
						->or_where('away_team',$team)
					->group_end()
					->select('start_time')
					->get('NFL_Schedule_'.$year);
		
		$status = $query->result_array();
		if(!empty($status)){
			$return= $status['0']['start_time'];
		}
      	else{
        	$return='';
        }
		
		return $return;
	}



//*********************************************************************
	
	public function get_all_nfl_teams(){
		
		$query = $this->NFL_db->select('team_abbr')
					->where("conference<>''")
					->order_by('team_abbr','ASC')
					->get('NFL_Teams');
		foreach($query->result_array() as $row){
			$nfl_teams[] = $row['team_abbr'];	
		}
		
		
		return $nfl_teams;
	}
	
//********************************************************************

	public function get_team_opponent($team,$week,$year){
		$query = $this->NFL_db->where('week',$week)
					->group_start()
						->or_where('home_team',$team)
						->or_where('away_team',$team)
					->group_end()
					->select('home_team,away_team')
					->get('NFL_Schedule_'.$year);
		foreach($query->result_array() as $row){
			if($row['home_team']==$team){ return $row['away_team']; }
			elseif($row['away_team']==$team){ return $row['home_team']; }
		}
	}
	
}


/*End of file Teams.php*/
/*Location: ./application/models/Teams.php*/