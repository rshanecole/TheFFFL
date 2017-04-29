<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Drafts Model.
	 *
	 * ?????
	 *		
	 */
	
Class Drafts extends CI_Model 
{
	public function __construct() 
    {
		parent::__construct();
		//$ci = get_instance();
		$this->load->helper('date');
		
		$this->load->model('Teams');
		$this->load->model('Database_Manager');
		$this->load->model('Leagues');
		$this->load->model('Players');
		$this->load->model('Free_Agents');
	}
	
//***************************************************
	//gets details about a particular draft
	public function get_draft_details($draft_id){
		$draft_query = $this->db->where('draft_id',$draft_id)
								->get('Drafts');
		$draft = $draft_query->row();
		
		$return_array = array(
			'year' => $draft->year,
			'league_id' => $draft->league_id,
			'type' => $draft->type,
			'number_of_rounds' => $draft->number_of_rounds,
			'picks_per_round' => $draft->picks_per_round,
			'start_time' => $draft->start_time,
			'status' => $draft->status,
			'timer_expiration' => $draft->timer_expiration,
			'paused' => $draft->paused
		
		);
		
		return $return_array;
	}
	
//**********************************************************************************

	//returns an id of a  draft that is currently in progress, status of 1
	//for a given league and year
	 public function get_in_progress_drafts($league_id, $year){
		 $drafts_query = $this->db->select('draft_id')
		 							->where('league_id',$league_id)
									->where('year',$year)
									->where('status',1)
									->limit(1)
									->get('Drafts');
		$draft=0;
		foreach($drafts_query->result_array() as $draft_id){
			$draft = $draft_id['draft_id'];	
		}
		
		return $draft;
	 }
//*********************************************************************************

	//
	//This creates the entries for all drafts for a league
	//Should be done after the drafts are complete and the first 
	//week of the season begins. 
	public function create_next_season_drafts($league_id,$current_year) {
		$this->Database_Manager->database_backup(array('Drafts'));
		//get the number of teams,number of common/supp drafts and rounds per draft from league settings.
		$league_settings = $this->db->select('num_teams,num_common_drafts,num_supplemental_drafts,num_common_rounds,num_supplemental_rounds')
								->where('league_id',$league_id)
								->get('League_Settings');
		$settings = $league_settings->row();								

		//COMMON DRAFTS
		//calculate number of picks per round
		$picks_per_round = $settings->num_teams / $settings->num_common_drafts;
		//count is for counting number of drafts entered
		$count=0;
		while($count<$settings->num_common_drafts){
			$this->db->set('league_id',$league_id)
					->set('type','Common')
					->set('number_of_rounds',$settings->num_common_rounds)
					->set('picks_per_round',$picks_per_round)
					->set('year',$current_year+1)
					->insert('Drafts');
			$count++;
		}
		
		//SUPPLEMENTAL DRAFTS
		//calculate number of picks per round
		$picks_per_round = $settings->num_teams / $settings->num_supplemental_drafts;
		//count is for counting number of drafts entered
		$count=0;
		while($count<$settings->num_supplemental_drafts){
			$this->db->set('league_id',$league_id)
					->set('type','Supplemental')
					->set('number_of_rounds',$settings->num_supplemental_rounds)
					->set('picks_per_round',$picks_per_round)
					->set('year',$current_year+1)
					->insert('Drafts');
			$count++;
		}
		
		//use the assign_draft_picks method to assign this league's draft picks
	}

//**************************************************************************
	
	//
	//This creates the entries for all draft picks for a league
	//Should assign a pick for each team for each round, order won't matter.
	//teams ranking irrelevant. Updating picks will be done by another method
	public function assign_draft_picks($league_id,$season){
		$this->Database_Manager->database_backup(array('Draft_Picks'));
		// array with all teams, ranking irrelevant. Updating picks will be done by another method
		$all_teams_array = $this->Teams->get_all_team_id($league_id);
		$number_of_teams = count($all_teams_array);
		//	get the draft ids for the season
		$all_drafts_array=$this->get_league_draft_ids($league_id,$season);
	
		//COMMON DRAFTS
		// divide them by number of groups equal to number of drafts 
		// draft ids as keys for the groups
		$number_of_drafts = (count($all_drafts_array['Common']));
		$teams_per_draft = $number_of_teams/$number_of_drafts;
		$offset = 0;
		//each iteration will be a draft
		foreach($all_drafts_array['Common'] as $draft => $number_of_rounds){
			$group=array_slice($all_teams_array,$offset,$teams_per_draft);
			$round=1;
			$pick_number = 1;
			while($round<=$number_of_rounds){
				foreach($group as $team_id){
					$this->db->set('draft_id',$draft)
							->set('original_owner',$team_id)
							->set('current_owner',$team_id)
							->set('round',$round)
							->set('pick_number',$pick_number)
							->insert('Draft_Picks');
					$pick_number++;
				}//end pick
				
				//reverse the order and advance to next round
				$group = array_reverse($group);
				$round++;
			}//end round
			$offset = $offset + $teams_per_draft;
			
		}//end each common draft
		
		//SUPPLEMENTAL DRAFTS
		// divide them by number of groups equal to number of drafts 
		// draft ids as keys for the groups
		$number_of_drafts = (count($all_drafts_array['Supplemental']));
		$teams_per_draft = $number_of_teams/$number_of_drafts;
		$offset = 0;
		
		foreach($all_drafts_array['Supplemental'] as $draft => $number_of_rounds){
			$group=array_slice($all_teams_array,$offset,$teams_per_draft);
			$round=1;
			$pick_number = 1;
			while($round<=$number_of_rounds){
				foreach($group as $team_id){
					$this->db->set('draft_id',$draft)
							->set('original_owner',$team_id)
							->set('current_owner',$team_id)
							->set('round',$round)
							->set('pick_number',$pick_number)
							->insert('Draft_Picks');
					$pick_number++;
				}//end pick
				
				//reverse the order and advance to next round
				$group = array_reverse($group);
				$round++;
			}//end round
			$offset = $offset + $teams_per_draft;
			
		}//end each common draft
		
	}
		
//************************************************************************************		
		
	//gets all the draft ids for a league for a season. 
	// returns array, The first key is type, each draft has a key of draft id with value of num of rounds
	public function get_league_draft_ids($league_id,$season){
		$query = $this->db->where('league_id',$league_id)
						->where('year',$season)
						->select('draft_id,type,number_of_rounds')
						->get('Drafts');
		
		$return_array = array();
		foreach ($query->result_array() as $row)
		{
			$return_array[$row['type']][$row['draft_id']]=$row['number_of_rounds'];
		}
		
		return $return_array;
	}
  	
//************************************************************************************
	
	//ONLY FOR PURPOSE OF UPDATING DRAFT PICKS for the NEXT year's draft
	//If want to get data about certain draft picks, use the draft_picks table
  	public function get_draft_groups($year,$league_id,$draft_type='Common'){
		$all_teams_sorted_array = $this->Standings->sort_teams_by($year,$league_id,$conference='',$division='',$sort_order_array=array('last_game','wins','points'));
		
		if($draft_type == 'Supplemental'){
			//d($all_teams_sorted_array);
			//if it's supplemental, move the TB winners to the top, if it's done
			$TB_query = $this->db->select_max('winner','winner')
								->where('year',$year)
								->get('Toilet_Bowls');
								foreach ($TB_query->result() as $row)
									{
										$winner= $row->winner;
									}
			//if max is >0 thenthere's a winner. GEt the max week for that year in TB table.
			//then find the max score for that week where teh opponent isn't the winner. Just
			//in case more than 2 teams qualify.  
			if($winner && $winner>0){
				$RU_query = $this->db->select('opponent')
								->where('year',$year)
								->where('opponent != '.$winner)
								->order_by('week','DESC')
								->order_by('opponent_score','DESC')
								->limit(1)
								->get('Toilet_Bowls');
				
			
				foreach ($RU_query->result_array() as $row)
					{
						
						$runner_up= $row['opponent'];
					}
					
				//remove the winner and runner_up from the array and move them to the front
				//start with runner up
				$teams = array($runner_up,$winner);
				foreach($teams as $team_id){
				   foreach($all_teams_sorted_array as $key => $team)
				   {
					  if ( $team['team_id'] === $team_id )
						 $found_key = $key;
				   }
				
					$all_teams_sorted_array = array($found_key => $all_teams_sorted_array[$found_key]) + $all_teams_sorted_array;
				}
				$all_teams_sorted_array = array_values($all_teams_sorted_array);
				
			}
		}
		//get the draft ids, year has to be the next year
		$draft_ids_array = $this->get_league_draft_ids($league_id,($year+1));
		$number_of_drafts = count($draft_ids_array[$draft_type]);
		$draft_groups = array();
		$draft_ids = array_keys( $draft_ids_array[$draft_type]);
		$draft_iterator=0;
		foreach($all_teams_sorted_array as $team_info){
				$draft_groups[$draft_ids[$draft_iterator]][] = $team_info['team_id'];
				$draft_iterator++;
				if($draft_iterator == $number_of_drafts){
					$draft_ids=array_reverse($draft_ids);
					$draft_iterator=0;
				}
				
		}

		return $draft_groups;

	}

//*********************************************************************************
	
	//ONLY FOR PURPOSE OF UPDATING DRAFT PICKS for the NEXT year's draft
	//If want a certain drafts picks, use the draft_picks table
	public function get_all_draft_picks($current_year,$league_id,$number_of_rounds,$draft_type='Common'){
		$draft_groups = $this->get_draft_groups($current_year,$league_id,$draft_type);//will be the draft for the NEXT year
		
		$round=1;
		$pick=1;
		$all_draft_picks=array();
		
		while($round<=$number_of_rounds){
			$round_starting_pick = $pick;
			foreach($draft_groups as $draft_id => $order_array){
				$pick=$round_starting_pick;
				foreach($order_array as $team_id){
					$all_draft_picks[$draft_id][$round][$pick]['original_team_id']=$team_id;
					$pick++;
				}
				
				//round over for this draft, reverse the order for next round
				$draft_groups[$draft_id]=array_reverse($draft_groups[$draft_id]);
			}
			$round++;
		}
		
		return $all_draft_picks;
		
		
	}


//**************************************************************************************
	
	//automated at advancement to next week including
	// 		doing this advancing to week 17
	//assigns sequence numbers based on standings and playoff advancement
	//of the CURRENT year but for the NEXT year's draft
	public function update_draft_order($league_id,$current_year,$current_week) {
		$this->Database_Manager->database_backup(array('Draft_Picks'));
		
		//COMMON DRAFTS
		
		//get the draft ids for the NEXT year, the year after the the year's standings the order was going to be based on
		$draft_ids_array = $this->get_league_draft_ids($league_id,($current_year+1));

		//get number of rounds
		$number_of_rounds = reset($draft_ids_array['Common']);
		//get all teams, and number of teams in league
		$all_teams_array = $this->Teams->get_all_team_id($league_id);
		$number_of_teams = count($all_teams_array);
		$number_of_drafts = count($draft_ids_array['Common']);
		$picks_per_round = $number_of_teams/$number_of_drafts;
		
		//get all picks for each draft draft_id=>round=>pick=>"original team id"
		$all_draft_picks = $this->Drafts->get_all_draft_picks($current_year,$league_id,$number_of_rounds,'Common');
		//d($all_draft_picks);
		//add the draft_pick_id and current owner of the pick for that team in that round
		//each draft
		foreach($all_draft_picks as $draft_id => $rounds){
			//each round for that draft_id
			foreach($rounds as $round=>$picks){
				//each pick for that round
				//d($picks);
				foreach($picks as $pick=>$data){
					
					//need to get current owner of pick for either draft_id, that round and matches original owner
					//because the pick may have been traded
					$conditions = "original_owner = ".$data['original_team_id']." and round =".$round." and (";
					//add to conditions all draft ids because we don't know which draft the team is currently assigned
					$count=1;
					foreach($draft_ids_array['Common'] as $id=>$number_rounds){
						$conditions .= 'draft_id = '.$id;
						if($count<$number_of_drafts){
							$conditions .= ' or ';
						}
						$count++;
					}
					$conditions .=')';
					
					$this->db->select('pick_id,current_owner');
					$this->db->where($conditions);
					$current_query = $this->db->get('Draft_Picks');
					$current_owner = $current_query->row('current_owner');
					$pick_id = $current_query->row('pick_id');
					
					 
					$all_draft_picks[$draft_id][$round][$pick]['current_owner']=$current_owner;
					$all_draft_picks[$draft_id][$round][$pick]['pick_id']=$pick_id;
					
				}
				
			}
		}
		//d($all_draft_picks);
		
		//foreach draft, replace the pick number and draft for each pickin the draft
		foreach($all_draft_picks as $draft_id => $rounds){
			
			//each round
			foreach($rounds as $round => $picks){
				//each pick
				foreach($picks as $pick_number => $owners){
					$this->db->set('draft_id',$draft_id)
							->set('pick_number',$pick_number)
							->where('pick_id',$owners['pick_id'])
							->update('Draft_Picks');
				
				}
				
			}
			
		}
		
		//SUPPLEMENTAL DRAFTS

		//get number of rounds
		$number_of_rounds = reset($draft_ids_array['Supplemental']);
		$number_of_drafts = count($draft_ids_array['Supplemental']);
		$picks_per_round = $number_of_teams/$number_of_drafts;
		
		//get all picks for each draft draft_id=>round=>pick=>"original team id"
		$all_draft_picks= $this->Drafts->get_all_draft_picks($current_year,$league_id,$number_of_rounds,'Supplemental');
		
		//add the current owner of the pick for that team in that round
		//each draft
		foreach($all_draft_picks as $draft_id => $rounds){
			//each round for that draft_id
			foreach($rounds as $round=>$picks){
				//each pick for that round
				foreach($picks as $pick=>$data){
					//need to get current owner of pick for either draft_id, that round and matches original owner
					//because the pick may have been traded
					$conditions = "original_owner = ".$data['original_team_id']." and round =".$round." and (";
					//add to conditions all draft ids because we don't know which draft the team is currently assigned
					$count=1;
					foreach($draft_ids_array['Supplemental'] as $id=>$number_rounds){
						$conditions .= 'draft_id = '.$id;
						if($count<$number_of_drafts){
							$conditions .= ' or ';
						}
						$count++;
					}
					$conditions .=')';
					
					$this->db->select('current_owner,pick_id');
					$this->db->where($conditions);
					$current_query = $this->db->get('Draft_Picks');
					$current_owner = $current_query->row('current_owner');
					$pick_id = $current_query->row('pick_id');
					
					 
					$all_draft_picks[$draft_id][$round][$pick]['current_owner']=$current_owner;
					$all_draft_picks[$draft_id][$round][$pick]['pick_id']=$pick_id;
				}
				
			}
		}
		
		
		//foreach draft, delete the draft then replace the picks
		//each draft
		foreach($all_draft_picks as $draft_id => $rounds){
			
			foreach($rounds as $round => $picks){
				//each pick
				foreach($picks as $pick_number => $owners){
					$this->db->set('draft_id',$draft_id)
							->set('pick_number',$pick_number)
							->where('pick_id',$owners['pick_id'])
							->update('Draft_Picks');
					
				}
				
			}
		}
	}//end function update_draft_picks
	
	
//**************************************************************************************************

	public function get_team_original_draft($team_id,$year){
		$league_id = $this->Teams->get_team_league_id($team_id);
		$return_array = array();
		
		//COMMON DRAFTS
		//create an array with keys of start_time => 'id' 'pick' 'player' 'original owner'
		//get all the draft ids for the given year(s)
		
		$this->db->where('year',$year);
		$this->db->where('league_id',$league_id);
		$this->db->where('type','Common');
		$this->db->order_by('start_time','ASC');
		$this->db->order_by('draft_id','ASC'); //for the old drafts that don't have start times
		$query = $this->db->get('Drafts');
		
		
		$key=0;
		foreach ($query->result() as $draft)
		{
			//if(!isset($return_array[$draft->year])){$return_array[$draft->start_time]=array();}
			//get the picks for that draft
			$conditions = 'draft_id='.$draft->draft_id.' and original_owner='.$team_id;
			$picks_query = $this->db->select('pick_id, round, pick_number')
						->where($conditions)
						->get('Draft_Picks');
			//add the picks to the array for that year key
			foreach($picks_query->result() as $picks){
				
				$return_array[$draft->start_time][$key]['draft_id'] = $draft->draft_id;
				$return_array[$draft->start_time][$key]['pick_id'] = $picks->pick_id;
				$return_array[$draft->start_time][$key]['round'] = $picks->round;
				$return_array[$draft->start_time][$key]['pick_number'] = $picks->pick_number;
				$key++;
			}

		}
		return $return_array;
		
	}



//***************************************************************************************************
	
	//This gets an individual team's draft by year or all years
	public function get_team_draft_by_year($team_id,$year='all'){
		$league_id = $this->Teams->get_team_league_id($team_id);
		$return_array = array();
		
		//COMMON DRAFTS
		//create an array with keys of year => 'id' 'pick' 'player' 'original owner'
		//get all the draft ids for the given year(s)
		if($year!='all'){
			$this->db->where('year',$year);
		} 
		$this->db->where('league_id',$league_id);
		$this->db->where('type','Common');
		$this->db->order_by('year','DESC');
		$this->db->order_by('start_time','ASC');
		$this->db->order_by('draft_id','ASC'); //for the old drafts that don't have start times
		$query = $this->db->get('Drafts');
		
		
		//set each year as a key
		$key=0;
		foreach ($query->result() as $draft)
		{
			if(!isset($return_array[$draft->year])){$return_array[$draft->year]=array();}
			//get the picks for that draft
			$conditions = 'draft_id='.$draft->draft_id.' and current_owner='.$team_id;
			$picks_query = $this->db->select('pick_id,original_owner, round, pick_number,fffl_player_id')
						->where($conditions)
						->get('Draft_Picks');
			//add the picks to the array for that year key
			foreach($picks_query->result() as $picks){
				
				$return_array[$draft->year][$key]['draft_id'] = $draft->draft_id;
				$return_array[$draft->year][$key]['start_time'] = $draft->start_time;
				$return_array[$draft->year][$key]['pick_id'] = $picks->pick_id;
				$return_array[$draft->year][$key]['original_owner'] = $picks->original_owner;
				$return_array[$draft->year][$key]['round'] = $picks->round;
				$return_array[$draft->year][$key]['pick_number'] = $picks->pick_number;
				$return_array[$draft->year][$key]['fffl_player_id'] = $picks->fffl_player_id;
				
				if($picks->fffl_player_id>0){
					$position = $this->Players->get_player_info(array($picks->fffl_player_id),"fffl_player_id","first_name last_name position");
					$return_array[$draft->year][$key]['position'] = $position['position'];
					$return_array[$draft->year][$key]['name']=$position['first_name'].' '.$position['last_name'];
				}
				else {
					$return_array[$draft->year][$key]['position'] = '';
					$return_array[$draft->year][$key]['name']='';
				}
				$return_array[$draft->year][$key]['type'] = 'Common';
              
				$key++;
			}
			//reorder that year by pick
			// Obtain a list of columns
			$pick_array=array();
			$draft_id_array=array();
			foreach ($return_array[$draft->year] as $key2 => $row) {
				$pick_array[$key2]  = $row['pick_number'];
				$draft_id_array[$key2] = $row['draft_id'];
			}
			
			// Sort the data with pick asc, draft_id ascending
			// Add $data as the last parameter, to sort by the common key
			array_multisort($pick_array, SORT_ASC, $return_array[$draft->year]);
			unset($pick_array); unset($draft_id_array);
          
		}
		
		
		//SUPPLEMENTAL
		//create an array with keys of year => 'id' 'pick' 'player' 'original owner'
		//get all the draft ids for the given year(s)
		if($year!='all'){
			$this->db->where('year',$year);
		} 
		$this->db->where('league_id',$league_id);
		$this->db->where('type','Supplemental');
		$this->db->order_by('year','DESC');
		$this->db->order_by('start_time','ASC');
		$this->db->order_by('draft_id','ASC'); //for the old drafts that don't have start times
		$query = $this->db->get('Drafts');
		
		//set each year as a key

		foreach ($query->result() as $draft)
		{
			//get the picks for that draft
			$conditions = 'draft_id='.$draft->draft_id.' and current_owner='.$team_id;
			$picks_query = $this->db->select('original_owner, round, pick_number,fffl_player_id')
						->where($conditions)
						->get('Draft_Picks');
			//add the picks to the array for that year key
			foreach($picks_query->result() as $picks){
				$return_array[$draft->year][$key]['draft_id'] = $draft->draft_id;
				$return_array[$draft->year][$key]['start_time'] = $draft->start_time;
				$return_array[$draft->year][$key]['draft_id'] = $draft->draft_id;
				$return_array[$draft->year][$key]['original_owner'] = $picks->original_owner;
				$return_array[$draft->year][$key]['round'] = $picks->round;
				$return_array[$draft->year][$key]['pick_number'] = $picks->pick_number;
				$return_array[$draft->year][$key]['fffl_player_id'] = $picks->fffl_player_id;
				if($picks->fffl_player_id>0){
					$position = $this->Players->get_player_info(array($picks->fffl_player_id),"fffl_player_id","first_name last_name position");
					$return_array[$draft->year][$key]['position'] = $position['position'];
					$return_array[$draft->year][$key]['name']=$position['first_name'].' '.$position['last_name'];
				}
				else {
					$return_array[$draft->year][$key]['position'] = '';
					$return_array[$draft->year][$key]['name']='';
				}
				$return_array[$draft->year][$key]['type'] = 'Supplemental';
				$key++;
			}
		}
      //d($return_array['2016']);
		
		if(count($return_array)>0){
			return $return_array;
		
		}
		else {
			return false;
		}
		
		
	}// end get team draft by year function 
	
//***********************************************************************	
	//simply gets the first and last draft years for a league
	//currently used to create dropdown menu on draft results view
	public function get_first_last_draft_years($league_id){
		$max_query = $this->db->select_max('year','max')
				->where('league_id',$league_id)
				->get('Drafts');
		$result = $max_query->row();
		$max = $result->max;
		
		$min_query = $this->db->select_min('year','min')
				->where('league_id',$league_id)
				->get('Drafts');
		$result = $min_query->row();
		$min = $result->min;
		
		$return_array['last'] = $max;
		$return_array['first'] = $min;
		
		return $return_array;
		
	}

//******************************************************************	
	//get all results for a year's draft
	public function get_draft_results_year($league_id,$year){
		
		$return_array = array();
		
		//COMMON DRAFTS
		
		//get all the draft ids for the given year(s)
		
		$this->db->where('year',$year);
		$this->db->where('league_id',$league_id);
		$this->db->where('type','Common');
		$this->db->order_by('year','DESC');
		$this->db->order_by('start_time','ASC');
		$this->db->order_by('draft_id','ASC'); //for the old drafts that don't have start times
		$query = $this->db->get('Drafts');
		
		//create results for each draft
		$key=0;
		foreach ($query->result() as $draft)
		{
			$return_array['Common'][$draft->draft_id]['start_time'] =  $draft->start_time;
			
			//get the picks for that draft
			$conditions = 'draft_id='.$draft->draft_id;
			$picks_query = $this->db->select('pick_id,original_owner, current_owner, round, pick_number,fffl_player_id')
						->where($conditions)
						->order_by('pick_number')
						->get('Draft_Picks');
			
			//add the picks to the array for that year key
			foreach($picks_query->result() as $picks){
				
				$return_array['Common'][$draft->draft_id]['picks'][$picks->pick_number]['original_owner'] = $picks->original_owner;
				$return_array['Common'][$draft->draft_id]['picks'][$picks->pick_number]['team_id'] = $picks->current_owner;
				$return_array['Common'][$draft->draft_id]['picks'][$picks->pick_number]['round'] = $picks->round;
				$return_array['Common'][$draft->draft_id]['picks'][$picks->pick_number]['fffl_player_id'] = $picks->fffl_player_id;
				$return_array['Common'][$draft->draft_id]['picks'][$picks->pick_number]['pick_id'] = $picks->pick_id;
				if($picks->fffl_player_id>0){
					$position = $this->Players->get_player_info(array($picks->fffl_player_id),"fffl_player_id","first_name last_name position");
                  
					$return_array['Common'][$draft->draft_id]['picks'][$picks->pick_number]['position'] = $position['position'];
					$return_array['Common'][$draft->draft_id]['picks'][$picks->pick_number]['name']=$position['first_name'].' '.$position['last_name'];
				}
				else {
					$return_array['Common'][$draft->draft_id]['picks'][$picks->pick_number]['position'] = '';
					$return_array['Common'][$draft->draft_id]['picks'][$picks->pick_number]['name']='';
				}
				$return_array['Common'][$draft->draft_id]['picks'][$picks->pick_number]['team_name'] = $this->Teams->get_team_name_first_nickname($picks->current_owner);
			}
			
		}
		
		
		//SUPPLEMENTAL
		//create an array with keys of year => 'id' 'pick' 'player' 'original owner'
		//get all the draft ids for the given year(s)
		$this->db->where('year',$year);
		$this->db->where('league_id',$league_id);
		$this->db->where('type','Supplemental');
		$this->db->order_by('year','DESC');
		$this->db->order_by('start_time','ASC');
		$this->db->order_by('draft_id','ASC'); //for the old drafts that don't have start times
		$query = $this->db->get('Drafts');
		
		//create results for each draft
		foreach ($query->result() as $draft)
		{	
			$return_array['Supplemental'][$draft->draft_id]['start_time'] =  $draft->start_time;
			//get the picks for that draft
			$conditions = 'draft_id='.$draft->draft_id;
			$picks_query = $this->db->select('pick_id,original_owner, current_owner, round, pick_number,fffl_player_id')
						->where($conditions)
						->order_by('pick_number')
						->get('Draft_Picks');
			//add the picks to the array for that year key
			foreach($picks_query->result() as $picks){
				
				$return_array['Supplemental'][$draft->draft_id]['picks'][$picks->pick_number]['original_owner'] = $picks->original_owner;
				$return_array['Supplemental'][$draft->draft_id]['picks'][$picks->pick_number]['team_id'] = $picks->current_owner;
				$return_array['Supplemental'][$draft->draft_id]['picks'][$picks->pick_number]['round'] = $picks->round;
				$return_array['Supplemental'][$draft->draft_id]['picks'][$picks->pick_number]['fffl_player_id'] = $picks->fffl_player_id;
				$return_array['Supplemental'][$draft->draft_id]['picks'][$picks->pick_number]['pick_id'] = $picks->pick_id;
				if($picks->fffl_player_id>0){
					$position = $this->Players->get_player_info(array($picks->fffl_player_id),"fffl_player_id","first_name last_name position");
					$return_array['Supplemental'][$draft->draft_id]['picks'][$picks->pick_number]['position'] = $position['position'];
					$return_array['Supplemental'][$draft->draft_id]['picks'][$picks->pick_number]['name']=$position['first_name'].' '.$position['last_name'];
				}
				else {
					$return_array['Supplemental'][$draft->draft_id]['picks'][$picks->pick_number]['position'] = '';
					$return_array['Supplemental'][$draft->draft_id]['picks'][$picks->pick_number]['name']='';
				}
				$return_array['Supplemental'][$draft->draft_id]['picks'][$picks->pick_number]['team_name'] = $this->Teams->get_team_name_first_nickname($picks->current_owner);
			}
		}
		
		if(count($return_array)>0){
			return $return_array;
		
		}
		else {
			return false;
		}
		
	}
	

//*******************************************************************************************	
	//gets a draft picks details, round, pick_number, original_owner, current_owner, timestamp(for draft)
	public function get_pick_details($pick_id){
	
		$draft_pick_query = $this->db->select('*')
									->where('pick_id',$pick_id)
									->get('Draft_Picks'); 
		$draft_pick = $draft_pick_query->row();
		
		$draft_details = $this->get_draft_details($draft_pick->draft_id);
		$start_time = $draft_details['start_time'];
		$return_array = array(
			'original_owner' => $draft_pick->original_owner,
			'current_owner' => $draft_pick->current_owner,
			'round' => $draft_pick->round,
			'pick_number' => $draft_pick->pick_number,
			'fffl_player_id' => $draft_pick->fffl_player_id,
			'start_time' => $start_time	
		);
		
		
		return $return_array;

	}
	
	
//*************************************************************
  
  public function get_player_draft($fffl_player_id,$league_id){
    $draft_query = $this->db->where('Draft_Picks.fffl_player_id',$fffl_player_id)
							->where('Drafts.league_id',$league_id)
							->select('Draft_Picks.current_owner,Draft_Picks.pick_number,Drafts.type,Drafts.year')
							->join('Drafts','Draft_Picks.draft_id = Drafts.draft_id')
							->order_by('Drafts.year','Desc')
							->get('Draft_Picks');
							
    $drafts=array();
    foreach($draft_query->result_array() as $data){
		
      	$drafts[$data['year']][$data['current_owner']]['pick_number']=$data['pick_number'];
      	$drafts[$data['year']][$data['current_owner']]['draft_type']=$data['type'];
      
      }
    return $drafts;
  }
  
 //************************************************************
 //gets an array of all the players already drafted in a particular draft
 //fffl_player_id => pick_number, owner
 public function get_drafted_players($draft_id){
	 $draft_query = $this->db->select('current_owner,pick_number,fffl_player_id,round')
	 						->where('draft_id',$draft_id)
							->order_by('pick_number','ASC')
							->get('Draft_Picks');
	$draft_array = array();
	foreach($draft_query->result_array() as $draft_pick){
		$draft_array[$draft_pick['pick_number']]['team_id']=$draft_pick['current_owner'];	
		$draft_array[$draft_pick['pick_number']]['fffl_player_id']=$draft_pick['fffl_player_id'];
		$draft_array[$draft_pick['pick_number']]['round']=$draft_pick['round'];
		
	}
	 
	 return $draft_array;
 }
 
 
 //**************************************************************
 
 	public function get_available_supplemental_players($league_id){
 		$next_draft = $this->get_next_draft_details($league_id);
		
		if($next_draft['type']!='Supplemental'){
			//the next draft isn't supp so do not return players
			return NULL;
		}
		
		$available_players = array();
		
		//supp draft status 0 means approaching round 1, status 1 means in progress approachign round 2
		if($next_draft['status']==0){
			//available players will have 1 FA
			$all_players = $this->Players->get_all_player_ids("","fffl_player_id","Players.last_name",'ASC',0);
			
			foreach($all_players['ids'] as $fffl_player_id){
				
				$num_FA=$this->Free_Agents->get_player_number_free_agents($fffl_player_id->fffl_player_id);
				if($num_FA==1){
					$available_players[]=$fffl_player_id->fffl_player_id;	
				}
			}
		}
		else{
			//available players will have >0 FA
			$all_players = $this->Players->get_all_player_ids("Players.current_team<>'RET'","fffl_player_id","Players.last_name",'ASC',0);
			
			foreach($all_players['ids'] as $fffl_player_id){
				
				$num_FA=$this->Free_Agents->get_player_number_free_agents($fffl_player_id->fffl_player_id);
				if($num_FA==1){
					$available_players[]=$fffl_player_id->fffl_player_id;	
				}
			}
			
		}
		
		
		return $available_players;
		
 
	}
	
//********************************************************************

	public function get_team_supplemental_selections($team_id){
		$query = $this->db->where('team_id',$team_id)
						->order_by('priority','ASC')
						->get('Supplemental_Selections');
						
		$return_array = array();
		foreach($query->result_array() as $selections){
			$return_array[] = $selections['fffl_player_id'];
		}
		
		return $return_array;
	}

//********************************************************************
	public function add_supplemental_selection($team_id,$fffl_player_id){
		//get teh max priority first
		$max = $this->db->select_max('priority')
							->where('team_id',$team_id)
							->get('Supplemental_Selections');
		if($max->num_rows() > 0	){
			$max = $max->result_array();	
			$priority = $max['0']['priority'] + 1;
		}
		else{
			$priority=1;	
		}
		$this->db->set('team_id',$team_id)
				->set('priority',$priority)
				->set('fffl_player_id',$fffl_player_id)
				->insert('Supplemental_Selections');
		
	}
	
	//********************************************************************
	public function remove_supplemental_selection($team_id,$fffl_player_id){
		//get the priority first
		$player_priority = $this->db->select('priority')
									->where('team_id',$team_id)
									->where('fffl_player_id',$fffl_player_id)
									->get('Supplemental_Selections');
		if($player_priority->num_rows() > 0	){
			$player_priority = $player_priority->result_array();	
			$priority = $player_priority['0']['priority'];
		}
		else{
			return FALSE;	
		}
		$this->db->where('team_id',$team_id)
				->where('fffl_player_id',$fffl_player_id)
				->delete('Supplemental_Selections');
				
		//renumber the player with a higher priority
		$this->db->where('team_id',$team_id)
				->where('priority>'.$priority)
				->order_by('priority')
				->set('priority','priority-1')
				->update('Supplemental_Selections');
		
	}
	
//*****************************************************************
	public function get_next_draft_details($league_id){
		
		$query=$this->db->select('draft_id,start_time,type,status')
					->where('league_id',$league_id)
					->group_start()
						->or_where('start_time>'.now())
						->or_where('status',1)
					->group_end()
					->order_by('start_time','ASC')
					->limit(1)
					->get('Drafts');
		foreach($query->result_array() as $draft_data){
			return array('draft_id'=>$draft_data['draft_id'],'start_time'=>$draft_data['start_time'],'type'=>$draft_data['type'],'status'=>$draft_data['status']);
		}
	}

//*******************************************************************
	//returns 2 if the team is in autodraft, 1 if auto for 1 pick, 0 if not
	public function is_autodraft($team_id,$draft_id){
		
		$this->db->where('team_id',$team_id)
					->where('draft_id',$draft_id);
		$count_all = $this->db->count_all_results('Draft_Lists');
		$count_auto_off = $this->db->where('autodraft',0)
								->where('team_id',$team_id)
								->where('draft_id',$draft_id)
								->count_all_results('Draft_Lists');
		
		$count_auto_one = $this->db->where('autodraft',1)
								->where('team_id',$team_id)
								->where('draft_id',$draft_id)
								->count_all_results('Draft_Lists');
		if($count_all==0 || $count_auto_off>0){
			return 0;
		}
		elseif($count_auto_one>0) {
			return 1;	
		}
		else{
			return 2;	
		}
	}
									
//*******************************************************************
	//returns the palyers in a teams draft list by priority
	public function team_draft_list($team_id,$draft_id){
		$query = $this->db->where('team_id',$team_id)
							->where('draft_id',$draft_id)
							->order_by('priority','ASC')
							->get('Draft_Lists');
		$return_array = array();
		foreach($query->result_array() as $data){
			$return_array[]=$data['fffl_player_id'];	
			
		}
		return $return_array;
		
	}
			
					
//********************************************************************
	//adds a plyer to a teams draft list for a draft_id
	public function add_draft_selection($team_id,$fffl_player_id,$draft_id){
		//get teh max priority first
		//$max = $this->db->select_max('priority')
		//					->where('team_id',$team_id)
		//					->where('draft_id',$draft_id)
		//					->get('Draft_Lists');
		//if($max->num_rows() > 0	){
		//	$max = $max->result_array();	
		//	$priority = $max['0']['priority'] + 1;
		//}
		//else{
			$priority=1;	
		//}
			//temp until lists functionallity can be restored. Problem might have been the submit pick button being stuck in submit mode. 
			$this->db->where('team_id',$team_id)
					->where('draft_id',$draft_id)
					->delete('Draft_Lists');
			//end temp solution
		//determine what to set autodraft to
		$autodraft = $this->is_autodraft($team_id,$draft_id);
		
		$this->db->set('team_id',$team_id)
				->set('priority',$priority)
				->set('fffl_player_id',$fffl_player_id)
				->set('draft_id',$draft_id)
				->set('autodraft',0)
				->insert('Draft_Lists');
	}
//********************************************************************
	public function remove_draft_selection($team_id,$fffl_player_id,$draft_id){
		//get the priority first
		$player_priority = $this->db->select('priority')
									->where('team_id',$team_id)
									->where('draft_id',$draft_id)
									->where('fffl_player_id',$fffl_player_id)
									->get('Draft_Lists');
		if($player_priority->num_rows() > 0	){
			$player_priority = $player_priority->result_array();	
			$priority = $player_priority['0']['priority'];
		}
		else{
			return FALSE;	
		}
		$this->db->where('team_id',$team_id)
				->where('fffl_player_id',$fffl_player_id)
				->where('draft_id',$draft_id)
				->delete('Draft_Lists');
				
		//renumber the player with a higher priority
		$this->db->where('team_id',$team_id)
				->where('priority>'.$priority)
				->where('draft_id',$draft_id)
				->order_by('priority')
				->set('priority','priority-1')
				->update('Draft_Lists');
		
	}

//*******************************************************************
	//gets the current pick of a draft
	public function get_current_pick($draft_id){
		
		$query = $this->db->where('fffl_player_id',0)
							->where('draft_id',$draft_id)
							->order_by('pick_number','ASC')
							->limit(1)
							->get('Draft_Picks');
							
		foreach($query->result_array() as $row){
			return array('pick_id'=>$row['pick_id'],'pick_number'=>$row['pick_number']);	
		}
	}

//******************************************************************
	//makes a draft pick
	public function make_draft_pick($team_id,$draft_id,$pick_id){
		//get the first player in team's draft list for this draft
		$draft_list = $this->team_draft_list($team_id,$draft_id);
		$next_player = reset($draft_list);
		//make the pick
		//update Draft_Picks
		
		$this->db->where('pick_id',$pick_id)
				->set('fffl_player_id',$next_player)
				->update('Draft_Picks');
		//delete from all Draft_Lists for this draft
		$this->db->where('fffl_player_id',$next_player)
					->where('draft_id',$draft_id)
					->delete('Draft_Lists');		
		//add to roster
		$position = $this->Players->get_player_info(array($next_player),'fffl_player_id','position');
		$this->db->set('fffl_player_id',$next_player)
				->set('team_id',$team_id)
				->set('salary',0)
			->set('weeks_on_pup',0)
			->set('position',$position['position'])
			->set('lineup_area','Roster')
			->set('sub_priority',0)
				->insert('Rosters');
		return $next_player;
		
	}
	
	
//******************************************************************
	//makes a draft pick
	public function record_pick_pass($team_id,$draft_id,$pick_id){
		
		//update Draft_Picks
		
		$this->db->where('pick_id',$pick_id)
				->set('fffl_player_id',-1)
				->update('Draft_Picks');
		
		return -1;
		
	}
	
//******************************************************************
	public function reset_timer($draft_id){
		//reset the timer
				$this->db->set('timer_expiration',(now()+180))
						->where('draft_id',$draft_id)
						->update('Drafts');	
		
	}

//*********************************************************************
	public function get_passing_picks($draft_id){
		$query = $this->db->where('fffl_player_id',-1)
						->where('draft_id',$draft_id)
						->order_by('pick_number','ASC')
						->select('pick_id,current_owner')
						->get('Draft_Picks');
						
		$return_array=array();
		foreach($query->result_array() as $picks){
			$return_array[$picks['pick_id']]=$picks['current_owner'];	
		}
		return $return_array;
		
	}
	
//*********************************************************************
	public function pause_draft($draft_id,$pause_status){
		$this->db->where('draft_id',$draft_id)
				->set('paused',$pause_status)
				->update('Drafts');
		if($pause_status==0){//unpause the draft
			$this->reset_timer($draft_id);
		}
		
	}
	

//********************************************************************
	public function start_end_draft($draft_id,$action){
		$this->Database_Manager->database_backup(array('Draft_Picks'));
		$this->Database_Manager->database_backup(array('Drafts'));
		$this->Database_Manager->database_backup(array('Rosters'));
		$this->Database_Manager->database_backup(array('Trades'));
		$this->db->set('status',$action)
				->where('draft_id',$draft_id)
				->update('Drafts');
		if($action==1) { $this->reset_timer($draft_id); }
		
	}
	
	
}//end model


/*End of file Database_Manager.php*/
/*Location: ./application/models/Database_Manager.php*/