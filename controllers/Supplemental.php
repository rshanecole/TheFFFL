<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Supplemental extends MY_Controller
{
	/**
	 * Supplemental controller.
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
		$this->load->model('Rosters');
		$this->load->model('Drafts');
		$this->load->model('Rosters_View');
		$this->load->model('NFL_stats');
		$this->load->model('NFL_Teams');
		
		$this->current_year = $this->Leagues->get_current_season($this->league_id);
		$this->current_week = $this->Leagues->get_current_week($this->league_id);

	}


	// 
	// 
	public function index($page_content='supplemental_container', $content_data=array()) 
    {
		
		//this is loading a view from one of the methods to display on the page
			//RESTRICTED TO LOGGED IN MEMBERS ONLY
			//Just in case not logged in we direct to login page
			if (!$this->session->userdata('logged_in') )
			{
				redirect("/Restricted");
			} 
			
			$team_id = $this->session->team_id;
			
			
			$path = 'draft/'.$page_content;
			
			$this->load_view($path, $content_data, false, false, false);

	}


//**************************************************************
  
	public function select() 
	{
		//RESTRICTED TO LOGGED IN MEMBERS ONLY
		//Just in case not logged in we direct to login page
		if (!$this->session->userdata('logged_in') )
		{
			redirect("/Restricted");
		} 
		
		
		
		//titles of the pages will be upper cased
		
		$content_data['load_path'] = 'Supplemental/selections';
		$title = 'Supplemental Draft Selection';
		$content_data['title']= ucwords($title);
		$path = 'draft/supplemental_container';
		
		
		$this->load_view($path, $content_data, true);
		
	}
	
	
	//*****************************************************************************
	
	public function selections(){
		
		$team_id = $this->session->userdata('team_id');
		$content_data['team_id']=$team_id;
		
		//get team's supp pick number
		$next_draft_data = $this->Drafts->get_next_draft_details($this->league_id);
		
		$next_draft = $next_draft_data['draft_id'];
		if($next_draft){
			if($next_draft_data['status']==0){ $round = 1; } else { $round=2; }
		}
		else {
			$round=2;	
		}
		
		$all_drafts = $this->Drafts->get_team_draft_by_year($team_id,$this->current_year);
		
		foreach($all_drafts[$this->current_year] as $draft_pick_data){
			
			if($draft_pick_data['draft_id']==$next_draft && $draft_pick_data['round']==$round){
				
				$content_data['team_pick']=$draft_pick_data['pick_number'];
				if($round==2){ $content_data['team_pick']=$draft_pick_data['pick_number']-20;}
			}
		}
		//get all available players
		$all_available_players= $this->Drafts->get_available_supplemental_players($this->league_id,$round);
		
		$content_data['available_players']=array();
		$content_data['current_selections']=array();
		if(!is_null($all_available_players)){
			//get the teams current selections
			$content_data['current_selections'] = $this->Drafts->get_team_supplemental_selections($team_id);
			
			//get teh team current roster
			$current_roster = $this->Rosters->get_team_complete_roster($team_id);
			
			//remove the roster and selected players from available
			$content_data['available_players']=array();
			foreach($all_available_players as $fffl_player_id){
				$content_data['all_players'][$fffl_player_id] = $this->Rosters_View->add_all_player_roster_data($team_id,$fffl_player_id,$this->current_year,$this->current_week);
				if(!in_array($fffl_player_id,$content_data['current_selections']) && !in_array($fffl_player_id,$current_roster)){
					$content_data['available_players'][] = array('fffl_player_id'=>$fffl_player_id,'salary'=>$this->Salaries->get_free_agent_salary($fffl_player_id));
				}
				
			}
		}
		$content_data['number_selections']=count($content_data['current_selections']);
		$content_data['week']=$this->current_week;
		$content_data['year']=$this->current_year;
		
		
			$this->index('supplemental_selection', $content_data);
		
	}
	
	
	//****************************************************************************
	
	public function add_selection($fffl_player_id){
		
		$team_id = $this->session->userdata('team_id');
		$this->Drafts->add_supplemental_selection($team_id,$fffl_player_id);	
		
		$this->selections();
		
	}
	
	//****************************************************************************
	
	public function remove_selection($fffl_player_id){
		
		$team_id = $this->session->userdata('team_id');
		$this->Drafts->remove_supplemental_selection($team_id,$fffl_player_id);	
		
		$this->selections();
		
	}
	
	//******************************************************************************
	 public function update_selections($team_id){
	 
	 	//update the selections
		$update_selections = $this->input->post('list_order');
		if(is_null($update_selections)) { redirect("/Restricted");}
		$list = explode(',',$update_selections);
		$i=1;
		foreach($list as $fffl_player_id){
			$this->db->set('priority',$i)
					->where('team_id',$team_id)
					->where('fffl_player_id',$fffl_player_id)
					->update('Supplemental_Selections');	
			$i++;
			
		}
		
	 }

}//end Class Standings extends MY_Controller

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

