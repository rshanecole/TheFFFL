<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Trades Model.
	 *
	 * ?????
	 *		
	 */
	
Class Trades extends CI_Model 
{
	public function __construct() 
    {
		parent::__construct();
		//$ci = get_instance();
		$this->load->Model('Players');
		$this->load->Model('Drafts');
		$this->load->Model('Database_Manager');
		$this->load->Model('Salaries');
		$this->load->Model('Owners');
		$this->load->Model('Teams');
		$this->load->Model('Facebook_Interact');
		$this->load->Model('Calendars');
		
		$this->load->helper('string');
		$this->load->library('email');
	}

//****************************************************************************	
	//simply gets the first and last trade years for a league
	//currently used to create dropdown menu on trade  view
	public function get_first_last_trade_years($league_id){
		$max_query = $this->db->select_max('year','max')
				->where('league_id',$league_id)
				->get('Trades');
		$result = $max_query->row();
		$max = $result->max;
		
		$min_query = $this->db->select_min('year','min')
				->where('league_id',$league_id)
				->get('Trades');
		$result = $min_query->row();
		$min = $result->min;
		
		$return_array['last'] = $max;
		$return_array['first'] = $min;
		
		return $return_array;
		
	}

//*****************************************************************************	
	
	//get all trades for a year's filtered by team and completed
	public function get_trades_year($league_id,$year,$team,$completed){
		
		$return_array = array();
		
		$conditions = 'year = '.$year;
		//get the filtered trades
		if($team!='All'){
			$conditions .= ' and (offerend_by='.$team.' or offered_to='.$team.')';
		}
		
		if($completed=='Completed'){
			$conditions .= ' and approval_status=1';
		}
		elseif($completed=='Pending'){
			$conditions .= ' and approval_status=0';
		}
		
		
		$trades_query = $this->db->select('trade_id')
					->where($conditions)
					->get('Trades');
		
		//add the picks to the array for that year key
		foreach($trades_query->result() as $trade){
			
			$return_array[$trade->trade_id]=$this->get_trade_details($trade->trade_id,'Minimum');
			
		}
			
		
		
		if(count($return_array)>0){
			return $return_array;
		
		}
		else {
			return false;
		}
		
	}
	
//*********************************************************************************
	//gets all trade details, but chaning $detail_level to "Minimum" will jsut get teams involved and status/times
	//reverse switches the offerer and receiver. It does not reverse the traded items.  Only used for undoing a trade
	//to do the same trade in reverse
	
	public function get_trade_details($trade_id,$detail_level='All',$reverse=FALSE){
		
		$trade_query = $this->db->where('trade_id',$trade_id)
							->get('Trades');
		$trade = $trade_query->row();
		
		$return_array[$trade_id]['trade_id'] = $trade->trade_id;
		if($reverse){
			$return_array[$trade_id]['offered_by'] = $trade->offered_to;
			$return_array[$trade_id]['offered_to'] = $trade->offered_by;
		}
		else{
			$return_array[$trade_id]['offered_by'] = $trade->offered_by;
			$return_array[$trade_id]['offered_to'] = $trade->offered_to;
		}
		$return_array[$trade_id]['time_offered'] = $trade->time_offered;
		$return_array[$trade_id]['time_accepted_rejected'] = $trade->time_accepted_rejected;
		$return_array[$trade_id]['time_approved'] = $trade->time_approved;
		$return_array[$trade_id]['response_status'] = $trade->response_status;
		$return_array[$trade_id]['approval_status'] = $trade->approval_status;
		
		if($detail_level =='All'){
				
			//get players offered [id]=>pos name team
			if($trade->players_offered){
				$players = explode(',',$trade->players_offered);
				$return_array[$trade->trade_id]['players_offered']=$players;			
			}
			//get players recived [id]=>pos name team
			if($trade->players_received){
				$players = explode(',',$trade->players_received);
				$return_array[$trade->trade_id]['players_received']=$players;
			}
			//get draft_picks offered array of [pick_id]=>round, pick_number, timestamp of draft
			if($trade->draft_picks_offered){
				$picks = explode(',',$trade->draft_picks_offered);
				foreach($picks as $pick_id){
					$data = $this->Drafts->get_pick_details($pick_id);
					$return_array[$trade->trade_id]['draft_picks_offered'][$pick_id] = $data;
				}
			
			}
			//get draft_picks received array of [pick_id]=>round, pick_number, timestamp of draft
			if($trade->draft_picks_received){
				$picks = explode(',',$trade->draft_picks_received);
				foreach($picks as $pick_id){
					$data = $this->Drafts->get_pick_details($pick_id);
					$return_array[$trade->trade_id]['draft_picks_received'][$pick_id] = $data;
				}
			}
			
			$return_array[$trade_id]['comments'] = $trade->comments;
		}//end all details
		
		return $return_array[$trade_id];
	}//get details function
	
	
//***************************************************************************************************	
	
	public function insert_update_trade($offered_by,$offered_to,$players_offered,$draft_picks_offered,$players_received,$draft_picks_received,$comments='',$trade_id){
		
		$data = array(
				'league_id' => $this->Teams->get_team_league_id($offered_by),
				'year' => $this->current_year,
				'offered_by' => $offered_by,
				'offered_to' => $offered_to,
				'players_offered' => $players_offered,
				'draft_picks_offered' => $draft_picks_offered,
				'players_received' => $players_received,
				'draft_picks_received' => $draft_picks_received,
				'time_offered' => now(),
				'comments' => $comments
		);
				if($trade_id){
					
					$query = $this->db->where('trade_id',$trade_id)
								->update('Trades', $data);
					
				}
				else {
					$query = $this->db->insert('Trades', $data);
				}
				
				
			//send email
			$offer_user_id = $this->Teams->get_user_id($offered_by);
			$user_id = $this->Teams->get_user_id($offered_to);
			$email = $this->Owners->get_owner_email($user_id);
			$this->email->from('admin@thefffl.com', 'TheFFFL');
			$this->email->to($email);
			$this->email->subject('You have a trade offer');
			$this->email->set_mailtype("html");
			$first_name = $this->Owners->get_owner_first_name($user_id);
			$message = 'Hi '.$first_name.',<br><br>You, have a new trade offer from '.$this->Owners->get_owner_first_name($offer_user_id).'.<br>Please respond to the trade offer on the league website.';
			
			$message .='<br><br>Thanks,<br>The FFFL';
			$this->email->message($message);	
			$this->email->send();
				
		return $this->db->last_query();				
	
	}
	
//*******************************************************************************************************

	//deletes a trade
	public function delete_trade($trade_id){
		$this->db->where('trade_id',$trade_id)
				->delete('Trades');	
	}
	
//*******************************************************************************************************

	//accepts a trade
	public function accept_trade($trade_id){
		$this->db->where('trade_id',$trade_id)
				->set('time_accepted_rejected',now())
				->set('response_status',1)
				->update('Trades');	
      //email trade committee
				$trade_committee = $this->get_trade_committee($_SESSION['league_id']);
      
				foreach($trade_committee['team_id'] as $team){
					
					$user_id = $this->Teams->get_user_id($team);
					$email = $this->Owners->get_owner_email($user_id);
                 
					$this->email->from('admin@thefffl.com', 'TheFFFL');
					$this->email->to($email);
					$this->email->subject('There is a trade that needs your vote');
					$this->email->set_mailtype("html");
					$first_name = $this->Owners->get_owner_first_name($user_id);
					$message = 'Hi '.$first_name.',<br><br>You have a new trade to vote on.<br>Please vote on the trade on the league website.';
					
					$message .='<br><br>Thanks,<br>The FFFL';
                   
					$this->email->message($message);	
					$this->email->send();
				}
	}	

	
//*******************************************************************************************************

	//decline a trade
	public function decline_trade($trade_id){
		$this->db->where('trade_id',$trade_id)
				->set('time_accepted_rejected',now())
				->set('response_status',-1)
				->update('Trades');	
	}	

//*******************************************************************************************************

	//gets an array of the trade committee
	public function get_trade_committee($league_id){
		$query = $this->db->select('team_id, group')
				->where('league_id',$league_id)
				->get('Trade_Committee');
		$return_array=array();
		foreach($query->result() as $team){
			$return_array['team_id'][] = $team->team_id;	
			$return_array['group'][] = $team->group;
		}
		
		return $return_array;
	}	
	
	
//*******************************************************************************************************

	//updates a trade committee members vote on a trade
	public function add_trade_vote($trade_id,$committee_member_group,$vote){
		$query = $this->db->where('trade_id',$trade_id)
						->set($committee_member_group.'_decision',$vote)
						->update('Trades');
	}	
	

//***************************************************************************************************	
	
	//gets the votes of each member of the committee for a a trade
	//returns an array with kyes of for, against, team_id of each committee member, and an array call votes which 
	//has keys of the name of each group (eg AW NW, etc) with the value being the vote recorded
	public function committee_votes($trade_id){
		$committee_array = $this->get_trade_committee($_SESSION['league_id']);
		$return_array = array();
		$return_array['for']=0; $return_array['against']=0;
		foreach($committee_array['group'] as $key=>$group){
			$query=$this->db->select($group.'_decision')
					->where('trade_id',$trade_id)
					->get('Trades');
			$row = $query->row();
			$concat = $group.'_decision';
			$return_array['votes'][$group]=$row->$concat;
			$return_array[$committee_array['team_id'][$key]]=$row->$concat;
			if($row->$concat==1){
				$return_array['for']=$return_array['for']+1;
			}
			elseif($row->$concat==-1){
				$return_array['against']=$return_array['against']+1;
			}
		}
		
		return $return_array;
		
	}

//***************************************************************************************************	
	//big function. Finalizes trades
	//approves or disapproves a trade. determines if votes are enough, then approves or disapproves
	//allows auto deny or auto approve with second parameter
	public function trade_approval_check($trade_id,$auto=0,$undo=FALSE){
		$this->Database_Manager->database_backup(array('Trades','Rosters','Draft_Picks','Starting_Lineups'));
		//determine if the trade has passed with enough votes
		$votes_array = $this->committee_votes($trade_id);
		if($votes_array['for']==3 || $auto==1){
			$result=1;
			$facebook_message='Trade Approved: ';
			$facebook_players_offered='';
			$facebook_players_received='';
			//trade approved, execute the trade
			if($undo){
				$trade_detail_array = $this->get_trade_details($trade_id,'All',TRUE);
			}
			else {
				$trade_detail_array = $this->get_trade_details($trade_id,'All',FALSE);
			}
			// first check to see if any players are a match. a legal trade, but if done separately, it
			//will give both players to one team
			if(isset($trade_detail_array['players_offered'])){
				$same_players = array_intersect($trade_detail_array['players_offered'], $trade_detail_array['players_received']);
				if(count($same_players)>0){
					//there's at least one common player, take care of tehse first, and eliminate them from the array
					foreach($same_players as $fffl_player_id){
						//first compare their salaries, if the same leave them alone, if different, swap based on salary
						$salary_offered_by = $this->Salaries->get_player_team_salary($trade_detail_array['offered_by'],$fffl_player_id);
						$salary_offered_to = $this->Salaries->get_player_team_salary($trade_detail_array['offered_to'],$fffl_player_id);
						if($salary_offered_by != $salary_offered_to){
							//they are different, swap players by salary
							//set the first team to 0 to avoid mysql kicking back a duplicate Player-team entry
							$this->db->set('team_id',0)
									->where('team_id',$trade_detail_array['offered_to'])
									->where('salary',$salary_offered_to)
									->where('fffl_player_id',$fffl_player_id)
									->update('Rosters');
							$this->db->set('team_id',$trade_detail_array['offered_to'])
									->where('team_id',$trade_detail_array['offered_by'])
									->where('salary',$salary_offered_by)
									->where('fffl_player_id',$fffl_player_id)
									->update('Rosters');
							//undo the team 0
							$this->db->set('team_id',$trade_detail_array['offered_by'])
									->where('team_id',0)
									->where('salary',$salary_offered_to)
									->where('fffl_player_id',$fffl_player_id)
									->update('Rosters');	
							
							//delete from franchise in case he's been added
							if($this->current_week==0){
								$this->db->or_where('team_id',$trade_detail_array['offered_by'])
											->or_where('team_id',$trade_detail_array['offered_to'])
											->where('fffl_player_id',$fffl_player_id)
											->where('season',$this->current_year)
											->delete('Franchise');
							}
						}
						
						$facebook_players_offered .= player_name_no_link($fffl_player_id).', ';
						$facebook_players_received .= player_name_no_link($fffl_player_id).', ';
						//remove the player from both arrays
						if(($key = array_search($fffl_player_id, $trade_detail_array['players_offered'])) !== false) {
							unset($trade_detail_array['players_offered'][$key]);
						}
						if(($key = array_search($fffl_player_id, $trade_detail_array['players_received'])) !== false) {
							unset($trade_detail_array['players_received'][$key]);
						}
						
					}//foreach duplicate player
				}//if count same >0
			}//if players offered set
			
			if(isset($trade_detail_array['players_offered'])){
				//all duplicate players are now out of the arrays, swap the remaining for each array
				foreach($trade_detail_array['players_offered'] as $fffl_player_id){
					$this->db->set('team_id',$trade_detail_array['offered_to'])
							->where('team_id',$trade_detail_array['offered_by'])
							->where('fffl_player_id',$fffl_player_id)
							->update('Rosters');
					//delete player from starting lineup for current and upcoming weeks
							$this->db->where('team_id',$trade_detail_array['offered_by'])
									->where('week >= '.$this->current_week)
									->where('year',$this->current_year)
									->where('fffl_player_id',$fffl_player_id)
									->delete('Starting_Lineups');
									
					//delete from franchise list if it's before franchise deadline
					if($this->current_week==0){
							$this->db->where('team_id',$trade_detail_array['offered_by'])
								->where('fffl_player_id',$fffl_player_id)
								->where('season',$this->current_year)
								->delete('Franchise');
					}

									
					$facebook_players_offered .= player_name_no_link($fffl_player_id).', ';
				}
			}
			if(isset($trade_detail_array['players_received'])){
				foreach($trade_detail_array['players_received'] as $fffl_player_id){
					$this->db->set('team_id',$trade_detail_array['offered_by'])
							->where('team_id',$trade_detail_array['offered_to'])
							->where('fffl_player_id',$fffl_player_id)
							->update('Rosters');
					//delete player from starting lineup for current and upcoming weeks
							$this->db->where('team_id',$trade_detail_array['offered_to'])
									->where('week >= '.$this->current_week)
									->where('year',$this->current_year)
									->where('fffl_player_id',$fffl_player_id)
									->delete('Starting_Lineups');
					//delete from franchise list if it's before franchise deadline
					if($this->current_week==0){
							$this->db->where('team_id',$trade_detail_array['offered_to'])
								->where('fffl_player_id',$fffl_player_id)
								->where('season',$this->current_year)
								->delete('Franchise');
					}
									
					$facebook_players_received .= player_name_no_link($fffl_player_id).', ';
				}
			}
			
			//swap the traded draft_picks
            if(isset($trade_detail_array['draft_picks_offered'])){
				$facebook_picks_offered = '';
				foreach($trade_detail_array['draft_picks_offered'] as $pick_id=>$data){
					$this->db->set('current_owner',$trade_detail_array['offered_to'])
						->where('pick_id',$pick_id)
						->update('Draft_Picks');
					$facebook_picks_offered .= 'Rd. '.$data['round'].' (#'.$data['pick_number'].' '.date('D' ,$data['start_time']).'), ';
				}
            }
          	if(isset($trade_detail_array['draft_picks_received'])){
				$facebook_picks_received = '';
				foreach($trade_detail_array['draft_picks_received'] as $pick_id=>$data){
					$this->db->set('current_owner',$trade_detail_array['offered_by'])
						->where('pick_id',$pick_id)
						->update('Draft_Picks');
					$facebook_picks_received .= 'Rd. '.$data['round'].' (#'.$data['pick_number'].' '.date('D' ,$data['start_time']).'), ';
				}
            }
			
			
			
			if(!$undo){
				//post to FB
				$league_id = $this->Teams->get_team_league_id($trade_detail_array['offered_by']);
				$facebook_message .=  $this->Teams->get_team_name_first_nickname($trade_detail_array['offered_by']).' traded '.$facebook_players_offered.$facebook_picks_offered.' to '.$this->Teams->get_team_name_first_nickname($trade_detail_array['offered_to']).' for '.$facebook_players_received.$facebook_picks_received;
				$facebook = $this->Facebook_Interact->post_to_facebook($facebook_message,$league_id);
				
			}
			
			
			
		}
		elseif(($votes_array['against']==3 || $auto==-1)){
			$result=-1;
			
		}
		else{
			$result=0;	
		}
		
		if($undo) { $result=-1; }
		if($result!=0){
			$query = $this->db->where('trade_id',$trade_id)
							->set('approval_status',$result)
							->set('time_approved',now())
							->update('Trades');
							
		}

		
	}
	
//******************************************************************************************************
	public function append_comments($trade_id,$comments){
		$old_comments_query = $this->db->select('comments')
							->where('trade_id',$trade_id)
							->get('Trades');
		if(strpos($comments, '</a>: </strong><br>') !== false){
			$comments = '';
		}   	
	
		$old_comments_row = $old_comments_query->row();
		$old_comments = $old_comments_row->comments;
		
		$comments = $old_comments.$comments;
		
		$this->db->set("comments",$comments)
				->where('trade_id',$trade_id)
				->update('Trades');
				
		return $comments;
					
	}
	
//***********************************************************************************************************
	//simply returns the number of trades the team hasn't responded too yet
	public function get_team_open_trades($team_id,$year){
		$return = $this->db->where('offered_to',$team_id)
							->where('year',$year)
							->where('response_status',0)
							->count_all_results('Trades');
							
		return $return;
	}
	
  
 //***********************************************************************************************************
	//simply returns the trade_ids for completed trades for a team
	public function get_team_trades($team_id,$year){
		$return = $this->db->group_start()
								->or_where('offered_to',$team_id)
								->or_where('offered_by',$team_id)
							->group_end()
							->where('year',$year)
							->where('approval_status',1)
							->get('Trades');
		$return_array = array();
		foreach($return->result_array() as $trade_data){
			$return_array[] = $trade_data['trade_id'];
		}
							
		return $return_array;
	}


 //***********************************************************************************************************
	//simply returns the trade_ids for completed trades for a team
	public function get_player_trades($fffl_player_id,$league_id){
		$return = $this->db->group_start()
								->or_where("players_offered LIKE '%".$fffl_player_id."%'")
								->or_where("players_received LIKE '%".$fffl_player_id."%'")
							->group_end()
							->where('league_id',$league_id)
							->where('approval_status',1)
							->get('Trades');
		$return_array = array();
		foreach($return->result_array() as $trade_data){
			$return_array[] = $trade_data['trade_id'];
		}
							
		return $return_array;
	}
	

  //********************************************************************************
  
	  public function get_is_trading_open($league_id){
			//get opening time of trading season, training camp
			$trading_begins = $this->Calendars->get_calendar_time('trainingcamp',$league_id);
		
			//get trade deadline
			$trade_deadline = $this->Calendars->get_trade_deadline($league_id);
			
			//compare to now()
			if($trading_begins < now() && $trade_deadline>now()){
				return TRUE;
				
			}
			else {
				return FALSE;
				
			}
	  }
  
  //**********************************************************************************
  
  	public function decline_all_open_trades($league_id){
		$this->db->where('response_status',0)
					->where('approval_status',0)
					->where('league_id',$league_id)
					->set('response_status',-1)
					->update('Trades');
		
	}
	
	//*****************************************************************************
	public function get_team_player_open_trade($team_id,$fffl_player_id){
		$query = $this->db->group_start()
								->or_where("players_offered LIKE '%".$fffl_player_id."%'")
								->or_where("players_received LIKE '%".$fffl_player_id."%'")
							->group_end()
							->group_start()
								->or_where("offered_by",$team_id)
								->or_where("offered_to",$team_id)
							->group_end()
							->where('response_status',0)
							->get('Trades');
		$return_array = array();
		foreach($query->result_array() as $trade_data){
			$return_array[]=$trade_data['trade_id'];	
		}
		return $return_array;
	}
  
}//end of class Trades





/*End of file Trades.php*/
/*Location: ./application/models/Teams.php*/