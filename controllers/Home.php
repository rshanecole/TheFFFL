<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {
	
	public $league_id = 1; //***NI*** remove when multiple leagues are in place
	public $current_year;	
	public $current_week;
	//Load the needed libraries.  
	public function __construct() 
    {
		parent::__construct();

		$this->load->model('Owners');
		$this->load->model('Leagues');
		$this->load->model('Teams');
		$this->load->model('Calendars');
		$this->load->model('Standings');
		$this->load->model('NFL_Games');
		$this->load->model('NFL_stats');
		$this->load->helper('date');
		$this->load->library('simple_html_dom');
		
		$this->current_year = $this->Leagues->get_current_season($this->league_id);
		$this->current_week = $this->Leagues->get_current_week($this->league_id);
	}

	public function index()
	{
		
		$page='home';
		$data=array();
		$data['week']=$this->current_week;
		if(isset($_SESSION['team_id'])){
			$league_id = $this->Teams->get_team_league_id($_SESSION['team_id']);
		}
		else {
			$league_id = 1;
		}
		
		//get calendar 
		$data['calendar'] = $this->get_calendar_dates($league_id);
		
		//get standings 
		$year=$this->current_year;
		if($this->current_week == 0){ $year = $this->current_year-1; }
		$data['afc_east_standings'] = $this->Standings->get_standings($year,'AFC','East');
		foreach($data['afc_east_standings'] as $key=>$standings_data){
			$data['afc_east_standings'][$key]['team_logo_path']= $this->Teams->get_team_logo_path($standings_data['team_id']);	
		}
		$data['afc_west_standings'] = $this->Standings->get_standings($year,'AFC','West');
		foreach($data['afc_west_standings'] as $key=>$standings_data){
			$data['afc_west_standings'][$key]['team_logo_path']= $this->Teams->get_team_logo_path($standings_data['team_id']);	
		}
		$data['nfc_east_standings'] = $this->Standings->get_standings($year,'NFC','East');
		foreach($data['nfc_east_standings'] as $key=>$standings_data){
			$data['nfc_east_standings'][$key]['team_logo_path']= $this->Teams->get_team_logo_path($standings_data['team_id']);	
		}
		$data['nfc_west_standings'] = $this->Standings->get_standings($year,'NFC','West');
		foreach($data['nfc_west_standings'] as $key=>$standings_data){
			$data['nfc_west_standings'][$key]['team_logo_path']= $this->Teams->get_team_logo_path($standings_data['team_id']);	
		}
		//get marquee photo and headline
		$data['marquee']=$this->get_cbs_marquee_photo();
		
		//get game of the week
		if($this->current_week<14){
			$gow_week=$this->current_week;
			if($this->current_week==0){ $gow_week =1; }
			$gow_query = $this->db->where('priority',1)
					->where('week',$gow_week)
					->where('year',$this->current_year)
					->limit(1)
					->get('Games');
			$data['gow']=$gow_query->result_array();
			$data['gow']['0']['logo_a_path']=$this->Teams->get_team_logo_path($data['gow']['0']['opponent_a']);
			$data['gow']['0']['logo_b_path']=$this->Teams->get_team_logo_path($data['gow']['0']['opponent_b']);
			$data['gow']['0']['record_a']=$this->Standings->get_team_wins_losses_year($data['gow']['0']['opponent_a'],$this->current_year,TRUE);
			$data['gow']['0']['record_b']=$this->Standings->get_team_wins_losses_year($data['gow']['0']['opponent_b'],$this->current_year,TRUE);
			$data['gow']['0']['points_a']=$this->Standings->get_team_total_points_year($data['gow']['0']['opponent_a'],$this->current_year,TRUE);
			$data['gow']['0']['points_b']=$this->Standings->get_team_total_points_year($data['gow']['0']['opponent_b'],$this->current_year,TRUE);
		}
		elseif($this->current_week==14){
			$games=$this->Games->get_week_games($this->league_id,$this->current_year,$this->current_week);
			foreach($games as $game){
				if($game['is_playoff']==1){
					$game['logo_a_path']=$this->Teams->get_team_logo_path($game['opponent_a']);
					$game['logo_b_path']=$this->Teams->get_team_logo_path($game['opponent_b']);
					$data['gow'][]=$game;
					
				}
			}
		}
		elseif($this->current_week==15){
			$games=$this->Games->get_week_games($this->league_id,$this->current_year,$this->current_week);
			foreach($games as $game){
				
					$game['logo_a_path']=$this->Teams->get_team_logo_path($game['opponent_a']);
					$game['logo_b_path']=$this->Teams->get_team_logo_path($game['opponent_b']);
					$data['gow'][]=$game;
					
				
			}
		}
		elseif($this->current_week==16){
			$games=$this->Games->get_week_games($this->league_id,$this->current_year,$this->current_week);
			foreach($games as $game){
				
					$game['logo_a_path']=$this->Teams->get_team_logo_path($game['opponent_a']);
					$game['logo_b_path']=$this->Teams->get_team_logo_path($game['opponent_b']);
					$data['gow'][]=$game;
					
				
			}
		}
		
		
		$prev_event=$this->Calendars->get_previous_event($league_id);
		$data['prev_event_time'] = $prev_event['time'];
		//get league leaders
						
			//record
			$standings = $this->Standings->sort_teams_by($year,$league_id,'','',array('wins','points','losses'),FALSE);
				//sort the standings
				// Obtain a list of columns
				foreach ($standings as $key => $row) {
					$wins[$key]  = $row['wins'];
					$points[$key] = $row['points'];
				}
				
				// Sort the data with volume descending, edition ascending
				// Add $data as the last parameter, to sort by the common key
				array_multisort($wins, SORT_DESC, $points, SORT_DESC, $standings);
				
			$data['best_record'] = array('team_id'=>$standings['0']['team_id'],'record'=>$standings['0']['wins'].'-'.$standings['0']['losses'],'team_logo_path'=>$this->Teams->get_team_logo_path($standings['0']['team_id']));
			
			//scoring
			//sort the standings
				
				// Sort the data with volume descending, edition ascending
				// Add $data as the last parameter, to sort by the common key
				array_multisort($points, SORT_DESC, $wins, SORT_DESC, $standings);
				
			$data['best_scoring'] = array('team_id'=>$standings['0']['team_id'],'points'=>$standings['0']['points'],'team_logo_path'=>$this->Teams->get_team_logo_path($standings['0']['team_id']));
			
			//best week
			if($this->current_week<1){$year=$year-1; };
			$best_week = $this->Games->get_week_best_score($league_id,$year,'All');
			$best_week['team_logo_path']=$this->Teams->get_team_logo_path($best_week['team_id']);
			$data['best_week']= $best_week;
			
			//qb, rb, wr, te, k
			foreach(array('QB','RB','WR','TE','K') as $position){
				$rankings = $this->NFL_stats->get_position_rankings($position,$year,0);	
				$data['best_'.$position] = $rankings['0'];
				$esbid = $this->Players->convert_player_id($data['best_'.$position]['fffl_player_id'], 'fffl_player_id', 'nfl_esbid');
				$data['best_'.$position]['picture_path'] = 'http://static.nfl.com/static/content/public/static/img/fantasy/transparent/200x200/'.$esbid.'.png';
			}
			
			//get headline data
			$headlines = $this->db->order_by('date','DESC')
								->or_where('source','FFToday')
								->or_where('source','ESPN')
								
								->limit(6)
								->get('RSS');
			foreach($headlines->result_array() as $story_data){
				$data['headlines'][]=array('link'=>$story_data['link'],'title'=>$story_data['title']);
			}
		
		$this->load_view($page,$data);
	}

//***********************************************************************************	
	public function get_calendar_dates($league_id){
		//array keys will be unix time=>short_name,long_name
		$calendar_array=array();
		//get the dates stored in Calendar table
		$calendar_array = $this->Leagues->get_league_calendar_dates($league_id);
		
		//get dates not in calendar table
			//draft dates
			$draft_times = $this->Calendars->get_draft_times($league_id,$this->current_year);
			
			foreach($draft_times as $type=>$time_array){
				foreach($time_array as $time){
					if($type=='Common') {$name='FFFL Draft '.date('D g A',$time); $short_name='FFFL Drafts';}
					else{$name=$short_name='Supplemenal Draft';}
					$calendar_array[$time]['long_name']=$name;
					$calendar_array[$time]['short_name']=$short_name;
				}
			}
			//nfl first game week 1
      		$calendar_array[$this->NFL_Games->get_week_first_game(1,$this->current_year)]['long_name']='First NFL Game';
      
			
			//playoffs 1st round week 14
      		$calendar_array[$this->NFL_Games->get_week_first_game(14,$this->current_year)]['long_name']='Divisional Playoffs';
			
			//playoffs 2nd round week 15
      		$calendar_array[$this->NFL_Games->get_week_first_game(15,$this->current_year)]['long_name']='Conference Championships';
			
			//superbowl week 16
      		$calendar_array[$this->NFL_Games->get_week_first_game(16,$this->current_year)]['long_name']='Super Bowl XIX';
			
			//pro powl week 17
      		$calendar_array[$this->NFL_Games->get_week_first_game(17,$this->current_year)]['long_name']='Pro Bowl';
      
      		//ps deadline
      		$calendar_array[$this->NFL_Games->get_week_sunday_deadline(2,$this->current_year)]['long_name']='PS Deadline';
			
			//trade deadline
			$calendar_array[$this->NFL_Games->get_week_sunday_deadline(7,$this->current_year)]['long_name']='Trade Deadline';
			
			ksort($calendar_array);
		
		return $calendar_array;
			
	}
	
//************************************************************************

	public function get_cbs_marquee_photo(){
		$html = file_get_html('http://www.cbssports.com/fantasy/football/');			
		$marquee_div = $html->find('div.marquee');
      if(count($marquee_div)>0){
        
		$marquee_link = 'http://www.cbssports.com/'.$marquee_div['0']->children['0']->attr['href'];			
		$marquee_img = $marquee_div['0']->children['0']->children['0']->attr['src'];
		$text_div =  $html->find('div.marquee-text');
		$text = $text_div['0']->plaintext;
      }
      else{
        
        $marquee_img='';
        $marquee_link='';
        $text='';
        
      }
		
		return array('img'=>$marquee_img,'text'=>$text,'link'=>$marquee_link);
		
	}

	
}//end controller

/* End of file home.php */
/* Location: ./application/controllers/home.php */