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

			$(".check_player").on("click", function() {
				var number_checked = $('.check_player:checked').size();
				//correct number selected
				if(number_checked==<? echo $number_to_release; ?>){
					$('.check_player:not(:checked)').addClass('fade');
					$('.check_player:not(:checked)').attr('disabled',true);
					$('#alert').text('You must confirm the release of the player(s).');
					$('#alert').removeClass('alert-danger').addClass('alert-warning');
					$('#confirm_button').removeClass('fade');
					
				}
				else if(number_checked<<? echo $number_to_release; ?>){
					$('.check_player:not(:checked)').removeClass('fade');
					$('.check_player:not(:checked)').attr('disabled',false);
					$('#alert').html('You must select <span id="number_left"></span> more players.');
					$('#alert').removeClass('alert-warning').addClass('alert-danger');
					$('#number_left').text((<? echo $number_to_release; ?>-number_checked));
					$('#confirm_button').addClass('fade');
				}
				
			});
			
			$("#confirm_button").on("click", function() {
				if(!$("#confirm_button").hasClass('fade')){
					//get all teh players
					var released_players = '';
					$(".check_player:checked").each(function() {
						released_players = released_players + $(this).val() + '#';
					});
					//release the player
					
					window.location.href='<? echo base_url().'Team/release_players/'.$team_id.'/'; ?>' + released_players;
				}
			});

	</script>
    <? 
		$alert = 'alert alert-danger';
		
	?>
	 <div class="text-center <? echo $alert; ?>" id="alert">
     	You must select <span id="number_left"><? echo $number_to_release; ?></span> more players.
     </div>
     <div class="col-xs-24" >
    	<div class="row text-center" style="margin-bottom:5px;">
             <button type="button" class="btn btn-primary btn-sm fade" id="confirm_button" >
                  Confirm
             </button>
    	</div>
    </div>
	 <div class="col-xs-24 col-sm-18 col-sm-offset-3 ">
        <div class="panel panel-primary blue_panel-primary" >
            <div class="panel-heading blue_panel-heading">
                <h4 class="panel-title blue_panel-title text-center"><small>Current Active Roster</small></h4>
            </div>
             <table class="table table-hover table-striped table-condensed" id="selected_table">
                <tbody>
					<? 
                    foreach($active_roster as $fffl_player_id){ 
                        echo '<tr>';
                            echo '<td class="text-left col-xs-24">
									<div class="checkbox" style="padding:0px;margin:0px;">
										<label><input type="checkbox" class="check_player" value="'.$fffl_player_id.'"><small>'.player_name_link($fffl_player_id).'</small></input></label>
									</div>
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