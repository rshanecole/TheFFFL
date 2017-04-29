<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Game_Updates extends CI_Controller
{
	/**
	 * Game_Updates Controller.
	 * CLI-Cron job to run every day
	 * Updates for each NFL game's date and time
	 */
	
	public $league_id = 1; //***NI*** remove when multiple leagues are in place
	public $current_year;	
	public $current_week;
	//Load the needed libraries.  
	public function __construct() 
    {
		parent::__construct();
		// this controller can only be called from the command line
        //if (!$this->input->is_cli_request()) show_error('Direct access is not allowed');
		
		$this->load->model('NFL_Games');
		$this->load->model('Players');
		$this->load->model('Leagues');
		$this->load->model('Games');
		$this->load->model('Rosters');
		$league_id = 1;
		$this->current_year = $this->Leagues->get_current_season($league_id);
		$this->current_week = $this->Leagues->get_current_week($league_id);
		//backup the database
			// Load the DB utility class
			$this->load->model('Database_Manager');
		//end database backup
	}

	public function index()
	{
		
		
	}//end index function
	
//**********************************************************************
	//will update all the games for all the weeks between start and end week. Updates
		//start time, date . This will be run daily in case of start time changes
		//will also run during game times to update status and lock players
		
	public function update_NFL_games_status($update_type='Week',$year=NULL,$start_week=NULL,$end_week=NULL)
	{	
		if(is_null($year)) { $year = $this->current_year; }
		
		if($update_type=='Week' && is_null($start_week)) { 
			$start_week = $end_week = $this->current_week; 
		} 
		elseif(is_null($start_week)){
			$start_week	=1; $end_week=17;
		}
		if($start_week<$this->current_week) { $start_week=$this->current_week; }
		$week=$start_week;
		while($week<=$end_week){
			$this->NFL_Games->update_NFL_games_status($week,$year);
			$week++;
		}
		
	}
	
//****************************************************************************

	public function live_scoring($league_id=1){
		$year = $this->current_year;
		$week = $this->current_week;
		
		
		
		//parse the gaems and tally scores for players
		//get the game ids that are active games
		$games = $this->NFL_Games->get_active_games();

		foreach($games as $game_id){
			$this->NFL_Games->update_nfl_stats_game($year,$week,$game_id);
		}
		
		//update the game status
		$this->update_NFL_games_status('Week');
		
		//update team game scores
		//get array of each game
		$games_query = $this->db->where('week',$week)
				->where('year',$year)
				->get('Games');
		$games_=$this->Games->get_week_games($league_id,$year,$week);	
		//foreach game
		foreach($games_ as $game_data){
			//d($game_data);
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
				$score_array = $this->Games->calculate_team_game_score($$team_id, $week, $year);
				
				
				//store the scores in the table
				if($game_data['is_toilet']==1){ $table="Toilet_Bowls"; } 
				elseif($week==17){$table="Probowl";}
				else { $table = "Games"; }
				$this->Games->update_game_team_score($year,$week,$$team_id,$letter,$score_array['score'],$score_array['decimal'],$table);
			}
		}
			
	}
	
	
}//end Class Player_Updates 

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

