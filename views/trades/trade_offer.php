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
		//choosign a team to trade with
		$('select[name="teams"]').change(function(e) {
			e.preventDefault();
			var partner_id = $('select[name="teams"]').val();
			var path = '<? echo base_url(); ?>Trade/trade_offer/<? echo $team_id; ?>/'+partner_id;
			$("#offer_players_table").empty();
			$("#partner_players_table").empty();
			$("#offer_picks_table").empty();
			$("#partner_picks_table").empty();
			
			$("#trade_offer").load(path);
		});
		
		
		
		//expanding and collapsing the groups of players and picks
		$(".expand").on("click", function() {
			$(this).parents('.expand_container').next("table").toggle();
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
		
		//adding the players and picks to the table when selected
		$(".offer_player_checkbox").on("change", function() {
			if($(this).is(':checked')){
				$("#offer_players_table").append("<tr id='offer_player_"+$(this).val()+"'><td><small>"+$(this).attr('player')+"</small></td></tr>");	
			}
			else{
				$('#offer_player_'+$(this).val()).remove();
			}
		});
		
		$(".partner_player_checkbox").on("change", function() {
			if($(this).is(':checked')){
				$("#partner_players_table").append("<tr id='partner_player_"+$(this).val()+"'><td><small>"+$(this).attr('player')+"</small></td></tr>");	
			}
			else{
				$('#partner_player_'+$(this).val()).remove();
			}
		});
		
		$(".offer_picks_checkbox").on("change", function() {
			if($(this).is(':checked')){
				$("#offer_picks_table").append("<tr id='offer_pick_"+$(this).val()+"'><td><small>"+$(this).attr('pick')+"</small></td></tr>");	
			}
			else{
				$('#offer_pick_'+$(this).val()).remove();
			}
		});
		
		$(".partner_picks_checkbox").on("change", function() {
			if($(this).is(':checked')){
				$("#partner_picks_table").append("<tr id='partner_pick_"+$(this).val()+"'><td><small>"+$(this).attr('pick')+"</small></td></tr>");	
			}
			else{
				$('#partner_pick_'+$(this).val()).remove();
			}
		});
		
		//trigger a click event for counter offer
		$(document).ready(function(){
			
			<? //goes through each pick id and player id and triggers a click. the prefix is 
			// draft_offer#### player_offer#### draft_receive#### and player_receive####
			//controller should have switched offer and receive to make the previous offerer now the
			//receiver and vice versa to make this a new offer just with the same trade id
			if(isset($counter)){
					foreach($counter as $trade_id_num=>$data){
							$trade_id=$trade_id_num;
							foreach($data as $prefix => $players_and_picks_arrays){
								foreach($players_and_picks_arrays as $item_id){ ?>
									$("input[name='<? echo $prefix.$item_id; ?>']").trigger("click");
								<? }
							}
					 }
			} ?>
			
		});
		
		//submit button clicked
		$('#submit').on("click",function() {
			var $offer_players='';
			$('#offer_players_table > tbody  > tr').each(function() {
				$offer_players += $(this).attr('id').replace('offer_player_','') + ',';
			});
			var $partner_players='';
			$('#partner_players_table > tbody  > tr').each(function() {
				$partner_players += $(this).attr('id').replace('partner_player_','') + ',';
			});
			
			var $offer_picks='';
			$('#offer_picks_table > tbody  > tr').each(function() {
				$offer_picks += $(this).attr('id').replace('offer_pick_','') + ',';
			});
			var $partner_picks='';
			$('#partner_picks_table > tbody  > tr').each(function() {
				$partner_picks += $(this).attr('id').replace('partner_pick_','') + ',';
			});
			var $comments= '<strong><? echo team_name_link($team_id); ?>: </strong>' + $('#comments').val() + '<br>';
			$.ajax({
				url: "<? echo base_url(); ?>Trade/submit_trade_offer/<? echo $team_id; ?>/<? if(isset($partner_id)){ echo $partner_id; } if(isset($trade_id)){ echo '/'.$trade_id; } ?>",
				type: "POST",
				data: {
					offer_players: $offer_players,
					partner_players: $partner_players,
					offer_picks: $offer_picks,
					partner_picks: $partner_picks,
					comments: $comments
				},
				success: function(data){ 
					location.reload();
				 }
			});
			
			
		});
	</script>
    <div class="text-center " style="margin-bottom:3px;">
		<?php 
		echo form_open(base_url().'Trade/trade_offer');
			$all_teams = array("0"=>'Choose a Team') + $all_teams;
			if(!isset($partner_id)){ $partner_id=0; }
			echo form_dropdown('teams', $all_teams,$partner_id,set_value($partner_id),"class='form-control'");
		?>
     
    </div>
    <? 
	//only if a trading partner has been selected
	if(isset($team_players)){ ?>
    <script type="text/javascript">
		$('#current_trade').show();
		$('#offer_team').html('<? echo $all_teams[$team_id]; ?>');
		$('#partner_team').html('<? echo $all_teams[$partner_id]; ?>'); 
	
	</script>
    <?
    	// begin submit button
		echo '<div class="col-xs-24" style="display:inline; margin-bottom:5px;">';
    	$data = array(
				'name'          => 'comments',
				'id'            => 'comments',
				'value'         => '',
				'size'          => '50',
			
		);
		
		echo '<strong><small>Add a comment: </small></strong>'.form_input($data);

    	
		echo '<button type="submit" class="btn btn-primary btn-center" name="submit" id="submit">Submit</button></div>';
        ?>
		<div class="col-xs-24 col-sm-12">
            <div class="panel panel-primary blue_panel-primary" >
                <div class="panel-heading blue_panel-heading">
                    <h4 class="panel-title blue_panel-title text-center"><small>From <? echo $all_teams[$team_id]; ?></small></h4>
                </div>
                <div class="text-center expand_container"><strong><div style="display:inline" aria-hidden='true' class="pointer expand"><small>Players</small> +</div></strong></div>
                 <table class="table table-hover table-condensed group_table table-responsive" style="display:none" id="offer_player_table">
                    <tbody>
                    	
            <?		
					foreach($team_players as $fffl_player_id => $data){
						echo '<tr><td class="col-xs-24"><small>';
						echo form_checkbox('player_offer'.$fffl_player_id, $fffl_player_id, FALSE, 'class="offer_player_checkbox" player="'.$data['position'].' '.$data['first_name'].' '.$data['last_name'].' '.$data['current_team'].' '.$data['current_salary'].'"');
						echo $data['position'].' '.$data['first_name'].' '.$data['last_name'].' '.$data['current_team'].' '.$data['current_salary'];
						echo '</small></td></tr>';
						
					} ?>
                    </tbody>
              	</table>
                <div class="text-center  expand_container"><strong><div style="display:inline" aria-hidden='true'  class="pointer expand"><small>Draft Picks</small> &plus;</div></strong></div>
                <table class="table table-hover table-condensed group_table table-responsive"  style="display:none" id="offer_draft_table">
                    <tbody>
                    	
            <?		unset($data);
					foreach($offer_draft_picks as $year => $picks){
						foreach($picks as $data){
							if($data['type']=='Common' && $data['round']<8) {//remove when supplemtnal picks are tradeable
								echo '<tr><td class="col-xs-24"><small>';
								$pick_string =  'Rd. '.$data['round'].' ('.$data['pick_number'].' ';
								if($data['type']=='Common'){
									$pick_string .= date('D',$data['start_time']).')';
								}
								elseif($data['type']=='Supplemental'){
									$pick_string .= 'Supplemental)';
								}
								
								echo form_checkbox('draft_offer'.$data['pick_id'], $data['pick_id'], FALSE, 'class="offer_picks_checkbox" pick="'.$pick_string.'"');
								echo $pick_string;
								if($data['original_owner']!=$team_id){ echo ' from '.team_name_link($data['original_owner']); }
								echo '</small></td></tr>';
								
							}//remove when supplemtnal picks are tradeable
						}
					} ?>
                    </tbody>
              	</table>
                   	
            </div>
       </div>
       <div class="col-xs-24 col-sm-12">
            <div class="panel panel-primary blue_panel-primary" >
                <div class="panel-heading blue_panel-heading">
                    <h4 class="panel-title blue_panel-title text-center"><small>From <? echo $all_teams[$partner_id]; ?></small></h4>
                </div>
                <div class="text-center expand_container"><strong><div style="display:inline" aria-hidden='true' class="pointer expand"><small>Players</small> +</div></strong></div>
                <table class="table table-hover table-condensed group_table table-responsive" style="display:none" id="partner_player_table">
                    <tbody>
                    	
            <?		unset($data);
					foreach($partner_players as $fffl_player_id => $data){
						echo '<tr><td class="col-xs-24"><small>';
						echo form_checkbox('player_receive'.$fffl_player_id, $fffl_player_id, FALSE, 'class="partner_player_checkbox" player="'.$data['position'].' '.$data['first_name'].' '.$data['last_name'].' '.$data['current_team'].' '.$data['current_salary'].'"');
						echo $data['position'].' '.$data['first_name'].' '.$data['last_name'].' '.$data['current_team'].' '.$data['current_salary'];
						echo '</small></td></tr>';
						
					} ?>
                    </tbody>
              	</table>
                <div class="text-center  expand_container"><strong><div style="display:inline" aria-hidden='true'  class="pointer expand"><small>Draft Picks</small> &plus;</div></strong></div>
                <table class="table table-hover table-condensed group_table"  style="display:none" id="partner_draft_table">
                    <tbody>
                    	
            <?		unset($data);
					foreach($partner_draft_picks as $year => $picks){
						foreach($picks as $data){
							if($data['type']=='Common' && $data['round']<8) {//remove when supplemtnal picks are tradeable
								echo '<tr><td class="col-xs-24"><small>';
								$pick_string =  'Rd. '.$data['round'].' ('.$data['pick_number'].' ';
								if($data['type']=='Common'){
									$pick_string .= date('D',$data['start_time']).')';
								}
								elseif($data['type']=='Supplemental'){
									$pick_string .= 'Supplemental)';
								}
								echo form_checkbox('draft_receive'.$data['pick_id'], $data['pick_id'], FALSE, 'class="partner_picks_checkbox" pick="'.$pick_string.'"');
								echo $pick_string;
								if($data['original_owner']!=$partner_id){ echo ' from '.team_name_link($data['original_owner']); }
								echo '</small></td></tr>';
								
							}//remove when supplemtnal picks are tradeable
						}
					} ?>
                    </tbody>
              	</table>   	
            </div>
       </div>
        
        <div class="clearfix visible-xs-block visible-sm-block visible-md-block visible-lg-block"></div>
        <?
                   
        
        	

		?>
    
    </div>
    

	<? }// end if isset(team_players)
		echo form_close(); ?>

<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/