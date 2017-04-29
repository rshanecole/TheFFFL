<?php 

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
       
		
		$this->load->model('NFL_Games');
		$this->load->model('Players');
		$this->load->model('Leagues');
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
	public function update_NFL_games_status($year=NULL,$start_week=1,$end_week=16)
	{	
		if(is_null($year)) { $year = $this->current_year; } 
		$week=$start_week;
		while($week<=$end_week){
			$this->NFL_Games->update_NFL_games_status($week,$year);
			$week++;
		}
		
	}
	
	
}//end Class Player_Updates 

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

