<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Supplemental_Draft extends CI_Controller
{
	/**
	 * Supplemental_Draft Controller.
	 * CLI-Cron job to run on day of draft
	 * 
	 */
	
	public $league_id = 1; //***NI*** remove when multiple leagues are in place
	public $current_year;	
	public $current_week;
	//Load the needed libraries.  
	public function __construct() 
    {
		parent::__construct();
		// this controller can only be called from the command line
        //if (!$this->input->is_cli_request()) show_error('Direct access is not allowed');
		
		$this->load->model('Leagues');
		$league_id = 1;
		$this->current_year = $this->Leagues->get_current_season($league_id);
		$this->current_week = $this->Leagues->get_current_week($league_id);
		//backup the database
			// Load the DB utility class
			$this->load->model('Database_Manager');
			$this->load->model('Drafts');
			$this->load->model('Salaries');
		//end database backup
	}

	public function index()
	{
		
		
	}//end index function
	
//**********************************************************************
	//THIS IS A ONE ROUND DRAFT ON TUESDAY BEFORE REGULAR DRAFTS INVOLVING
	//ALL TEAMS AND ONLY THE PLAYERS KEPT BY ONE TEAM
	//OWNERS TAKE A PLAYER OR TAKE A COMPENSATORY PICK ON DRAFT NIGHT
	public function supplemental_draft($round)
	{	
		$this->Database_Manager->database_backup(array('Draft_Picks'));
		$this->Database_Manager->database_backup(array('Drafts'));
		$this->Database_Manager->database_backup(array('Players'));
		$this->Database_Manager->database_backup(array('Rosters','Supplemental_Selections'));

		
		$draft_order = $this->Drafts->get_draft_results_year($this->league_id,$this->current_year);
		//get the id and start time for this draft
		foreach($draft_order['Supplemental'] as $draft_id => $draft_data){
			$start_time = $draft_data['start_time'];
		}
		//work the array down until we have just the first or second half of the draft order, depending on which round it is
		$draft_order = array_pop($draft_order['Supplemental']);
		$draft_order=$draft_order['picks'];
		$half = floor(count($draft_order)/2);
		$draft_order = array_chunk($draft_order,$half);
		$draft_order=$draft_order[$round-1];
		//go through each pick
		foreach($draft_order as $pick_minus_1 => $pick_data) {
			$team_selection = $this->db->where('team_id',$pick_data['team_id'])
							->order_by('priority','ASC')
							->limit(1)
							->get('Supplemental_Selections');
			if($team_selection->num_rows()>0){
				//there's a player selected, proceed with the selection
				foreach($team_selection->result_array() as $selection){
					echo ($pick_minus_1+1).'. '.team_name_link($pick_data['team_id']).' '.player_name_link($selection['fffl_player_id']).'<br>';
					//check to make sure it's time for this draft before making selections
					if($start_time< now()){
						//update the draft_picks table where pick_id matches
						$this->db->set('fffl_player_id',$selection['fffl_player_id'])
								->where('pick_id',$pick_data['pick_id'])
								->update('Draft_Picks');
								
						//get the free agent salary for this player to set as the salary for the new team
						$salary = $this->Salaries->get_free_agent_salary($selection['fffl_player_id']);
						//get player position
						$player_info = $this->Players->get_player_info(array($selection['fffl_player_id']),"fffl_player_id","position");
						$position = $player_info['position'];
						//add to roster using salary
						$this->db->set('fffl_player_id',$selection['fffl_player_id'])
								->set('team_id',$pick_data['team_id'])
								->set('salary',$salary)
								->set('position', $position)
								->set('lineup_area','Roster')
								->insert('Rosters');
						
						
						//delete any request in the supplemental selections for this player
						$this->db->where('fffl_player_id',$selection['fffl_player_id'])
								->delete('Supplemental_Selections');
					}
				}
			}//end if team has selections > 0
			//there isn't a palyer ot pick, pass the team.
			else{
				//************************THIS IS OLD CODE FROM THE OLD SITE THAT MANAGED COMPENSATORY PICKS. ONLY NECESSARY TO REDO THIS CODE IF WE REINSTITUTE COMPENSATORY PICKS*************************//
								//There's no player to pick, so the team passes and (THIS USED TO GIVE A COMPENSATORY PICK) //needs a compensatory pick assigned
								$teamId=$pick_data['team_id'];
								//update the draft_picks table where pick_id matches
								$this->db->set('fffl_player_id',0)
										->where('pick_id',$pick_data['pick_id'])
										->update('Draft_Picks');
								//Below is what used to award a compensatory pick to a passing team
								/*if($s==0) { //This is the first of a new pair of passing teams.  The first in the pair gets lined up with his drafting day.
									if($draftorder[$i]['pos']<11) {
										$sql2 = mysql_query("UPDATE draft_picks SET round=2, Supplemental='$supplemental' WHERE curOwner='$teamId' and originalOwner='$originalOwner' and season='$CurSeasonId' and Supplemental>0");
										
										$s=2; //s=2 means the next guy gets a Saturday pick
									} else {
										$supplemental=$supplemental+10;
										$sql2 = mysql_query("UPDATE draft_picks SET round=2, Supplemental='$supplemental' WHERE curOwner='$teamId' and originalOwner='$originalOwner' and season='$CurSeasonId' and Supplemental>0");
										
										$supplemental=$supplemental-10;
										$s=1; //s=1 means the next guy gets a Friday pick
									}
									$sql = mysql_query("INSERT into SupplementalDrafts values($CurSeasonId,".$draftorder[$i]['pick'].",".$draftorder[$i]['team'].",0)");
									
								} else { //This is the second passer in a pair, he gets put on the other day from the team above when s=0.
									if($s==1) { //This team gets a fRiday pick
									
										$sql2 = mysql_query("UPDATE draft_picks SET round=2, Supplemental='$supplemental' WHERE curOwner='$teamId' and originalOwner='$originalOwner' and season='$CurSeasonId' and Supplemental>0");
										
										
									
									} else if($s==2) { //This team gets a Saturday pick
										$supplemental=$supplemental+10;
										$sql2 = mysql_query("UPDATE draft_picks SET round=2, Supplemental='$supplemental' WHERE curOwner='$teamId' originalOwner='$originalOwner' and season='$CurSeasonId' and Supplemental>0");
										
										$supplemental=$supplemental-10;
									
									}
									$sql = mysql_query("INSERT into SupplementalDrafts values($CurSeasonId,".$draftorder[$i]['pick'].",".$draftorder[$i]['team'].",0)");
								
									$s=0; //the next passer will get put with his draft day in the above IF statement
									$supplemental++; 
								}*/
							
							/*}*/
			}
			
		
		}//end of the draft
		//delete all supplemental selections for the next supplemental draft
		if($start_time< now()){
			$this->db->where(1)->delete('Supplemental_Selections');
		}
		
	}//end supplemental draft method
	
	
	
	
	
}//end Class Supplemental_Draft

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

