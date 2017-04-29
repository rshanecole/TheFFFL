<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Schedules Model.
	 *
	 * ?????
	 *		
	 */
	
Class Schedules extends CI_Model 
{
	public function __construct() 
    {
		parent::__construct();
		//$ci = get_instance();
		$this->load->helper('date');
		$this->load->model('Database_Manager');
		$this->load->model('Teams');
	}

//*******************************************************************
	
	//schedules the games for a given year
	//should be done at the creation of a new season
	public function schedule_games($league_id,$year){
		$this->Database_Manager->database_backup(array('Games'));
		
		//get the variation of the schedule. Based on a rotation of 5 schedules.
		//divide year by 5 and take remainder. That's the variation version, 0-4
		$schedule_variation = fmod($year,5);
		//afc teams will adjust by 1 position each year, and nfc by 2. This makes a rotation
		//every 5 years of each possible matchup/everyone playes everyone at least every 5 years
		$Afc_adjust = $schedule_variation * 1;
		$Nfc_adjust = $schedule_variation * 2;
		
		//set base number for each team based on simply numerical order of team_ids
		//1-5 for each division
		//al teams array is in format of [conference]=>[division]=>[team_id]=>team name. 
		//create the schedule_position_array to record new schedule positions
		$all_teams_array = $this->Teams->get_all_teams_by_division_id_nickname($league_id);
		$schedule_positions_array = array();
		foreach($all_teams_array as $conference=> $divisions){
			$conf = substr($conference,0,1);
			foreach($divisions as $division => $team_ids){
				$div = substr($division,0,1);
				ksort($team_ids);
				$position = 1;
				foreach($team_ids as $team_id => $nickname){
					//position is the base number
					//add the base number for each team to the adjustment for that conference
					$adjust = $conf.'fc_adjust';
					$schedule_position = $position + $$adjust;
					//if the team's number is greater than 5, subtract 5, repeat until 5 or less
					while($schedule_position>5){
						$schedule_position=$schedule_position-5;
					}
					$schedule_positions_array[$conf.$div.$schedule_position]=$team_id;
					$position++;
				}
			}
		}
		//now each team has a proper schedule position
		//assign the games
		ksort($schedule_positions_array);
		d($schedule_positions_array);
		$schedule_query = $this->db->get('Schedule_20_teams');
		foreach ($schedule_query->result_array() as $game)
		{
				$team_a = $game['team_a'];
				$team_b = $game['team_b'];
				$week = $game['week'];
				//create a game between team_a and team_b using schedule_positions_array
				$this->db->set('year',$year)
						->set('week',$week)
						->set('opponent_a',$schedule_positions_array[$team_a])
						->set('opponent_b',$schedule_positions_array[$team_b])
						->insert('Games');
				
		}
		
		
			
	}

//*************************************************************************

public function get_team_schedule($team_id,$year){
	$query = $this->db->where('(opponent_a='.$team_id.' or opponent_b='.$team_id.')')
						->where('year',$year)
						->select('week,opponent_a,opponent_b,opponent_a_score,opponent_b_score,winner')
						->order_by('week')
						->get('Games');
						
	
	return $query->result_array();
	
	
}

//*************************************************************************
public function set_playoff_games($league_id,$standings,$year,$week){
	$AFC=$NFC=$priority=1;
	$AFC1=$AFC2=$NFC1=$NFC2=0;
	foreach($standings as $team_info){
		if($team_info['playoffs']){
			//seed
			$this->db->set("seed",$$team_info['conference'])
					->where("team_id",$team_info['team_id'])
					->where("year",$year)
					->update("Teams_Seasons");
			//create game
			if($$team_info['conference']<3 ){
				$this->db->set("league_id",$league_id)
							->set("year",$year)
							->set("week",$week)
							->set("is_playoff",1)
							->set("opponent_a",$team_info['team_id'])
							->set("priority",$priority)
							->insert("Games");
				$string = $team_info['conference'].$$team_info['conference'];
				$$string=$team_info['team_id'];
				$priority++;
			}
			elseif($$team_info['conference']==3){
				$string=$team_info['conference']."2";
				$this->db->set("league_id",$league_id)
							->where("year",$year)
							->where("week",$week)
							->where("opponent_a",$$string)
							->set("opponent_b",$team_info['team_id'])
							->update("Games");
			}
			elseif($$team_info['conference']==4){
				$string = $team_info['conference']."1";
				$this->db->set("league_id",$league_id)
							->where("year",$year)
							->where("week",$week)
							->where("opponent_a",$$string)
							->set("opponent_b",$team_info['team_id'])
							->update("Games");
			}
			
			$$team_info['conference']++;
		}
		else{//TB, not playoffs
			$this->db->set("league_id",$league_id)
						->set("year",$year)
						->set("week",$week)
						->set("opponent",$team_info['team_id'])
						->insert("Toilet_Bowls");
		}
	}
}

//*************************************************************************
public function update_playoff_games($league_id,$year,$week){
	
	$AFC=array();
	$NFC=array();
	
	$winners_query=$this->db->where("league_id",$league_id)
				->where("year",$year)
				->where("week",$week)
				->where("is_playoff",1)
				->select("winner")
				->get("Games");
	$winners = $winners_query->result_array();
	$count = count($winners);
	
	foreach($winners as $winner){
		$team_id = $winner['winner'];
		$conference = $this->Teams->get_team_conference_division($team_id);
		if($conference['conference'] == "AFC"){
			$AFC[]=$team_id;
		}
		else {
			$NFC[]=$team_id;
		}
	}

	//not a superbowl yet, divide by conference
	if($count>2){
		$priority=1;
		
		$this->db->set("league_id",$league_id)
				->set("year",$year)
				->set("week",$week+1)
				->set("is_playoff",1)
				->set("opponent_a",$AFC['0'])
				->set("opponent_b",$AFC['1'])
				->set("priority",$priority)
				->insert("Games");
		$priority++;
		$this->db->set("league_id",$league_id)
				->set("year",$year)
				->set("week",$week+1)
				->set("is_playoff",1)
				->set("opponent_a",$NFC['0'])
				->set("opponent_b",$NFC['1'])
				->set("priority",$priority)
				->insert("Games");
		
	}
	elseif($count==2){//superbowl
		
		$this->db->set("league_id",$league_id)
				->set("year",$year)
				->set("week",$week+1)
				->set("is_playoff",1)
				->set("opponent_a",$AFC['0'])
				->set("opponent_b",$NFC['0'])
				->set("priority",1)
				->insert("Games");
		$this->db->set("afc",$AFC['0'])
				->set("nfc",$NFC['0'])
				->where("year",$year)
				->where("league_id",$league_id)
				->update("Championships");
		

	}elseif($count==2){//set winner of superbowl
		
		if(empty($AFC)){
			$champ = $NFC['0'];
		} else {
			$champ = $AFC['0'];
		}
		$this->db->set("superbowl",$champ)
				->where("year",$year)
				->where("league_id",$league_id)
				->update("Championships");
		

	}

}

//******************************************************

	//$week should be the new week, the week of the new game
	public function update_toilet_bowl($league_id,$year,$week){
				
		//get the two best from week
		$tb_query= $this->db->where("league_id",$league_id)
							->where("year",$year)
							->where("week",$week)
							->limit(3)
							->order_by("opponent_score DESC,opponent_dec DESC") 
							->select("opponent")
							->get("Toilet_Bowls");
		$tb_results = $tb_query->result_array();
		$count_tb = count($tb_results);
		if($count_tb==3){
			$count=1;					
			foreach($tb_results as $team_info){
				if($count<3){
					$this->db->set("league_id",$league_id)
							->set("year",$year)
							->set("week",$week+1)
							->set("opponent",$team_info['opponent'])
							->insert("Toilet_Bowls");
				}
				$count++;
			}
		}
		elseif($count_tb==2){
				$this->db->set("winner",$tb_results['0']['opponent'])
							->where("year",$year)
							->where("week",$week)
							->where("opponent",$tb_results['0']['opponent'])
							->update("Toilet_Bowls");
				$this->db->set("toilet_bowl",$tb_results['0']['opponent'])
							->where("year",$year)
							->where("league_id",$league_id)
							->update("Championships");
		}
	}


}//end model


/*End of file Schedules.php*/
/*Location: ./application/models/Schedules.php*/