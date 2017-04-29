<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Free_Agent extends MY_Controller
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
			$this->load->model('Free_Agents');
			$this->load->model('NFL_Teams');

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
	public function index($year=NULL, $page_content='fa_results', $content_data=array()) 
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
			$path = 'transactions/'.$page_content;
			
			$this->load_view($path, $content_data, false, false, false);
			
	}

//****************************************************************************
	
	//writes to a json file the available fas
	public function write_available_fa($league_id) 
	{
		$all_fa = $this->Free_Agents->get_all_free_agents($league_id);
		$fa_data_array = array();
	
		$all_fa = $this->Players->get_player_info($all_fa,"fffl_player_id","current_team position first_name last_name");
		
		foreach($all_fa as $fffl_player_id=>$data){
			
			$all_fa[$fffl_player_id]['salary']=$this->Salaries->get_free_agent_salary($fffl_player_id);
			$all_fa[$fffl_player_id]['bye']=$this->NFL_Teams->get_team_bye_week($data['current_team']);
		}

		$fp = fopen('/home1/theffflc/public_html/fantasy/assets/json/fa.json', 'w');
		fwrite($fp, json_encode(array(now()=>$all_fa)));
		
	}

//*****************************************************************************
	public function order(){
		//content to initially display
		$content_data['display_page']='fa_draft_order';
		$content_data['load_path'] = 'Free_Agent/fa_draft_order';
		//titles of the pages will be upper cased
		$title = 'Free Agency Draft Order' ;
		$content_data['title']= ucwords($title);
		$path = 'transactions/transactions_container';
		
		$this->load_view($path, $content_data, true);	
	}
	
//******************************************************************************
	public function fa_draft_order(){
		$league_id=$this->league_id;
		$draft_order = $this->Free_Agents->get_FA_draft_order($league_id);
		foreach($draft_order as $pick=>$team_id){
			
			$query = $this->db->where('team_id',$team_id)
								->where('transaction_type','FA')
								->order_by('time','DESC')
								->limit(1)
								->get('Transactions');
			foreach($query->result_array() as $data){
				$content_data['fa_draft_order'][$pick]['last'] = date('M j, Y g:i a',$data['time']);
				$content_data['fa_draft_order'][$pick]['week'] = $data['week']; 	
			}
			$content_data['fa_draft_order'][$pick]['team_id']=$team_id;
			
			
		}
		
		$this->index(NULL, 'free_agency_draft_order', $content_data) ;
			
	}
	
//*****************************************************************************

	//
	public function results($year=NULL,$week=NULL,$day='Wednesday'){
		//RESTRICTED TO LOGGED IN MEMBERS ONLY
		//Just in case not logged in we direct to login page
		if (!$this->session->userdata('logged_in') )
		{
			redirect("/Restricted");
		} 
		
		if(is_null($year)){ $year=$this->current_year; }
		if(is_null($week)){ $week=$this->current_week; }
		$team_id = $this->session->team_id;

		$content_data['current_year'] = $this->current_year;
		
		//content to initially display
		$content_data['display_page']='FA_Drafts';
		$content_data['load_path'] = 'Free_Agent/fa_draft/'.$this->league_id.'/'.$year.'/'.$week.'/'.$day;
		//send a display for the dropdown. it should match what is in the content_selector array
		//should be the same as the page_content which is also the method. multiple words
		//can be separated by _ and it will take those out and capitalize each word
		$content_data['dropdown_title'] = $year;
		//content selector will be activated for these views
		//set the list of items for content selector
		$content_data['content_selector'] = array();
			//each key is the display in the dropdown, linked to the path to the method
			$y=$this->current_year;
			while($y >=2006){
				$content_data['content_selector'][$y]= base_url().'Free_Agent/fa_draft/'.$this->league_id.'/'.$y.'/14/Wednesday';
				$y--;
			}
		
		//titles of the pages will be upper cased
		$title = 'FA Draft Results';
		$content_data['title']= ucwords($title);
		$path = 'transactions/fa_draft_container';
		
		
		$this->load_view($path, $content_data, true);
		
	}

//**************************************************************
  
	public function fa_draft($league_id=1,$year,$week, $day) 
	{
		$content_data['year']=$year;
		$content_data['current_year']=$this->current_year;
		$content_data['week']=$week;
		$content_data['league_id']=$league_id;
		
		//get all distinct drafts for the year
			$query = $this->db->where('year',$year)
								->order_by('week','DESC')
								->select('day,week')
								->distinct()
								->get('FA_drafts');
			$drafts=array();
			foreach($query->result_array() as $draft){
				$drafts[] = array('week'=>$draft['week'],'day'=>$draft['day']);
				
			}
			$content_data['drafts_array']=$drafts;
			
			$content_data['day']=$day;
			
			
			//gather the results
			$content_data['draft_results_array']=$this->Free_Agents->get_fa_draft_results($year,$week,$day,$league_id);
		
		
		 $this->index('transactions/fa_draft','fa_draft', $content_data);
	}
	

	
//*****************************************************************************

	public function request(){
			
		//content to initially display
		$content_data['display_page']='manage_request';
		$content_data['load_path'] = 'Free_Agent/manage_request';
		//titles of the pages will be upper cased
		$title = 'Manage Free Agency' ;
		$content_data['title']= ucwords($title);
		$path = 'transactions/transactions_container';
		
		$this->load_view($path, $content_data, true);	
	}

//***************************************************************************

	public function manage_request(){
		$league_id=$this->league_id;
		$year=$this->current_year;
		$content_data['team_id']=$this->team_id;
		$content_data['lists'] = $this->Free_Agents->get_team_lists($this->team_id);
		//send active roster
		$content_data['roster'] = $this->Rosters->get_team_active_roster($this->team_id);
		$content_data['empty_spots'] = $this->Rosters->get_league_active_roser_limit($league_id) - count($content_data['roster']);
		if($content_data['empty_spots']<0){$content_data['empty_spots']=0;}
		
		//for each list add the list_players
		foreach($content_data['lists'] as $list_priority => $data){
			$content_data['lists'][$list_priority]['list_players'] = $this->Free_Agents->get_list_player_data($this->team_id,$data['list_id']);
			$content_data['lists'][$list_priority]['release_players'] = $this->Free_Agents->get_list_release_player_data($this->team_id,$data['list_id']);
		}
			$content_data['requests'] = $this->Free_Agents->get_team_distinct_requests($this->team_id);
			//$release = $this->Free_Agents->get_team_release($this->team_id);
				
		$this->index($year, 'free_agency_request', $content_data) ;
	}
	
//***************************************************************************************
	public function add_fa_request($fffl_player_id){
		$this->Free_Agents->add_player_request($this->team_id,$fffl_player_id);
		
	}

//***************************************************************************************
	public function delete_fa_request($fffl_player_id){
		$this->Free_Agents->remove_player_request($this->team_id,$fffl_player_id);
		
	}

//***************************************************************************************
	public function add_open_fa($fffl_player_id){
		$this->Free_Agents->add_open_fa($fffl_player_id,$this->team_id);
		
	}


//******************************************************************************

	public function list_order($team_id,$list_id,$increase_decrease){
		$current_lists_order = $this->Free_Agents->get_team_lists($team_id);
		$i=1;
		foreach($current_lists_order as $list_data){
			$adjusted_list[$i]=$list_data['list_id'];
			$i++;
		}
		d($adjusted_list);
		$i=1; $skip_next=FALSE;
		foreach($adjusted_list as $list_ids){
			if($list_ids==$list_id && ($i+$increase_decrease)!=0){
				$list[$i]=$adjusted_list[$i+$increase_decrease];
				$list[$i+$increase_decrease]=$list_ids;
				if($increase_decrease==1){ $skip_next=TRUE; }
				d($list);
			}
			elseif($skip_next) {
				$skip_next=FALSE;
			}
			else{
				$list[$i]=$list_ids;
				d($list);	
			}
			$i++;
		}
		
		
		$this->Free_Agents->reorder_lists($team_id,$list);
		
	}

//*********************************************************************************

	public function list_activate($list_id,$team_id){
		$this->Free_Agents->activate_list($list_id,$team_id);
		
	}

//*********************************************************************************

	public function list_deactivate($list_id,$team_id){
		$this->Free_Agents->deactivate_list($list_id,$team_id);
		
	}
	
//*********************************************************************************

	public function list_add($team_id){
		$this->Free_Agents->add_list($team_id);
		
	}
	
//*********************************************************************************

	public function list_delete($team_id,$list_id){
		$this->Free_Agents->delete_list($team_id,$list_id);
		
	}
	
//*********************************************************************************

	public function update_number_desired($team_id,$list_id,$number){
		$this->Free_Agents->update_number_desired($team_id,$list_id,$number);
		
	}
	
//*********************************************************************************

	public function add_player_list($team_id,$list_id,$fffl_player_id){
		$this->Free_Agents->add_player_list($team_id,$fffl_player_id,$list_id);
	}

//***********************************************************************************

	public function list_player_priority($team_id,$list_id){
		$update_list = $this->input->post('list_order');
		if(is_null($update_list)) { redirect("/Restricted");}
		$list = explode(',',$update_list);
		$this->Free_Agents->update_list_player_priorty($team_id,$list_id,$list);
	}
	
//***********************************************************************************

	public function list_release_priority($team_id,$list_id){
		$update_list = $this->input->post('list_order');
		if(is_null($update_list)) { redirect("/Restricted");}
		$list = explode(',',$update_list);
		$this->Free_Agents->update_list_release_priorty($team_id,$list_id,$list);
	}
	
//**********************************************************************************

	public function list_player_delete($team_id,$list_id,$fffl_player_id){
		$this->Free_Agents->remove_player_from_list($team_id,$fffl_player_id,$list_id);
		
	}

//*********************************************************************************

	public function add_release_player($team_id,$list_id,$fffl_player_id){
		$this->Free_Agents->add_release_player_list($team_id,$fffl_player_id,$list_id);
	}
	
//**********************************************************************************

	public function list_release_player_delete($team_id,$list_id,$fffl_player_id){
		$this->Free_Agents->remove_player_from_release_list($team_id,$fffl_player_id,$list_id);
		
	}
	

	
}//end Class Player extends MY_Controller

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

