<?PHP
	/**
	 * franchise_list view.
	 *
	 * loads the franchise list into a modal located in the footer
	 * also handels the updating of the franchise playes
	 */
	//d($this->_ci_cached_vars);
?>
	<script type="text/javascript">

		//on click of non-franchise player, add him to franchise
		$('.add_player').on('click', function(){
			id=$(this).attr('id');
			$(this).parent().parent().parent().fadeOut("slow", function(){
				$('#franchise_list').load('<? echo base_url().'/Team/add_franchise_player/'.$team_id.'/'; ?>' + id);
			});
			
		});

		//on click of franchise player, removes him from franchise
		$('.remove_player').on('click', function(){
			id=$(this).attr('id');
			$(this).parent().parent().parent().fadeOut("slow", function(){
				$('#franchise_list').load('<? echo base_url().'/Team/remove_franchise_player/'.$team_id.'/'; ?>' + id);
			});
		});
	
	
	</script>
    <? 
		$alert = 'alert alert-info';
		if($error_message != ''){
			if (strpos($error_message, 'Success') !== false){
				$alert = 'alert alert-info';
			} 
			else {
				$alert = 'alert alert-danger';	
			}
		} elseif($salary_total == $league_salary_cap){
			$alert = 'alert alert-warning';
		}
	?>
	 <div class="text-center <? echo $alert; ?>">
     	Current Salary Total: <? echo $salary_total.' - Remaining: '.($league_salary_cap-$salary_total);?><br><? echo $error_message; ?>
     </div>
	 <div class="col-xs-24 col-sm-12">
        <div class="panel panel-primary blue_panel-primary" >
            <div class="panel-heading blue_panel-heading">
                <h4 class="panel-title blue_panel-title text-center"><small>Current Selections</small></h4>
            </div>
             <table class="table table-hover table-condensed" id="selected_table">
                <tbody>
                    <tr >
                        <td class="text-center col-xs-18" colspan="2"><small>Player</small></td>
                        <td class="text-center col-xs-1" colspan="1" ><small>Sal</small></td>
                        
                        
                    </tr>
			<? 
            if($selected_franchise_players){ 
                    foreach($selected_franchise_players[$year] as $area=>$positions){	
                        foreach($positions as $position => $players){		
                            foreach($players as $fffl_player_id =>$data){
                                $area_display='';
                                if($area!='Roster'){
                                    $area_display=$area;
                                }
								echo '<tr>
										<td class="col-xs-6">
											<small>
												<span class="glyphicon glyphicon-remove-sign red_font remove_player" id="'.$fffl_player_id.'" aria-hidden="true" ></span>
												'.$position.'
											</small> 
										</td>
										<td class="col-xs-14">
											<strong>'.$data['name'].'</strong>
										</td>
										<td class="text-center col-xs-1">
											<small>'.$data['salary'].'</small>
										</td>
										<td class="text-center col-xs-1">
											<small>'.$area_display.'</small>
										</td>';
										
								echo '</tr>';

                            }
                        }	
                    }
            }
            ?>
                </tbody> 
            </table>
        </div>
	</div>
    <div class="col-xs-24 col-sm-12">
        <div class="panel panel-primary blue_panel-primary" >
            <div class="panel-heading blue_panel-heading">
                <h4 class="panel-title blue_panel-title text-center"><small>Remaining Roster</small></h4>
            </div>
             <table class="table table-hover table-condensed" id="unselected_table">
                <tbody>
                    <tr >
                        <td class="text-center col-xs-18" colspan="2"><small>Player</small></td>
                        <td class="text-center col-xs-1" colspan="1" ><small>Sal</small></td>
                        
                        
                    </tr>
                        <? 
                foreach($unselected_franchise_players as $player){
                    if($player['area']!='Roster'){
                        $area=$player['area'];
                    }
                    else{
                        $area='';
                    }
                    echo '<tr>
                            <td class="col-xs-6">
                                <small>
                                    <span class="glyphicon glyphicon-plus-sign blue_font add_player" id="'.$player['fffl_player_id'].'" aria-hidden="true" ></span>
                                    '.$player['display_data']['position'].'
                                </small> 
                            </td>
                            <td class="col-xs-14">
                                <strong>'.$player['display_data']['first_name'].' '.$player['display_data']['last_name'].'</strong>
                            </td>
                            <td class="text-center col-xs-1">
                                <small>'.$player['salary'].'</small>
                            </td>
							<td class="text-center col-xs-1">
								<small>'.$area.'</small>
							</td>';
							
                        echo '</tr>';	
                }
                    
             ?>
             	</tbody>
             </table>
        </div>
    </div>



<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/