<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Live_Scoring extends CI_Controller
{
	/**
	 * Live Scoring controller.
	 * CLI-Cron job to run during games
	 *
	 */
	
	//Load the needed libraries.  
	public function __construct() 
    {
		parent::__construct();
		// this controller can only be called from the command line
      //  if (!$this->input->is_cli_request()) show_error('Direct access is not allowed');
		//$this->load->library('rssparser');
		
		//backup the database
			// Load the DB utility class
			$this->load->model('Database_Manager');	
			$this->load->model("NFL_Games");			
			
	
		//end database backup
	}


	public function index()
	{
		
		
		$league_id = 1; //***NI*** remove when multiple leagues are in place
		$current_year = $this->Leagues->get_current_season($league_id);
		$current_week = $this->Leagues->get_current_week($league_id);

		//select all games with current active status
		
		//foreach game
		
			//set update time
			//call update stats function in NFL_Games model
		
	} //end index
	
//******************************************************************************************

	
	
				

	
} //end Class Free_Agent_Draft 

/*End of file Free_Agent_Draft.php*/
/*Location: ./application/controllers/CLI/RSS_Updates.php*/

