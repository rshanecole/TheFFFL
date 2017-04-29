<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sandbox extends CI_Controller {

	/**
	 * 
	 */
	public $league_id = 1; //***NI*** remove when multiple leagues are in place
	public $current_year;	
	public $current_week;
	 public function __construct()
        {
                parent::__construct();
                
                $this->load->helper('url_helper');
				$this->load->helper('string');
		$this->load->model('Players');
		$this->load->model('Database_Manager');
		$this->load->model('Drafts');
		$this->load->model('Leagues');
		$this->load->model('Standings');
		$this->load->model('Schedules');
		$this->load->model('Facebook_Interact');
		$this->load->model('NFL_Teams');
		$this->load->model('NFL_Games');
		$this->load->model('Projections');
		$this->load->model('Franchise');
		$this->load->model('NFL_stats');
		$this->current_year=2016;
		$this->current_week=13;
	
        }
	public function update_bye_weeks($season_id,$year)
	{
		$db2 = $this->load->database('old', TRUE);
		$db2->select('playerNo, ByeWeek,team');
		$db2->from('playerstats');
		$db2->where('seasonId',$season_id);//change season ID
		$query = $db2->get();
		foreach($query->result() as $row)
			{
				
				$player = $row->playerNo;
				$db2->select('playername');
				$db2->from('nflstats');
				$db2->where('seasonId='.$season_id.' AND playerNo='.$player);//Change season ID
				$db2->limit(1);
				$query2 = $db2->get();
				$name=$query2->row('playername');
				if(is_null($name)){
					$name='';
					
				}
				$bye = $row->ByeWeek;
				$team=$row->team;
				$data = array('fffl_player_id' => $player, 'season' => $year, 'week' => $bye, 'player_opponent'=>'Bye', 'player_name'=>$name, 'player_team'=>$team);//Change season year
$nfl = $this->load->database('NFL', TRUE);
				$str = $nfl->insert('NFL_stats_'.$year, $data);//change season year
			
			
			}

	}
	public function create_schedule_tables()
	{
		$year=2014
			;
		while($year>2004)
		{
		$this->db->query("INSERT NFL_Schedule_".$year." SELECT * FROM NFL_Schedule_2015");
		
			$year--;
		}
	}
	
	
	public function check_champs()
	{
		$standings = $this->Standings->determine_playoff_teams(2016,$standings=NULL);
		$this->Standings->set_regular_season_championships(1,2016,$standings);
	}
	
	public function create_first_starters() {
		$this->db->where("league_id=1");
		$query = $this->db->get('Teams');
		foreach($query->result_array() as $team_id){
			$this->db->select("fffl_player_id");
			$this->db->where("team_id=".$team_id['team_id']." and lineup_area='Starter'");
			
			$query2 = $this->db->get('Rosters');
			echo $this->db->last_query();
			foreach($query2->result_array() as $player) {
				$position = $this->Players->get_player_info(array($player['fffl_player_id']),"fffl_player_id","position");
				$data = array(
					'team_id' => $team_id['team_id'],
					'week' => 1,
					'year' => 2016,
					'fffl_player_id' => $player['fffl_player_id'],
					'position' => $position['position']
				
				);
				$this->db->insert('Starting_Lineups',$data);
			}
				
		}
		
	}
	
	public function add_position_to_lineups(){
		$query =$this->db->select("fffl_player_id")
		->where('position','x')
		  ->get('Starting_Lineups');
		foreach($query->result() as $player){
			
			$this->db->select("position");
			$this->db->where("fffl_player_id",$player->fffl_player_id);
			$query=$this->db->get('Players');
			foreach ($query->result() as $row)
			{
					$pos= $row->position;
			}
			
			$this->db->set('position',$pos);
			$this->db->where('fffl_player_id',$player->fffl_player_id);
			$this->db->update('Starting_Lineups');
			
		}
	}
	
	//simply running the function for the first time and testing
	public function add_draft_picks($league_id,$season){
		
		$this->Drafts->assign_draft_picks($league_id,$season);
		
	}
	
	//simply running the function for the first time and testing
	public function update_draft_picks($league_id,$season,$current_week){
		
		$this->Drafts->update_draft_order($league_id,$season,$current_week);
		
			
	}
	
	public function post_to_facebook(){
		
		$this->Facebook_Interact->post_to_facebook('test',1);
	}
	
	public function update_fa(){
			$players = $this->Players->get_all_player_ids("","fffl_player_id","last_name");
			d($players);
			foreach($players as $fffl_player_id){
				$this->Salaries->set_free_agent_salary($fffl_player_id->fffl_player_id);
			}
	}


	public function schedules($league_id,$year){
		$this->Schedules->schedule_games($league_id,$year);
	}
	
	public function import_games(){
		
		
		$this->Game_Updates->update_NFL_games_status();
		
	}
	
	public function import_projections($year){
		$this->Projections->import_adp($year);
	}
	
	public function standings($year){
		
			$stand = $this->Standings->sort_teams_by($year,1,'','',array('wins','losses','points','streak'),FALSE);
		$this->output->enable_profiler(TRUE);d($stand);
	}
	
	public function test_tables(){
		$this->Database_Manager->NFL_database_backup('All');
	}
	
	public function write_json(){
		//create JSON files of the draftable and supplemental players
		//get all players and check each one
		$all_players = $this->Players->get_all_player_ids("Players.current_team<>'RET'","fffl_player_id","Players.last_name",'ASC',0);
		$draftable = array();
		
		foreach($all_players['ids'] as $fffl_player_id){
			$player_info=$this->Players->get_player_info(array($fffl_player_id->fffl_player_id),'fffl_player_id','position');

			if($this->Free_Agents->get_player_number_free_agents($fffl_player_id->fffl_player_id)==2){
				$draftable[$fffl_player_id->fffl_player_id]=$player_info['position'];
			}
			
		}
		$fp = fopen('/home1/theffflc/public_html/fantasy/assets/json/draftable.json', 'w');
		fwrite($fp, json_encode(array('draftable'=>$draftable)));
		
	}
	
	public function fix_franchise(){
		$teams=$this->Teams->get_all_team_id(1);
		foreach($teams as $team_id){
			$roster_array=$this->Rosters->get_team_active_roster($team_id);
			$inactive = $this->Rosters->get_team_inactives($team_id);
			
			//$roster_array[]=$inactive['PUP'];
			//$roster_array[]=$inactive['PS'];
			$all_franchise = $this->Franchise->get_simple_franchise_by_year($team_id,'2016');
			foreach($all_franchise as $franchise_player){
				//d($franchise_player,$roster_array);
				if(!in_array($franchise_player,$roster_array)){
					echo $team_id.' '.$franchise_player.'<br>';	
				}
				
			}
		}
		
	}
	
	
	public function bu(){
		$this->Database_Manager->database_backup(array('Transactions'));
		
	}
	
	public function resort_draftable(){
		$all_players = $this->Players->get_all_player_ids("Players.current_team<>'RET' and Players.current_team<>'FA'","fffl_player_id","Players.last_name",'ASC',0);
		$draftable = array();
		$supplemental_eligible=array();
		foreach($all_players['ids'] as $fffl_player_id){
			$player_info=$this->Players->get_player_info(array($fffl_player_id->fffl_player_id),'fffl_player_id','position');

			if($this->Free_Agents->get_player_number_free_agents($fffl_player_id->fffl_player_id)==2){
				$draftable[$fffl_player_id->fffl_player_id]=$player_info['position'];
			}
			elseif($this->Free_Agents->get_player_number_free_agents($fffl_player_id->fffl_player_id)==1){
				$supplemental_eligible[]=$fffl_player_id->fffl_player_id;
			}
		}
		$fp = fopen('/home1/theffflc/public_html/fantasy/assets/json/draftable.json', 'w');
		fwrite($fp, json_encode(array('draftable'=>$draftable)));	
		
	}
	
	public function test_scores($week,$year,$fffl_player_id){
	
		$score = $this->NFL_stats->get_player_scores_season($year,$fffl_player_id,0,$week,$week,0);
		d($score);
	}
	
	
	public function add_bye($year,$week){
		$this->NFL_stats->add_bye_weeks($year,$week);	
	}
	
	public function test_pro(){
		$this->Rosters->update_all_pro_team(1,2016);
					
						
	}
	
	public function fix_dups(){
		$nfl = $this->load->database('NFL', TRUE);
				
		$players=$nfl->select('nfl_gsis_player_id')
		->where('week',1)
			->distinct()
			->get('NFL_stats_2016');
			//d($players->result_array());
		foreach($players->result_array() as $player){
			$dups = $nfl->where('nfl_gsis_player_id',$player['nfl_gsis_player_id'])
						->where('week',1)
						->count_all_results('NFL_stats_2016');
					
			if($dups>1 && $player['nfl_gsis_player_id']!=''){
				d($player['nfl_gsis_player_id']);
				/*$nfl->where('nfl_gsis_player_id',$player['nfl_gsis_player_id'])
					->where('week',1)
					->where('player_name','')
					->limit(1)
					->delete('NFL_stats_2016');*/
			}
		}
		
	}
	
}
