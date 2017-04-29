<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Rosters Model.
	 *
	 * ?????
	 *		
	 */
	
Class Rosters extends CI_Model 
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
		$this->load->model('Calendars');	
		$this->load->model('Salaries');	
		$this->load->model('Trades');
		$this->load->model('Leagues');
		$this->load->model('Teams');
		
		$this->current_year = $this->Leagues->get_current_season($this->league_id);
		$this->current_week = $this->Leagues->get_current_week($this->league_id);	
		
	}


//************************************************************************
	
	//validates a starting lineup and returns an array of the positions that are still open in the starting lineup
	//valid lineup is ['valid']= TRUE
	//open positiosn is ['open_positions'] = array('QB','RB');
	//$starting_lineup_array must have individual position as value
	public function validate_starting_lineup($starting_lineup_array,$league_id) {
		$return_value=array();
		$valid_lineup_string = $this->get_league_lineup($league_id);
		
		//breakup the starting lineup string into an array
		$valid_lineup = explode(',',$valid_lineup_string);
		
		//maximum starting players used later to fill in empty positions in starting lienup
		$max_number_of_positions = count($valid_lineup);
		
		//in valid_lineup_array each position slot is an array of the valid positions for that slot
		//simply breaking the flex positions into an array creating an multi dimensional array 
		//for each of the starting positions
		$valid_lineup_array=array();
		foreach($valid_lineup as $position) {
			$valid_lineup_array[]=explode('/',$position);
		}
		
		//a lineup will be validated by getting each possible combination
		//of valid starting lineups, and each possible permutation of the 
		//submitted starting lineup with Xs to fill empty spots, and then 
		//checking to see if the two arrays intersect at all.  
		
		//get the number of players in the submitted lineup then add Xs
		//for each missing position
		$number_players = count($starting_lineup_array);
		while($number_players<$max_number_of_positions){
			$starting_lineup_array[]='X';
			$number_players++;
		}
		
		//the first array is every possible valid lineup based on
		//every combo of the positions in the valid lineup taking one position
		//from each of the flex spots. Xs representing empty spots must be included in the string submitted
		//to this function so that empty positions can be valid.
		//Each value is a string of positions separated by a comma
		$cartisian_product = cartesian_product($valid_lineup_array);
		$validation = array();
		foreach($cartisian_product as $combo){
			$string='';
			foreach($combo as $position){
				$string .=$position.',';
			}
			$validation[]=$string;
		}
		
		//the second array is the permutations of the submitted starting lineup
		//each value in the array is a string of positions separated by a comma
		$permutations = permutations($starting_lineup_array);
		foreach($permutations as $perm){
			$string='';
			foreach($perm as $position){
				$string .=$position.',';
			}
			$starting_lineup_permutations[]=$string;
		}
		
		//now see if there are any common values between the two arrays
		//also eliminating any duplicate mathes.  The array $intersect 
		//now includes all the possible valid combinations of the submitted
		//lineup.  
		$intersection = array_unique(array_intersect($starting_lineup_permutations,$validation));
		
		//if there is at least 1 intersection, it's a valid lineup
		if(!empty($intersection)){
			//indicates a valid lineup
			$return_array['valid']=TRUE;
			$open_positions_array = array();
			//get possible players for open positions
			foreach($intersection as $value){
				$valid_lineup=explode(',',$value);
				foreach($valid_lineup as $key => $position){
					if($position=="X"){
						foreach($valid_lineup_array[$key] as $open_pos){
							if($open_pos != 'X'){
								$open_positions_array[] = $open_pos;
							}
						}
					}
				}
			}
			$open_positions_array = array_unique($open_positions_array);
			//set it to empty if the lineup is full.
			if(empty($open_positions_array)){
				$open_positions_array=array();
			}
			$return_array['open_positions']=$open_positions_array;	
		}
		//not a valid lineup. Return FALSE
		else{
			$return_array['valid']=FALSE;
			$return_array['open_positions']=array();
		}
		
		return $return_array;
		
	}//validate_starting_lineup
	

//**************************************************************************
			
	//resets all players to bench except pup and ps for a team and week
	public function reset_starting_lineup($team_id,$year,$week){
		//$this->Database_Manager->database_backup(array('Rosters','Starting_Lineups'));
		
		if($week===0){$week=1;}
		
		//delete entries in starting_lineups table
		
		//NEED TO NOT REMOVE LOCKED PLAYERS
		if($week==$this->current_week){
			//get array of locked players
			$locked_query = $this->db->get('Locked_Players');
			$locked_players_array = $locked_query->result_array();
			$starters_array = $this->get_team_starters($team_id, $week, $year);
			foreach($starters_array as $fffl_player_id){
				if(!in_array($fffl_player_id,$locked_players_array)){
					$conditions = "team_id =".$team_id." and week = ".$week." and year=".$year." and fffl_player_id=".$fffl_player_id;
					$this->db->where($conditions);
					$this->db->delete('Starting_Lineups');	
				}
			}
		}
		else{
			$conditions = "team_id =".$team_id." and week = ".$week." and year=".$year;
			$this->db->where($conditions);
			$this->db->delete('Starting_Lineups');
		}
		
	}

//****************************************************************
	public function update_roster($team_id,$year,$week,$starting_lineup_array){
		//need to get positions of the players
		$validation_array=array();
		foreach($starting_lineup_array as $player){
			$position = $this->Players->get_player_info(array($player),"fffl_player_id","position");
			$validation_array[]=$position['position'];
			
		}
		$validation = $this->Rosters->validate_starting_lineup($validation_array,$this->league_id);
		
		if($validation['valid']==TRUE){
			
			//update the starting lineup
			//if a locked player is to be moved, then it returns FALSE
			//send it back to roster method to check the lineup and decide what to do
			$update_success=$this->Rosters->update_starting_lineup($team_id,$year,$week,$starting_lineup_array);
			
			if($update_success==FALSE){
				$validation=array();	

			}
		}
		else {
			//do not reset the lineup, just send back to view without changes
			//and empty validation so that it will re-check the lineup
			//and decide what needs to be done
			$validation = array();
		}
		
		return $validation;
	}

//****************************************************************
	//$week is the week of the playoff game
	public function set_playoff_lineups($league_id,$year,$week){
		
		//get teams in the playoffs and update rosters
		$team_query = $this->db->where("league_id",$league_id)
							->where("year",$year)
							->where("week",$week)
							->select("opponent_a,opponent_b")
							->get("Games");
		$teams=array();
		foreach($team_query->result_array() as $game_info){
			$teams[] = $game_info['opponent_a'];
			$teams[] = $game_info['opponent_b'];	
		}
		
		//get teams in the tb  and update rosters
		$team_query = $this->db->where("league_id",$league_id)
							->where("year",$year)
							->where("week",$week)
							->select("opponent")
							->get("Toilet_Bowls");
		
		foreach($team_query->result_array() as $game_info){
			$teams[] = $game_info['opponent'];
				
		}
		
		//set rostes
		foreach($teams as $team_id){
			$next_starters = $this->Rosters->get_team_starters($team_id, $week, $year);
			if(empty($next_starters)){
				$current_starters=$this->Rosters->get_team_starters($team_id,($week-1), $year);
				$this->Rosters->update_starting_lineup($team_id,$year,$week,$current_starters);
			}
			
		}	
		
		
	}



//**************************************************************
	
	//returns a league's valid lineup string
	public function get_league_lineup($league_id){
		$this->db->select('valid_lineup_string');
		
		$this->db->where('league_id',$league_id);
		$this->db->limit(1);
		$query = $this->db->get('League_Settings');
		
		return $query->row('valid_lineup_string');
	}
	
//**************************************************************
	
	//returns a league's number of active roster
	public function get_league_active_roser_limit($league_id){
		$this->db->select('active_roster_spots');
		
		$this->db->where('league_id',$league_id);
		$this->db->limit(1);
		$query = $this->db->get('League_Settings');
		
		return $query->row('active_roster_spots');
	}


//************************************************************************
	
	//return an array of a team's entire current roster regardless of current area
	public function get_team_complete_roster($team_id) {
		$this->db->select('Rosters.fffl_player_id');
		$this->db->from('Rosters');
		$this->db->join('Players','Players.fffl_player_id = Rosters.fffl_player_id');
		$this->db->where('Rosters.team_id',$team_id);
		$this->db->order_by("FIELD(Rosters.position,'QB','RB','WR','TE','K'), Players.last_name ASC");
		$query = $this->db->get();
		
		$return_array = array();
		foreach($query->result() as $row){
			$return_array[]=$row->fffl_player_id;
		}

		return $return_array;
		
		
	}


//************************************************************
	
	//returns the players in the starting lineup or on the bench for a team
	public function get_team_active_roster($team_id){
		$this->db->select('fffl_player_id');
		$conditions = "team_id =".$team_id." and lineup_area='Roster'";
		$this->db->where($conditions);
		$this->db->order_by("FIELD(position,'QB','RB','WR','TE','K')");
		$query = $this->db->get('Rosters');
		$return_array = array();
		foreach($query->result() as $row){
			$return_array[]=$row->fffl_player_id;
		}
		return $return_array;
		
	}

//**************************************************************

	public function release_player($team_id,$fffl_player_id){
		//delete player from Rosters
		$this->db->where('team_id',$team_id)
				->where('fffl_player_id',$fffl_player_id)
				->delete('Rosters');
		//delete any startinglineup for this season current week and future that has the player
		$this->db->where('fffl_player_id',$fffl_player_id)
				->where('team_id',$team_id)
				->where('week>='.$this->current_week)
				->where('year',$this->current_year)
				->delete('Starting_Lineups');
		//decline any trade from this team that has the player in it
			//get trade ids 
			$trades = $this->Trades->get_team_player_open_trade($team_id,$fffl_player_id);
			foreach($trades as $trade_id){
				$this->Trades->decline_trade($trade_id);	
			}

		//add relese to transacitons
		$player_team = $this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","current_team");
		$this->db->set('team_id',$team_id)
				->set('league_id',$this->Teams->get_team_league_id($team_id))
				->set('transaction_type','Release')
				->set('fffl_player_id',$fffl_player_id)
				->set('team',$player_team['current_team'])
				->set('time',now())
				->set('week',$this->current_week)
				->set('season',$this->current_year)
				->insert('Transactions');
	}
	
//*************************************************************
	
	public function add_fa_to_roster($team_id,$fffl_player_id){
		
		
		$player_info_array = $this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","position current_team");
		$position = $player_info_array['position'];
		$player_team = $player_info_array['current_team'];
		
		//adds player to Rosters
		$this->db->set('team_id',$team_id)
				->set('fffl_player_id',$fffl_player_id)
				->set('salary', $this->Salaries->get_free_agent_salary($fffl_player_id))
				->set('weeks_on_pup', 0)
				->set('position', $position)
				->set('lineup_area', 'Roster')
				->set('sub_priority', 999)
				->insert('Rosters');
		
		//add fa pickeup to transacitons
		$this->db->set('team_id',$team_id)
				->set('league_id',$this->Teams->get_team_league_id($team_id))
				->set('transaction_type','FA')
				->set('fffl_player_id',$fffl_player_id)
				->set('team',$player_team)
				->set('time',now())
				->set('week',$this->current_week)
				->set('season',$this->current_year)
				->insert('Transactions');

	}

	
//**********************************************************
	
	//returns the starters for a team for a particular week and year
	public function get_team_starters($team_id, $week, $year){
		if($week===0){$week=1;}
		$return_array = array();
		if($week<17){
			$this->db->select('fffl_player_id');
			$conditions = "team_id =".$team_id." and week = ".$week." and year = ".$year;
			$this->db->where($conditions);
			$this->db->order_by("FIELD(position,'QB','RB','WR','TE','K')");
			$query = $this->db->get('Starting_Lineups');
			
			foreach($query->result() as $row){
				$return_array[]=$row->fffl_player_id;
			}
		}
		else{
			foreach($this->get_probowl_roster($team_id,$year) as $position){
				foreach($position as $fffl_player_id){
					$return_array[]= $fffl_player_id;
				}
			}
			
		}
		
		return $return_array;
	}

//*****************************************************************
	
	public function get_team_bench($team_id,$week,$year) {
		$starters = $this->get_team_starters($team_id, $week, $year);
		$all_active_roster = $this->get_team_active_roster($team_id);
		foreach($starters as $fffl_player_id){
			$key = array_search($fffl_player_id,$all_active_roster)	;
			unset($all_active_roster[$key]);
		}
		return $all_active_roster;
	}
	

//****************************************************************
	
	//returns the inactive players for a team
	public function get_team_inactives($team_id){
		$this->db->select('fffl_player_id,lineup_area');
		$conditions = "team_id =".$team_id." and (lineup_area='PUP' or lineup_area='PS')";
		$this->db->where($conditions);
		$this->db->order_by("FIELD(lineup_area,'PUP','PS')");
		$query = $this->db->get('Rosters');
		$return_array = array();
		foreach($query->result() as $row){
			$return_array[$row->lineup_area]=$row->fffl_player_id;
			
		}
		return $return_array;
		
	}

//******************************************************************
	
	//retrns either pup, ps or roster for the player for a team
	public function get_player_roster_area($team_id,$fffl_player_id){
		$this->db->select('lineup_area');
		$conditions = "team_id =".$team_id." and fffl_player_id=".$fffl_player_id;
		$this->db->where($conditions);
		$query = $this->db->get('Rosters');
		return $query->row('lineup_area');
			
	}

//******************************************************************
	
	//returns the number of weeks a players been on pup
	public function get_player_team_weeks_on_pup($team_id,$fffl_player_id){
		$this->db->select('weeks_on_pup');
		$conditions = "team_id =".$team_id." and fffl_player_id=".$fffl_player_id;
		$this->db->where($conditions);
		$query = $this->db->get('Rosters');
		return $query->row('weeks_on_pup');
			
	}
	
//*********************************************************************

	public function get_eligible_pup_players($team_id){
		$return_array=array();
		$query = $this->db->select('Players.fffl_player_id')
							->from('Players')
							->join('Rosters','Players.fffl_player_id = Rosters.fffl_player_id')
							->where('Players.is_injured',1)
							->where("Rosters.lineup_area<>'PS'")
							->where("Rosters.team_id",$team_id)
							->order_by('Players.last_name')
							->get();
		foreach($query->result_array() as $row){
			if($this->Players->is_player_locked($row['fffl_player_id'])==0){
				$return_array[]=$row['fffl_player_id'];
				
			}
		}
		return $return_array;
		
	}
	
//***********************************************************************
	
	//adds a pup player for a team
	public function add_pup($team_id,$fffl_player_id){
		$league_id = $this->Teams->get_team_league_id($team_id);
		$this->Database_Manager->database_backup(array('Rosters'));
		$this->db->set('weeks_on_pup',0);
		$this->db->set('lineup_area','PUP');
		$this->db->where('fffl_player_id='.$fffl_player_id.' and team_id='.$team_id);
		$this->db->update('Rosters');
		
		//insert into transactions
		$team = $this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","current_team");
		$data = array(
			'team_id' => $team_id,
			'league_id' => $league_id,
			'transaction_type' => 'Add PUP',
			'fffl_player_id' => $fffl_player_id,
			'team' => $team['current_team'],
			'time' => now(),
			'season' => $this->current_year,
			'can_undo' => 1,
		);
		$this->db->insert('Transactions',$data);
		
		//remove any trade offers for this player
		$query = $this->db->where('response_status',0)
						->group_start()
							->or_where('offered_by',$team_id)
							->or_where('offered_to',$team_id)
						->group_end()
						->group_start()
							->or_where("players_offered like '%".$fffl_player_id."%'")
							->or_where("players_received like '%".$fffl_player_id."%'")
						->group_end()
						->get('Trades');
		foreach($query->result_array() as $trade_data){
			$this->Trades->decline_trade($trade_data['trade_id']);
		}
		
	}

//**************************************************************************

	//end of week advance all pup players by one week on pup
	public function add_week_to_pup(){
		$this->db->where('lineup_area','PUP')
				->set("weeks_on_pup","weeks_on_pup+1",FALSE)
				->update('Rosters');	
	}

//***********************************************************************
	
	//adds a ps player for a team
	public function add_ps($team_id,$fffl_player_id){
		$league_id = $this->Teams->get_team_league_id($team_id);
		$this->Database_Manager->database_backup(array('Rosters'));
		
		$this->db->set('lineup_area','PS');
		$this->db->where('fffl_player_id='.$fffl_player_id.' and team_id='.$team_id);
		$this->db->update('Rosters');
		
		//insert into transactions
		$team = $this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","current_team");
		$data = array(
			'team_id' => $team_id,
			'league_id' => $league_id,
			'transaction_type' => 'Add PS',
			'fffl_player_id' => $fffl_player_id,
			'team' => $team['current_team'],
			'time' => now(),
			'season' => $this->current_year,
			'can_undo' => 1,
		);
		$this->db->insert('Transactions',$data);
		
		//remove any trade offers for this player
		$query = $this->db->where('response_status',0)
						->group_start()
							->or_where('offered_by',$team_id)
							->or_where('offered_to',$team_id)
						->group_end()
						->group_start()
							->or_where("players_offered like '%".$fffl_player_id."%'")
							->or_where("players_received like '%".$fffl_player_id."%'")
						->group_end()
						->get('Trades');
		foreach($query->result_array() as $trade_data){
			$this->Trades->decline_trade($trade_data['trade_id']);
		}
		
	}


//*********************************************************************

	public function get_eligible_ps_players($team_id){
		$return_array=array();
		$query = $this->db->select('Players.fffl_player_id')
							->from('Players')
							->join('Rosters','Players.fffl_player_id = Rosters.fffl_player_id')
							->where('Players.is_rookie',1)
							->where("Rosters.lineup_area<>'PUP'")
							->where("Rosters.team_id",$team_id)
							->order_by('Players.last_name')
							->get();
		foreach($query->result_array() as $row){
			if($this->Players->is_player_locked($row['fffl_player_id'])==0){
				$return_array[]=$row['fffl_player_id'];
				
			}
		}
		return $return_array;
		
	}


	
//************************************************************************
	
	//update the starting lineup by elimnation of the current starting lineup and going through
	//each player and putting them in. starting_lineup_array is an array of ids
	public function update_starting_lineup($team_id,$year,$week,$starting_lineup_array){
		$current_starters = $this->get_team_starters($team_id, $week, $year);
		//foreach current starter if not in starting lineup array check if locked if locked
		//	return FALSE
		
		//foreach starting lineup array check if in current starters. if not in current starters
		// check if locked. If locked return FALSE
		// check if pup or ps. If is return FALSE
		
		//no locked players being moved or in pup or ps continue with update
		$this->reset_starting_lineup($team_id,$year,$week);
		foreach($starting_lineup_array as $fffl_player_id){
			$position = $this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","position");
			
			$data=array(
				'team_id' => $team_id,
				'week' => $week,
				'year' => $year,
				'fffl_player_id' => $fffl_player_id,
				'position' => $position['position']
			);
			$this->db->insert('Starting_Lineups',$data);
		}
		return TRUE;
	}


//***********************************************************************
	
	//activates the pup player for a team
	public function activate_pup($team_id,$fffl_player_id){
		$league_id = $this->Teams->get_team_league_id($team_id);
		$this->Database_Manager->database_backup(array('Rosters'));
		$this->db->set('weeks_on_pup',0);
		$this->db->set('lineup_area','Roster');
		$this->db->where('fffl_player_id='.$fffl_player_id.' and team_id='.$team_id);
		$this->db->update('Rosters');
		//if it's prior to franchise, his salary must go up
		if(now()<$this->Calendars->get_calendar_time('franchise',$league_id)){
			$this->Salaries->increase_salary($team_id,$fffl_player_id);	
		}
		//insert into transactions
		$team = $this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","current_team");
		$data = array(
			'team_id' => $team_id,
			'league_id' => $league_id,
			'transaction_type' => 'Activate PUP',
			'fffl_player_id' => $fffl_player_id,
			'team' => $team['current_team'],
			'time' => now(),
			'season' => $this->current_year,
			'can_undo' => 1,
		);
		$this->db->insert('Transactions',$data);
		
	}
	
	
//**********************************************************************************

	//sorts the team's roster by sub priority
	public function sort_by_sub_prioity($team_id){
		$this->db->select('fffl_player_id');
		$conditions = "team_id =".$team_id." and lineup_area='Roster'";
		$this->db->where($conditions);
		$this->db->order_by("sub_priority","ASC");
		$query = $this->db->get('Rosters');
		$return_array = array();
		foreach($query->result() as $row){
			$return_array[]=$row->fffl_player_id;
		}
		return $return_array;
		
	}
	
//**************************************************************************************
	//updates the sub order for a team
	public function update_subs($team_id,$sub_array){
		$count = 1;
		foreach($sub_array as $fffl_player_id){
			$this->db->set('sub_priority',$count)
					->where('team_id',$team_id)
					->where("fffl_player_id",$fffl_player_id)
					->update('Rosters');
			
			$count++;
		}
		
	}


//**************************************************************************************
	//get a team's probowl roster indexes are positions
	public function get_probowl_roster($team_id,$year){
		$roster_array = array('QB'=>array(),'RB'=>array(),'WR'=>array(),'TE'=>array(),'K'=>array(),);
		
		
		$query = $this->db->where('team_id',$team_id)
				->where("year",$year)
				->get('Probowl');
					
		$result = $query->result_array();
		
		if(!empty($result)){
			$roster = $result['0'];
			$count=1;
			while($count<=8){
				if(isset($roster["player_".$count]) && $roster["player_".$count] !=0 ){
					$fffl_player_id=$roster["player_".$count];
				
					$pos_array = $this->Players->get_player_info(array($fffl_player_id),"fffl_player_id",$elements_string="position");
				
					$position = $pos_array['position'];
					//get player position and add fffl_player_id to that position index
					$roster_array[$position][]=$fffl_player_id;
				}			
				$count++;
			}
		}
		else { 
			return $roster_array=array();
		}
		while(count($roster_array['QB'])==0){
			$roster_array['QB'][]=0;

		}
		while(count($roster_array['RB'])<2){
			$roster_array['RB'][]=0;	
		}
		while(count($roster_array['WR'])<3){
			$roster_array['WR'][]=0;	
		}
		while(count($roster_array['TE'])==0){
			$roster_array['TE'][]=0;	
		}
		while(count($roster_array['K'])==0){
			$roster_array['K'][]=0;	
		}
		
		return $roster_array;
		
	}
	
	
//*********************************************************

	public function insert_probowl_roster($team_id,$year,$players) {
		$count=1;
		d($players);
		while($count<=8){
			$this->db->set("player_".$count,$players[$count-1]);
			$count++;
		}
		return $this->db->set("year",$year)
				->set("team_id",$team_id)
				->set("league_id",$this->Teams->get_team_league_id($team_id))
				->insert("Probowl");
				
		
		
	}
	
//*********************************************************

	public function update_probowl_roster($team_id,$year,$players) {
		$count=1;
		while($count<=8){
			$this->db->set("player_".$count,$players[$count-1]);
			$count++;
		}
		return $this->db->where("year",$year)
				->where("team_id",$team_id)
				->update("Probowl");
		
	}
	
//*********************************************************

	public function update_all_pro_team($league_id,$year) {
		
	$query = $this->db->where("year",$year)
						->where("league_id",$league_id)
						->where("team_id",0)
						->get("Probowl"); 
						
if (count($query->result_array())==0){
		//get all play that are selected as probowlers, regardless of position, we'll sort that out later
		$query = $this->db->where("year",$year)
						->where("league_id",$league_id)
						->where("team_id>0")
						->get("Probowl");
		//fill all_players with each fffl_player_id
		$all_players = array();
		foreach($query->result_array() as $team_roster){
			$count=1;
			while($count<=8 && $team_roster['player_'.$count]>0){
				$all_players[]=$team_roster['player_'.$count];
				$count++;
			}
		}
		
		//create indexes of each unique player in pro_bowl_count array and count number of instances
		$pro_bowl_count = array_count_values($all_players);
		arsort($pro_bowl_count);
		
		//add the highest to appropriate index in pro_bowl_team
		$pro_bowl_team = array("QB1"=>0,"RB1"=>0,"RB2"=>0,"WR1"=>0,"WR2"=>0,"WR3"=>0,"TE1"=>0,"K1"=>0);
		
		foreach($pro_bowl_count as $fffl_player_id => $count){
			
			//get position of player
			$player_info = $this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","position");
			$position = $player_info['position'];
			
			$count=1;
			//while the index exists, counting up from PO1, PO2...
			while(isset($pro_bowl_team[$position.$count])){
				//if this index is 0, add the player and break, otherwise move to next index
				if($pro_bowl_team[$position.$count]==0){
					$pro_bowl_team[$position.$count]=$fffl_player_id;
					break;
				}
				$count++;
			}
			//as long as one player is still 0 it will continue looking
			if(!in_array(0,$pro_bowl_team)){
				break;
			}
		}
		
		//add the palyers to the team
		$this->db->set("league_id",$league_id)
				->set("year",$year)
				->set("team_id",0);
		$count=1;
		foreach($pro_bowl_team as $fffl_player_id){
			$this->db->set("player_".$count,$fffl_player_id);
			$count++;
		}
		$this->db->insert("Probowl");
		
		}	
		
		
	}
		
	
}
/*End of file Rosters.php*/
/*Location: ./application/models/Teams.php*/