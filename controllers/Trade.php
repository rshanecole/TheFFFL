<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Trade extends MY_Controller
{
	/**
	 * Trade controller.
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
			$this->load->model('Trades');
			$this->load->model('Rosters');
			$this->load->model('Rosters_View');
			$this->load->model('NFL_stats');
			$this->load->model('NFL_Teams');
			
			
		$this->current_year = $this->Leagues->get_current_season($this->league_id);
		$this->current_week = $this->Leagues->get_current_week($this->league_id);
		
		$this->load->helper('links');
		$this->load->helper('form');
		
	}

//***************************************************************************************************

	// Loads the content for the trades view:
	// 
	// 
	public function index($year=NULL, $page_content='trade_list', $content_data=array()) 
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
				$this->trades($year,'year');
				
			}
			else{
			$path = 'trades/'.$page_content;
			
			$this->load_view($path, $content_data, false, false, false);
			}
	}

//***************************************************************************************************
	
	public function trades($year=NULL,$page_content='year') 
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
		$content_data['load_path'] = 'Trade/'.$page_content.'/'.$league_id.'/'.$year;
		
		$content_data['dropdown_title'] = $year;
		//content selector will be activated for these views
		//set the list of items for content selector
		//get the max and min trades in the league
		$first_last_trade_array = $this->Trades->get_first_last_trade_years($league_id);
		//d($first_last_trade_array);
		$year_display = $first_last_trade_array['last'];
		while($year_display >= $first_last_trade_array['first']){
			//each key is the display in the dropdown, linked to the path to the method
				//in this class to run to get content to display
				$content_data['content_selector'][$year_display] = base_url().'Trade/year/'.$league_id.'/'.$year_display;
			$year_display--;
		}
		
		//titles of the pages will be upper cased
		$title = 'Trades' ;
		$content_data['title']= ucwords($title);
		$path = 'trades/trade_container';
		
		
		$this->load_view($path, $content_data, true);
		
	}

//****************************************************************************************	
	//request to view trades for a year and by team and completed page
	public function year($league_id,$year='current',$team='All',$completed='All')
	{
		$league_id=1; //***NI***
		if($year==='current'){
			$year = $this->current_year;
		}
		
		//get the id of the session team 
		$team_id = $this->session->userdata('team_id');
		
		$content_data = array();
      //send flag indicating trading is currently open to enable or disable buttom
      $content_data['trading_open'] = $this->Trades->get_is_trading_open($league_id);
		$content_data['team_id']=$team_id;
		$content_data['year']=$year;
		$content_data['team_display']=$team;
		$content_data['completed']=$completed;
		$trades_array = $this->Trades->get_trades_year($league_id,$year,$team,$completed);
		if($trades_array) { $trades_array = array_reverse($trades_array); }
		$content_data['trades_array']=$trades_array;
		
		$content_data['all_teams_id_name'] = $this->Teams->get_all_teams_id_to_first_nickname($league_id);

		$this->index($year, 'trade_list', $content_data);
	}//year function
	
//***************************************************************************************************
	
	//this function loads the trade_details view and populates
	//the variables for it. The data is displayed in a modal for the
	//user to view players and picks that were traded. 
	public function load_trade_details($trade_id){
		//collect data for trade details
		//$this->output->enable_profiler(TRUE);
			$data['trade_id']=$trade_id;
			$data['trade_committee'] = $this->Trades->get_trade_committee($this->league_id);
			$data['committee_votes_array'] =$this->Trades->committee_votes($trade_id);
			$data['trade_data_array'] = $this->Trades->get_trade_details($trade_id);
			

			$path = 'trades/trade_details';
			
			$this->load_view($path, $data, false, false, false);

		
	}//end load_trade_list 

//***************************************************************************************************	
	
	//this function loads the trade_offer view 
	//user sends back a team to trade with and it populates
	//both team's rosters and current picks
	public function trade_offer($team_id,$trade_partner=NULL,$trade_id=NULL,$data=array()){
		
		//if this is a counter offer
		if($trade_id){
			
			$data['counter']=array();
			
			$trade_details = $this->Trades->get_trade_details($trade_id,'All');
			 
			$trade_partner=$trade_details['offered_by'];
			$team_id=$trade_details['offered_to'];
			$data['counter'][$trade_id]=array();
			//prefix => array of player ids
			if(isset($trade_details['players_received'])){
				$data['counter'][$trade_id]['player_offer']=$trade_details['players_received'];
			}	
			if(isset($trade_details['players_offered'])){
				$data['counter'][$trade_id]['player_receive']=$trade_details['players_offered'];
			}
			
			//prefix => array of draft pick ids
			if(isset($trade_details['draft_picks_received'])){
				//$data['counter'][$trade_id]['draft_offer']=array();
				foreach($trade_details['draft_picks_received'] as $pick_id => $details){
					$data['counter'][$trade_id]['draft_offer'][]=$pick_id;
				}
			}
			if(isset($trade_details['draft_picks_offered'])){
				//$data['counter'][$trade_id]['draft_received']=array();
				foreach($trade_details['draft_picks_offered'] as $pick_id => $details){
					$data['counter'][$trade_id]['draft_receive'][]=$pick_id;
				}
			}
			
			
		}
		//an array of id => team name
		$data['all_teams'] = $this->Teams->get_all_teams_id_to_first_nickname($this->session->userdata('league_id'));
		$data['team_id']=$team_id;
		if(!is_null($trade_partner)){
			$data['partner_id'] = $trade_partner;
			
			//get the offering team's players
			$players=$this->Rosters->get_team_active_roster($team_id);
			foreach($players as $fffl_player_id){
				$data['team_players'][$fffl_player_id]= $this->Rosters_View->add_all_player_roster_data($team_id,$fffl_player_id,$this->current_year,$this->current_week);	
			}
			
			//get the partner team's players
			$players=$this->Rosters->get_team_active_roster($trade_partner);
			foreach($players as $fffl_player_id){
				$data['partner_players'][$fffl_player_id]= $this->Rosters_View->add_all_player_roster_data($trade_partner,$fffl_player_id,$this->current_year,$this->current_week);	
			}
			
			//get offerening team's draft picks
			if($this->current_week==0){
				$year=$this->current_year;	
			}
			else {
				$year=$this->current_year+1;	
			}
			$data['offer_draft_picks'] = $this->Drafts->get_team_draft_by_year($team_id,$year);
			$data['partner_draft_picks'] = $this->Drafts->get_team_draft_by_year($trade_partner,$year);
		}
		
		
		
		$path = 'trades/trade_offer.php';
		$this->load_view($path, $data, false, false, false);
		
	}//end trade_offer 
	
//***************************************************************************************************	
		
	//receives a post data for a trade to be proposed
	//puts it in the database then sends an email to the partner
	public function submit_trade_offer($team_id, $partner_id,$trade_id=NULL){
		$players_offered = rtrim($this->input->post('offer_players'),',');
		$players_received = rtrim($this->input->post('partner_players'),',');
		$draft_picks_offered = rtrim($this->input->post('offer_picks'),',');
		$draft_picks_received = rtrim($this->input->post('partner_picks'),',');
		if($trade_id){
			$comments = $this->Trades->append_comments($trade_id,$this->input->post('comments'));	
		}
		else {
			$comments = $this->input->post('comments');
			if(strpos($comments, '</a>: </strong><br>') !== false){
				$comments = '';
			} 
		}
		$return = $this->Trades->insert_update_trade($team_id,$partner_id,$players_offered,$draft_picks_offered,$players_received,$draft_picks_received,$comments,$trade_id);
		
	}
	
//*****************************************************************************************************

	//deletes the given trade
	public function delete_trade_offer($trade_id){
		$this->Trades->delete_trade($trade_id);	
		
	}
	
//*****************************************************************************************************

	//Accept the given trade
	public function accept_trade_offer($trade_id){
		$this->Trades->accept_trade($trade_id);	
		$this->Trades->append_comments($trade_id,$this->input->post('comments'));
		
		
	}
	
//*****************************************************************************************************

	//Decline the given trade
	public function decline_trade_offer($trade_id){
		$this->Trades->decline_trade($trade_id);
		
		$this->Trades->append_comments($trade_id,$this->input->post('comments'));	
		
	}
	

//*****************************************************************************************************

	//record the committee decision
	public function committee_trade_offer($trade_id,$committee_member_group,$vote='1'){
		$this->Trades->add_trade_vote($trade_id,$committee_member_group,$vote);
		//check if approved or denied
		$this->Trades->trade_approval_check($trade_id);
		
	}

//*****************************************************************************************************

	//auto approve or deny a trade
	public function auto_trade_offer($trade_id,$vote='1'){
		$this->output->enable_profiler(TRUE);
		$this->Trades->add_trade_vote($trade_id,'commissioner',$vote);
		//check if approved or denied
		$this->Trades->trade_approval_check($trade_id,$vote);
		
		$this->Trades->append_comments($trade_id,$this->input->post('comments'));
		
	}
	
//*****************************************************************************************************

	//undo a trade
	public function undo_trade_offer($trade_id){
		
		//check if approved or denied
		$this->Trades->trade_approval_check($trade_id,1,TRUE);
		
		$this->Trades->add_trade_vote($trade_id,'commissioner',-1);
	}


	
}//end Class Trade extends MY_Controller

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

