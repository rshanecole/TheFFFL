<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Probowl extends MY_Controller
{
	/**
	 * Free_Agent controller.
	 *
	 * 
	 */

	public $league_id = 1; //***NI*** remove when multiple leagues are in place
	public $team_id;
	//Load the needed libraries.  
	public function __construct() 
    {
			parent::__construct();

			$this->load->model('Players');
			$this->load->model('Leagues');
			$this->load->helper('form');
			//RESTRICTED TO LOGGED IN MEMBERS ONLY
			//Just in case not logged in we direct to login page
			if ($this->session->userdata('logged_in'))
			{
				
				$this->team_id = $this->session->team_id;
				$this->league_id=$this->Teams->get_team_league_id($this->team_id);
				$this->current_year = $this->Leagues->get_current_season($this->league_id);
				$this->current_week = $this->Leagues->get_current_week($this->league_id);
			}
			
		
		
	}
//*************************************************************************************
	// Loads the content for the drafts view:
	// 
	// 
	public function index($year=NULL, $page_content='probowl', $content_data=array()) 
    {
		
		//this is loading a view from one of the methods to display on the page
			//RESTRICTED TO LOGGED IN MEMBERS ONLY
			//Just in case not logged in we direct to login page
			if (!$this->session->userdata('logged_in') )
			{
				redirect("/Restricted");
			} 
			
			if(is_NULL($year)) 
			{
				$year = $this->current_year;

			}
			//titles of the pages will be upper cased either Register Login or Update Profile
			$title = str_replace('_',' ',$page_content);
			$content_data['title']= ucwords($title);
			$path = 'team/'.$page_content;
			
			$this->load_view($path, $content_data, false, false, false);
			
	}

//*****************************************************************************

	public function selection(){
			
		//content to initially display
		$content_data['display_page']='manage_selection';
		$content_data['load_path'] = 'Probowl/manage_selection';
		
		//titles of the pages will be upper cased
		$title = 'Select Pro Bowl Team' ;
		$content_data['title']= ucwords($title);
		$path = 'team/probowl_container';
		
		$this->load_view($path, $content_data, true);	
	}

//***************************************************************************

	public function manage_selection(){
		$league_id=$this->league_id;
		$year=$this->current_year;
		$data['team_id']=$this->team_id;
		//send current selections
		$data['current_roster']=$this->Rosters->get_probowl_roster($this->team_id,$year);
		//get all players by position
		foreach(array('QB','RB','WR','TE','K') as $pos){
			$data[$pos.'s']=array();
			$players= $this->Players->get_all_player_ids_no_objects("position='".$pos."'#current_team<>'RET'#current_team<>'FA'","fffl_player_id","Players.last_name, Players.first_name",'ASC');
			foreach($players['ids'] as $fffl_player_id){
				$get_average = $this->NFL_stats->get_player_scores_season($year,$fffl_player_id,1,1,$this->current_week);
				$data[$pos.'s'][$fffl_player_id]=$get_average['average'];	
			}
		}
				
		
		$this->index(2016,'probowl', $data);
	}
	
//***************************************************************************************************	
		
	//receives a post data for a roster 
	//puts it in the database 
	public function submit_probowl($team_id){
		
		$year=$this->current_year;
		
		$received_players = rtrim($this->input->post('players'),',');
		$players = explode(",",$received_players);
		$incomplete=0;
		while(count($players)<8){
			$incomplete=1;
			$players[]=0;	
		}
		$closed=1;

if($closed){ echo "Pro Bowl Closed"; }
	else{	

		if(!empty($this->Rosters->get_probowl_roster($team_id,$year))){
			$success = $this->Rosters->update_probowl_roster($team_id,$year,$players);
			
		}
		else {
			$success = $this->Rosters->insert_probowl_roster($team_id,$year,$players);
		}
		
		if(!$success){
			echo "<div class='col-xs-24 bg-danger'><strong>Error. Please contact the administrator</strong></div>";
		}
		elseif($incomplete){
			echo "<div class='col-xs-24 bg-danger'><strong>Incomplete Roster</strong></div>";
		}
		else{
			echo "<div class='col-xs-24 bg-info'><strong>Success</strong></div>";
		}
}
	}

	
}//end Class Player extends MY_Controller

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

