<?PHP
	/**
	 * trade_details view.
	 *
	 * loads the details for a trade
	 * provides opportunity to rspond to trade if applicable
	 */
	//d($this->_ci_cached_vars);
	
?>
	
	<script type="text/javascript">

	</script>
    <div class="col-md-12">
        <div class="panel panel-primary blue_panel-primary" >
            <div class="panel-heading blue_panel-heading col-sm-24" >
                <h4 class="panel-title blue_panel-title text-center" ><small><? echo team_name_link($trade_data_array['offered_by']); ?> Receives</small></h4>
            </div>
             <table class="table table-hover table-condensed" id="selected_table">
                <tbody>
                	<? if(isset($trade_data_array['players_received'])){
						foreach($trade_data_array['players_received'] as $fffl_player_id){ ?>
                        <tr >
                            <td class="text-center col-xs-18" colspan="2"><small><? echo player_name_link($fffl_player_id); ?></small></td>
                        </tr>
                    <? } 
					}?>
                    <? if(isset($trade_data_array['draft_picks_received'])){
						foreach($trade_data_array['draft_picks_received'] as $pick_id => $data){ ?>
                        <tr >
                            <td class="text-center col-xs-18" colspan="2"><small><? echo 'Rd. '.$data['round'].' (#'.$data['pick_number'].' '.date('D',$data['start_time']).')';
																					if($data['original_owner']!=$trade_data_array['offered_to']){
																						echo ' from '.team_name_link($data['original_owner']);
																					}?></small></td>
                        </tr>
                    <? } 
					}?>
                </tbody> 
            </table>
        </div>
	</div>
   <div class="col-md-12">
        <div class="panel panel-primary blue_panel-primary" >
            <div class="panel-heading blue_panel-heading col-sm-24" >
                <h4 class="panel-title blue_panel-title text-center" ><small><? echo team_name_link($trade_data_array['offered_to']); ?> Receives</small></h4>
            </div>
             <table class="table table-hover table-condensed" id="selected_table">
                <tbody>
                    <? if(isset($trade_data_array['players_offered'])){
						foreach($trade_data_array['players_offered'] as $fffl_player_id){ ?>
                        <tr >
                            <td class="text-center col-xs-18" colspan="2"><small><? echo player_name_link($fffl_player_id); ?></small></td>
                        </tr>
                    <? } 
					}?>
                    <? if(isset($trade_data_array['draft_picks_offered'])){
						foreach($trade_data_array['draft_picks_offered'] as $pick_id => $data){ ?>
                        <tr >
                            <td class="text-center col-xs-18" colspan="2"><small><? echo 'Rd. '.$data['round'].' (#'.$data['pick_number'].' '.date('D',$data['start_time']).')';
																					if($data['original_owner']!=$trade_data_array['offered_by']){
																						echo ' from '.team_name_link($data['original_owner']);
																					}?></small></td>
                        </tr>
                    <? } 
					}?>
                </tbody> 
            </table>
        </div>
	</div>
    <div class="clearfix visible-xs-block visible-md-block visible-sm-block visible-lg-block"></div>
    <div >
    	<small><strong>Offered: </strong><? echo date('D M j, g:i a',$trade_data_array['time_offered']); ?><br>
        <? if($trade_data_array['time_accepted_rejected']>0 && $trade_data_array['response_status']!=0){
				if($trade_data_array['response_status']==1){
					?><strong>Accepted: </strong><?
				}
                else{
                	?><strong>Declined: </strong><?
                }
			echo date('D M j, g:i a',$trade_data_array['time_accepted_rejected']); ?><br><?
		} ?>
        <? if($trade_data_array['time_approved']>0 && $trade_data_array['approval_status']!=0){
				if($trade_data_array['approval_status']==1){
					?><strong>Approved: </strong><?
				}
                else{
                	?><strong>Approval Denied: </strong><?
                }
			echo date('D M j, g:i a',$trade_data_array['time_approved']).' '.$committee_votes_array['for'].'-'.$committee_votes_array['against']; ?><br><?
		} 
		elseif($trade_data_array['response_status']==1){
			echo '<strong>Current Vote: </strong>'.$committee_votes_array['for'].'-'.$committee_votes_array['against'].'<br>';	
		}?>
    </div>

    <div > 
    	<strong>Comments:</strong><br>
        <div style="margin-left:10px;  ">
			<? echo $trade_data_array['comments']; ?>
        </div>	
    </div> 
	<div class="col-xs-24" style="margin-bottom:5px;"><?
    	$data = array(
				'name'          => 'comments',
				'id'            => 'comments',
				'value'         => '',
				'width'			=> '100em'
				
			
		);
		
		echo '<strong>Add a comment: </strong>'.form_input($data);

    echo '</div>'; ?>
    
      <script>
       	$(".action_btns").on("click",function(){
			$.ajax({
				url: "<? echo base_url(); ?>Trade/" + $(this).attr("action") + "_trade_offer/<? echo $trade_id; ?>/" + $(this).attr("variables"),
				type: "POST",
				data: {
					comments: '<strong><? echo team_name_link($_SESSION['team_id']); ?></strong>' + $('#comments').val() +'<br>'
				},
				success: function(data){ 
					location.reload();
				 }
			});
		});
		
        $("#counter_btn").on("click",function(){
			
				//load the trade_offer page
				
				var path = '<? echo base_url().'Trade/trade_offer/0/0/'.$trade_data_array['trade_id']; ?>';
				$('#trade_offer').load(path);
				
		});
      </script>
       
    
      <div class="row text-center" style="margin-top:5px">
     
      	<? if(($trade_data_array['response_status']==0 && $_SESSION['team_id']==$trade_data_array['offered_by']) || $_SESSION['security_level']==3){ ?>
            <button type="button" class="btn btn-primary btn-sm action_btns" action="delete" id="delete_btn" variables="" style="margin-top:5px;">
                Delete
            </button>
        <? } ?>
        <? if($trade_data_array['response_status']==0 && ($_SESSION['team_id']==$trade_data_array['offered_to'] || $_SESSION['security_level']==3)){ ?>
            <button type="button" class="btn btn-primary btn-sm action_btns" action="decline" id="decline_btn" variables=""  style="margin-top:5px;">
                Decline
            </button>
            <button type="button" class="btn btn-primary btn-sm action_btns" action="accept" id="accept_btn" variables=""  style="margin-top:5px;">
                Accept
            </button>
            <button type="button" class="btn btn-primary btn-sm " data-toggle="modal" data-target="#trade_modal" id="counter_btn" variables=""  style="margin-top:5px;">
                Counter
            </button>
   		<? } ?>
        <? if($trade_data_array['response_status']==1 && $_SESSION['security_level']==3 && $trade_data_array['approval_status']==0){ ?>
            <button type="button" class="btn btn-primary btn-sm action_btns" action="auto" id="approve_btn" variables="1" style="margin-top:5px;">
                Auto Approve
            </button>
            <button type="button" class="btn btn-primary btn-sm action_btns" action="auto" id="deny_btn" variables="-1" style="margin-top:5px;">
                Auto Deny
            </button>
		<? } ?>
        <? if($trade_data_array['response_status']==1 && $trade_data_array['approval_status']==0 && (in_array($_SESSION['team_id'],$trade_committee['team_id']) && $committee_votes_array[$_SESSION['team_id']]==0)){ ?>
            <button type="button" class="btn btn-primary btn-sm action_btns" action="committee" variables="<? echo $trade_committee['group'][array_search($_SESSION['team_id'],$trade_committee['team_id'])].'/1'; ?>" id="vote_for_btn"  style="margin-top:5px;">
               Approve
            </button>
            <button type="button" class="btn btn-primary btn-sm action_btns" action="committee" variables="<? echo $trade_committee['group'][array_search($_SESSION['team_id'],$trade_committee['team_id'])].'/-1'; ?>" id="vote_against_btn"  style="margin-top:5px;">
               Deny
            </button>
		<? } ?>
        
        <? if($trade_data_array['approval_status']==1 && $_SESSION['security_level']==3){ 
			?>
            <button type="button" class="btn btn-primary btn-sm action_btns" action="undo" id="undo_btn" variables="" style="margin-top:5px;">
                Undo
            </button>
        <? } ?>
   		
      </div>


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/