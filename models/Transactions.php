<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Transactions Model.
	 *
	 * ?????
	 *		
	 */
	
Class Transactions extends CI_Model 
{
	public function __construct() 
    {
		parent::__construct();
		//$ci = get_instance();
		$this->load->Model('Database_Manager');
		
		$this->load->helper('string');
		$this->load->library('email');
	}

//********************************************************************	
	//simply gets the first and last trade years for a league
	//currently used to create dropdown menu on trade  view
	public function get_first_last_transaction_years($league_id){
		$max_query = $this->db->select_max('season','max')
				->where('league_id',$league_id)
				->get('Transactions');
		$result = $max_query->row();
		$max = $result->max;
		
		$min_query = $this->db->select_min('season','min')
				->where('league_id',$league_id)
				->get('Transactions');
		$result = $min_query->row();
		$min = $result->min;
		
		$return_array['last'] = $max;
		$return_array['first'] = $min;
		
		return $return_array;
		
	}

//*****************************************************************************	
	
	//get all transactions for a year's
	public function get_transactions_year($league_id,$year,$team_id='All'){
		
		$return_array = array();
		
		$conditions = 'season = '.$year;
		if($team_id!='All'){
			$conditions .=' and team_id = '.$team_id.' ';
			
		}
		
		$transactions_query = $this->db->select('transaction_id,team_id,transaction_type,fffl_player_id,team,time,season')
					->where($conditions)
					->order_by('time','DESC')
					->get('Transactions');
		
		//add the picks to the array for that year key
		foreach($transactions_query->result() as $transaction){
			if($transaction->team==''){ $add_team = FALSE; } else { $add_team= TRUE; }
			$return_array[$transaction->transaction_id]['team_id']=$transaction->team_id;
			
			//add the transaction text
			
			if($transaction->transaction_type=='Release'){ $text = 'Released '.player_name_link($transaction->fffl_player_id,TRUE,$add_team).'.'; }
			if($transaction->transaction_type=='Activate PUP'){ $text = 'Activated '.player_name_link($transaction->fffl_player_id,TRUE,$add_team).' from PUP.'; }
			if($transaction->transaction_type=='Activate PS'){ $text = 'Activated '.player_name_link($transaction->fffl_player_id,TRUE,$add_team).' from PS.'; }
			if($transaction->transaction_type=='Add PUP'){ $text = 'Added '.player_name_link($transaction->fffl_player_id,TRUE,$add_team).' to PUP.'; }
			if($transaction->transaction_type=='Add PS'){ $text = 'Added '.player_name_link($transaction->fffl_player_id,TRUE,$add_team).' to PS.'; }
			if($transaction->transaction_type=='FA'){ $text = 'Signed freeagent '.player_name_link($transaction->fffl_player_id,TRUE,$add_team).'.'; }
			$return_array[$transaction->transaction_id]['text']=$text;
			
			$return_array[$transaction->transaction_id]['transaction_type']=$transaction->transaction_type;
			$return_array[$transaction->transaction_id]['time']=$transaction->time;
			
		}
		
		if(count($return_array)>0){
			return $return_array;
		
		}
		else {
			return false;
		}
		
	}
  
  
}//end of class Transactions





/*End of file Transactions.php*/
/*Location: ./application/models/Transactions.php*/