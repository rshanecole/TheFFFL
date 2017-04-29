<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Free Agents Model.
	 *
	 * ?????
	 *		
	 */
	
Class Free_Agents extends CI_Model 
{
	public $league_id = 1; //***NI*** remove when multiple leagues are in place
	public $current_year;	
	public $current_week;
	public function __construct() 
    {
		parent::__construct();
		//$ci = get_instance();

		$this->load->helper('string');
		$this->load->model('Database_Manager');	
		$this->load->model('Players');
		$this->load->model('Salaries');
		$this->load->model('Rosters');
		$this->load->model('Leagues');
		
		$this->current_year = $this->Leagues->get_current_season($this->league_id);
		$this->current_week = $this->Leagues->get_current_week($this->league_id);	
			
		
	}
	
	//**********************************************************
	
	//takes the player id and determines if he is elegible for open free agency
	//and if he can be added now
	public function open_free_agency_open($fffl_player_id){
			
		//if week >0 and <14
		if( ($this->current_week>0 && $this->current_week<14) && ( (date('N',now())>5 || (date('N',now())==5 && date('G', now())>20 ) || (date('N',now())==1 && date('G', now())<19)) ) ){
			
			//check if player is locked
			if($this->Players->is_player_locked($fffl_player_id)){
					return FALSE;
			}
			else{
				//if the # of FA for the player minus the number of him released in the Friday FA draft is > 0
				$number_fa = $this->get_player_number_free_agents($fffl_player_id);
				$count_query = $this->db->where('released_fffl_player_id',$fffl_player_id)
										->where('week',$this->current_week)
										->where('day','Friday')
										->where('year',$this->current_year)
										->count_all_results('FA_drafts');
				if($number_fa-$count_query>0){
					
						return TRUE;
				}
				else{
					return FALSE;	
				}
				
			}
		}

		return FALSE;
		
	}
//*****************************************************************8

	//gets the results fora  freeagent draft
	public function get_fa_draft_results($year,$week,$day,$league_id=1){
		
		$query=$this->db->where('year',$year)
				->where('day',$day)
				->where('week',$week)
				->where('league_id',$league_id)
				->order_by('pick','ASC')
				->get('FA_drafts');
				$return_array=array();
		foreach($query->result_array() as $draft){
				$return_array[] = array('pick'=>$draft['pick'],'team_id'=>$draft['team_id'],'fffl_player_id'=>$draft['fffl_player_id'],'released_fffl_player_id'=>$draft['released_fffl_player_id']);
				
		}
		
		return $return_array;
		
	}

	
	//***************************************************************
	public function get_player_number_free_agents($fffl_player_id){
		$count_query = $this->db->where('fffl_player_id',$fffl_player_id)
							->count_all_results('Rosters');
		if($count_query<2){ $num_fa = 2-$count_query; } else { $num_fa=0; }
		return $num_fa;
		
	}
	
//*********************************************************************

	public function get_all_free_agents($league_id){
		$return_array = array();
		//get all players and check each one
		$all_players = $this->Players->get_all_player_ids("Players.current_team<>'RET' and Players.current_team<>'FA'","fffl_player_id","Players.first_name",'ASC',0);
		
		foreach($all_players['ids'] as $fffl_player_id){
			
			if($this->get_player_number_free_agents($fffl_player_id->fffl_player_id)>0){
				$return_array[]=$fffl_player_id->fffl_player_id;
			}
		}
		return $return_array;
		
	}
	

//**********************************************************************

	public function get_FA_draft_order($league_id){
		$query = $this->db->where("league_id",$league_id)
						->order_by('pick','ASC')
						->get('FA_Draft_Order');
						
		$return_array = array();
		foreach($query->result_array() as $data){
			$return_array[$data['pick']] = $data['team_id'];
				
		}
		
		return $return_array;
	}
	
//************************************************************************

	public function update_FA_draft_order($draft_order){
		
		$pick=1;
		foreach($draft_order as $team_id){
			$this->db->set('pick',$pick)
					->where('team_id',$team_id)
					->update('FA_Draft_Order');
			$pick++;
		}
		
	}

//*************************************************************************

	public function get_highest_list_priority($team_id){
		$max = $this->db->select_max('list_priority','max')
						->where('team_id',$team_id)
						->get('FA_Lists');
		foreach($max->result_array() as $data){
			return $data['max'];
			
		}
		
	}


//************************************************************************

	public function get_list_highest_priority($list_id){
		$max = $this->db->select_max('priority','max')
						->where('list_id',$list_id)
						->get('FA_Requests');
		foreach($max->result_array() as $data){
			return $data['max'];
			
		}
		
	}

//************************************************************************

	public function get_release_list_highest_priority($list_id){
		$max = $this->db->select_max('priority','max')
						->where('list_id',$list_id)
						->get('FA_Release');
		foreach($max->result_array() as $data){
			return $data['max'];
			
		}
		
	}


//**********************************************************************

	//an initial add of a player to the reqeusts not assigned to a list, 0
	public function add_player_request($team_id,$fffl_player_id){
		$this->db->set("team_id",$team_id)
				->set('fffl_player_id',$fffl_player_id)
				->set('list_id',0)
				->insert('FA_Requests');
	}
	
//**********************************************************************

	//remove player from teams watchlist, list_id 0 and all
	public function remove_player_request($team_id,$fffl_player_id){
		$this->db->where("team_id",$team_id)
				->where('fffl_player_id',$fffl_player_id)
				->delete('FA_Requests');
	}
	
//**********************************************************************

	//adds a player to a list
	public function add_player_list($team_id,$fffl_player_id,$list_id){
		$priority = $this->get_list_highest_priority($list_id)+1;
		$this->db->set("team_id",$team_id)
				->set('fffl_player_id',$fffl_player_id)
				->set('list_id',$list_id)
				->set('priority',$priority)
				->insert('FA_Requests');
	}
	
//**********************************************************************

	//adds a player to a release list
	public function add_release_player_list($team_id,$fffl_player_id,$list_id){
		$priority = $this->get_release_list_highest_priority($list_id)+1;
		$this->db->set("team_id",$team_id)
				->set('fffl_player_id',$fffl_player_id)
				->set('list_id',$list_id)
				->set('priority',$priority)
				->insert('FA_Release');
	}
	
//**********************************************************************

	//removes a player from a list
	public function remove_player_from_list($team_id,$fffl_player_id,$list_id){
		$this->db->where("team_id",$team_id)
				->where('fffl_player_id',$fffl_player_id)
				->where('list_id',$list_id)
				->delete('FA_Requests');
	}
	
//**********************************************************************

	//adds a teams list
	public function add_list($team_id){
		$priority = $this->get_highest_list_priority($team_id)+1;
		$this->db->set("team_id",$team_id)
				->set('list_priority',$priority)
				->insert('FA_Lists');
	}
	
//**********************************************************************

	//deletes a teams list
	public function delete_list($team_id,$list_id){
		$this->db->where("team_id",$team_id)
				->where('list_id',$list_id)
				->delete('FA_Lists');
	}


//**********************************************************************

	public function get_team_lists($team_id){
		$query = $this->db->where("team_id",$team_id)
						->order_by('list_priority','ASC')
						->get('FA_Lists');
						
		$return_array = array();
		foreach($query->result_array() as $data){
			$return_array[$data['list_priority']]=$data;
				
		}
		
		return $return_array;
	}
	
//**********************************************************************
	//returns array of all players a team has on request
	public function get_team_distinct_requests($team_id){
		$query = $this->db->from('FA_Requests')
							->join('Players','FA_Requests.fffl_player_id = Players.fffl_player_id')
							->where('FA_Requests.team_id',$team_id)
							->where('FA_Requests.list_id',0)
							->order_by('Players.last_name','ASC')
							->get();
		$return_array=array();
		foreach($query->result_array() as $row){
			$return_array[]=$row['fffl_player_id'];	
		}
		return $return_array;	
	}
	
//**********************************************************************

	public function reorder_lists($team_id,$list_order){
		$i=1;
		foreach($list_order as $list_id){
			$this->db->set('list_priority',$i)
					->where('team_id',$team_id)
					->where('list_id',$list_id)
					->update('FA_Lists');	
			$i++;
			
		}	
		
	}
	
//************************************************************************

	public function activate_list($list_id,$team_id){
		$this->db->set('is_submitted',1)
				->where('list_id',$list_id)
				->where('team_id',$team_id)
				->update('FA_Lists');
		
	}
	
//************************************************************************

	public function deactivate_list($list_id,$team_id){
		$this->db->set('is_submitted',0)
				->where('list_id',$list_id)
				->where('team_id',$team_id)
				->update('FA_Lists');
	}
	
//****************************************************************************

	public function deactivate_all_lists(){
		$this->db->set('is_submitted',0)
				->update('FA_Lists');
		
	}


//**************************************************************************

	public function update_number_desired($team_id,$list_id,$number){
		$this->db->set('number_desired',$number)
				->where('team_id',$team_id)
				->where('list_id',$list_id)
				->update('FA_Lists');	
	}

//**************************************************************************

	public function get_list_player_data($team_id,$list_id){
		$query = $this->db->where('list_id',$list_id)
							->where('team_id',$team_id)
							->order_by('priority','ASC')
							->get('FA_Requests');
		$return_array=array();
		foreach($query->result_array() as $data){
			$return_array[$data['priority']]=$data['fffl_player_id'];	
			
		}
		return $return_array;
		
	}
	
//**************************************************************************

	public function get_list_release_player_data($team_id,$list_id){
		$query = $this->db->where('list_id',$list_id)
							->where('team_id',$team_id)
							->order_by('priority','ASC')
							->get('FA_Release');
		$return_array=array();
		foreach($query->result_array() as $data){
			$return_array[$data['priority']]=$data['fffl_player_id'];	
			
		}
		return $return_array;
		
	}
	
//***************************************************************************

	public function update_list_player_priorty($team_id,$list_id,$list){
		$p = 1;
	
		foreach($list as $fffl_player_id){
			$this->db->set('priority',$p)
					->where('fffl_player_id',$fffl_player_id)
					->where('team_id',$team_id)
					->where('list_id',$list_id)
					->update('FA_Requests');
			$p++;
		}
	
	}
	
//***************************************************************************

	public function update_list_release_priorty($team_id,$list_id,$list){
		$p = 1;
	
		foreach($list as $fffl_player_id){
			$this->db->set('priority',$p)
					->where('fffl_player_id',$fffl_player_id)
					->where('team_id',$team_id)
					->where('list_id',$list_id)
					->update('FA_Release');
			$p++;
		}
	
	}
	
//**********************************************************************

	//removes a player from a list
	public function remove_player_from_release_list($team_id,$fffl_player_id,$list_id){
		$this->db->where("team_id",$team_id)
				->where('fffl_player_id',$fffl_player_id)
				->where('list_id',$list_id)
				->delete('FA_Release');
	}
	
//*************************************************************************
	
	public function get_team_submitted_lists($team_id){
		$query = $this->db->where('team_id',$team_id)
							->where('is_submitted',1)
							->where('number_desired>"0"')
							->order_by('list_priority','ASC')
							->get('FA_Lists');
		$return_array = array();
		foreach($query->result_array() as $list_data){
			$return_array[$list_data['list_id']]=$list_data['number_desired'];
		}
		return $return_array;
	}

//********************************************************************************

	public function make_draft_pick($pick_data,$team_id){
		//insert player into roster 
		$this->Rosters->add_fa_to_roster($team_id,$pick_data['pick_player']);
		
		//remove player from this team's request lists
		$this->remove_player_request($team_id,$pick_data['pick_player']);			
										
		//reduce number desired for this list by 1
		$number = $pick_data['number_desired']-1;
		$this->update_number_desired($team_id,$pick_data['list_id'],$number);
						
		//release player
		if($pick_data['release_player']>0){
			$this->Rosters->release_player($team_id,$pick_data['release_player']);
	
			//set fa salary for released player
			$this->Salaries->set_free_agent_salary($pick_data['release_player'],0);
							
			//remove release player from all fa_release for this team
			$this->db->where("team_id",$team_id)
					->where('fffl_player_id',$pick_data['release_player'])
					->delete('FA_Release');	
		}
					
		//insert pick into fa_drafts
		$this->db->set("year",$this->current_year)
				->set('week',$this->current_week)
				->set("day", date("l",now()))
				->set("league_id",$this->league_id)
				->set('pick',$pick_data['pick'])
				->set("team_id", $team_id)
				->set('fffl_player_id',$pick_data['pick_player'])
				->set('released_fffl_player_id',$pick_data['release_player'])
				->insert('FA_drafts');
						
		//how many fa are still available for this player
		//if no fa left for player, delete him from all fa requests
		if($this->get_player_number_free_agents($pick_data['pick_player'])==0){
			$this->db->where('fffl_player_id',$pick_data['pick_player'])
				->delete('FA_Requests');
		}
							
		//set all transactions for a this team can undo to 0
		$this->db->set('can_undo',0)
				->where('team_id',$team_id)
				->update('Transactions');
		
	}

//********************************************************************************

	public function add_open_fa($fffl_player_id,$team_id){
		$this->load->Model('Facebook_Interact');	
		//insert player into roster 
		$this->Rosters->add_fa_to_roster($team_id,$fffl_player_id);
		
		//remove player from this team's request lists
		$this->remove_player_request($team_id,$fffl_player_id);			
						
		//how many fa are still available for this player
		//if no fa left for player, delete him from all fa requests
		if($this->get_player_number_free_agents($fffl_player_id)==0){
			$this->db->where('fffl_player_id',$fffl_player_id)
				->delete('FA_Requests');
		}
		
		//move team to bottom of FA draft order
		$draft_order = $this->get_FA_draft_order($this->league_id);
		foreach($draft_order as $key => $order_team_id){
			if($order_team_id==$team_id){
				unset($draft_order[$key]);
			}
		
		}
		$draft_order[]=$team_id;
		$this->update_FA_draft_order($draft_order);

		//create string of transaction and send to facebook
		$facebook_message = 'Free Agent Signing: '.team_name_no_link($team_id).' signed '.player_name_no_link($fffl_player_id);
		$facebook = $this->Facebook_Interact->post_to_facebook($facebook_message,$league_id);
		
	}



}
/*End of file Free_Agents.php*/
/*Location: ./application/models/Free_Agents.php*/