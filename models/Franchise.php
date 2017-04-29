<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Franchise Model.
	 *
	 * ?????
	 *		
	 */
	
Class Franchise extends CI_Model 
{
	public function __construct() 
    {
		parent::__construct();
		//$ci = get_instance();

		$this->load->helper('string');
		$this->load->model('Database_Manager');	
		$this->load->model('Players');	
		$this->load->model('Salaries');	
		
	}


//******************************************************************	
	//gets the franchise players for a team
	//for a single year or all years
	//array has format [season]=>[area (roster,pup,ps)]=>[position]=>[player_id]=>salary
	public function get_all_franchise_by_year($team_id,$year='all')	 {
		
		if($year!='all'){
			$this->db->where('season',$year);
		} 
		$this->db->where('team_id',$team_id);
		$query = $this->db->get('Franchise');
		
		$return_array = array();
		foreach ($query->result() as $row)
		{
				$position = $this->Players->get_player_info(array($row->fffl_player_id),"fffl_player_id","first_name last_name position");
				$return_array[$row->season][$row->area][$position['position']][$row->fffl_player_id]['salary']=$row->salary;
				$return_array[$row->season][$row->area][$position['position']][$row->fffl_player_id]['name']=$position['first_name'].' '.$position['last_name'];
				
				
		}
		if(count($return_array)>0){
			return $return_array;
		
		}
		else {
			return false;
		}
		
	}

//**************************************************************************	
	//gets the franchise players for a team in simple array
	//for a single year 
	//array has jsut fffl_player_ids
	public function get_simple_franchise_by_year($team_id,$year){
		
		
		$this->db->where('season',$year);
		$this->db->where('team_id',$team_id);
		$query = $this->db->get('Franchise');
		
		$return_array = array();
		foreach ($query->result() as $row)
		{
				$return_array[] = $row->fffl_player_id;
		}

			return $return_array;

	}


//*************************************************************************************	
	//this specifically returns an array of just the players who are NOT currently
	//selected as a franchise player for a given team. Only has one use and that's the franchise
	//selection modal.
	public function get_non_franchise_players($team_id,$current_year){
			$query= $this->db->select('fffl_player_id')
							->where('season',$current_year)
							->where('team_id',$team_id)
							->get('Franchise');
			$franchise_array = array();
			
			foreach ($query->result() as $row){
				$franchise_array[]=$row->fffl_player_id;
			}
			
			$query2= $this->db->select('fffl_player_id')
							->where('team_id',$team_id)
							->order_by('lineup_area','DESC')
							->order_by('salary','DESC')
							->get('Rosters');
			$return_array = array();
			foreach ($query2->result() as $row){
				if(!in_array($row->fffl_player_id,$franchise_array)){
					$return_array[]=$row->fffl_player_id;
				}
			}
			
			return $return_array;
			
	}

//**********************************************************************************	
	//adds a player to the franchise list for a team
	public function insert_franchise_player($team_id,$fffl_player_id,$year){
		//check to make sure he's not already franchise for that team, just to be sure
		$query = $this->db->where('team_id',$team_id)
						->where('fffl_player_id',$fffl_player_id)
						->where('season',$this->current_year)
						->get('Franchise');
		$message = '';
		//check to make sure salary is under the limit
		$player_salary = $this->Salaries->get_player_team_salary($team_id,$fffl_player_id);
		$total_salary_with = $this->get_team_franchise_salary($team_id,$year) + $player_salary;
		$under_cap = $this->Leagues->get_league_salary_cap($this->Teams->get_team_league_id($team_id)) - $total_salary_with;
		$player_name = $this->Players->get_player_info(array($fffl_player_id),'fffl_player_id','first_name last_name');
		//if he's not there and he won't exceed the cap, insert him
		if($under_cap>=0){
			if($query->num_rows()==0){
			$this->db->set('team_id',$team_id)
					->set('fffl_player_id',$fffl_player_id)
					->set('salary',$player_salary)
					->set('season',$year)
					->set('area', $this->Rosters_View->get_team_player_area($team_id,$fffl_player_id))
					->insert('Franchise');
			$message .= $player_name['first_name'].' '.$player_name['last_name'].' - Success';
			
			}//already there
			else{
				$message .= 'There was an error. Please refresh the page. If it continues, contact me.';
				
			}
		}//under cap
		else {
			
			$message .= $player_name['first_name'].' '.$player_name['last_name'].' not added. Would exceed salary cap.';
			
		}
		
		return $message;
	}//end insert franchise player

//*****************************************************************************************	
	//removes a player from the franchise list for a team
	public function remove_franchise_player($team_id,$fffl_player_id,$year){

			$this->db->where('team_id',$team_id)
					->where('fffl_player_id',$fffl_player_id)
					->where('season',$year)
					->delete('Franchise');
	}


//**********************************************************************
	//gets teh salary that a team kept a player at for a given year	
	public function get_team_franchise_salary($team_id, $year){
		//get all the franchise players
		$all_franchise_array = $this->get_all_franchise_by_year($team_id,$year);
		//[season]=>[area (roster,pup,ps)]=>[position]=>[player_id]=>salary
		$total_salary = 0;
		if(isset($all_franchise_array[$year])){
			foreach($all_franchise_array[$year] as $area){
				foreach($area as $position){
					foreach($position as $fffl_player_id){
						foreach($fffl_player_id as $salary){
							$total_salary = $total_salary + $salary;	
						}
					}
				}
			}
		}
		return $total_salary;
	}
  
  
  //*************************************************************
  //gets the years and teams for the franchise history of a player
  public function get_player_franchise($fffl_player_id,$league_id){
    $this->db->where('fffl_player_id',$fffl_player_id)
     							->order_by('season','Desc');
    if($this->current_week==0){
      
      $this->db->where('season<'.$this->current_year);
    }
      							$franchise_query=$this->db->get('Franchise');
    $franchise=array();
    foreach($franchise_query->result_array() as $data){
      	$franchise[$data['season']][$data['team_id']]['salary']=$data['salary'];
      	$franchise[$data['season']][$data['team_id']]['area']=$data['area'];
      
      }
    return $franchise;
  }
    
    
  //**************************************************************
  
  //gets the players that are frnachised for a year. 
  //specify if 1 or 2 franchise
  public function get_franchise_players($league_id,$season,$num_franchise=2){
	  
	  $franchise_query = $this->db->query('SELECT DISTINCT fffl_player_id FROM Franchise  WHERE season = '.$season.' GROUP BY fffl_player_id HAVING COUNT(*)='.$num_franchise);
	  foreach($franchise_query->result_array() as $player){
		 
	  	$franchise_array[]=$player['fffl_player_id'];
	  }
	  return $franchise_array;
	  
  }
    
  //************************************************************
  //drops from rosters the players who are not designated as franchise players
  public function drop_non_franchise($league_id){
	 $this->Database_Manager->database_backup(array('Transactions','Rosters','Franchise','Players','Starting_Lineups'));
	 $year=$this->current_year;
	 //get all teams
	 $all_teams = $this->Teams->get_all_team_id($league_id);
	 
	 //foreach team get the franchise players in array form
	 foreach($all_teams as $team_id){
	 	$franchise_array = $this->get_simple_franchise_by_year($team_id,$year);
		
		//foreach roster player if not in franchise array , delete from rosters
		$roster_array = $this->Rosters->get_team_complete_roster($team_id);
		foreach($roster_array as $fffl_player_id){
			if(!in_array($fffl_player_id,$franchise_array)){
				//delete from roster
				$this->Rosters->release_player($team_id,$fffl_player_id);
			}
		}
	 }
	 
	 //move teh PS players to the roster
	 $this->db->where('lineup_area','PS')
	 			->set('lineup_area','Roster')
				->update('Rosters');
	 
	 //create JSON files of the draftable and supplemental players
		//get all players and check each one
		$all_players = $this->Players->get_all_player_ids("Players.current_team<>'RET' and Players.current_team<>'FA'","fffl_player_id","Players.last_name",'ASC',0);
		$draftable = array();
		$supplemental_eligible=array();
		foreach($all_players['ids'] as $fffl_player_id){
			$player_info=$this->Players->get_player_info(array($fffl_player_id->fffl_player_id),'fffl_player_id','position');

			if($this->Free_Agents->get_player_number_free_agents($fffl_player_id->fffl_player_id)==2){
				$draftable[$fffl_player_id->fffl_player_id]=$player_info['position'];
			}
			elseif($this->Free_Agents->get_player_number_free_agents($fffl_player_id->fffl_player_id)==1){
				$supplemental_eligible[]=$fffl_player_id->fffl_player_id;
			}
		}
		$fp = fopen('/home1/theffflc/public_html/fantasy/assets/json/draftable.json', 'w');
		fwrite($fp, json_encode(array('draftable'=>$draftable)));
		$fp = fopen('/home1/theffflc/public_html/fantasy/assets/json/supplemental_eligible.json', 'w');
		fwrite($fp, json_encode(array('supplemental'=>$supplemental_eligible)));
	 
	 
	 //reset all free agent salaries
	 $this->Salaries->reset_all_free_agent_salaries();
  }
    
    
}
/*End of file Rosters.php*/
/*Location: ./application/models/Teams.php*/