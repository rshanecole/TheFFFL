<?PHP
	/**
	 * draft year view.
	 *
	 * through ajax will display the results of the given year's draft
	 */
	//d($this->_ci_cached_vars);
?>

	<script type="text/javascript">

      
		$('.details_button').popover({
			html: true,
			trigger: 'click',
			placement: 'left',
          title: 'Trade Details' + '',
			container: '#trade_panel',
			content: function() {
				var id = $(this).attr('id');
			  return $.ajax({url: 'http://fantasy.thefffl.com/Trade/load_trade_details/'+id,
							 dataType: 'html',
							 async: false}).responseText;
			}
		  }).click(function(e) {
			$(this).popover('toggle');
         
        });
      
		$('body').on('click', function (e) {
			$('[data-original-title]').each(function () { 
				//the 'is' for buttons that trigger popups
				//the 'has' for icons within a button that triggers a popup
				if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
					$(this).popover('hide');
					
				}
			});
		});
		function filter(){
			//show them all first
			$('.trade_filter').show();
			//check each button. if danger, hide the class
			if($('#completed_filter_btn').hasClass("fade"))	{
				$('.completed_filter').hide();
			}
			if($('#team_filter_btn').hasClass("fade"))	{
				$('.team_filter').hide();
			} 
		}
		
		$('#completed_filter_btn').on("click", function() {
			$(this).toggleClass("fade");
		
			filter();
		});
			
		$('#team_filter_btn').on("click", function() {
			$(this).toggleClass("fade");
		
			filter();
		});
		
		//launch the trade proposal modal
		var modal = "<!--trade poposal modal -->\
					<!--data loaded into elements by the button that launches this on the actual trade list page-->\
					<div class='modal fade' id='trade_modal' tabindex='-1' role='dialog' aria-labelledby='trade_modal_Label'>\
						<div class='modal-dialog' role='document'>\
							<div class='modal-content'>\
								<div class='modal-header'>\
									<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>\
									<h4 class='modal-title' id='trade_modal_Label'>Offer a Trade</h4>\
								</div>\
								<div class='modal-body'>\
									<div id='current_trade' style='display:none' class='col-xs-24'>\
										<div class='col-xs-24 col-sm-12'>\
											<div class='panel panel-primary blue_panel-primary'>\
												<div class='panel-heading blue_panel-heading'>\
													<h4 class='panel-title blue_panel-title text-center'><small>From <span id='offer_team'></span></small></h4>\
												</div>\
												<table id='offer_players_table' class='table table-hover table-condensed'>\
												</table>\
												<table id='offer_picks_table' class='table table-hover table-condensed'>\
												</table>\
											</div>\
										</div>\
										<div class='col-xs-24 col-sm-12'>\
											<div class='panel panel-primary blue_panel-primary'>\
												<div class='panel-heading blue_panel-heading'>\
													<h4 class='panel-title blue_panel-title text-center'><small>From <span id='partner_team'></span></small></h4>\
												</div>\
											 	<table id='partner_players_table' class='table table-hover table-condensed'>\
												</table>\
												<table id='partner_picks_table' class='table table-hover table-condensed'>\
												</table>\
											</div>\
										</div>\
									</div>\
								</div>\
							  	<div class='col-xs-24' id='trade_offer'>\
							  	</div>\
								<div class='modal-footer'>\
								</div>\
							</div>\
						</div>\
					</div>";
			jQuery(function() { $('#modal_area').html(modal);});
		
		
		
			$("#trade_btn").on("click",function(){
				//load the trade_offer page
				$("#offer_players_table").empty();
				$("#partner_players_table").empty();
				$("#offer_picks_table").empty();
				$("#partner_picks_table").empty();
				var path = '<? echo base_url().'Trade/trade_offer/'.$team_id; ?>';
				$('#trade_offer').load(path);
				
			});
			
	</script> 

    <div>
    	<? if($trading_open){ ?>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#trade_modal" id="trade_btn" style="margin-bottom:5px;" >
                     Offer a Trade 
            </button>
        <? } else { ?>
				Trading is Currently Closed<br>
			
		<? }?>
        <br>
    	<button type="button" class="btn btn-primary btn-sm" id="completed_filter_btn" >
                 Declined 
        </button>
        <button type="button" class="btn btn-primary btn-sm" id="team_filter_btn" >
                 All Teams 
        </button>
        
        <div class="panel panel-primary blue_panel-primary" style="margin-top:5px;" id="trade_panel">
            <div class="panel-heading blue_panel-heading">
                <h3 class="panel-title blue_panel-title">
                   <strong><? echo $year; ?> Trades</strong>
                </h3>
            </div> 
            <div class="panel-body">
      
                    <div class="text-center hidden-xs col-sm-12" style="border-bottom:solid #ccc 1px; "><small>Teams</small></div>
                    
                    <div class="text-center hidden-xs col-sm-6" style="border-bottom:solid #ccc 1px; "><small>Status</small></div>
                    <div class="text-center hidden-xs col-sm-4" style="border-bottom:solid #ccc 1px; " ><small>Last Action</small></div>
                    <div class="text-center hidden-xs col-sm-2" style="border-bottom:solid #ccc 1px; "><small>Details</small></div>
               

                
                
                <?
				$trade_shade = '';
				
				if($trades_array){

                     foreach($trades_array as $trade){ 
					 	//add filter classes
					 	if($trade['approval_status']==1){ $status_class=''; } else { $status_class='completed_filter'; }
						if($trade['offered_to']==$team_id || $trade['offered_by']==$team_id){ $team_filter =''; } else { $team_filter='team_filter'; }
					 	
						//div for filtering
						echo '<div class="col-xs-24 trade_filter '.$status_class.' '.$team_filter.'" style="border-bottom:solid #ccc 1px; padding-left:0px; padding-right:0px;">';
						
					 		/*if($trade_shade==''){
                        		$trade_shade = 'style="background-color:#eee"';
							}
							else {
								$trade_shade = '';	
							}*/
							
                            echo '<div class="text-left col-xs-10 col-sm-6 ellipses"><small>'.team_name_link($trade['offered_by']).'</small></div>';
                            echo '<div class="text-left col-xs-10 col-sm-6 ellipses"><small>'.team_name_link($trade['offered_to']).'</small></div>';
                            //no action from other owner
                            if($trade['response_status']==0){
                                $status = 'Pending Action';
                                $last_action = $trade['time_offered'];
                            }
                            //other owner declined
                            elseif($trade['response_status']==-1){
                                $status = 'Declined';
                                $last_action = $trade['time_accepted_rejected'];
                            }
                            //accepted
                            elseif($trade['response_status']==1){
                                //no action from committee yet
                                if($trade['approval_status']==0){
                                    $status = 'Accepted - Pending Approval';
                                    $last_action = $trade['time_accepted_rejected'];
                                }
                                //approval denied
                                elseif($trade['approval_status']==-1){
                                    $status = 'Accepted - Not Approved';
                                    $last_action = $trade['time_approved'];
                                }
                                //approved and completed
                                else{
                                    $status = 'Accepted - Approved';
                                    $last_action = $trade['time_approved'];
                                }
                            }
							echo '<div class="text-center hidden-sm hidden-md hidden-lg col-xs-4 pointer" ><a tabindex="0" class="blue_links details_button" role="button" id="'.$trade['trade_id'].'"><small>Details</small></a></div>';
							
							echo '<div class="text-left col-xs-14 hidden-sm hidden-md hidden-lg"  ><small>'.$status.'</small></div>';
							echo '<div class="text-right col-xs-10  hidden-sm hidden-md hidden-lg"  ><small>'.date('M j, g:ia',$last_action).'</small></div>';
                           	echo '<div class="text-center hidden-xs col-sm-6"  ><small>'.$status.'</small></div>';
							echo '<div class="text-center hidden-xs col-sm-4"  ><small>'.date('M j g:ia',$last_action).'</small></div>';
    
							echo '<div class="text-center hidden-xs col-sm-2 pointer" ><a tabindex="0" class="blue_links details_button" role="button" id="'.$trade['trade_id'].'"><small>Details</small></a></div>';
						    
						echo '</div>';
                        
                    }
                } ?>
                
            </div>
        </div>
    </div>

       
 


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/