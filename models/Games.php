<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Games Model.
	 *
	 * ?????
	 *		
	 */
	
Class Games extends CI_Model 
{
	public function __construct() 
    {
		parent::__construct();
		//$ci = get_instance();

		$this->load->helper('string');
		$this->load->model('Database_Manager');	

		
	}
	
	//**********************************************************
	
	//gets the outcome for a game for a team and week and year
	//returns true if a win, false if a loss
	public function get_result_team_week_year($team_id,$week,$year){
			
		$this->db->select('winner')
				->group_start
					->or_where('opponent_a',$team_id)
					->or_where('opponent_b',$team_id)
				->group_end
				->where('week',$week)
				->where('year',$year)
				->get('Games');
		foreach($query->result_array() as $winner){
			if($winner==$team_id) { return TRUE; }
			else { return FALSE; }	
		}
		
	}
	
	//**************************************************************
	
	public function get_playoffs_results_year($league_id,$year){
		$query = $this->db->where('	league_id',$league_id)
							->where('year',$year)
							->where('is_playoff',1)
							->order_by('week','ASC')
							->get('Games');
		$return_array = array();					
		foreach($query->result_array() as $data){
			if($data['winner']==$data['opponent_a']){ $loser = $data['opponent_b']; } else { $loser = $data['opponent_a']; }
			$return_array[$data['week']][] = array('opponent_a'=>$data['opponent_a'],'opponent_b'=>$data['opponent_b'],'opponent_a_score'=>$data['opponent_a_score'],'opponent_b_score'=>$data['opponent_b_score'],'winner'=>$data['winner'],'loser'=>$loser);
		}
		return $return_array;
	}
	//**************************************************************
	
	public function get_toilet_bowl_results_year($league_id,$year){
		$query = $this->db->where('	league_id',$league_id)
							->where('year',$year)
							->order_by('week','DESC')
							->order_by('opponent_score','DESC')
							->get('Toilet_Bowls');
		$return_array = array();				
		$week = 0;	
		foreach($query->result_array() as $data){
			if($week == 0) {
				$week = $data['week'];	
			}
			
			if($data['week']==$week){
				if($data['winner']!=$data['opponent']){ 
					$return_array[$data['week']]['loser'] =$data['opponent'];
					$return_array[$data['week']]['loser_score']=$data['opponent_score'];
				} 
				else{ 
					$return_array[$data['week']]['winner'] =$data['opponent'];
					$return_array[$data['week']]['winner_score']=$data['opponent_score'];
				} 
			}
			
		}
		return $return_array;
	}
	
	//**************************************************************
	
	public function get_pro_bowl_results_year($league_id,$year){
		$query = $this->db->where('league_id',$league_id)
							->where('year',$year)
							->where('team_id>0')
							->order_by('score','DESC')
							->limit(1)
							->get('Probowl');
							
		$return_array = array();				
			
		foreach($query->result_array() as $data){
			$return_array['winner'] =$data['team_id'];
			$return_array['winner_score']=$data['score'];

		}
		
		return $return_array;
	}	
	
	//**************************************************************
	
	public function get_team_last_game($team_id,$year){
		
		$superbowl_week = $this->Leagues->get_superbowl_week(1);//***NI**** add league
		$conditions = "(opponent_a =".$team_id." or opponent_b =".$team_id.") and opponent_b>0 and year=".$year." and winner>0";
		$this->db->select_max('week','max');
		$this->db->where($conditions);
		$get_last_game = $this->db->get('Games');
		$last_game = $get_last_game->row('max');
			
		return $last_game;
		
	}

//*****************************************************
	public function get_team_streak($team_id,$year,$streak=0){
		
		$query = $this->db->select('winner')
							->where('year',$year)
							->where('winner>0')
							->group_start()
								->or_where('opponent_a',$team_id)
								->or_where('opponent_b',$team_id)
							->group_end()
							->order_by('week','desc')
							->get('Games');
		foreach($query->result_array() as $winner){
			if($winner['winner']==$team_id && $streak>-1){ 	
				if($streak!=0){
					$streak++;
				}
				else{
					$streak=1;	
				}	
			}
			elseif($winner['winner']!=$team_id && $streak<1){ 
				if($streak!=0){
					$streak--;
				}
				else{
					$streak=-1;
				}		
			}
			else{
				return $streak;
					
			}

		}
		
		return $this->get_team_streak($team_id,$year-1,$streak);
		
	}
	
//*****************************************************
	public function get_week_best_score($league_id,$year,$week='All'){
		
		foreach(array('a','b') as $letter){
			$this->db->select_max('opponent_'.$letter.'_score','max')
								->where('year',$year);
				if($week != 'All'){
					$this->db->where('week',$week);
				}
			$query = $this->db->get('Games');
			foreach($query->result_array() as $max){
				$$letter = $max['max'];
			}
		}
		if($a>$b){ $max = $a; $team = 'a'; } else { $max=$b; $team = 'b';}
		
		//get the team that scored this first
		$this->db->where('opponent_'.$team.'_score',$max)
						->where('year',$year)
						->order_by('week','ASC');
			if($week != 'All'){
				$this->db->where('week',$week);
			}	
		$query = $this->db->get('Games');
		
		foreach($query->result_array() as $score_data){
			return array('team_id'=>$score_data['opponent_'.$team],'score'=>$max, 'week'=>$score_data['week']);
		}

	}
	
//***************************************************************
	
	//determines the winner for each game and updates teh Games table with the winner and scores
	//used at the end of a week
	public function finalize_games($week, $year){
		//get array of each game
		$games_query = $this->db->where('week',$week)
				->where('year',$year)
				->get('Games');
				
		//foreach game
		foreach($games_query->result_array() as $game_data){
			//get the two teams
			$team_a = $game_data['opponent_a'];
			$team_b = $game_data['opponent_b'];
			
			$array = array('a','b');
			//foreach team in the game
			foreach($array as $letter){
				//get each team's score, store in games table
				$team_id = 'team_'.$letter; //use $$team_id to refer to the team's id
				$score = 'score_'.$letter; //use $$score for score_a and score_b
				$decimal = 'decimal_'.$letter; //ditto
				$score_array = $this->calculate_team_game_score($$team_id, $week, $year);
				$$score = $score_array['score'];
				$$decimal = $score_array['decimal'];
				//store the scores in the table
				$this->update_game_team_score($year,$week,$$team_id,$letter,$score_array['score'],$score_array['decimal']);
			}

			//if scores are teh same get teh decimal values and add to team scores
			if($score_a == $score_b){
				$score_a = $score_a + $decimal_a;
				$score_b = $score_b + $decimal_b;
				
			}

			//determine the greater score, store as winner
			if($score_a > $score_b){ 
				$winner = $team_a;
			}
			else{
				$winner = $team_b;
			}
			
			//set the winner in the db
			$this->db->set('winner',$winner)
					->where('year',$year)
					->where('week',$week)
					->where('opponent_a',$team_a)
					->where('opponent_b',$team_b)
					->update('Games');
			
		}//end of foreach game
		
	}
	
//***************************************************************
	
	//adds the scores of a team's starting lineup for a game and return array of score and decimal
	public function calculate_team_game_score($team_id, $week, $year){
		$total_score = 0;
		$total_decimal = 0;
		
		//get the team's starting roster for the week
		
		$starters = $this->Rosters->get_team_starters($team_id, $week, $year);
		
		//foreach player get the player's score
		foreach($starters as $fffl_player_id){
			$score_array = $this->NFL_stats->get_player_scores_season($year,$fffl_player_id,0,$week,$week,0);
			$total_score = $total_score + $score_array['weeks'][$week]['points'];
			$total_decimal = $total_decimal + $score_array['weeks'][$week]['decimal'];
		}

		return array('score'=>$total_score,'decimal'=>$total_decimal);
	}

//****************************************************************

	//stores the score and decimal of a team in the opponent_a or oppoent_b score in Games table
	public function update_game_team_score($year,$week,$team_id,$a_b,$score,$decimal,$table="Games"){
		
		$a_b = "_".$a_b;
		
		if($table=="Toilet_Bowls" || $table=="Probowl" ) { $a_b=""; }
		if($table=="Probowl"){$team_string = "team_id"; } else { $team_string="opponent".$a_b; }
		//d($score,$decimal,$team_id,$table,$week,$year);
		$this->db->set('opponent'.$a_b.'_score',$score)
				->set('opponent'.$a_b.'_dec',$decimal)
				->where('week',$week)
				->where('year',$year)
				->where($team_string,$team_id)
				->update($table);
	}
	
//**************************************************************
	//gets array of all the week's games with team_a, team_a score, team_a decimal and same for b, and winner
	public function get_week_games($league_id,$year,$week){
		if($week<17){ //not probowl
			$query = $this->db->where('year',$year)
							->where('league_id',$league_id)
							->where('week',$week)
							->order_by('priority','ASC')
							->get('Games');
			$return_array = $query->result_array();	
				
			//toiletbowl games, include toilet bowl if first playoff week
			$toilet_bowl_array=array();
			if($week >= $this->Leagues->get_first_playoff_week($league_id)){
				$query = $this->db->where("year",$year)
									->where("league_id",$league_id)
									->where("week",$week)
									->select("opponent, opponent_score, opponent_dec")
									->get("Toilet_Bowls");
				$query_array = $query->result_array();
				$letter = "a";
				$toilet_bowl_array = array();
				foreach($query_array as $toilet_team){
					$opponent_string = "opponent_".$letter;
					$$opponent_string = $toilet_team['opponent'];
					$opponent_score = $opponent_string."_score";
					$$opponent_score = $toilet_team['opponent_score'];
					$opponent_dec = $opponent_string."_dec";
					$$opponent_dec = $toilet_team['opponent_dec'];
					
					if($letter=="b"){
						$toilet_bowl_array[] = array(
								"league_id"=>$league_id,
								"year"=>$year,"week"=>$week,
								"is_playoff"=>0,
								"priority"=>"100",
								"opponent_a"=>$opponent_a,"opponent_b"=>$opponent_b,
								"opponent_a_score"=>$opponent_a_score,"opponent_b_score"=>$opponent_b_score,
								"opponent_a_dec"=>$opponent_a_dec,"opponent_b_dec"=>$opponent_b_dec,
								"winner"=>"0",
								"is_toilet"=>"1"
						);
					}
					
					if($letter=="a"){$letter="b";}else{$letter="a";}
				}
				$return_array = array_merge($return_array,$toilet_bowl_array);
			}
		}
		//probowl		
		else {
			$pro_bowl_array=array();
			
			$query = $this->db->where("year",$year)
								->where("league_id",$league_id)
								->order_by("team_id","ASC")
								->get("Probowl");
			$query_array = $query->result_array();
			$letter = "a";
			
			if(count($query_array)%2 == 1){
				$merge = array();
				$merge[]=array('team_id'=>'-1');
				$query_array = array_merge($query_array,$merge);	
				
			}
			
			foreach($query_array as $pro_team){
				if($pro_team['team_id']!=-1){
					$opponent_string = "opponent_".$letter;
					$$opponent_string = $pro_team['team_id'];
					$opponent_score = $opponent_string."_score";
					$$opponent_score = $pro_team['opponent_score'];
					$opponent_dec = $opponent_string."_dec";
					$$opponent_dec = 0.0;
					
					
				}
				else {
					$opponent_string = "opponent_".$letter;
					$$opponent_string = -1;
					$opponent_score = $opponent_string."_score";
					$$opponent_score = 0;
					$opponent_dec = $opponent_string."_dec";
					$$opponent_dec = 0.0;
				}
				if($letter=="b"){
					$pro_bowl_array[] = array(
							"league_id"=>$league_id,
							"year"=>$year,
							"week"=>$week,
							"is_playoff"=>0,
							"priority"=>"100",
							"opponent_a"=>$opponent_a,
							"opponent_b"=>$opponent_b,
							"opponent_a_score"=>$opponent_a_score,
							"opponent_b_score"=>$opponent_b_score,
							"opponent_a_dec"=>$opponent_a_dec,
							"opponent_b_dec"=>$opponent_b_dec,
							"winner"=>"0",
							"is_toilet"=>"0"
					);
				}
				if($letter=="a"){$letter="b";}else{$letter="a";}
			}
			$return_array = $pro_bowl_array;
			
		}
				
		return $return_array;
		
	}
	
//****************************************************************
	//gets a team's record vs another team
	public function get_team_record_vs_team($team_id,$opponent){
		
		$query = $this->db->group_start()
							->or_where('opponent_a',$team_id)
							->or_where('opponent_a',$opponent)
						->group_end()
						->group_start()
							->or_where('opponent_b',$opponent)
							->or_where('opponent_b',$team_id)
						->group_end()
						->get('Games');
		$wins=0;
		$total_games = $query->num_rows();
		foreach($query->result_array() as $game){
			if($game['winner']==$team_id){
				$wins++;	
			}
		}
		$losses = $total_games-$wins;
		return array('wins'=>$wins,'losses'=>$losses);
	}
	
//******************************************************************

	public function update_gow($league_id,$week,$year,$gow_opponent_a,$gow_opponent_b){
		//set all games to 99
		$this->db->where('league_id',$league_id)
					->where('week',$week)
					->where('year',$year)
					->set('priority',99)
					->update('Games');
		
		//update teh GOW
		$this->db->where('league_id',$league_id)
					->where('week',$week)
					->where('year',$year)
					->where('opponent_a',$gow_opponent_a)
					->where('opponent_b',$gow_opponent_b)
					->set('priority',1)
					->update('Games');
					
		//reorder the other games
		$priority = 2;
		$rankings = array_reverse($this->Standings->sort_teams_by($year,$league_id,'','',$sort_order_array=array('wins','points')));
		foreach($rankings as $team){
			$team_id = $team['team_id'];
			//check if the team is still in a priority 99 game
			$priority_query = $this->db->where('league_id',$league_id)
										->where('week',$week)
										->where('year',$year)
										->where('priority',99)
										->group_start()
											->or_where('opponent_a',$team_id)
											->or_where('opponent_b',$team_id)
										->group_end()
										->get('Games');
			if($priority_query->num_rows() > 0){
				//it's still a 99 so update the priority, otherwise it would have moved to next team
				$this->db->where('league_id',$league_id)
										->where('week',$week)
										->where('year',$year)
										->group_start()
											->or_where('opponent_a',$team_id)
											->or_where('opponent_b',$team_id)
										->group_end()
										->set('priority',$priority)
										->update('Games');
				
				$priority++;
			}
										
		}
		
	}
}
/*End of file Games.php*/
/*Location: ./application/models/Free_Agents.php*/