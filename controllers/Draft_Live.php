<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Draft_Live extends MY_Controller
{
	/**
	 * Draft_Live controller.
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
	public function index($draft_id,$page_content='live_draft', $content_data=array()) 
    {
		
		//this is loading a view from one of the methods to display on the page
			//RESTRICTED TO LOGGED IN MEMBERS ONLY
			//Just in case not logged in we direct to login page
			if (!$this->session->userdata('logged_in') )
			{
				redirect("/Restricted");
			} 
			
			
			$path = 'draft/live_draft/'.$page_content;
			
			$this->load_view($path, $content_data, false, false, false);
			
	}
	
//**************************************************************************
	
	//send th eviewer of a live draft to here. Loads the container.
	//it then loads the view for the live draft which manages the calling
	//of the methods that retrun the SSEs
	public function live($draft_id=0,$page_content='live_draft',$team_id=0){
		//RESTRICTED TO LOGGED IN MEMBERS ONLY
		//Just in case not logged in we direct to login page
		if (!$this->session->userdata('logged_in') )
		{
			redirect("/Restricted");
		} 
		if($draft_id==0){
			$next_details = $this->Drafts->get_next_draft_details($this->league_id);
			$draft_id = $next_details['draft_id'];
		}

		$year = $this->current_year;
		//get the id of the session team 
		if($team_id==0){
			$team_id = $this->session->userdata('team_id');
		}
		
		$league_id = $this->league_id;
		
		$content_data['current_year'] = $year;
		$content_data['team_id'] = $this->session->team_id;
		
		
		//content to initially display
		$content_data['display_page']=$page_content;
		$content_data['load_path'] = 'Draft_Live/'.$page_content.'/'.$draft_id.'/'.$team_id;
		$details= $this->Drafts->get_draft_details($draft_id);
		$content_data['dropdown_title'] = date('l',$details['start_time']);
		//content selector will be activated for these views
		//set the list of items for content selector
		//get the max and min drafts in the league
		$draft_ids_array = $this->Drafts->get_league_draft_ids($league_id,$year);
		foreach($draft_ids_array['Common'] as $draft_id => $number_of_rounds){
			$details= $this->Drafts->get_draft_details($draft_id);
			$content_data['content_selector'][date('l',$details['start_time'])] = base_url().'Draft_Live/live_draft/'.$draft_id.'/'.$team_id;
		}

		
		//titles of the pages will be upper cased
		$title = 'FFFL Live Drafts' ;
		$content_data['title']= ucwords($title);
		$path = 'draft/live_draft/live_draft_container';
		
		
		$this->load_view($path, $content_data, true);
		
	}
	
	
//***************************************************************************
	//this is view related. Sending the data for the initial view. The other methods
	//will manage the data Pushing. The other methods will be called by the JS in the views
	//this method sends the initial, current status of the draft, loading data to the view. 
	public function live_draft($draft_id,$team_id){
		
		//team viewing and picking from this browser
		$content_data['team_id']=$team_id;
      	$content_data['security_level']=$this->session->userdata('security_level');
		$content_data['draft_id']=$draft_id;
		
		$content_data['year']=$this->current_year;
		//get draft details: number_of_rounds, picks_per_round,start_time, status, timer_expiration, paused
		$draft_details = $this->Drafts->get_draft_details($draft_id);
		//send current timer end time
		$content_data['remaining_time']=$draft_details['timer_expiration']-now();
		$content_data['pause']=$draft_details['paused'];
		$content_data['status']=$draft_details['status']; 
		
		//get draft order and picks already made from draft_picks table
		//returns array of pick_number=>team_id,fffl_player_id in pick order
		$all_picks = $this->Drafts->get_drafted_players($draft_id);
		$content_data['draft_picks']=$all_picks;
		//send team's current draft selections from draft_lists
		$content_data['team_draft_list'] = $this->Drafts->team_draft_list($team_id,$draft_id);
		
		//get current pick
		$current_pick=$this->Drafts->get_current_pick($draft_id);
		$content_data['current_pick'] = $current_pick['pick_number'];
		
		//send array of available players minus teams roster from json draftable file minus drafted players from draft_picks
		//get the draftable players array
		$url = base_url().'assets/json/draftable.json';
		$headers = get_headers($url);
		$file_exists = substr($headers[0], 9, 3);
		
		if($file_exists === "200")
		{
			$file = file_get_contents($url);
			$draftable = json_decode($file,true);
			$draftable = $draftable['draftable'];
		}
		
		foreach($all_picks as $pick_number => $pick_data){
			
			//add team logo path
			$content_data['draft_picks'][$pick_number]['logo_path']=$this->Teams->get_team_logo_path($pick_data['team_id']);
			//remove players already drafted
			
				unset($draftable[$pick_data['fffl_player_id']]);
			
		}
		
		//remove players from roster
		$team_roster = $this->Rosters->get_team_complete_roster($team_id);
		foreach($team_roster as $fffl_player_id){
			
				unset($draftable[$fffl_player_id]);
			
		}
			
		//add inactive to inactives
			$inactives = $this->Players->get_all_player_ids_no_objects("Players.current_team='FA' or Players.current_team='RET'","fffl_player_id");
			
		foreach($draftable as $fffl_player_id=>$position){

			$addon='';
			if(in_array($fffl_player_id,$inactives['ids'])){
				$addon = ' inactive';	
			}
			$available_players[$fffl_player_id]=$position.$addon;
		}
		
		$content_data['draftable']=$available_players;
		
		//send is team in auto draft mode from draft_lists
		$content_data['autodraft']=$this->Drafts->is_autodraft($team_id,$draft_id);
		
		//send all teams for commissioner view
		if($content_data['security_level']==3){
			$content_data['all_teams_id_name'] = $this->Teams->get_all_team_id($this->league_id);
		}
		
		
		//send chat messages from json file
		$url = base_url().'assets/json/chat'.$this->current_year.'.json';
		$headers = get_headers($url);
    	$file_exists = substr($headers[0], 9, 3);

		$chat_file = file_get_contents($url);
		
		$chat_array = json_decode($chat_file,true);
		
		$content_data['chat_array'] = $chat_array['chat'];
		
		
		$this->index($draft_id,'live_draft', $content_data);
	}
//**************************************************************************
	
	//an SSE that gets the available players list updated
	/*
	public function available_players($draft_id,$team_id){
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		
		$time = date('r');
		$url = base_url().'assets/json/draftable.json';
		$headers = get_headers($url);
    	$file_exists = substr($headers[0], 9, 3);
		
		if($file_exists === "200")
		{
			$draftable_file = file_get_contents($url);
			$draftable = json_decode($draftable_file,true);
			echo 'event: draftable';
			foreach($draftable['draftable'] as $fffl_player_id){
				echo "data: ".$fffl_player_id.PHP_EOL;
			}
			echo "\n\n";
			flush();
		}
			
		
	}*/
	
//********************************************************************

	public function chat_update(){
		require_once('/home1/theffflc/public_html/fantasy/application/libraries/composer/vendor/autoload.php');

		$text = htmlspecialchars($_POST['message']);
		$team = team_name_no_link($this->session->userdata('team_id'));
		
		//write to json file
		//get the list of players who were fa before the last fa draft
		$url = base_url().'assets/json/chat'.$this->current_year.'.json';
		$headers = get_headers($url);
    	$file_exists = substr($headers[0], 9, 3);

		$chat_file = file_get_contents($url);
		$chat_array = json_decode($chat_file,true);
		
		$new_message_array= array('chat_team' => $team, 'message' => $text);
		array_unshift($chat_array['chat'],$new_message_array);
		
		$fp = fopen('/home1/theffflc/public_html/fantasy/assets/json/chat'.$this->current_year.'.json', 'w');
		fwrite($fp, json_encode($chat_array));
		
		//send to pusher
		
		$pusher = new Pusher('524300fc2161d2996a58', 'a91e156959c4de36dc64', '164802');
		$data['chat_team'] = $team;
		$data['message']= $text;
		$pusher->trigger('chats', 'new_chat', $data);
		
		
		// you can run the built-in PHP web server using the following command:
		// `php -S localhost:8000`	
		
		
	}
	
//*********************************************************************
	//adds to a teams draft selection list
	public function add_selection($fffl_player_id,$draft_id,$team_id=0){
		if($team_id==0){
			$team_id = $this->session->userdata('team_id');
		}
		$this->Drafts->add_draft_selection($team_id,$fffl_player_id,$draft_id);	
		
		//$this->live_draft($draft_id,$team_id);
		
		echo json_encode(array('id'=>$fffl_player_id));
	}
	
//*********************************************************************
	//removes player from a teams draft selection list
	public function remove_selection($fffl_player_id,$draft_id,$team_id=0){
		if($team_id==0){
			$team_id = $this->session->userdata('team_id');
		}
		$this->Drafts->remove_draft_selection($team_id,$fffl_player_id,$draft_id);	
		
		echo json_encode(array('id'=>$fffl_player_id));
	}

//******************************************************************************
	 public function update_selections($team_id,$draft_id){
	 
	 	//update the selections
		$update_selections = $this->input->post('list_order');
		if(is_null($update_selections)) { redirect("/Restricted");}
		$list = explode(',',$update_selections);
		$i=1;
		foreach($list as $fffl_player_id){
			$this->db->set('priority',$i)
					->where('team_id',$team_id)
					->where('draft_id',$draft_id)
					->where('fffl_player_id',$fffl_player_id)
					->update('Draft_Lists');	
			$i++;
			
		}
		
	 }	

//********************************************************************************
	//changes autodraft to 0 for off, 1 for submit one pick, 2 for on
	public function update_autodraft($team_id,$draft_id,$autodraft){
		$this->db->set('autodraft',$autodraft)
					->where('team_id',$team_id)
					->where('draft_id',$draft_id)
					->update('Draft_Lists');		
		
	}


//********************************************************************************
	//checks if a pick is available, if so it sends to model to make the pick, then pussher to broadcast
	//if no  it checks the timer. If timer expired, advance the pick and record the pass, then pusher to broadcast
	public function pick_check($draft_id){
		require_once('/home1/theffflc/public_html/fantasy/application/libraries/composer/vendor/autoload.php');	
		$draft_details = $this->Drafts->get_draft_details($draft_id);
		$passing_picks = $this->Drafts->get_passing_picks($draft_id);
		$data['makeup_picks']=$passing_picks;
		
		if($draft_details['status']==1 && $draft_details['paused']==0){
			//check all -1 players, passed teams, to see if a pick is ready. and make a pick
			$makeup_pick=0; //if a makeup pick is made, change to 1 so as not to make another pick
			$passing_picks = $this->Drafts->get_passing_picks($draft_id);
			if(count($passing_picks)>0){
				foreach($passing_picks as $pick_id=>$team_id){
					$makeup_pick_details = $this->Drafts->get_pick_details($pick_id);
					//check if the team autodraft setting is >0
					$team_autodraft = $this->Drafts->is_autodraft($makeup_pick_details['current_owner'],$draft_id);
					if($team_autodraft>0 && $makeup_pick==0){//if >0 there must be a player and autodraft is on
						
						$fffl_player_id = $this->Drafts->make_draft_pick($makeup_pick_details['current_owner'],$draft_id,$pick_id);
						$this->update_autodraft($makeup_pick_details['current_owner'],$draft_id);
						unset($data['makeup_picks'][$pick_id]);
						//pusher all draft details: pick number, player name, new current pick, new timer
						//send to pusher
						
						$pusher = new Pusher('524300fc2161d2996a58', 'a91e156959c4de36dc64', '164802');
						//pick just made
						$data['pick_team_id'] = $makeup_pick_details['current_owner'];
						$data['player_id'] = $fffl_player_id;
						$data['pick_number'] = $makeup_pick_details['pick_number'];
						$data['player_name']= player_name_no_link($fffl_player_id);
						//next pick
							$current_pick = $this->Drafts->get_current_pick($draft_id);
							$pick_details = $this->Drafts->get_pick_details($current_pick['pick_id']);				
							$draft_details = $this->Drafts->get_draft_details($draft_id);
						$data['current_pick']= $current_pick['pick_number'];
						$data['round_number']=$pick_details['round'];
						$data['timer_expiration'] = $draft_details['timer_expiration'];
						$data['current_pick_team']=team_name_no_link($pick_details['current_owner']);	
						$data['current_pick_team_id']=$pick_details['current_owner'];	
						$data['current_logo']=$this->Teams->get_team_logo_path($pick_details['current_owner']);
						$pusher->trigger('pick_made', 'new_pick', $data);
						
						$makeup_pick=1;
						
					}//end if the team is ready to make a pick
								
				}//end for each passed pick
				
			}//end count passed picks
			
			
			
			//check if a pick is available
			//whose pick is it? $current_pick = [pick_id],[pick_number]
			$current_pick = $this->Drafts->get_current_pick($draft_id);
			$pick_details = $this->Drafts->get_pick_details($current_pick['pick_id']);
			//check if the team autodraft setting is >0
			$team_autodraft = $this->Drafts->is_autodraft($pick_details['current_owner'],$draft_id);
			if($team_autodraft>0 && $makeup_pick==0){//if >0 there must be a player and autodraft is on  ****MAKE AN ELSE IF ONCE PASSING IS ADDED
				
				$fffl_player_id = $this->Drafts->make_draft_pick($pick_details['current_owner'],$draft_id,$current_pick['pick_id']);
				$this->update_autodraft($makeup_pick_details['current_owner'],$draft_id);
				
				//reset the timer
				$this->Drafts->reset_timer($draft_id);
				
				//pusher all draft details: pick number, player name, new current pick, new timer
				//send to pusher
				
				$pusher = new Pusher('524300fc2161d2996a58', 'a91e156959c4de36dc64', '164802');
				//pick just made
				$data['pick_team_id'] = $pick_details['current_owner'];
				$data['player_id'] = $fffl_player_id;
				$data['pick_number'] = $current_pick['pick_number'];
				$data['player_name']= player_name_no_link($fffl_player_id);
				//next pick
					$current_pick = $this->Drafts->get_current_pick($draft_id);
					$pick_details = $this->Drafts->get_pick_details($current_pick['pick_id']);				
					$draft_details = $this->Drafts->get_draft_details($draft_id);
				$data['current_pick']= $current_pick['pick_number'];
				$data['round_number']=$pick_details['round'];
				$data['timer_expiration'] = $draft_details['timer_expiration'];
				$data['current_pick_team']=team_name_no_link($pick_details['current_owner']);		
				$data['current_logo']=$this->Teams->get_team_logo_path($pick_details['current_owner']);
				$data['current_pick_team_id']=$pick_details['current_owner'];
				$pusher->trigger('pick_made', 'new_pick', $data);
				
				
			}//end if the team is ready to make a pick
			elseif($makeup_pick==0){ //check the timer
				//get the current timer expiration
				$details = $this->Drafts->get_draft_details($draft_id);
				$timer_expiration = $details['timer_expiration'];
				
				//if time passed, record the pass, send to pusher
				if($timer_expiration<now()){
					$this->Drafts->record_pick_pass($pick_details['current_owner'],$draft_id,$current_pick['pick_id']);
					//reset the timer
					$this->Drafts->reset_timer($draft_id);
					$data['makeup_picks'][$current_pick['pick_id']]=$pick_details['current_owner'];
					//pusher all draft details: pick number, player name, new current pick, new timer
					//send to pusher
					
					$pusher = new Pusher('524300fc2161d2996a58', 'a91e156959c4de36dc64', '164802');
					//pick just made
					$data['pick_team_id'] = $current_pick['current_owner'];
					$data['player_id'] = -1;
					$data['pick_number'] = $current_pick['pick_number'];
					$data['player_name']= 'Time Expired';
					//next pick
						$current_pick = $this->Drafts->get_current_pick($draft_id);
						$pick_details = $this->Drafts->get_pick_details($current_pick['pick_id']);				
						$draft_details = $this->Drafts->get_draft_details($draft_id);
					$data['current_pick']= $current_pick['pick_number'];
					$data['round_number']=$pick_details['round'];
					$data['timer_expiration'] = $draft_details['timer_expiration'];
					$data['current_pick_team']=team_name_no_link($pick_details['current_owner']);		
					$data['current_logo']=$this->Teams->get_team_logo_path($pick_details['current_owner']);
					$data['current_pick_team_id']=$pick_details['current_owner'];
					$pusher->trigger('pick_made', 'new_pick', $data);	
					
				}
			
			}
		}//if status = 1 so draft is started
		$time = now();
		echo json_encode($time);
	}
	
//*************************************************************************************
	
	public function	pause_draft($draft_id,$pause_status){
			require_once('/home1/theffflc/public_html/fantasy/application/libraries/composer/vendor/autoload.php');	
			$this->Drafts->pause_draft($draft_id,$pause_status);
			
				
			//pusher all draft details: pick number, player name, new current pick, new timer
			//send to pusher
			if($pause_status==1){//pause it
				$data['message'] = "Please Wait. We'll Continue Shortly.";
				$data['pause_status'] = $pause_status;
			}
			else {
				$data['message'] = "Resuming...";
				$data['pause_status'] = $pause_status;
				$draft_details = $this->Drafts->get_draft_details($draft_id);
				$data['timer_expiration'] = $draft_details['timer_expiration'];
				
			}
			$pusher = new Pusher('524300fc2161d2996a58', 'a91e156959c4de36dc64', '164802');
	
			$pusher->trigger('pauser', 'pause', $data);	
		
	}
	
//*************************************************************************************
	
	public function	start_end_draft($draft_id,$action){
			require_once('/home1/theffflc/public_html/fantasy/application/libraries/composer/vendor/autoload.php');	
			$this->Drafts->start_end_draft($draft_id,$action);
			
				
			//pusher all draft details: pick number, player name, new current pick, new timer
			//send to pusher
			$data['status']=$action;
			if($action==1){//start it
				$data['message'] = "Draft Begins Now.";
				$draft_details = $this->Drafts->get_draft_details($draft_id);
				$data['timer_expiration'] = $draft_details['timer_expiration'];
			}
			else {
				$data['message'] = "Draft Complete";
				$data['timer_expiration'] = 0;
			}
			$pusher = new Pusher('524300fc2161d2996a58', 'a91e156959c4de36dc64', '164802');
	
			$pusher->trigger('start_end', 'start_end', $data);	
		
	}
	
	
//**********************************************************************

	public function load_absentee_pick($draft_id,$team_id){
		
      	if($this->session->userdata('security_level')==3){
			$content_data['draft_id']=$draft_id;
			$content_data['team_id']=$team_id;
			$content_data['year']=$this->current_year;
			//send is team in auto draft mode from draft_lists
			$content_data['autodraft']=$this->Drafts->is_autodraft($team_id,$draft_id);
			$all_picks = $this->Drafts->get_drafted_players($draft_id);
			$content_data['draft_picks']=$all_picks;
			//send team's current draft selections from draft_lists
			$content_data['team_draft_list'] = $this->Drafts->team_draft_list($team_id,$draft_id);
			
			//send array of available players minus teams roster from json draftable file minus drafted players from draft_picks
			//get the draftable players array
			$url = base_url().'assets/json/draftable.json';
			$headers = get_headers($url);
			$file_exists = substr($headers[0], 9, 3);
			
			if($file_exists === "200")
			{
				$file = file_get_contents($url);
				$draftable = json_decode($file,true);
				$draftable = $draftable['draftable'];
			}
			
			foreach($all_picks as $pick_number => $pick_data){
				//remove players already drafted
				
					unset($draftable[$pick_data['fffl_player_id']]);
				
			}
			
			//remove players from roster
			$team_roster = $this->Rosters->get_team_complete_roster($team_id);
			foreach($team_roster as $fffl_player_id){
				
					unset($draftable[$fffl_player_id]);
				
			}
			//remove from draft list
			foreach($content_data['team_draft_list'] as $fffl_player_id){
				unset($draftable[$fffl_player_id]);
			}
			
			foreach($draftable as $fffl_player_id=>$position){

			
				$available_players[$fffl_player_id]=$position;
			}
			$content_data['draftable']=$available_players;
			
			//titles of the pages will be upper cased
		$title = 'Absentee Pick' ;
		$content_data['title']= ucwords($title);
		$path = 'draft/live_draft/absentee_pick';
		//content to initially display
		$content_data['display_page']='absentee_pick';
		$content_data['load_path'] = 'Draft_Live/load_absentee_pick/'.$draft_id.'/'.$team_id;
		
		$this->load_view($path, $content_data, true);
		}
			
	}


	
}//end Class Player extends MY_Controller

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

