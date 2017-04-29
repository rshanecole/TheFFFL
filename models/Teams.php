<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Teams Model.
	 *
	 * ?????
	 *		
	 */
	
Class Teams extends CI_Model 
{
	public function __construct() 
    {
		parent::__construct();
		//$ci = get_instance();

		$this->load->model('Standings');
		$this->load->helper('string');
	}

//*******************************************************
	
	public function get_team_league_id($team_id){
		$this->db->select('league_id');
		$this->db->from('Teams');
		$this->db->where('team_id',$team_id);
		$this->db->limit(1);
		$query_league_id = $this->db->get();
		if ($query_league_id->num_rows() === 1) 
		{
			return $query_league_id->row('league_id');
		}
		else 
		{
			return NULL;
		}
	}
	

//*************************************************************
	
	public function get_user_id($team_id)
	{
		$this->db->select('user_id');
		$this->db->from('Teams');
		$this->db->where('team_id',$team_id);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() === 1) 
		{
			return $query->row('user_id');
		}
		else 
		{
			return NULL;
		}
	}

//******************************************************************
	
	//gets alll active team ids and returns an array of them all
	public function get_all_team_id($league_id){
		$this->db->select('team_id');
		$this->db->from('Teams');
		$this->db->where('league_id',$league_id);
		$this->db->order_by('team_name');
		$query = $this->db->get();
		if ($query->num_rows() > 0) 
		{
			foreach($query->result_array() as $team_id){
				$result[]=$team_id['team_id'];
			}
			return $result;
		}
		else 
		{
			return NULL;
		}
	}


//**************************************************************
	//returns current conf and division alignments
	public function get_all_teams_by_division_id_nickname($league_id){
		$return_array = array();
		$team_ids = $this->get_all_team_id($league_id);
		foreach($team_ids as $team_id){
			$conference_division = $this->get_team_conference_division($team_id);
			$return_array[$conference_division['conference']][$conference_division['division']][$team_id]=$this->get_team_name_first_nickname($team_id);
		}
		return $return_array;
	}


//****************************************************************	
	//returns an array of [conference] and [division]
	public function get_team_conference_division($team_id,$year=NULL){
		if(is_null($year)){ $year=$this->current_year; }
		$this->db->select('conference, division');
		$this->db->from('Teams_Seasons');
	
		$this->db->where('team_id',$team_id);
		$this->db->where('year',$year);
		$this->db->limit(1);
		$query = $this->db->get();
		$result_array = array();
		
		$result_array['conference']=$query->row('conference');
		$result_array['division']=$query->row('division');
		return $result_array;
	}
	

//***********************************************************
		
	public function get_team_id($user_id,$league_id)
	{
		$this->db->select('team_id');
		$this->db->from('Teams');
		$this->db->where('user_id',$user_id);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() === 1) 
		{
			return $query->row('team_id');
		}
		else 
		{
			return NULL;
		}
	}


//******************************************************************

	public function get_team_name_first_nickname($team_id, $first=TRUE,$nick=TRUE ) {
		$this->db->select('user_id, team_name');
		$this->db->from('Teams');
		$this->db->where('team_id',$team_id);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() === 1) 
		{
			$nickname = $query->row('team_name');
		
			$this->db->select('first_name');
			$this->db->from('Owners');
			$this->db->where('user_id',$query->row('user_id'));
			$this->db->limit(1);
			$query = $this->db->get();
			if ($query->num_rows() === 1) 
			{
				
				if(!$first) {$fist='';} else { $first = $query->row('first_name').' '; }
				if(!$nick) { $nickname=''; }
				$team_name_first_nickname = $query->row('first_name').' '.$nickname;
				return $team_name_first_nickname;
			}
			else
			{
				return NULL;
			}
		}
		else 
		{
			return NULL;
		}
		
	}
	

//************************************************************************
	
	//gets all the teams in an array of team_id => first name nickname
	public function get_all_teams_id_to_first_nickname($league_id){
		$all_team_ids = $this->get_all_team_id($league_id);
		$all_teams=array();
		foreach($all_team_ids as $team_id){
			
			$all_teams[$team_id] = $this->get_team_name_first_nickname($team_id);	
		}	
		return $all_teams;
	}

//**************************************************************************	
	//get team's first year
	public function get_team_first_year($team_id){
		$this->db->select('first_season');
		$this->db->from('Teams');
		$this->db->where('team_id',$team_id);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() === 1) 
		{
			return $query->row('first_season');
		}
		else 
		{
			return NULL;
		}
	}

//*******************************************************************************

	//get team's nonconsecutive years, that they missed from first year to last season
		public function get_team_non_consecutive_years($team_id){
			$this->db->select('non_consecutive_seasons');
			$this->db->from('Teams');
			$this->db->where('team_id',$team_id);
			$this->db->limit(1);
			$query = $this->db->get();
			if ($query->num_rows() === 1) 
			{
				return $query->row('non_consecutive_seasons');
			}
			else 
			{
				return NULL;
			}
		}

	
//**********************************************************************************

	//gets the path for a team's logo
	//doesn't interact with database but provides one spot
	//to have to change if changes are made to file structure
	public function get_team_logo_path($team_id) {
		return base_url().'assets/img/team_logos/team_'.$team_id;

	}
	

//*********************************************************************************
 	//gets team wins and losses stats
	public function get_team_stats($teams){
		
		if(!is_array($teams)){
			$teams = array($teams);	
		}
		
		//adds array for each stats category and year for that team
		$stats_array = array('max_wins_reg'=>array(),'max_points_reg'=>array(),'min_losses_reg'=>array(),'min_losses_all'=>array(),'max_streak_season'=>array(),'max_streak_all'=>array(),'max_points_game'=>array(),'max_points_superbowl'=>array());
		$years_array = array('max_wins_reg'=>array(),'max_points_reg'=>array(),'min_losses_reg'=>array(),'min_losses_all'=>array(),'max_streak_season'=>array(),'max_streak_all'=>array(),'max_points_game'=>array(),'max_points_superbowl'=>array());
		
		//each team
		foreach($teams as $team_id){
			
			//get all games for the team
			$query = $this->db->or_where('opponent_a',$team_id)
					->or_where('opponent_b',$team_id)
					->order_by("year,week")
					->get("Games");
					
			//initialize indexes for this team
			//maximums
			$stats_array['max_wins_reg'][$team_id]=$stats_array['max_wins_all'][$team_id]=$stats_array['max_points_reg'][$team_id]=$stats_array['max_points_all'][$team_id]=$stats_array['max_points_playoffs'][$team_id]=$stats_array['max_points_game'][$team_id]=$stats_array['max_streak_all'][$team_id]=$stats_array['max_streak_season'][$team_id]=$stats_array['max_points_superbowl'][$team_id]=0;
			//minimums
			$stats_array['min_losses_reg'][$team_id]=$stats_array['min_losses_all'][$team_id]=99;
			 
			
			$year=$reg_wins=$all_wins=$streak_season=$streak_all=$streak_all_start=$streak_all_end= 0;
			$reg_losses = $all_losses = 100;
			//each game that team is involved in. Will detect in the foreach
			//when it begins looking at a different season 
			foreach($query->result_array() as $game){
				
				//get letter team represents in case needed later
				if($game['opponent_a']==$team_id){
					$letter='a';
				}
				else{
					$letter='b';
				}
				
				
				//determine if we keep counting wins for this year or start over for a different year
				//also establish points for that year
				if($year != $game['year']){
					$streak_season=$streak_season_start=$streak_season_end=0;
					//fisrt time through neither of these can be true becasue all_losses=100 and stats_array =99
					if($all_losses<$stats_array['min_losses_all'][$team_id] && $made_playoffs){
						$stats_array['min_losses_all'][$team_id] = $all_losses;
						$years_array['min_losses_all'][$team_id]=array($year);
					}
					elseif($all_losses==$stats_array['min_losses_all'][$team_id] && $made_playoffs){
						$years_array['min_losses_all'][$team_id][]=$year;
					}
					//regular season losses.  same logic as above
					if($reg_losses<$stats_array['min_losses_reg'][$team_id]){
						$stats_array['min_losses_reg'][$team_id] = $reg_losses;
						$years_array['min_losses_reg'][$team_id]=array($year);	
					}
					elseif($reg_losses==$stats_array['min_losses_reg'][$team_id]){
						$years_array['min_losses_reg'][$team_id][]=$year;
					}
					$made_playoffs=FALSE;
					
					$year = $game['year'];
					$reg_wins = $all_wins = 0;
					$reg_losses = $all_losses = 0;
					
					//most points in a regular season, points and years they occured
					//get points for new year regular season
					$points_reg = $this->Standings->get_team_total_points_year($team_id, $year,FALSE);
					if($points_reg>$stats_array['max_points_reg'][$team_id]){
						$stats_array['max_points_reg'][$team_id] = $points_reg;
						$years_array['max_points_reg'][$team_id]=array($year);	
					}
					elseif($points_reg==$stats_array['max_points_reg'][$team_id]){
						$years_array['max_points_reg'][$team_id][]=$year;
					}
					
					//most points in a season, playoffs included points and years they occured
					//get points for new year all season including playoffs
					$points_all = $this->Standings->get_team_total_points_year($team_id, $year,TRUE);
					if($points_reg>$stats_array['max_points_all'][$team_id]){
						$stats_array['max_points_all'][$team_id] = $points_all;
						$years_array['max_points_all'][$team_id]=array($year);	
					}
					elseif($points_reg==$stats_array['max_points_all'][$team_id]){
						$years_array['max_points_all'][$team_id][]=$year;
					}
					
					//most points in a playoffs only
					$points_playoffs = $points_all - $points_reg;
					if($points_playoffs>$stats_array['max_points_playoffs'][$team_id]){
						$stats_array['max_points_playoffs'][$team_id] = $points_playoffs;
						$years_array['max_points_playoffs'][$team_id]=array($year);	
					}
					elseif($points_reg==$stats_array['max_points_playoffs'][$team_id]){
						$years_array['max_points_playoffs'][$team_id][]=$year;
					}
				}
				
				//get score for game, compare to team's max score				
				$score = $game["opponent_".$letter."_score"];
				if($score>$stats_array['max_points_game'][$team_id]){
					$stats_array['max_points_game'][$team_id] = $score;
					$years_array['max_points_game'][$team_id]=array($year.' wk. '.$game['week']);	
				}
				elseif($score==$stats_array['max_points_game'][$team_id]){
					$years_array['max_points_game'][$team_id][]=$year.' wk. '.$game['week'];
				}
				//superbowl
				if($game['week']==16 && $game['is_playoff']==1){
					if($score>$stats_array['max_points_superbowl'][$team_id]){
						$stats_array['max_points_superbowl'][$team_id] = $score;
						$years_array['max_points_superbowl'][$team_id]=array($year);	
					}
					elseif($score==$stats_array['max_points_superbowl'][$team_id]){
						$years_array['max_points_superbowl'][$team_id][]=$year;
					}
				}
				
				
				
				//accumulate wins stats
				if($game['winner']==$team_id){
					
					//add to winning streaks
					if($game['year']>=2004){
						$streak_all++;
						$streak_season++;
						
						if($streak_season_start==0){
							$streak_season_start=$game['week'];
						}
						if($streak_all_start==0){
							$streak_all_start=$game['year'].' wk. '.$game['week'];
						}
						$streak_all_end=$game['year'].' wk. '.$game['week'];
						$streak_season_end=$game['week'];
						
						//in case last win of season is week 16, end the season streak now
						if($game['week']==16){
							if($streak_season>$stats_array['max_streak_season'][$team_id]){
								$stats_array['max_streak_season'][$team_id] = $streak_season;
								$years_array['max_streak_season'][$team_id]=array($year.' wks. '.$streak_season_start.'-'.$streak_season_end);	
							}
							elseif($streak_season==$stats_array['max_streak_season'][$team_id]){
								$years_array['max_streak_season'][$team_id][]=$year.' wks. '.$streak_season_start.'-'.$streak_season_end;
							}
							$streak_season=$streak_season_start=$streak_season_end=0;
						}
						
					}
					
					//most wins in a season with playoffs
					$all_wins++;
					if($all_wins>$stats_array['max_wins_all'][$team_id]){
						$stats_array['max_wins_all'][$team_id] = $all_wins;
						$years_array['max_wins_all'][$team_id]=array($year);	
					}
					elseif($all_wins==$stats_array['max_wins_all'][$team_id]){
						$years_array['max_wins_all'][$team_id][]=$year;
					}
					
					//most wins in a regular season, wins and years they occured
					if($game['is_playoff']==0){
						$reg_wins++;
						$made_playoffs=TRUE;
						if($reg_wins>$stats_array['max_wins_reg'][$team_id]){
							$stats_array['max_wins_reg'][$team_id] = $reg_wins;
							$years_array['max_wins_reg'][$team_id]=array($year);	
						}
						elseif($reg_wins==$stats_array['max_wins_reg'][$team_id]){
							$years_array['max_wins_reg'][$team_id][]=$year;
						}
					}
				}
				//accumulate loss stats
				else {
					//min losses in a season with playoffs
					$all_losses++;
					
					//most wins in a regular season, wins and years they occured
					if($game['is_playoff']==0){
						$reg_losses++;
					}
					else{
						$made_playoffs=TRUE;	
					}
					
					//end winning streaks
					if($game['year']>=2004){
						if($streak_all>$stats_array['max_streak_all'][$team_id]){
							$stats_array['max_streak_all'][$team_id] = $streak_all;
							$years_array['max_streak_all'][$team_id]=array($streak_all_start.'-'.$streak_all_end);	
						}
						elseif($streak_all==$stats_array['max_streak_all'][$team_id]){
							$years_array['max_streak_all'][$team_id][]=$streak_all_start.'-'.$streak_all_end;
						}
						if($streak_season>$stats_array['max_streak_season'][$team_id]){
							$stats_array['max_streak_season'][$team_id] = $streak_season;
							$years_array['max_streak_season'][$team_id]=array($year.' wks. '.$streak_season_start.'-'.$streak_season_end);	
						}
						elseif($streak_season==$stats_array['max_streak_season'][$team_id]){
							$years_array['max_streak_season'][$team_id][]=$year.' wks. '.$streak_season_start.'-'.$streak_season_end;
						}
						$streak_all=$streak_season=$streak_all_end=$streak_all_start=$streak_season_start=$streak_season_end=0;
					}
					
				}
			}	
			

				
				
			
		}
		
		return array("stats_array"=>$stats_array,"years_array"=>$years_array);

	}
	
	//************************************************************
	public function get_team_career_stats($teams){
		
		if(!is_array($teams)){
			$teams = array($teams);	
		}
		//adds array for each stats category and year for that team
		$stats_array = array('max_wins_reg'=>array(),'max_points_reg'=>array(),);
		
		
		//each team
		foreach($teams as $team_id){
			
			
		}
		
		
	}
	
	
}


/*End of file Teams.php*/
/*Location: ./application/models/Teams.php*/