<?PHP
	/**
	 * trade_offer view.
	 *
	 * loads the trade_offer dialogue into a modal located in the footer
	 * also handels the submission of an offer
	 */
//d($this->_ci_cached_vars);
?>
	<script type="text/javascript">
		

		//expanding and collapsing the groups of players and picks
		$(".expand").on("click", function() {
			$(this).parents('.expand_container').next().children("table").toggle();
			
			if($(this).parents('.expand_container').next().height() == 0){
				$(this).parents('.expand_container').next().height("200px");
			}
			else{
				$(this).parents('.expand_container').next().height("0px");
			}
			var current = $(this).html().slice(-1);
			var text = toggleHtml(current,'+','-');
			$(this).html($(this).html().slice(0,-1)+text);
			
		});
		function toggleHtml(thisHtml, t1, t2){
		  
		  if (thisHtml == t2){
			var text = t1;  
		  }
		  else   {               
		  	var text = t2;
		  }
		  return text;
		};
		
		//adding the players to the table when selected
		$(".player_checkbox").on("change", function() {
			var position = $(this).attr("position");
			
			//if the player is being added
			if($(this).is(':checked')){
				
				$("#" + position + $("."+position+"_checkbox:checked").length ).addClass("selected_player").attr("player_id",$(this).val()).html("<small><strong>"+position+"</strong> "+$(this).attr('player_name')+"</small>");
					
				//shut down all the checkboxes except the ones checked if this was position and position limit reached
				if(position == "RB" && $(".RB_checkbox:checked").length == 2 ){
					$(".RB_checkbox").each(function( index ) {
						if(!$(this).is(':checked')) {
							$(this).prop("disabled",true);
						}
					});
				}
				else if(position == "WR" && $(".WR_checkbox:checked").length == 3 ){
					$(".WR_checkbox").each(function( index ) {
						if(!$(this).is(':checked')) {
							$(this).prop("disabled",true);
						}
					});
				}
				else if(position == "QB" || position == "TE" || position == "K"){
					$("."+position+"_checkbox").each(function( index ) {
						if(!$(this).is(':checked')) {
							$(this).prop("disabled",true);
						}
					});
				}
				
				
			}
			
			//the player is being removed
			else{
				//remove the player
				$("div[player_id='"+$(this).val()+"']" ).removeClass("selected_player").attr("player_id","").html("<small><strong>"+position+"</strong></small>");
				
				//open back up the check boxes for this position
				$("."+position+"_checkbox").prop("disabled",false);

			}
			
			
			
		});
		
		
		//submit button clicked
		$('#submit').on("click",function() {
			var players='';
			$('.selected_player').each(function() {
				players += $(this).attr('player_id') + ',';
			});
			$("#test_area").html("<div class='col-xs-24 bg-warning'>Please Wait...</div>");
			
			$.ajax({
				url: "<? echo base_url(); ?>Probowl/submit_probowl/<? echo $team_id; ?>",
				type: "POST",
				data: {
					players: players,
				},
				success: function(data){ 
					$("#test_area").html(data);
				 }
			});
			
			
		});
	</script>
   
    
    <?
    	// begin submit button
		echo '<div class="col-xs-24" style="display:inline; margin-bottom:5px;">';

		echo '<button type="submit" class="btn btn-primary btn-center" name="submit" id="submit">Submit</button></div>';
        ?>
      	<div class="row" id="test_area"></div>
        <div class="col-xs-24">
            <div class="panel panel-primary blue_panel-primary" >
                <div class="panel-heading blue_panel-heading">
                    <h4 class="panel-title blue_panel-title text-center"><small>Selected Roster</small></h4>
                </div>

        			<div id='players_table' class='table table-hover table-condensed'>
                    	
                        
						<? 
						if(!empty($current_roster)){
							foreach($current_roster as $position => $players ){
							?> 
								<div class="row" id="<? echo $position; ?>" >
									<?
									$counter=1; 
									foreach($players as $fffl_player_id) { ?>
										
											<div class="col-xs-24" id="<? echo $position.$counter; ?>"><small><strong><? echo $position; ?></strong></small></div>
										<? if($fffl_player_id !=0 ){ ?>
											 <script>
												$("#cb<? echo $fffl_player_id; ?>").click();
											 </script>
										
									  <?
										}
										$counter++;
									} ?>
								</div>
							<? }   
                         } else { ?> 
                         	<div class="col-xs-24" id="QB1"><small><strong>QB</strong></small></div>
                            <div class="col-xs-24" id="QB1"><small><strong>RB</strong></small></div>
                            <div class="col-xs-24" id="QB1"><small><strong>RB</strong></small></div>
                            <div class="col-xs-24" id="QB1"><small><strong>WR</strong></small></div>
                            <div class="col-xs-24" id="QB1"><small><strong>WR</strong></small></div>
                            <div class="col-xs-24" id="QB1"><small><strong>WR</strong></small></div>
                            <div class="col-xs-24" id="QB1"><small><strong>TE</strong></small></div>
                            <div class="col-xs-24" id="QB1"><small><strong>K</strong></small></div>
                         <? } ?>
                        
                    </div>
            </div>
        </div>
        
        
		<div class="col-xs-24">
            <div class="panel panel-primary blue_panel-primary" >
                <div class="panel-heading blue_panel-heading">
                    <h4 class="panel-title blue_panel-title text-center"><small>Players </small></h4>
                </div>
                <div class="text-center expand_container"><strong><div style="display:inline" aria-hidden='true' class="pointer expand"><small>Quarterbacks</small> +</div></strong></div>
                <div style="overflow:auto;height:0px;">
                 <table class="table table-hover table-condensed group_table table-responsive text-left" style="display:none" >
                    <tbody style="">
                    	
            <?		
					foreach($QBs as $fffl_player_id=>$average){
						echo '<tr><td class="col-xs-24"><small>';
						echo form_checkbox('player'.$fffl_player_id, $fffl_player_id, FALSE, 'id="cb'.$fffl_player_id.'" class="player_checkbox QB_checkbox" player_name="'.player_name_no_link($fffl_player_id,FALSE,TRUE).'" position="QB"');
						echo player_name_link($fffl_player_id,FALSE,TRUE).' '.$average;
						echo '</small></td></tr>';
						
					} ?>
                    </tbody>
              	</table>
                </div>
                
                <div class="text-center expand_container"><strong><div style="display:inline" aria-hidden='true' class="pointer expand"><small>Running Backs</small> +</div></strong></div>
                <div style="overflow:auto;height:0px;">
                 <table class="table table-hover table-condensed group_table table-responsive text-left" style="display:none" >
                    <tbody style="">
                    	
            <?		
					foreach($RBs as $fffl_player_id=>$average){
						echo '<tr><td class="col-xs-24"><small>';
						echo form_checkbox('player'.$fffl_player_id, $fffl_player_id, FALSE, 'id="cb'.$fffl_player_id.'" class="player_checkbox RB_checkbox" player_name="'.player_name_no_link($fffl_player_id,FALSE,TRUE).'" position="RB"');
						echo player_name_link($fffl_player_id,FALSE,TRUE).' '.$average;
						echo '</small></td></tr>';
						
					} ?>
                    </tbody>
              	</table>
                </div>
                
                <div class="text-center expand_container"><strong><div style="display:inline" aria-hidden='true' class="pointer expand"><small>Wide Receivers</small> +</div></strong></div>
                <div style="overflow:auto;height:0px;">
                 <table class="table table-hover table-condensed group_table table-responsive text-left" style="display:none" >
                    <tbody style="">
                    	
            <?		
					foreach($WRs as $fffl_player_id=>$average){
						echo '<tr><td class="col-xs-24"><small>';
						echo form_checkbox('player'.$fffl_player_id, $fffl_player_id, FALSE, 'id="cb'.$fffl_player_id.'" class="player_checkbox WR_checkbox" player_name="'.player_name_no_link($fffl_player_id,FALSE,TRUE).'" position="WR"');
						echo player_name_link($fffl_player_id,FALSE,TRUE).' '.$average;
						echo '</small></td></tr>';
						
					} ?>
                    </tbody>
              	</table>
                </div>
                
                <div class="text-center expand_container"><strong><div style="display:inline" aria-hidden='true' class="pointer expand"><small>Tight Ends</small> +</div></strong></div>
                <div style="overflow:auto;height:0px;">
                 <table class="table table-hover table-condensed group_table table-responsive text-left" style="display:none" >
                    <tbody style="">
                    	
            <?		
					foreach($TEs as $fffl_player_id=>$average){
						echo '<tr><td class="col-xs-24"><small>';
						echo form_checkbox('player'.$fffl_player_id, $fffl_player_id, FALSE, 'id="cb'.$fffl_player_id.'" class="player_checkbox TE_checkbox" player_name="'.player_name_no_link($fffl_player_id,FALSE,TRUE).'" position="TE"');
						echo player_name_link($fffl_player_id,FALSE,TRUE).' '.$average;
						echo '</small></td></tr>';
						
					} ?>
                    </tbody>
              	</table>
                </div>
                
                <div class="text-center expand_container"><strong><div style="display:inline" aria-hidden='true' class="pointer expand"><small>Kickers</small> +</div></strong></div>
                <div style="overflow:auto;height:0px;">
                 <table class="table table-hover table-condensed group_table table-responsive text-left" style="display:none" >
                    <tbody style="">
                    	
            <?		
					foreach($Ks as $fffl_player_id=>$average){
						echo '<tr><td class="col-xs-24"><small>';
						echo form_checkbox('player'.$fffl_player_id, $fffl_player_id, FALSE, 'id="cb'.$fffl_player_id.'" class="player_checkbox K_checkbox" player_name="'.player_name_no_link($fffl_player_id,FALSE,TRUE).'" position="K"');
						echo player_name_link($fffl_player_id,FALSE,TRUE).' '.$average;
						echo '</small></td></tr>';
						
					} ?>
                    </tbody>
              	</table>
                </div>
                   	
            </div>
       </div>
       
       
        
        <div class="clearfix visible-xs-block visible-sm-block visible-md-block visible-lg-block"></div>

    </div>
    

	<? 
		echo form_close(); ?>

<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/