<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
Functions for linking team names, player names,


*/

	//Returns Owner First Name Team Nickname as a link to team page
	function team_name_link($team_id,$first=TRUE,$nick=TRUE) {
		$CI =& get_instance();
    	
		$team_name = $CI->Teams->get_team_name_first_nickname($team_id,$first,$nick);
    	$return_string = '<a href="'.base_url().'Team/id/'.$team_id.'" ><span class="link_color">'.$team_name.'</span></a>';
		return $return_string;
	}
	
	//Returns Owner First Name Team Nickname as a link to team page
	function team_name_no_link($team_id) {
		$CI =& get_instance();
    	
		$team_name = $CI->Teams->get_team_name_first_nickname($team_id);
    	$return_string = $team_name;
		return $return_string;
	}



	//Returns Player Pos First Name Last Name Team as a link to player page
	function player_name_link($fffl_player_id,$add_position=TRUE,$add_team=TRUE) {
		$CI =& get_instance();
    	
		$data = $CI->Players->get_player_info(array($fffl_player_id),"fffl_player_id","position first_name last_name current_team");
		$player_name='';
		if($add_position){
			$player_name .= $data['position'].' ';	
		}
		$player_name .= str_replace("\'","'", $data['first_name']).' '.str_replace("\'","'", $data['last_name']);
		if($add_team){
			$player_name .= ' '.$data['current_team'];
		}
    	$return_string = '<a href="'.base_url().'Player/id/'.$fffl_player_id.'">'.$player_name.'</a>';
		return $return_string;
	}
	
	//Returns Player Pos First Name Last Name Team as a link to player page
	function player_name_no_link($fffl_player_id,$add_position=TRUE,$add_team=TRUE) {
		$CI =& get_instance();
    	
		$data = $CI->Players->get_player_info(array($fffl_player_id),"fffl_player_id","position first_name last_name current_team");
		$player_name='';
		if($add_position){
			$player_name .= $data['position'].' ';	
		}
		$player_name .= str_replace("\'","'", $data['first_name']).' '.str_replace("\'","'", $data['last_name']);
		if($add_team){
			$player_name .= ' '.$data['current_team'];
		}
    	$return_string = $player_name;
		return $return_string;
	}
	
	//Returns proper roman numarl
	function roman_numeral($integer, $upcase = true) 
	 { 
		 $table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1); 
		 $return = ''; 
		 while($integer > 0) 
		 { 
			 foreach($table as $rom=>$arb) 
			 { 
				 if($integer >= $arb) 
				 { 
					 $integer -= $arb; 
					 $return .= $rom; 
					 break; 
				 } 
			 } 
		 } 
	
		 return $return; 
	 } 