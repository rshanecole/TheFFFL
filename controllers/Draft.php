<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Draft extends MY_Controller
{
	/**
	 * Draft controller.
	 *
	 * 
	 */
	public $league_id = 1; //***NI*** remove when multiple leagues are in place
	public $current_year;	
	public $current_week;
	//Load the needed libraries.  
	public function __construct() 
    {
			parent::__construct();

			$this->load->model('Players');
			$this->load->model('Leagues');
			$this->load->model('Drafts');
		
			
		
			//RESTRICTED TO LOGGED IN MEMBERS ONLY
			//Just in case not logged in we direct to login page
			if (!$this->session->userdata('logged_in') )
			{
				redirect("/Restricted");
			} 
			else
			{
				global $team_id;
				$team_id = $this->session->team_id;
			}
			
		$this->current_year = $this->Leagues->get_current_season($this->league_id);
		$this->current_week = $this->Leagues->get_current_week($this->league_id);
		
	}

	// Loads the content for the drafts view:
	// 
	// 
	public function index($year=NULL, $page_content='draft_year', $content_data=array()) 
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
				$this->results($year,'year');
				
			}
			else{
			$path = 'draft/'.$page_content;
			
			$this->load_view($path, $content_data, false, false, false);
			}
	}
	
	public function results($year=NULL,$page_content='year') 
	{
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
		
		$league_id = $this->league_id;
		
		$content_data['current_year'] = $year;
		$content_data['team_id'] = $this->session->team_id;
		
		
		//content to initially display
		$content_data['display_page']=$page_content;
		$content_data['load_path'] = 'Draft/'.$page_content.'/'.$league_id.'/'.$year;
		
		$content_data['dropdown_title'] = $year;
		//content selector will be activated for these views
		//set the list of items for content selector
		//get the max and min drafts in the league
		$first_last_draft_array = $this->Drafts->get_first_last_draft_years($league_id);
		$year_display = $first_last_draft_array['last'];
		while($year_display >= $first_last_draft_array['first']){
			//each key is the display in the dropdown, linked to the path to the method
				//in this class to run to get content to display
				$content_data['content_selector'][$year_display] = base_url().'Draft/year/'.$league_id.'/'.$year_display;
			$year_display--;
		}
		
		//titles of the pages will be upper cased
		$title = 'Draft Results' ;
		$content_data['title']= ucwords($title);
		$path = 'draft/draft_container';
		
		
		$this->load_view($path, $content_data, true);
		
	}

	
	//request to view a draft's results page
	public function year($league_id,$year='current')
	{
		$league_id=1; //***NI***
		if($year==='current'){
			$year = $this->current_year;
		}
		
		//get the id of the session team 
		$team_id = $this->session->userdata('team_id');
		
		$content_data = array();
		$content_data['team_id']=$team_id;
		$content_data['year']=$year;
		
		$content_data['draft_picks_array']=$this->Drafts->get_draft_results_year($league_id,$year);
		//$content_data['draft_times']['Common'] = $this->Drafts->get_draft_times($league_id,$year);
		$content_data['all_teams_id_name'] = $this->Teams->get_all_teams_id_to_first_nickname($league_id);
		
		
		$this->index($year, 'draft_year', $content_data);
	}//year function
	
		

	
}//end Class Player extends MY_Controller

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

