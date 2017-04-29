<?PHP
	/**
	 * franchise view.
	 *
	 * through ajax will display past franchise selections and add a link to 
	 * select new franchise if that is sent from controller
	 */
	//d($this->_ci_cached_vars);
?>
	<script type="text/javascript">
		
	</script>
	<? 
	function create_roster_table($team_id,$roster) {
		?>
        
        <div class="panel panel-primary " >
        	<div class="panel-heading blue_panel-heading">
            	<h3 class="panel-title text-center "><? echo team_name_no_link($team_id); ?></h3>
            </div>
            <div class="table-responsive">
            <table class="table table-hover table-condensed " id="">
                <tbody > 
                    <tr >
                        <td class="text-center"><small>QB</small></td>
                        <td class="text-center"><small>RB</small></td>
                        <td class="text-center"><small>WR</small></td>
                        <td class="text-center"><small>TE</small></td>
                        <td class="text-center"><small>K</small></td>
                        
                    </tr>
					<tr>
				<?
        
		//add rows for each individual player 
		$position_array = array('QB','RB','WR','TE','K');
		foreach($position_array as $position){
			echo '<td class="text-center ellipses" width="20%">';
			if(isset($roster[$position])){
				      
				foreach($roster[$position] as $players_array){
					if($players_array['area']!='Roster') { $area=$players_array['area']; } else { $area=''; }
					//create the display table
					echo '<strong><small>'.player_name_link($players_array['fffl_player_id'],FALSE,TRUE).' '.$players_array['average'].' '.$players_array['salary'].' '.$area.'</strong></small><br>';	
				}//end foreach of each position
				
			}
			echo '</td>';
		}//foreach position
		
		?>		</tr>
                </tbody>
            </table>
            </div>
        </div>
        
		<?
	}//end create table function
	
	
	
	
	?>
	<div id="content_area">
            <div class="row" id="login"> <!--login box--> 
                <div class="col-xs-24" >
	
    	<div class="col-xs-24 ">
        	
                <? 
				foreach($teams_and_rosters as $team_id=>$roster){
                    create_roster_table($team_id,$roster);
				}
                ?>

		</div>
 </div></div></div></div>

<?PHP
/*End of file franchise.php*/
/*Location: ./application/veiws/Team/franchise.php*/