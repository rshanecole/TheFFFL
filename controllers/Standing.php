<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Standing extends MY_Controller
{
	/**
	 * Standing controller.
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


	// 
	// 
	public function index($page_content='standings', $content_data=array()) 
    {
		
		//this is loading a view from one of the methods to display on the page
			//RESTRICTED TO LOGGED IN MEMBERS ONLY
			//Just in case not logged in we direct to login page
			if (!$this->session->userdata('logged_in') )
			{
				redirect("/Restricted");
			} 
			
			$team_id = $this->session->team_id;
			
			
			$path = 'standings/'.$page_content;
			
			$this->load_view($path, $content_data, false, false, false);

	}


//**************************************************************
  
	public function year($year=NULL,$conference='',$division=FALSE,$wildcard=FALSE) 
	{
		//RESTRICTED TO LOGGED IN MEMBERS ONLY
		//Just in case not logged in we direct to login page
		if (!$this->session->userdata('logged_in') )
		{
			redirect("/Restricted");
		} 
		
		if(is_null($year)){ $year=$this->current_year; }
		$team_id = $this->session->team_id;

		$content_data['current_year'] = $this->current_year;
		
		//content to initially display
		$content_data['display_page']='Standings';
		$content_data['load_path'] = 'Standing/regular/'.$year.'/'.$conference.'/'.$division.'/'.$wildcard;
		//send a display for the dropdown. it should match what is in the content_selector array
		//should be the same as the page_content which is also the method. multiple words
		//can be separated by _ and it will take those out and capitalize each word
		$content_data['dropdown_title'] = $year;
		//content selector will be activated for these views
		//set the list of items for content selector
		$content_data['content_selector'] = array();
			//each key is the display in the dropdown, linked to the path to the method
			$y=$this->current_year;
			while($y >=1998){
				$content_data['content_selector'][$y]= base_url().'Standing/regular/'.$y;
				$y--;
			}
		
		//titles of the pages will be upper cased
		$title = 'Standings';
		$content_data['title']= ucwords($title);
		$path = 'standings/standings_container';
		
		
		$this->load_view($path, $content_data, true);
		
	}

//**************************************************************
  
	public function regular($year,$conference='',$division=FALSE,$wildcard=FALSE) 
	{
		$content_data['year']=$year;
		$content_data['current_year']=$this->current_year;
		
		$content_data['standings'] = $this->Standings->get_standings($year,$conference,$division,$wildcard);
		foreach($content_data['standings'] as $key=>$data){
			$content_data['standings'][$key]['team_logo_path']= $this->Teams->get_team_logo_path($data['team_id']);	
		}
		$points_standings = $content_data['standings'];
		// Obtain a list of columns
		foreach ($points_standings as $key => $row) {
			$points[$key]  = $row['points'];
			$wins[$key] = $row['wins'];
		}

		// Sort the data with volume descending, edition ascending
		// Add $data as the last parameter, to sort by the common key
		array_multisort($points, SORT_DESC, $wins, SORT_DESC, $points_standings);
		$content_data['points_standings'] = $points_standings;
		$content_data['grouping']=$conference.' '.$division;
		if($conference=='') { $content_data['grouping']='FFFL'; }
		$this->index('regular', $content_data);
	}
	
}//end Class Standings extends MY_Controller

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

