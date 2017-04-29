<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class League extends MY_Controller
{
	/**
	 * League controller.
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
		$this->load->helper('links');
		
		
		$this->load->model('Leagues');
		$this->load->model('Games');
		
		$this->current_year = $this->Leagues->get_current_season($this->league_id);
		$this->current_week = $this->Leagues->get_current_week($this->league_id);

	}


	// Loads the content for the rules view, types of content area:
	// 
	// 
	public function index($league_id=1, $page_content='rules', $content_data=array()) 
    {
		
		//this is loading a view from one of the methods to display on the page
			//RESTRICTED TO LOGGED IN MEMBERS ONLY
			//Just in case not logged in we direct to login page
			if (!$this->session->userdata('logged_in') )
			{
				redirect("/Restricted");
			} 

		
			$path = 'league/'.$page_content;
			
			$this->load_view($path, $content_data, false, false, false);

	}


//**************************************************************
  
	public function rules($league_id=1,$page_content='revisions') 
	{
		//content to initially display
		$content_data['display_page']=$page_content;
		$content_data['load_path'] = 'League/rules_sections/'.$league_id.'/'.$page_content;
		//send a display for the dropdown. it should match what is in the content_selector array
		//should be the same as the page_content which is also the method. multiple words
		//can be separated by _ underscore and it will take those out and capitalize each word
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
			'Revisions' => base_url().'League/rules_sections/'.$league_id.'/revisions',
			'Introduction' => base_url().'League/rules_sections/'.$league_id.'/introduction',
			'Players' => base_url().'League/rules_sections/'.$league_id.'/players',
			'Rosters' => base_url().'League/rules_sections/'.$league_id.'/rosters',
			'Competition' => base_url().'League/rules_sections/'.$league_id.'/competition'
		);
		//titles of the pages will be upper cased
		$title = 'League Rules';
		$content_data['title']= ucwords($title);
		$path = 'league/rules';
		
		
		$this->load_view($path, $content_data, true);
		
	}

//**********************************************************

	public function rules_sections($league_id=1,$section){
		
		$this->index($league_id, $section);
		
	}
	
//**************************************************************
  
	public function records($league_id=1) 
	{
		$all_years = $this->Leagues->get_all_league_years(1);
		
		//get the superbowls
		$content_data['total_superbowls']=0;
		foreach($all_years as $year){
			$all_playoffs = $this->Games->get_playoffs_results_year($league_id,$year);
			if($all_playoffs){
				$content_data['superbowls'][$year]=end($all_playoffs);
				$content_data['total_superbowls']++;
			}
				
		}
		
		//get toilet bowl champs
		$content_data['total_toilet_bowls']=0;
		foreach($all_years as $year){
			$all_toilet_bowl = $this->Games->get_toilet_bowl_results_year($league_id,$year);
			$toilet_bowl=end($all_toilet_bowl);
			if($all_toilet_bowl && isset($toilet_bowl['winner'])){
				$content_data['toilet_bowls'][$year]=$toilet_bowl;
				$content_data['total_toilet_bowls']++;
			}
		}
		
		//get pro bowl champs
		$content_data['total_pro_bowls']=0;
		
		
		foreach($all_years as $year){
			$all_pro_bowl = $this->Games->get_pro_bowl_results_year($league_id,$year);
			
			if($all_pro_bowl){
				
				$content_data['pro_bowls'][$year]=$all_pro_bowl;
				$content_data['total_pro_bowls']++;
			}
		}
		
		
		$all_teams = $this->Teams->get_all_team_id($league_id);
		//get season records
		$all_team_games_stats = $this->Teams->get_team_stats($all_teams);
		
		//add probowl stats
		//d($content_data['toilet_bowls']);
		foreach(array("toilet_bowls","pro_bowls") as $bowl){
			$max_points=array();	
			$max_points_years=array();
			foreach($content_data[$bowl] as $year=>$scores){
				
				if(!isset($max_points[$scores['winner']]) || $scores['winner_score']>$max_points[$scores['winner']]){
					$max_points_years[$scores['winner']]=array($year);	
					$max_points[$scores['winner']]= $scores['winner_score'];
					
				}
				elseif($scores['winner_score']==$max_points[$scores['winner']]){
					$max_points_years[$scores['winner']][]=$year;	
				}
			}
			$all_team_games_stats['stats_array']['max_points_'.$bowl]=$max_points;
			$all_team_games_stats['years_array']['max_points_'.$bowl]=$max_points_years;
		}
		
		
		d($all_team_games_stats);
		foreach($all_team_games_stats['stats_array'] as $category=>$values){
			if(substr($category, 0, 3) === "max"){
				$value = max($values);
			} 
			elseif(substr($category, 0, 3) === "min"){
				$value = min($values);
			}
			$content_data[$category]=$value;
			$max_teams = array_keys($values,$value);
			foreach($max_teams as $team_id){
				$content_data[$category.'_array'][] = array("team_id"=>$team_id,"years"=>$all_team_games_stats['years_array'][$category][$team_id],"max"=>$value);
			}
		}
		
		
		$title = 'League Records';
		$content_data['title']= ucwords($title);
		$path = 'league/records';
		
		
		$this->load_view($path, $content_data, true);
		
	}	
	
	
	
}//end Class League extends MY_Controller

/*End of file League.php*/
/*Location: ./application/controllers/League.php*/

