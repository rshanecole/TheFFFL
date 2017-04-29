<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Standings extends MY_Controller
{
	/**
	 * Standings controller.
	 *
	 * ???????
	 */
	 
	public $league_id = 1; //***NI*** remove when multiple leagues are in place
	public $current_year;	
	public $current_week;
	
	//Load the needed libraries.  
	public function __construct() 
    {
		parent::__construct();

		$this->load->helper('date');
		$this->load->helper('combinations');
		$this->load->helper('links');
		
		
		$this->load->model('Leagues');
		$this->load->model('Standings');
		
		$this->current_year = $this->Leagues->get_current_season($this->league_id);
		$this->current_week = $this->Leagues->get_current_week($this->league_id);

	}


	// Loads the content for the team view, types of content area:
	// roster(starting lineup),schedule, set_franchise, franchise(history), set_inactive, franchise_stats
	// 
	public function index($team_id=NULL, $page_content='roster', $content_data=array()) 
    {
		
		//this is loading a view from one of the methods to display on the page
			//RESTRICTED TO LOGGED IN MEMBERS ONLY
			//Just in case not logged in we direct to login page
			if (!$this->session->userdata('logged_in') )
			{
				redirect("/Restricted");
			} 
			
			if(is_NULL($team_id)) 
			{
				$team_id = $this->session->team_id;
				$this->id($team_id,'roster');
			}
			
			$path = 'team/'.$page_content;
			
			$this->load_view($path, $content_data, false, false, false);

	}


//**************************************************************
  
	public function table($year,$conference='',$division='') 
	{
		//RESTRICTED TO LOGGED IN MEMBERS ONLY
		//Just in case not logged in we direct to login page
		if (!$this->session->userdata('logged_in') )
		{
			redirect("/Restricted");
		} 
		
		if(is_NULL($team_id)) 
		{
			$team_id = $this->session->team_id;
		}
		
		
		$content_data['wins_losses']= $this->Standings->get_team_wins_losses_year($team_id,$record_year);
		$content_data['points']= $this->Standings->get_team_total_points_year($team_id,$record_year);
		
		$content_data['current_year'] = $this->current_year;
		//get the user id of the viewer to set permissions
		$user_id = $this->session->userdata('user_id');
		$content_data['security_level'] = $this->session->userdata('security_level');
		
		
		//content to initially display
		$content_data['display_page']=$page_content;
		$content_data['load_path'] = 'Team/'.$page_content.'/'.$team_id;
		//send a display for the dropdown. it should match what is in the content_selector array
		//should be the same as the page_content which is also the method. multiple words
		//can be separated by _ and it will take those out and capitalize each word
		$create_dropdown_title = explode('_',$page_content);
		$dropdown_title='';
		foreach($create_dropdown_title as $part) {
			$dropdown_title .= ucfirst($part).' '; 	
		}
		$content_data['dropdown_title'] = $dropdown_title;
		//content selector will be activated for these views
		//set the list of items for content selector
		$content_data['content_selector'] = array(
			//each key is the display in the dropdown, linked to the path to the method
			//in this class to run to get content to display
			'Roster' => base_url().'Team/roster/'.$team_id,
			'Schedule' => base_url().'Team/schedule/'.$team_id.'/'.$this->current_year,
			'Draft' => base_url().'Team/draft/'.$team_id,
			'Franchise' => base_url().'Team/franchise/'.$team_id,
			'History' => base_url().'Team/history/'.$team_id
		);
		//titles of the pages will be upper cased
		$title = $this->Teams->get_team_name_first_nickname($team_id);
		$content_data['title']= ucwords($title);
		$path = 'team/team';
		
		
		$this->load_view($path, $content_data, true);
		
	}


	
	
	
}//end Class Standings extends MY_Controller

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

