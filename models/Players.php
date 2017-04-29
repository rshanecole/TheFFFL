<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Players Model.
	 *
	 * ?????
	 *		
	 */
	
Class Players extends CI_Model 
{
	public function __construct() 
    {
		$this->load->helper('string');
		$this->load->helper('links');
		$this->NFL_db = $this->load->database('NFL',true);
		$this->load->model('NFL_Teams');
	
		parent::__construct();
		//$ci = get_instance();

		
	}
	
//*****************************************************************

	//simply returns true if the player is in the database and false if it is not
	//checks to see if player exists in database
	public function is_in_database($id) {
		$this->db->from('Players');
		$this->db->where('nfl_player_id',$id);
		$query = $this->db->get();
		$row_count = $query->num_rows();
		if($row_count>0) {
			return true;
		} else {
			return false;	
		}
			
	}
	
//*******************************************************************************
	
	/*Allows for any format of player name. Required player id of any type, fffl_id or any nfl id, 
		but id type must be specified if not fffl_player_id.
		exclusions string is the criteria not to include a plyer ** not yet implemented. Add when needed
	*/
	public function get_player_info($player_id_array,$id_type="fffl_player_id",$elements_string="first_name last_name",$exclusions_string="")
	{
      /*formats
      first_name
      last_name
      current_team
      position
      is_rookie 0,1
      is_injured 0,1 
			nfl_injury_game_status
			injury_text
			nfl_status
			nfl_esbid
			
      */
		$player_info_array = array();
		$count = count($player_id_array);
		foreach($player_id_array as $player_id) 
		{
			
			$element_array = explode(" ",$elements_string);
			$this->db->select("*");
			$this->db->from('Players');
			$this->db->where($id_type,$player_id);
			
			$this->db->limit(1);
			$query = $this->db->get();
			foreach($element_array as $element) 
			{
				if ($query->num_rows() === 1) 
				{
					if($count>1){ $player_info_array[$player_id][$element] = $query->row($element); }
					else { $player_info_array[$element] = $query->row($element); }
				}
				
			}
		}
		
		return $player_info_array;
	}


//*********************************************************************************
	
	public function convert_player_id($player_id, $id_type, $convert_to_id_type)
	{
		$this->db->select($convert_to_id_type);
		$this->db->from('Players');
		$this->db->where($id_type,$player_id);
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			foreach($query->result_array() as $row)
			{
				$data = $row[$convert_to_id_type];
			}
			return $data;
		}
		else
		{
			return false;
		}

	}
	
//*******************************************************************************
	//place a # between each exclusion , do the full sql exclusion
	public function get_all_player_ids($exclusions_string="",$id_type_return="fffl_player_id",$order_by="Players.last_name",$direction='ASC',$join_rosters=0)
	{
		
		$this->db->select('Players.'.$id_type_return);
		
		if($exclusions_string<>"")
		{
			$exclusions_array= array();
			$exclusions_array = explode("#",$exclusions_string);
			foreach($exclusions_array as $exclusion)
			{
				$this->db->where($exclusion);
			}
		}
		if($join_rosters==1){
			$this->db->join('Rosters','Players.fffl_player_id=Rosters.fffl_player_id');
		}
		$this->db->order_by($order_by,$direction);
      	$count = $this->db->count_all_results('Players', FALSE);

		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$data[] = $row;
			}
			return $return = array("ids"=>$data,"count"=>$count);
		}
		else
		{
			return false;
		}
		
	}
	
//*******************************************************************************

	public function get_all_player_ids_no_objects($exclusions_string="",$id_type_return="fffl_player_id",$order_by="Players.last_name",$direction='ASC',$join_rosters=0)
	{
		
		$this->db->select('Players.'.$id_type_return);
		
		if($exclusions_string<>"")
		{
			$exclusions_array= array();
			$exclusions_array = explode("#",$exclusions_string);
			foreach($exclusions_array as $exclusion)
			{
				$this->db->where($exclusion);
			}
		}
		if($join_rosters==1){
			$this->db->join('Rosters','Players.fffl_player_id=Rosters.fffl_player_id');
		}
		$this->db->order_by($order_by,$direction);
      	$count = $this->db->count_all_results('Players', FALSE);

		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$data[] = $row->fffl_player_id;
			}
			return $return = array("ids"=>$data,"count"=>$count);
		}
		else
		{
			return false;
		}
		
	}
//**************************************************************

	public function get_player_owners($player_id, $id_type="fffl_player_id")
	{
		if($id_type<>'fffl_player_id')
		{
			$player_id = $this->convert_player_id($player_id,$id_type,'fffl_player_id');
		}
		
		$this->db->select('team_id');
		$this->db->from('Rosters');
		$this->db->where('fffl_player_id='.$player_id);
		$this->db->limit(2);
		$query=$this->db->get();
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$data[] = $row;
			}
			return $data;
		}
		else
		{
			return false;
		}
		
	}


//*********************************************************
	
	public function get_player_salaries($player_id, $id_type="fffl_player_id")
	{
		if($id_type<>'fffl_player_id')
		{
			$player_id = $this->convert_player_id($player_id,$id_type,'fffl_player_id');
		}
		
		$this->db->select('team_id, salary');
		$this->db->from('Rosters');
		$this->db->where('fffl_player_id='.$player_id);
		$this->db->limit(2);
		$query=$this->db->get();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$data[$row->team_id] = $row->salary;
			}
			
		}
		
		if($query->num_rows()<2)
		{
			$this->db->select('fa_salary');
			$this->db->from('Players');
			$this->db->where('fffl_player_id='.$player_id);
			$this->db->limit(1);
			$query=$this->db->get();
			if($query->num_rows() > 0)
			{
				foreach($query->result() as $row)
				{
					$data['fa_salary'] = $row->fa_salary;
				}
				
			}
		}
		return $data;
	}//get Player salaries
	
//********************************************************
	//returns if player is currently locked
	public function is_player_locked($fffl_player_id){
		$query = $this->db->where('fffl_player_id',$fffl_player_id)
						->from('Locked_Players')
						->count_all_results();
		
		return $query;
		
	}

//**********************************************************

	//locks the players for two teams if not already locked
	public function lock_players($home_team,$away_team,$year,$week){
		
		$this->NFL_db = $this->load->database('NFL',true);
		
		foreach(array($home_team,$away_team) as $team){
			if($team==$home_team){$opponent = $away_team; } else { $opponent=$home_team; }
			
			$players= $this->get_all_player_ids_no_objects("Players.current_team='".$team."'","fffl_player_id","Players.last_name",'ASC',0);
			
			foreach($players['ids'] as $fffl_player_id){
				if($this->is_player_locked($fffl_player_id)==0){
					$this->db->set('fffl_player_id',$fffl_player_id)
						->insert('Locked_Players');
	
					//add to the stats table
					$this->NFL_stats->add_player_stats_table($fffl_player_id,$week,$year,$team,$opponent);
					
				}
			}
		}
	}
//********************************************************
	//removes all locked players
	public function remove_player_locks(){
		
		$this->db->where('fffl_player_id>0')
				->delete('Locked_Players');
		
	}
	

//***********************************************************

	//gets the game status for a specific player
	public function get_player_game_status($fffl_player_id,$year=NULL, $week=NULL){
		if($week==NULL) { $week=$this->current_week; }
		if($year==NULL) { $year=$this->current_year; }
		if($year==$this->current_year){ 
			$team_array = $this->get_player_info(array($fffl_player_id),"fffl_player_id","current_team");
			$team = $team_array['current_team'];
		}
		else{
			$team_query = $this->NFL_db->where('week',$week)
										->where('season',$year)
										->where('fffl_player_id',$fffl_player_id)
										->limit(1)
										->get('NFL_stats_'.$year);
			foreach($team_query->result_array() as $row){	
				$team=$row['player_team'];
			}
			
			if(!isset($team)){		
			
				$team_query = $this->NFL_db->where('season',$year)
								->where('fffl_player_id',$fffl_player_id)
								->limit(1)
								->get('NFL_stats_'.$year);
				foreach($team_query->result_array() as $row){
					$team=$row['player_team'];
					
				}
				
			}
			
			
		}
		
		$team_query = $this->get_player_info(array($fffl_player_id),"fffl_player_id","current_team");
		$game_status = $this->NFL_Teams->get_game_status($team,$week,$year);
		return $game_status;
	}
	
	
//***********************************************************
	public function get_player_transactions($fffl_player_id,$league_id){
		
		$transactions_query = $this->db->where('fffl_player_id',$fffl_player_id)
				->where('league_id',$league_id)
				->order_by('time','DESC')
				->select('team_id,transaction_type,time')
				->get('Transactions');
		$transactions = $transactions_query->result_array();
		
		return $transactions;
			
		
		
	}
	
//***********************************************************
	public function get_times_all_pro($fffl_player_id,$league_id=1){
		
		$all_pro_count = $this->db->from('Probowl')
				->where('team_id',0)
				->group_start()
					->or_where('player_1',$fffl_player_id)
					->or_where('player_2',$fffl_player_id)
					->or_where('player_3',$fffl_player_id)
					->or_where('player_4',$fffl_player_id)
					->or_where('player_5',$fffl_player_id)
					->or_where('player_6',$fffl_player_id)
					->or_where('player_7',$fffl_player_id)
					->or_where('player_8',$fffl_player_id)
				->group_end()	
				->count_all_results();
	
		
		return $all_pro_count;
			
		
		
	}

//****************************************************************************

	public function get_player_headlines($fffl_player_id,$num_of_items){
		$player_name = player_name_no_link($fffl_player_id,FALSE,FALSE);
		$headlines_query=$this->db->or_like('title',$player_name)
					->or_like('description',$player_name)
          			->limit($num_of_items)
					->order_by('date','DESC')
          			->order_by('source','ASC')
					->get('RSS');
      	$headlines=array();
		foreach($headlines_query->result_array() as $item){
			$headlines[] = array(
				'title' => $item['title'],
				'description' => $item['description'],
				'date' => $item['date'],
				'link' => $item['link'],
				'source' => $item['source'],
			);
          
		}
		return $headlines;
			
		
	}



	
}//end controller


/*End of file Players.php*/
/*Location: ./application/models/Players.php*/