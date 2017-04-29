<?PHP
	/**
	 * ps_list view.
	 *
	 * loads the ps list into a modal located in the footer
	 * also handels the updating of the ps player
	 */
	//d($this->_ci_cached_vars);

		$alert = 'alert alert-info';
		if($error_message != ''){
			if (strpos($error_message, 'Success') !== false){
				$alert = 'alert alert-info';
			} 
			else {
				$alert = 'alert alert-danger';	
			}
		} 

		
	?>
	<script type="text/javascript">

		//on click of player, adds him to pup
		$('.add_to_ps').on('click', function(){
			id=$(this).attr('id');
			
				$("#confirm").show();
				$("#confirm").html('Confirm');
				$("#confirm").data('player_id',id);
				$("#dismiss").show();
				$("#dismiss").html('Dismiss');
				$("#selected_player").show();
				$("#selected_player").text($(this).next(".player_position").text() + $(this).parent().parent().next(".player_name").text() + $(this).parent().parent().next(".player_salary").text());

		});
		
		$('#confirm').on('click',function(){
                        $('#confirm').off('click');
			id=$(this).data('player_id');
			window.location.href="<? echo base_url().'Team/add_ps/'.$team_id.'/'; ?>" + id;
		});
		
		$('#dismiss').on('click',function(){
			$('#confirm').hide();
			$('#dismiss').hide();
			$('#selected_player').hide();
		});
	
	</script>
   
	 <div class="text-center <? echo $alert; ?>">
     	Any player added to PS will remain there for the remainder of the season.
     </div>
     <div class="text-center" style="margin-bottom:10px;">
     	 <small><strong><span id="selected_player" ></span><br>
			<span id="confirm"  class="alert alert-warning pointer" role="alert" style="padding:1px 4px 1px 4px;display:none"></span>&nbsp;&nbsp;
        <span id="dismiss" class="alert alert-danger pointer" role="alert" style="padding:1px 4px 1px 4px;display:none"></span>
       </strong></small>
     </div>
	 <div class="col-xs-24 col-md-16 col-md-offset-4">
        <div class="panel panel-primary blue_panel-primary " >
            <div class="panel-heading blue_panel-heading">
                <h4 class="panel-title blue_panel-title text-center"><small>Eligible Players</small></h4>
            </div>
             <table class="table table-hover table-condensed" id="selected_table">
                <tbody>
                    <tr >
                        <td class="text-center " colspan="2"><small>Player</small></td>
                        <td class="text-center " colspan="1" ><small>Sal</small></td>
                    </tr>
			<? 
            if($eligible_ps_players){ 
				foreach($eligible_ps_players as $fffl_player_id=>$data){	
					echo '<tr>
							<td class="">
								<small>
									<span class="pointer glyphicon glyphicon-plus-sign blue_font add_to_ps" id="'.$fffl_player_id.'" aria-hidden="true" ></span>
									
									<span class="player_position">'.$data['display_data']['position'].'</span>
								</small> 
							</td>
							<td class="player_name">
								<span >
								<small>
									<strong>'.$data['display_data']['first_name'].' '.$data['display_data']['last_name'].'</strong>
								</small>
								</span>
							</td>
							<td class="text-center " class="player_salary">
								<small>'.$data['salary'].'</small>
							</td>';
							
					echo '</tr>';
				}
            
            }
            ?>
                </tbody> 
            </table>
        </div>
	</div>
    



<?PHP
/*End of file ps_list.php*/
/*Location: ./application/veiws/team/ps_list.php*/