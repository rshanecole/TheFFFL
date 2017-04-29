<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Transaction extends MY_Controller
{
	/**
	 * Transaction controller.
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
			
			$this->load->model('Leagues');
			$this->load->model('Transactions');

		$this->current_year = $this->Leagues->get_current_season($this->league_id);
		$this->current_week = $this->Leagues->get_current_week($this->league_id);
		
		$this->load->helper('links');
		$this->load->helper('form');
		
	}

//***************************************************************************************************

	// Loads the content for the transactions view:
	// 
	// 
	public function index($year=NULL, $page_content='transactions_list', $content_data=array()) 
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
				$this->transactions($year,'year');
				
			}
			else{
			$path = 'transactions/'.$page_content;
			
			$this->load_view($path, $content_data, false, false, false);
			}
	}

//***************************************************************************************************
	
	public function transactions($year=NULL,$page_content='year') 
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
		
		$content_data['current_year'] = $this->current_year;
		$content_data['year']=$year;
		$content_data['team_id'] = $this->session->team_id;
		
		//content to initially display
		$content_data['display_page']=$page_content;
		$content_data['load_path'] = 'Transaction/'.$page_content.'/'.$league_id.'/'.$year;
		
		$content_data['dropdown_title'] = $year;
		//content selector will be activated for these views
		//set the list of items for content selector
		//get the max and min trades in the league
		$first_last_transaction_array = $this->Transactions->get_first_last_transaction_years($league_id);
		//d($first_last_trade_array);
		$year_display = $first_last_transaction_array['last'];
		while($year_display >= $first_last_transaction_array['first']){
			//each key is the display in the dropdown, linked to the path to the method
				//in this class to run to get content to display
				$content_data['content_selector'][$year_display] = base_url().'Transaction/year/'.$league_id.'/'.$year_display;
			$year_display--;
		}
		
		//titles of the pages will be upper cased
		$title = 'Transactions' ;
		$content_data['title']= ucwords($title);
		$path = 'transactions/transactions_container';
		
		
		$this->load_view($path, $content_data, true);
		
	}

//****************************************************************************************	
	//request to view transactions for a year and by team and completed page
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
		$content_data['transactions_array']=$this->Transactions->get_transactions_year($league_id,$year);

		$this->index($year, 'transactions_list', $content_data);
	}//year function
	
//***************************************************************************************************


	//undo a trade
	public function undo_trade($trade_id){
		
		//check if approved or denied
		$this->Trades->trade_approval_check($trade_id,1,TRUE);
		
		$this->Trades->add_trade_vote($trade_id,'commissioner',-1);
	}

//***************************************************************************************************

	
	
}//end Class Trade extends MY_Controller

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

