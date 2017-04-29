<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * MY_Controller controller.
	 *
	 * adds a function to the controller to manage view requests
	 * allows for dynamic content in header/footer by getting
	 *		header data for header view to be added to any view
	 *		request. All view requests site wide will go through 
	 *		the load_view function by providing the path to view
	 *		file. 
	 */


Class MY_Controller extends CI_Controller 
{
	
	public $current_year;	
	public $current_week;
	
	public function __construct() 
    {
		parent::__construct();
		
		$this->load->library('javascript');
		
		$this->load->model('Teams');
		$this->load->model('Trades');
		$this->load->model('Franchise');
		$this->load->model('Rosters');
		
		
			$league_id = $this->session->userdata('league_id');
			$this->current_year = $this->Leagues->get_current_season($league_id);
			$this->current_week = $this->Leagues->get_current_week($league_id);
		
		
	}
	
	//Loads all view ruequests by taking view files folder within
	//the ./application/views/ folder and any content data needed
	//by the view.  Prepends header, appends footer, with view in between
	public function load_view($path='home', $content_data=array(), $header=false, $footer=true, $menu=true)
	{
		
		//check ads
		
		$content_data['ads']=0;
		if($this->session->userdata('logged_in')==1){
			$last_ads_query = $this->db->select('last_adview')
										->where('user_id',$this->session->userdata('user_id'))
										->get('Owners');
			$ads_time = $last_ads_query->result_array();
			if($ads_time['0']['last_adview']<(now()-259200)){
				$content_data['ads']=1;
				//update the time
				$this->db->where('user_id',$this->session->userdata('user_id'))
							->set('last_adview',now())
							->update('Owners');
			}
		}
		//a variable to make sure the ads only appear on the home page
		$content_data['view']=$path;
		if($menu)
		{
			$header_data = $this->get_header_data();
			$this->load->view('header', $header_data);
		}
		//if it's a common page whith the title heading, then the $header true 
		//will be passed, otherwise no heading will be added to the page	
		if($header) {	
			$this->load->view('content_header',$content_data);
			$this->load->view($path);
		} else {
			$this->load->view($path, $content_data);
		}
		if($footer) {	
			$this->load->view('footer');
		}
	}

//************************************************************************	
	//Gets all data for header view
	function get_header_data()
	{
		//session data for general manager portion of menu
		$header_data['logged_in']=$this->session->userdata('logged_in');
      	$header_data['user_id']=$this->session->userdata('user_id');
		$header_data['team_id']=$this->Teams->get_team_id($this->session->userdata('user_id'),$this->session->userdata('league_id'));
		$header_data['team_name_first_nickname']=$this->Teams->get_team_name_first_nickname($header_data['team_id']);
		$header_data['week']=$this->current_week;
		$header_data['year']=$this->current_year;
		//all team names and ids for teams list
		$league_id =1;//***NI***change this when multi-league feature is available.
		$header_data['all_teams']=$this->Teams->get_all_teams_by_division_id_nickname($league_id);
		
		//get alerts.  types: warning, info, success, danger
		$header_data['alerts']=array();
		if($header_data['team_id'] != ''){
			
			//check next season event
			$next_event = $this->Calendars->get_next_event($league_id);
			
			if(!empty($next_event) && (($next_event['time'] < (now() + 604800)) || ($next_event['short_name']=='OTAs' && $next_event['time'] < (now() + 2628000)))){
				
				$header_data['alerts']['event']['alert_type']= 'info';
				$header_data['alerts']['event']['message']='<span id="clock" time="'.date('Y/m/d H:i:s',$next_event['time']).'"></span> until '.$next_event['short_name'];

			}
			//check if the team has open trade offers
			if($this->Trades->get_team_open_trades($header_data['team_id'],$this->current_year)>0){
				$header_data['alerts']['trades']['alert_type']= 'danger';
				$header_data['alerts']['trades']['message']='You have trade offers. <a href="'.base_url().'Trade">Respond</a>';
			}
			//check if the team has selected franchise players. only during week 0
			$franchise = $this->Franchise->get_simple_franchise_by_year($header_data['team_id'],$this->current_year);
			if(count($franchise)<1 && $this->current_week==0){
				$header_data['alerts']['franchise']['alert_type']= 'danger';
				$header_data['alerts']['franchise']['message']='No franchise players selected. <a href="'.base_url().'Team/id/'.$header_data['team_id'].'/franchise">Make Selections</a>';
			}
			//check if the team has too many players after week 0
			$roster_count = count($this->Rosters->get_team_active_roster($header_data['team_id']));
			
			if($roster_count>$this->Rosters->get_league_active_roser_limit($league_id) && $this->current_week>0){
				$header_data['alerts']['roster_cuts']['alert_type']= 'danger';
				$header_data['alerts']['roster_cuts']['message']='You must <a href="'.base_url().'Team/id/'.$header_data['team_id'].'/roster">release players</a>.';
			}
			
			//check facebook token
			if($this->session->userdata('security_level')==3){
				$facebook_time = $this->Facebook_Interact->get_token_update_time($league_id);
				$time_to_update_token = strtotime('+1 month',$facebook_time);
				if($time_to_update_token < now()){
					$header_data['alerts']['facebook']['alert_type']= 'danger';
					$header_data['alerts']['facebook']['message']='Update the Facebook Token <a href="'.base_url().'Admin">Admin</a>';
				}
			}
			
			
		}
		//end alerts
		
		return $header_data;
	}
}

/*End of file MY_Controller.php*/
/*Location: ./application/core/MY_Controller.php*/