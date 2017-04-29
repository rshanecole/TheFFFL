<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Player extends MY_Controller
{
	/**
	 * Player controller.
	 *
	 * accepts requests for player list and player page views 
	 */
	public $team_id;
	public $league_id=1; //ELIMINATE THIS WHEN A MEANS TO ADD MORE LEAGUES IS CREATED
	//Load the needed libraries.  
	public function __construct() 
    {
			parent::__construct();

			$this->load->model('Players');
			$this->load->model('NFL_Teams');
			$this->load->model('NFL_Games');
			$this->load->model('Leagues');
			$this->load->model('NFL_stats');
			$this->load->model('Rosters_View');
			$this->load->model('Free_Agents');
		
			$this->load->helper('links');
			$this->load->helper('form');
		
			//RESTRICTED TO LOGGED IN MEMBERS ONLY
			//Just in case not logged in we direct to login page
			if (!$this->session->userdata('logged_in') )
			{
				redirect("/Restricted");
			} 
			else
			{
				
				$this->team_id = $this->session->team_id;
			}
		
		}

//********************************************************************************
	// Loads the content for the players view, types of content are:
	// 
	// 
	public function index($league_id=1, $page_content='search', $content_data=array())
	{
		
				
			//titles of the pages will be upper cased
			global $team_id;
			$title = $content_data['title'];
			$content_data['title']= ucwords($title);
			$path ='players/'.$page_content;
			$this->load_view($path, $content_data, true);
	}

//********************************************************************************
	
	// Loads the content for the players search page
	// filter_array keeps only those that meet the criteria in an array in each key: 
	//		[position],[nfl_team],[team],[average_minimum],[rookies_veterans],[free_agent]
	// 
	public function search()
	{
		
		//content to initially display
		$data['display_page']='filter';
		$data['load_path'] = 'Player/filter/';
		//titles of the pages will be upper cased
		$title = 'Players';
		$data['title']= ucwords($title);
		$page='search_container';
		$path ='players/'.$page;
		
		
		$this->index('1',$page, $data);
	}
  
  //*****************************************************************************
  	//take sthe filter array and returns the search page with jsut the qualifying players
	//in paramters add sorts array items after filter items 
  	public function filter($number=75,$page=1,$QB=1,$RB=1,$WR=1,$TE=1,$K=1,$NFL_FA=0,$current_team='All',$is_rookie=0,$free_agents=0,$is_injured=0,$team='All',$salary_low='0',$salary_high="100",$name_like='0',$injured_players='Include',$supplemental_eligible=1,$draftable=1,$undraftable=1,$sort='Players.last_name',$direction='ASC',$sort_week=0)
	{	
		//these must be in the same order as the paramters
		
		$data['pagination']['number']=$number;
		$data['pagination']['page']=$page;
		////go through each fillter item and create an exclusion if it's truee
		$exclusions_string=''; $first_exclusion=0;
		$join_rosters = 0;
		$positions = array('QB','RB','WR','TE','K');
		foreach($positions as $position){
			$data['filters_array'][$position]=$$position;
			if(!$$position){
				if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
				$exclusions_string.="Players.position<>'".$position."'";	
			}
		}
		
		$data['filters_array']['NFL_FA']=$NFL_FA;
		if(!$NFL_FA){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.="Players.current_team<>'FA'#Players.current_team<>'RET'#Players.nfl_status<>'RET'";	
		}
		$data['filters_array']['current_team']=$current_team;
		if($current_team!='All'){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.="Players.current_team='".$current_team."'";	
		}
		$data['filters_array']['is_rookie']=$is_rookie;
		if($is_rookie){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.='Players.is_rookie=1';	
		}
		$data['filters_array']['free_agents']=$free_agents;//filter out free agents later
		
		$data['filters_array']['is_injured']=$is_injured;
		if($is_injured){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.='Players.is_injured<>1';	
		}
		$data['filters_array']['team']=$team;
		if($team!='All'){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.='Rosters.team_id='.$team;	
			$join_rosters=1;
		}
		$data['filters_array']['salary_low']=$salary_low;
		if($salary_low>0){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.='Rosters.salary>='.$salary_low;	
			$join_rosters=1;
		}
		$data['filters_array']['salary_high']=$salary_high;
		if($salary_high<100){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.='Rosters.salary<='.$salary_high;	
			$join_rosters=1;
		}
		$data['filters_array']['name_like']=$name_like;
		if($name_like!='0'){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.="CONCAT(Players.first_name,' ',Players.last_name) LIKE '%".$name_like."%'";	
		}
		$data['filters_array']['injured_players']=$injured_players;
		if($injured_players!='Include'){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			if($injured_players=='Only'){
				$exclusions_string.="Players.nfl_injury_game_status<>''";	
			}
			if($injured_players=='Remove'){
				$exclusions_string.="Players.nfl_injury_game_status=''";	
			}
		}
		$data['filters_array']['supplemental_eligible']=$supplemental_eligible;//filter out supplemental later
		$data['filters_array']['draftable']=$draftable;//filter out draftable later
		$data['filters_array']['undraftable']=$undraftable;//filter out undraftable later
		
		
		//an array of all the sort criteria
		$data['current_sort']='';
		if($sort=='Rosters.salary'){ $join_rosters=1; $data['current_sort']='Salary: '; }
		if($sort=='average'){ $data['current_sort']='Average: ';  }
		
		
		if($direction=='DESC'){ $data['current_sort'].='High to Low'; } else { $data['current_sort'] .= 'Low to High'; }
		if($sort=='Players.last_name'){ $data['current_sort']='Last Name';  }
		$data['sort_array']['sort']=$sort;
		$data['sort_array']['direction']=$direction;
		
		if($sort=='average'){ $sort = 'Players.last_name'; }
		if($sort=='week'){ $data['current_sort'] = 'Week '.$sort_week.':'; }
		if($sort=='week'){ $sort = 'Players.last_name'; }
		$data['sort_array']['sort_week']=$sort_week;
		
		$data['week']=$this->current_week;
		$data['year']=$this->current_year;
		
		//first get ids of all the active players, parameters are the status desired and a string of exceptions to add
		//to the where of the query separated by spaces
		
		$offset=$number*($page-1);
		
		$players = $this->Players->get_all_player_ids($exclusions_string,'fffl_player_id',$sort,$direction,$join_rosters);
		
		$players_array = $players['ids'];
		if(count($players_array)==0) { $players_array=array(); }
		//get free agents only
		if($data['filters_array']['free_agents']==1){
			foreach($players_array as $key => $fffl_player_id){
				if($this->Free_Agents->get_player_number_free_agents($fffl_player_id->fffl_player_id)==0){
					unset($players_array[$key]);
				}
				
			}
			$players['count']=count($players_array);
		}
		//if before the draft user might filter out the draftable, supplemental and undraftable players 
		//filter draftable players
		
				$url = base_url().'assets/json/draftable.json';
				$headers = get_headers($url);
				$file_exists = substr($headers[0], 9, 3);
				
				if($file_exists === "200")
				{
					$file = file_get_contents($url);
					$draftable = json_decode($file,true);
				}
				if($data['filters_array']['draftable']==0){
					foreach($players_array as $key=>$fffl_player_id){
						if(in_array($fffl_player_id->fffl_player_id,array_keys($draftable['draftable']))){
							unset($players_array[$key]);
						}
						
					}
					$players['count']=count($players_array);
				}
				
				//filter supplemental_eligible players
				$url = base_url().'assets/json/supplemental_eligible.json';
				$headers = get_headers($url);
				$file_exists = substr($headers[0], 9, 3);
				
				if($file_exists === "200")
				{
					$file = file_get_contents($url);
					$supplemental_eligible = json_decode($file,true);
				}
				
				if($data['filters_array']['supplemental_eligible']==0){ 
					foreach($players_array as $key => $fffl_player_id){ 
						if(in_array($fffl_player_id->fffl_player_id,$supplemental_eligible['supplemental'])){
							
							unset($players_array[$key]);
						}
						
					}
					$players['count']=count($players_array);
				}
				//filter undraftable players
				
				if($data['filters_array']['undraftable']==0){
					foreach($players_array as $key => $fffl_player_id){ 
						if(!in_array($fffl_player_id->fffl_player_id,$supplemental_eligible['supplemental']) && !in_array($fffl_player_id->fffl_player_id,array_keys($draftable['draftable']))){
							
							unset($players_array[$key]);
						}
						
					}
					$players['count']=count($players_array);
				}

		
		if(count($players_array)==0) { $players_array=array(); }
		if(!in_array($data['sort_array']['sort'],array('average','salary','week'))){
			//add the player info array as data to be sent to the view
			//slice now because average doesn't matter, we'll reduce the number of player averages calculated
			
			$players_array= array_slice($players_array,$offset,$number);
		}
       	$data['pages']=ceil($players['count']/$number);
		
      //create an array to house all player info
		$players_info_array = array();
		//go through each player id and add all player info for the search page to the player info array
		foreach($players_array as $id)
		{	
			//call the get_player_info function from the players model. each item returned is in an array with item name as the key
			//all items we want for this array are listed in the 3rd parameter separated by a space
			$players_info_array[$id->fffl_player_id]=$this->Players->get_player_info($id,"fffl_player_id","first_name last_name current_team position is_rookie is_injured injury_text nfl_injury_game_status nfl_status nfl_esbid"); 
			$players_info_array[$id->fffl_player_id]['salaries'] = $this->Players->get_player_salaries($id->fffl_player_id);
			
			$players_info_array[$id->fffl_player_id]['fffl_player_id']=$id->fffl_player_id;
			$players_info_array[$id->fffl_player_id]['bye_week']=$this->NFL_Teams->get_team_bye_week($players_info_array[$id->fffl_player_id]['current_team']);
			//add headlines
			$players_info_array[$id->fffl_player_id]['headlines']=$this->Players->get_player_headlines($id->fffl_player_id,1);
			//get a player's scores for the current season
			//get the current season from Leagues model
			$current_season = $this->Leagues->get_current_season($this->league_id);
			$current_week = $this->Leagues->get_current_week($this->league_id);
			if($current_week==0){ $current_week=16; $current_season=$current_season-1;}
			$players_info_array[$id->fffl_player_id]['scores'] = $this->NFL_stats->get_player_scores_season($current_season,$id->fffl_player_id,1,1,$current_week);
		}
		
		//sort by average after the array has been contstructed. Will slow down the load,but this sort must be done last
			
			if($data['sort_array']['sort']=='average' || $data['sort_array']['sort']=='salary'){
				// Obtain a list of columns
				foreach ($players_info_array as $key => $row) {
					$sort_criteria[$key]  = $row['scores'][$data['sort_array']['sort']];
					$last_name[$key] = $row['last_name'];
				}
				
				
				// Sort the data 
				// Add $data as the last parameter, to sort by the common key

				if($direction == 'DESC'){
					array_multisort($sort_criteria, SORT_DESC, $last_name, SORT_ASC, $players_info_array);
				} 
				else {
					array_multisort($sort_criteria, SORT_ASC, $last_name, SORT_ASC, $players_info_array);	
				}
			
		
				//add the player info array as data to be sent to the view
				//slice now because average had to be sorted first
				
				$players_info_array= array_slice($players_info_array,$offset,$number);
			}
			elseif($data['sort_array']['sort']=='week'){
				// Obtain a list of columns
				foreach ($players_info_array as $key => $row) {
					//d($row['scores']);
					$sort_criteria[$key]  = $row['scores']['weeks'][$sort_week]['points'];
					$last_name[$key] = $row['last_name'];
				}
				
				
				// Sort the data 
				// Add $data as the last parameter, to sort by the common key

				if($direction == 'DESC'){
					array_multisort($sort_criteria, SORT_DESC, $last_name, SORT_ASC, $players_info_array);
				} 
				else {
					array_multisort($sort_criteria, SORT_ASC, $last_name, SORT_ASC, $players_info_array);	
				}
			
		
				//add the player info array as data to be sent to the view
				//slice now because average had to be sorted first
				
				$players_info_array= array_slice($players_info_array,$offset,$number);
				
			}
		
		$data['players_array'] = $players_info_array;
		
		//add the viewing team_roster and team_fa_requests arrays
		
		$data['team_fa_requests'] = $this->Free_Agents->get_team_distinct_requests($this->team_id);
		$data['team_roster'] = $this->Rosters->get_team_complete_roster($this->team_id); 
		
		
		
		//determine if draft filters should be shown
		//get the franchise deadline
		$franchise_time = $this->Calendars->get_calendar_time('franchise',1);
		$franchise_deadline=$this->Calendars->get_calendar_time('franchise',$this->league_id);
		
			
		if($franchise_time<now() && $this->current_week==0){//franchise deadline has passed
			$data['after_franchise']=1;
			//determine if drafts are over
			$draft_ids = $this->Drafts->get_league_draft_ids($this->league_id,$this->current_year);
			
			$data['draft_upcoming']=0;
			foreach($draft_ids['Common'] as $draft_id){
				$draft_data=$this->Drafts->get_draft_details($draft_id);
				if($draft_data['status']<3){
					$data['draft_upcoming']=1;
				}
			}
			foreach($draft_ids['Supplemental'] as $draft_id){
				$draft_data=$this->Drafts->get_draft_details($draft_id);
				if($draft_data['status']<3){
					$data['draft_upcoming']=1;
				}
			}

		}
		else{
			$data['after_franchise']=0;	
			$data['draft_upcoming']=0;
		}
		$path = 'players/search';
			
			$this->load_view($path, $data, false, false, false);

	}//filter method
	
//*****************************************************************************
  	//returns the view of the other filter options 
  	public function load_filter_list($number=75,$page=1,$QB=1,$RB=1,$WR=1,$TE=1,$K=1,$NFL_FA=0,$current_team='All',$is_rookie=0,$free_agents=0,$is_injured=0,$team='All',$salary_low='0',$salary_high="100",$name_like='0',$injured_players='Include',$supplemental_eligible=1,$draftable=1,$undraftable=1,$sort='Players.last_name',$direction='ASC',$sort_week=0)
	{	
		//these must be in the same order as the paramters
		
		$data['pagination']['number']=$number;
		$data['pagination']['page']=$page;
		////go through each fillter item and create an exclusion if it's truee
		$exclusions_string=''; $first_exclusion=0;
		$join_rosters = 0;
		$positions = array('QB','RB','WR','TE','K');
		foreach($positions as $position){
			$data['filters_array'][$position]=intval($$position);
			if(!$$position){
				if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
				$exclusions_string.="Players.position<>'".$position."'";	
			}
		}
		
		$data['filters_array']['NFL_FA']=intval($NFL_FA);
		if(!$NFL_FA){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.="Players.current_team<>'FA'#Players.current_team<>'RET'#Players.nfl_status<>'RET'";	
		}
		$data['filters_array']['current_team']=$current_team;
		if($current_team!='All'){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.="Players.current_team='".$current_team."'";	
		}
		$data['filters_array']['is_rookie']=$is_rookie;
		if($is_rookie){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.='Players.is_rookie=1';	
		}
		$data['filters_array']['free_agents']=$free_agents;
		
		$data['filters_array']['is_injured']=$is_injured;
		if($is_injured){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.='Players.is_injured<>1';	
		}
		$data['filters_array']['team']=$team;
		if($team!='All'){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.='Rosters.team_id='.$team;	
			$join_rosters=1;
		}
		$data['filters_array']['salary_low']=$salary_low;
		if($salary_low>0){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.='Rosters.salary>='.$salary_low;	
			$join_rosters=1;
		}
		$data['filters_array']['salary_high']=$salary_high;
		if($salary_high<100){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.='Rosters.salary<='.$salary_high;	
			$join_rosters=1;
		}
		$data['filters_array']['name_like']=$name_like;
		if($name_like!='0'){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.="CONCAT(Players.first_name,' ',Players.last_name) LIKE '%".$name_like."%'";	
			$join_rosters=1;
		}
		$data['filters_array']['injured_players']=$injured_players;
		if($injured_players!='Include'){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			if($injured_players=='Only'){
				$exclusions_string.="Players.nfl_injury_game_status<>''";	
			}
			if($injured_players=='Remove'){
				$exclusions_string.="Players.nfl_injury_game_status=''";	
			}
		}
		$data['filters_array']['supplemental_eligible']=$supplemental_eligible;//filter out supplemental later
		$data['filters_array']['draftable']=$draftable;//filter out draftable later
		$data['filters_array']['undraftable']=$undraftable;//filter out undraftable later
		
		
		//an array of all the sort criteria
		$data['current_sort']='';
		if($sort=='Rosters.salary'){ $join_rosters=1; $data['current_sort']='Salary: '; }
		if($sort=='average'){ $data['current_sort']='Average: ';  }
		
		
		if($direction=='DESC'){ $data['current_sort'].='High to Low'; } else { $data['current_sort'] .= 'Low to High'; }
		if($sort=='Players.last_name'){ $data['current_sort']='Last Name';  }
		$data['sort_array']['sort']=$sort;
		$data['sort_array']['direction']=$direction;
		
		if($sort=='average'){ $sort = 'Players.last_name'; }
		if($sort=='week'){ $data['current_sort'] = 'Week '.$sort_week.':'; }
		if($sort=='week'){ $sort = 'Players.last_name'; }
		$data['sort_array']['sort_week']=$sort_week;
		
		//get all nfl teams
		$data['all_nfl_teams']=$this->NFL_Teams->get_all_nfl_teams();
		
		//get all FFFL teams
		$data['all_teams']=$this->Teams->get_all_team_id(1);//***NI*** leagueid 1
 		
		$path = 'players/filter_options';
			
		$this->load_view($path, $data, false, false, false);
 
	}
 
  //****************************************************************************
  
  //loads the view for the player page
	// 
	public function player($fffl_player_id=NULL, $page_content='scoring', $content_data=array()) 
    {
		
		//this is loading a view from one of the methods to display on the page
			//RESTRICTED TO LOGGED IN MEMBERS ONLY
			//Just in case not logged in we direct to login page
			if (!$this->session->userdata('logged_in') )
			{
				redirect("/Restricted");
			} 
			
			if(!isset($team_id)) 
			{
				$team_id = $this->session->team_id;
			}
			
			
			
			
			$path = 'players/'.$page_content;
			
			$this->load_view($path, $content_data, false, false, false);

	}

 //************************************************************************************
  
  //sets the data for the player container page which will service a page for 
  //player scoring, matchup history, tranaction history, draft history, and franchise history
  // conainer will give name, nfl info, picture, teams and salaries, add fa and open fa,
  // rss news items 
  public function id($fffl_player_id, $page_content='scoring'){
    
    //content to initially display
		$content_data['fffl_player_id']=$fffl_player_id;
		$content_data['display_page']=$page_content;
		$content_data['load_path'] = 'Player/'.$page_content.'/'.$fffl_player_id;
		
		$player_query = $this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","nfl_esbid current_team position first_name last_name depth_chart_order is_rookie is_injured nfl_status nfl_jersey_number nfl_injury_game_status injury_text fa_salary");
		$content_data['player_data'] = $player_query;
		
		//get owner and salary info
		$owners = $this->Players->get_player_owners($fffl_player_id, "fffl_player_id");
		$open_fa_roster=0;
		if($owners){
			foreach($owners as $team_id){
				$image  = $this->Teams->get_team_logo_path($team_id->team_id);
				$salary = $this->Salaries->get_player_team_salary($team_id->team_id,$fffl_player_id);
				$content_data['owners'][$team_id->team_id]['image_path']=$image;	
				$content_data['owners'][$team_id->team_id]['salary']=$salary;
				$content_data['owners'][$team_id->team_id]['area'] = $this->Rosters_View->get_team_player_area($team_id->team_id,$fffl_player_id);
				if($team_id->team_id==$this->team_id){ $open_fa_roster =1; } 
			}
		}

		
		//if fa, add appropriate free agent link
		if(count($owners)<2){
			
			$content_data['add_fa']=TRUE;
			//get the team's fa request players
			$requests = $this->Free_Agents->get_team_distinct_requests($this->team_id);
			if(in_array($fffl_player_id,$requests)){
				$content_data['fa_requested']=TRUE;
			}
			else{
				$content_data['fa_requested']=FALSE;
			}
			if($open_fa_roster==0){
				$content_data['open_fa'] =$this->Free_Agents->open_free_agency_open($fffl_player_id);
				//d($content_data['open_fa']);
			}
			else{
				$content_data['open_fa']=FALSE;
			}
		}
		
		//get position ranking information. 
		//get first and last seasons
		$first_last = $this->NFL_stats->get_player_first_last_year($fffl_player_id);
		
		$year=$first_last['first'];
                if($this->current_week<17){ $last=$first_last['last']-1; }
		$ranks=array(); 
		if($player_query['is_rookie']==0){
			while($year<=$last){
				
				$ranks[$year]=$this->NFL_stats->get_player_ranking_year($fffl_player_id,$year)+1;
				
				$year++;
			}
			
	
			krsort($ranks);
		}
		else{
			$ranks[$year]=0;	
		}
		$content_data['ranks']=$ranks;
		//$this->session->set_userdata('ranks',$ranks);
		
		//get all-pro
		$content_data['all_pro']=$this->Players->get_times_all_pro($fffl_player_id);
			
		//get news headlines
		$content_data['headlines']=$this->Players->get_player_headlines($fffl_player_id,10);

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
			'Scoring' => base_url().'Player/scoring/'.$fffl_player_id,
			'Stats' => base_url().'Player/stats/'.$fffl_player_id,
			'Draft' => base_url().'Player/draft/'.$fffl_player_id,
			'Transactions' => base_url().'Player/transactions/'.$fffl_player_id,
			'Franchise' => base_url().'Player/franchise/'.$fffl_player_id
		);
		//titles of the pages will be upper cased
		
		$title = $player_query['position'].' '.$player_query['first_name'].' '.$player_query['last_name'];
		$content_data['title']= ucwords($title);
		$path = 'players/player_container';
    
    $this->load_view($path, $content_data, true);
    
  }
 
 //*********************************************************************

	//loads the scoring view that displays the players career scoring
	
	public function scoring($fffl_player_id,$year=NULL){
		
		
		$content_data=array();
		$content_data['fffl_player_id']=$fffl_player_id;
		//get first and last seasons
		if(is_null($year)){
			$first_last = $this->NFL_stats->get_player_first_last_year($fffl_player_id);
			$content_data['single_year']=FALSE;
			$year=$first_last['first'];
			$last= $first_last['last'];
			
		}
		else{
			$content_data['single_year']=TRUE;
			$last=$year;
		}
		//ranks not necessary since now sent through session to be shared with views
		while($year<=$last){
			if($content_data['scores'][$year]=$this->NFL_stats->get_player_scores_season($year,$fffl_player_id,1,1,16)){
				//if(!is_null($year)){
					$content_data['scores'][$year]['rank']=$this->NFL_stats->get_player_ranking_year($fffl_player_id,$year)+1;
					
				//}
			}
			$year++;
		}
		krsort($content_data['scores']);
		
		
		
		
		$this->player($fffl_player_id, 'scoring', $content_data);
	}
	
	//*********************************************************************

	//loads the scoring view that displays the players career scoring
	
	public function scoring_info($fffl_player_id,$year=NULL){
		
		
		$content_data=array();
		
		//get first and last seasons
		if(is_null($year)){
			$first_last = $this->NFL_stats->get_player_first_last_year($fffl_player_id);
			$content_data['single_year']=FALSE;
			$year=$first_last['first'];
			$last= $first_last['last'];
		}
		else{
			$content_data['single_year']=TRUE;
			$last=$year;
		}
		//ranks not necessary since now sent through session to be shared with views
		while($year<=$last){
			if($content_data['scores'][$year]=$this->NFL_stats->get_player_scores_season($year,$fffl_player_id,1,1,16)){
				//if(!is_null($year)){
					$content_data['scores'][$year]['rank']=$this->NFL_stats->get_player_ranking_year($fffl_player_id,$year)+1;
				//}
			}
			$year++;
		}
		krsort($content_data['scores']);
		
		//get additioanl player info
		$player_query = $this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","fffl_player_id nfl_esbid current_team position first_name last_name depth_chart_order is_rookie is_injured nfl_status nfl_jersey_number nfl_injury_game_status injury_text fa_salary");
		$content_data['player'] = $player_query;
		$content_data['player']['bye_week']=$this->NFL_Teams->get_team_bye_week($content_data['player']['current_team']);
		
		
		$this->player($fffl_player_id, 'scoring_info', $content_data);
	}
	
	
//*********************************************************************

	//loads the scoring view that displays the players career stats
	
	public function stats($fffl_player_id){
		
		
		$content_data=array();
		//get first and last seasons
		$first_last = $this->NFL_stats->get_player_first_last_year($fffl_player_id);
		
		$year=$first_last['first'];
	
		while($year<=$first_last['last']){
			$content_data['stats'][$year]=$this->NFL_stats->get_player_stats_season($year,$fffl_player_id,1,16);
			$year++;
		}
		krsort($content_data['stats']);
		
		
		
		$this->player($fffl_player_id, 'stats', $content_data);
	}
	
//*********************************************************************

	//loads the stats view that displays the players stats from game
	
	public function stats_info($fffl_player_id,$year,$week){
		
		
		$content_data=array();
		
		//get the stats for the game
		$content_data['stats']=array();
		
		$content_data['stats'] = $this->NFL_stats->get_player_stats_season($year,$fffl_player_id,$week,$week);
		
		//get additioanl player info
		$player_query = $this->Players->get_player_info(array($fffl_player_id),"fffl_player_id","fffl_player_id nfl_esbid current_team position first_name last_name depth_chart_order is_rookie is_injured nfl_status nfl_jersey_number nfl_injury_game_status injury_text fa_salary");
		$content_data['player'] = $player_query;
		$content_data['stats']['opponent']=$this->NFL_Teams->get_team_opponent($content_data['player']['current_team'],$week,$year);
		$content_data['player']['bye_week']=$this->NFL_Teams->get_team_bye_week($content_data['player']['current_team']);
		
		
		$this->player($fffl_player_id, 'stats_info', $content_data);
	}
	

	
//********************************************************************


	//loads the transaction history for a player
	
	public function transactions($fffl_player_id){
		
		
		$data=array();
			
			$data['fffl_player_id']=$fffl_player_id;
			$data['transactions_array']=$this->Players->get_player_transactions($fffl_player_id,$this->session->league_id);
		
		$trades = $this->Trades->get_player_trades($fffl_player_id,$this->league_id);
		
		foreach($trades as $trade_id){
			$trade_details = $this->Trades->get_trade_details($trade_id,'All',FALSE);
			$given_text ='';
			$received_text ='';
			
				$offered_to = $trade_details['offered_to'];
				$offered_by = $trade_details['offered_by'];
				if(isset($trade_details['players_received'])){
					foreach($trade_details['players_received'] as $fffl_player_id){
						$received_text .= player_name_link($fffl_player_id,TRUE,FALSE).', ';
					}
					$received_text = chop($received_text,', ');
				}
				if(isset($trade_details['draft_picks_received'])){
					if($received_text!=''){ $received_text.=' and '; }
					foreach($trade_details['draft_picks_received'] as $pick_id => $pick_data){
						$received_text .= 'Rd. '.$pick_data['round'].'(#'.$pick_data['pick_number'].' '.date('D',$pick_data['start_time']).'), ';
					}
					$received_text = chop($received_text,', ');
				}
				if(isset($trade_details['players_offered'])){
					foreach($trade_details['players_offered'] as $fffl_player_id){
						$given_text .= player_name_link($fffl_player_id,TRUE,FALSE).', ';
					}
					$given_text = chop($given_text,', ');
				}
				if(isset($trade_details['draft_picks_offered'])){
					if($given_text!=''){ $given_text.=' and '; }
					foreach($trade_details['draft_picks_offered'] as $pick_id => $pick_data){
						$given_text .= 'Rd. '.$pick_data['round'].'(#'.$pick_data['pick_number'].' '.date('D',$pick_data['start_time']).'), ';					
					}
					$given_text = chop($given_text,', ');
				}
			
			$trades_array=array(
				
				'text' => team_name_link($offered_by).' traded '.$given_text.' to '.team_name_link($offered_to).' for '.$received_text,
				'transaction_type' => 'Trade',
				'time' => $trade_details['time_approved']
			);
          	if(!is_array($data['transactions_array'])){
				
            	$data['transactions_array']=array();
            }
          	array_push($data['transactions_array'],$trades_array);
		}
		
		//sort the transactions by time
		// Obtain a list of columns
		if(isset($data['transactions_array']) && !empty($data['transactions_array'])){
			foreach ($data['transactions_array'] as $key => $row) {
				$time[$key]  = $row['time'];
				
			}
			
			// Sort the data with volume descending, edition ascending
			// Add $data as the last parameter, to sort by the common key
			array_multisort($time, SORT_DESC, $data['transactions_array']);
		}
		
		$this->player($fffl_player_id, 'transactions', $data);
	}
  
  
  //********************************************************************


	//loads the franchise history for a player
	
	public function franchise($fffl_player_id){
		
		
		$content_data=array();
			
			$content_data['franchise']=$this->Franchise->get_player_franchise($fffl_player_id,$this->session->league_id);
		
		
		
		$this->player($fffl_player_id, 'franchise', $content_data);
	}
	
//********************************************************************


	//loads the draft history for a player
	
	public function draft($fffl_player_id){
		
		
		$content_data=array();
			
			$content_data['draft']=$this->Drafts->get_player_draft($fffl_player_id,$this->session->league_id);
		
		
		
		$this->player($fffl_player_id, 'draft', $content_data);
	}
	
  //****************************************************************************
  
  //loads the view for the player page
	// 
	public function rankings_view($year=NULL, $page_content='position_rankings', $content_data=array()) 
    {
		
		//this is loading a view from one of the methods to display on the page
			//RESTRICTED TO LOGGED IN MEMBERS ONLY
			//Just in case not logged in we direct to login page
			if (!$this->session->userdata('logged_in') )
			{
				redirect("/Restricted");
			} 
			
			if(!isset($team_id)) 
			{
				$team_id = $this->session->team_id;
			}
			
			
			
			
			$path = 'players/'.$page_content;
			
			$this->load_view($path, $content_data, false, false, false);

	}	
//************************************************************************************
  
  //sets the data for the rankings container page
  public function rankings($year=NULL){
    
	if(!$year){
		if($this->current_week>0){
			$year=$this->current_year;	
		}
		else {
			$year=$this->current_year-1;
		}
	}
	
    //content to initially display
		$content_data['display_page']=$year;
		$content_data['load_path'] = 'Player/rankings_year/'.$year;
		
		//send a display for the dropdown. it should match what is in the content_selector array
		//should be the same as the page_content which is also the method. multiple words
		//can be separated by _ and it will take those out and capitalize each word
		$dropdown_title=$year;
		$content_data['dropdown_title'] = $dropdown_title;
		
		//content selector will be activated for these views
		//set the list of items for content selector
		$content_data['content_selector'] = 
			//each key is the display in the dropdown, linked to the path to the method
			//in this class to run to get content to display
			$year=2003;
			$content_data['content_selector'] = array();
			$final_year = $this->current_year;
			if($this->current_week==0){
				$final_year = $this->current_year-1;
			}
			while($year<=$final_year){
				$content_data['content_selector'][$year] = base_url().'Player/rankings_year/'.$year;
				$year++;
			}
			krsort($content_data['content_selector']);
	
		//titles of the pages will be upper cased
		
		$content_data['title']='Position Rankings';
		$path = 'players/position_rankings_container';
    
    	$this->load_view($path, $content_data, true);
    
  }
  

	
	
//*********************************************************************

	//loads the position rank view 
	
	public function rankings_year($year){

		$content_data=array();
		//get rankings array 'rankings'=>position=>rank #=>fffl_player_id, average, team for that year
		foreach(array('QB','RB','WR','TE','K') as $position){
			
			$content_data['rankings'][$position]=$this->NFL_stats->get_position_rankings($position,$year);
			foreach($content_data['rankings'][$position] as $key => $data){
				$content_data['rankings'][$position][$key]['headlines'] = $this->Players->get_player_headlines($data['fffl_player_id'],1);
				$content_data['rankings'][$position][$key]['bye_week'] = $this->NFL_Teams->get_team_bye_week($content_data['rankings'][$position][$key]['team']);
				$content_data['rankings'][$position][$key]['injury'] = $this->Players->get_player_info(array($data['fffl_player_id']),"fffl_player_id","nfl_injury_game_status ");
			}
		}
		$content_data['week']=$this->current_week;
		$content_data['team_id']=$this->session->team_id;
		$content_data['year']=$year;
		
		$this->rankings_view($year, 'position_rankings', $content_data);
	}
	
	
//************************************************************************

//*****************************************************************************
  	//take sthe filter array and returns the search page with jsut the qualifying players
	//in paramters add sorts array items after filter items 
  	public function worksheet_load($number=75,$page=1,$QB=1,$RB=1,$WR=1,$TE=1,$K=1,$NFL_FA=0,$current_team='All',$is_rookie=0,$free_agents=0,$is_injured=0,$team='All',$salary_low='0',$salary_high="100",$name_like='0',$injured_players='Include',$supplemental_eligible=1,$draftable=1,$undraftable=1,$sort='Players.last_name',$direction='ASC',$sort_week=0)
	{	
		//these must be in the same order as the paramters
		$number=5000; $page=1;
		$data['pagination']['number']=$number;
		$data['pagination']['page']=$page;
		////go through each fillter item and create an exclusion if it's truee
		$exclusions_string=''; $first_exclusion=0;
		$join_rosters = 0;
		$positions = array('QB','RB','WR','TE','K');
		foreach($positions as $position){
			$data['filters_array'][$position]=$$position;
			if(!$$position){
				if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
				$exclusions_string.="Players.position<>'".$position."'";	
			}
		}
		
		$data['filters_array']['NFL_FA']=$NFL_FA;
		if(!$NFL_FA){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.="Players.current_team<>'FA'#Players.current_team<>'RET'#Players.nfl_status<>'RET'";	
		}
		$data['filters_array']['current_team']=$current_team;
		if($current_team!='All'){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.="Players.current_team='".$current_team."'";	
		}
		$data['filters_array']['is_rookie']=$is_rookie;
		if($is_rookie){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.='Players.is_rookie=1';	
		}
		$data['filters_array']['free_agents']=$free_agents;//filter out free agents later
		
		$data['filters_array']['is_injured']=$is_injured;
		if($is_injured){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.='Players.is_injured<>1';	
		}
		$data['filters_array']['team']=$team;
		if($team!='All'){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.='Rosters.team_id='.$team;	
			$join_rosters=1;
		}
		$data['filters_array']['salary_low']=$salary_low;
		if($salary_low>0){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.='Rosters.salary>='.$salary_low;	
			$join_rosters=1;
		}
		$data['filters_array']['salary_high']=$salary_high;
		if($salary_high<100){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.='Rosters.salary<='.$salary_high;	
			$join_rosters=1;
		}
		$data['filters_array']['name_like']=$name_like;
		if($name_like!='0'){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			$exclusions_string.="CONCAT(Players.first_name,' ',Players.last_name) LIKE '%".$name_like."%'";	
		}
		$data['filters_array']['injured_players']=$injured_players;
		if($injured_players!='Include'){
			if($first_exclusion==1){ $exclusions_string .='#'; } else { $first_exclusion=1; }
			if($injured_players=='Only'){
				$exclusions_string.="Players.nfl_injury_game_status<>''";	
			}
			if($injured_players=='Remove'){
				$exclusions_string.="Players.nfl_injury_game_status=''";	
			}
		}
		$data['filters_array']['supplemental_eligible']=$supplemental_eligible;//filter out supplemental later
		$data['filters_array']['draftable']=$draftable;//filter out draftable later
		$data['filters_array']['undraftable']=$undraftable;//filter out undraftable later
		
		
		//an array of all the sort criteria
		$data['current_sort']='';
		if($sort=='Rosters.salary'){ $join_rosters=1; $data['current_sort']='Salary: '; }
		if($sort=='average'){ $data['current_sort']='Average: ';  }
		
		
		if($direction=='DESC'){ $data['current_sort'].='High to Low'; } else { $data['current_sort'] .= 'Low to High'; }
		if($sort=='Players.last_name'){ $data['current_sort']='Last Name';  }
		$data['sort_array']['sort']=$sort;
		$data['sort_array']['direction']=$direction;
		
		if($sort=='average'){ $sort = 'Players.last_name'; }
		if($sort=='week'){ $data['current_sort'] = 'Week '.$sort_week.':'; }
		if($sort=='week'){ $sort = 'Players.last_name'; }
		$data['sort_array']['sort_week']=$sort_week;
		
		$data['week']=$this->current_week;
		//first get ids of all the active players, parameters are the status desired and a string of exceptions to add
		//to the where of the query separated by spaces
		
		$offset=$number*($page-1);
		
		$players = $this->Players->get_all_player_ids($exclusions_string,'fffl_player_id',$sort,$direction,$join_rosters);
		
		$players_array = $players['ids'];
		if(count($players_array)==0) { $players_array=array(); }
		//get free agents only
		if($data['filters_array']['free_agents']==1){
			foreach($players_array as $key => $fffl_player_id){
				if($this->Free_Agents->get_player_number_free_agents($fffl_player_id->fffl_player_id)==0){
					unset($players_array[$key]);
				}
				
			}
			$players['count']=count($players_array);
		}
		//if before the draft user might filter out the draftable, supplemental and undraftable players 
		//filter draftable players
		$url = base_url().'assets/json/draftable.json';
		$headers = get_headers($url);
    	$file_exists = substr($headers[0], 9, 3);
		
		if($file_exists === "200")
		{
			$file = file_get_contents($url);
			$draftable = json_decode($file,true);
		}
		if($data['filters_array']['draftable']==0){
			foreach($players_array as $key=>$fffl_player_id){
				if(in_array($fffl_player_id->fffl_player_id,array_keys($draftable['draftable']))){
					unset($players_array[$key]);
				}
				
			}
			$players['count']=count($players_array);
		}
		
		//filter supplemental_eligible players
		$url = base_url().'assets/json/supplemental_eligible.json';
		$headers = get_headers($url);
    	$file_exists = substr($headers[0], 9, 3);
		
		if($file_exists === "200")
		{
			$file = file_get_contents($url);
			$supplemental_eligible = json_decode($file,true);
		}
		
		if($data['filters_array']['supplemental_eligible']==0){ 
			foreach($players_array as $key => $fffl_player_id){ 
				if(in_array($fffl_player_id->fffl_player_id,$supplemental_eligible['supplemental'])){
					
					unset($players_array[$key]);
				}
				
			}
			$players['count']=count($players_array);
		}
		//filter undraftable players
		
		if($data['filters_array']['undraftable']==0){
			foreach($players_array as $key => $fffl_player_id){ 
				if(!in_array($fffl_player_id->fffl_player_id,$supplemental_eligible['supplemental']) && !in_array($fffl_player_id->fffl_player_id,array_keys($draftable['draftable']))){
					
					unset($players_array[$key]);
				}
				
			}
			$players['count']=count($players_array);
		}
		
		
		if(count($players_array)==0) { $players_array=array(); }
		if(!in_array($data['sort_array']['sort'],array('average','salary','week'))){
			//add the player info array as data to be sent to the view
			//slice now because average doesn't matter, we'll reduce the number of player averages calculated
			
			$players_array= array_slice($players_array,$offset,$number);
		}
       	$data['pages']=ceil($players['count']/$number);
		
      //create an array to house all player info
		$players_info_array = array();
		//go through each player id and add all player info for the search page to the player info array
		foreach($players_array as $id)
		{	
			//call the get_player_info function from the players model. each item returned is in an array with item name as the key
			//all items we want for this array are listed in the 3rd parameter separated by a space
			$players_info_array[$id->fffl_player_id]=$this->Players->get_player_info($id,"fffl_player_id","first_name last_name current_team position is_rookie is_injured injury_text nfl_injury_game_status nfl_status nfl_esbid"); 
			$players_info_array[$id->fffl_player_id]['salaries'] = $this->Players->get_player_salaries($id->fffl_player_id);
			
			$players_info_array[$id->fffl_player_id]['fffl_player_id']=$id->fffl_player_id;
			$players_info_array[$id->fffl_player_id]['bye_week']=$this->NFL_Teams->get_team_bye_week($players_info_array[$id->fffl_player_id]['current_team']);
			//add headlines
			$players_info_array[$id->fffl_player_id]['headlines']=$this->Players->get_player_headlines($id->fffl_player_id,1);
			//get a player's scores for the current season
			//get the current season from Leagues model
			$current_season = $this->Leagues->get_current_season($this->league_id);
			$current_week = $this->Leagues->get_current_week($this->league_id);
			if($current_week==0){ $current_week=16; $current_season=$current_season-1;}
			$players_info_array[$id->fffl_player_id]['scores'] = $this->NFL_stats->get_player_scores_season($current_season,$id->fffl_player_id,1,1,$current_week);
		}
		
		//sort by average after the array has been contstructed. Will slow down the load,but this sort must be done last
			
			if($data['sort_array']['sort']=='average' || $data['sort_array']['sort']=='salary'){
				// Obtain a list of columns
				foreach ($players_info_array as $key => $row) {
					$sort_criteria[$key]  = $row['scores'][$data['sort_array']['sort']];
					$last_name[$key] = $row['last_name'];
				}
				
				
				// Sort the data 
				// Add $data as the last parameter, to sort by the common key

				if($direction == 'DESC'){
					array_multisort($sort_criteria, SORT_DESC, $last_name, SORT_ASC, $players_info_array);
				} 
				else {
					array_multisort($sort_criteria, SORT_ASC, $last_name, SORT_ASC, $players_info_array);	
				}
			
		
				//add the player info array as data to be sent to the view
				//slice now because average had to be sorted first
				
				$players_info_array= array_slice($players_info_array,$offset,$number);
			}
			elseif($data['sort_array']['sort']=='week'){
				// Obtain a list of columns
				foreach ($players_info_array as $key => $row) {
					//d($row['scores']);
					$sort_criteria[$key]  = $row['scores']['weeks'][$sort_week]['points'];
					$last_name[$key] = $row['last_name'];
				}
				
				
				// Sort the data 
				// Add $data as the last parameter, to sort by the common key

				if($direction == 'DESC'){
					array_multisort($sort_criteria, SORT_DESC, $last_name, SORT_ASC, $players_info_array);
				} 
				else {
					array_multisort($sort_criteria, SORT_ASC, $last_name, SORT_ASC, $players_info_array);	
				}
			
		
				//add the player info array as data to be sent to the view
				//slice now because average had to be sorted first
				
				$players_info_array= array_slice($players_info_array,$offset,$number);
				
			}
		
		$data['players_array'] = $players_info_array;
		
		//determine if draft filters should be shown
		//get the franchise deadline
		$franchise_time = $this->Calendars->get_calendar_time('franchise',1);
		if($franchise_time<now() && $this->current_week==0){
			$data['after_franchise']=1;
		}
		else{
			$data['after_franchise']=0;	
		}
		$path = 'players/search';
			
		$this->worksheet($data['players_array']);

	}//filter method


//*************************************************************************

	public function worksheet($players_array){
		
		// The actual data
		$columns_array=array('first_name','last_name','current_team','position','is_rookie','bye_week','average','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16');
		
		$player_count=1; 
		foreach($players_array as $fffl_player_id => $data){
			$column_count=0;
			foreach($columns_array as $column){
				if($column=='average'){
					$worksheet[$player_count]['average']= $data['scores']['average']; 
				}
				elseif($column>0 && isset($data['scores']['weeks'][$column])){
					$worksheet[$player_count][$column]= $data['scores']['weeks'][$column]['points'];
				}
				elseif(isset($data[$column])){
					$worksheet[$player_count][$column]= $data[$column];
				}
				else{
					$worksheet[$player_count][$column]=0;
				}
				
			}
			$player_count++;
		}
		
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename=players.csv');
		header('Pragma: no-cache');
		//header('Content-Disposition: attachment; filename="players.csv";');//$this->download_send_headers("players.csv");
		
		echo $this->array2csv($worksheet);
		die();
		
	}
	
	
//*******************************************************

	public function array2csv(array &$array)
	{
	   if (count($array) == 0) {
		 return null;
	   }
	   ob_start();
	   $df = fopen("php://output", 'w');
	   fputcsv($df, array_keys(reset($array)));
	   foreach ($array as $row) {
		  fputcsv($df, $row);
	   }
	   fclose($df);
	   return ob_get_clean();
	}
	
//********************************************************

	public function download_send_headers($filename) {
		/*// disable caching
		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");
	
		// force download  
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
	
		// disposition / encoding on response body
		header("Content-Disposition: attachment;filename={$filename}");
		header("Content-Transfer-Encoding: binary");*/
		header('Content-Disposition: attachment; filename="'.$filename.'";');
	}
	
	
}//end Class Player extends MY_Controller

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

