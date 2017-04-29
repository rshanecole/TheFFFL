<?PHP
	/**
	 * live_draft view.
	 *
	 * through Pusher will get the data and update the different parts of this view
	 */
	//d($this->_ci_cached_vars);
?>


	<script type="text/javascript">
		//var source = new EventSource("<?// echo base_url(); ?>Draft/available_players/31/56");

		//source.onmessage = function (event) {
			  // a message without a type was fired
		 
			//$("#test").html(event + "<br>");
		//};	
		
		
		$(document).ready(function(){
		  $('.slider').slick({
			 
			 respondTo : 'slider',
			 slidesToShow : 4,
			 adaptiveHeight : true,
			 initialSlide : <? if(isset($current_pick)){ echo $current_pick-1; } else { echo 0; } ?>,
			 infinite: true,
			 responsive: [
				{
				  breakpoint: 825,
				  settings: {
					slidesToShow: 3,
					slidesToScroll: 1,
					
					
				  }
				},
				{
				  breakpoint: 675,
				  settings: {
					
				  }
				},
				
			]
		});	
		 $('.single-slider').slick({
			 respondTo : 'slider',
			 slidesToShow : 1,
			 centerMode:true,
			 adaptiveHeight : true,
			 initialSlide : <? if(isset($current_pick)){ echo $current_pick; } else { echo 0; } ?>,
			 slidesToScroll: 1,
			 infinite: true
			});
			
		
		
			//begin add player to list and sort functions
			
			 $(".add_selection_button").on("click",function(){
				
				var id=$(this).attr('data-id');
				
				$.ajax({
						url: "<? echo base_url(); ?>Draft_Live/add_selection/"+$(this).attr('data-id')+"/"+"<? echo $draft_id; ?>",
						type: "POST",
						success: function(result) {
							$("#submit_pick_button").attr('data-status',0);	
							$("#submit_pick_button").text('Submit Pick').addClass('btn-primary').removeClass('btn-danger');;
							
							var data = $.parseJSON(result);
							
							//hide the one being moved
							$("#available_"+data.id).hide();
							//remove the previous your list
								//get the id of the current on
								var remove_id = $("#sortable li").attr('data-id');
								
							$("#list_"+remove_id).remove();
							//unhide the previous pick from available list
							$("#available_"+remove_id).show();
							//add to your list
							$("#sortable").append($('<li data-id="' + data.id + '" id="list_'+data.id+'" class="text-left row" style="padding:0px;margin:0px;"><div class="text-left vertical_center col-xs-24"  style="margin-top:3px;" ><small><div style="float:left"><span class="glyphicon glyphicon-remove-sign red_font remove_selection_button pointer" data-id="'+data.id+'" id="remove_'+data.id+'" aria-hidden="true" style="display:inline" ></span></div><section><small><div class="glyphicon glyphicon-menu-hamburger" aria-hidden="true" style="display:inline; color:#aaa;"></div></small><strong>' + $("#available_name_"+data.id).text() + '</strong></section></small></div></li>'));
							
							
						}
					});
				
			});                                            
			
			//remove player from draft list
			$("#sortable").on("click",".remove_selection_button",function(){
				
				var id=$(this).attr('data-id');
				$.ajax({
						url: "<? echo base_url(); ?>Draft_Live/remove_selection/"+$(this).attr('data-id')+"/"+"<? echo $draft_id; ?>",
						type: "POST",
						success: function(result) {
							var data = $.parseJSON(result);
							
							//remove the one being moved
							$("#list_"+data.id).remove();
							//add to your list
							$("#available_"+data.id).show();
                          
                           
						}
					});
				
				
			});
			
			$('#sortable').sortable({
				axis: 'y',
				opacity: 0.7,
				handle: 'section',
				update: function(event, ui) {
					
					var list_sortable = $(this).sortable('toArray').toString();
					// change order in the database using Ajax
					$.ajax({
						url: "<? echo base_url(); ?>Draft_Live/update_selections/<? echo $team_id; ?>/<? echo $draft_id; ?>",
						type: 'POST',
						data: {list_order:list_sortable},
						success: function(data) {
							
							
						}
					});
				}
			}); // fin sortable
			
			
			
			
			
		//submit pick button
		<?// if($autodraft==2){ ?>
			//$("#submit_pick_button").toggle('fade');
			//$("#autodraft").prop('checked', true);
		<?// } 
		//else { ?>
			$("#submit_pick_button").on("click",function(){
				if($("#submit_pick_button").attr('data-status')==0){
					var autodraft = 1;
					$("#submit_pick_button").attr('data-status',1);	
					$("#submit_pick_button").text('Cancel Pick').addClass('btn-danger').removeClass('btn-primary');
					
					
				}
				else{
					var autodraft = 0;
					$("#submit_pick_button").attr('data-status',0);	
					$("#submit_pick_button").text('Submit Pick').addClass('btn-primary').removeClass('btn-danger');
					
				}
				
				$.ajax({
						url: "<? echo base_url(); ?>Draft_Live/update_autodraft/<? echo $team_id; ?>/<? echo $draft_id; ?>/" + autodraft,
						type: 'POST',
						success: function(data) {
							
						}
					});	
			});
		<? //} ?>
		  
		 //on off switch for autodraft
		options = { show_labels: true };
		$("#autodraft").switchButton(options);
		
		$("#autodraft").change(function(){
			if($("#autodraft").prop('checked')){
				var autodraft = 2;	
				$("#submit_pick_button").off("click");
				$("#submit_pick_button").toggleClass('fade');
			}
			else{
				var autodraft =0;
				$("#submit_pick_button").toggleClass('fade');
              $("#submit_pick_button").on("click",function(){
				if($("#submit_pick_button").attr('data-status')==0){
					var autodraft = 1;
					$("#submit_pick_button").attr('data-status',1);	
					$("#submit_pick_button").text('Cancel Pick').removeClass('btn-primary').addClass('btn-danger');
				}
				else{
					var autodraft = 0;
					$("#submit_pick_button").attr('data-status',0);	
					$("#submit_pick_button").text('Submit Pick').addClass('btn-primary').removeClass('btn-danger');
				}
				
				$.ajax({
						url: "<? echo base_url(); ?>Draft_Live/update_autodraft/<? echo $team_id; ?>/<? echo $draft_id; ?>/" + autodraft,
						type: 'POST',
						success: function(data) {
							
						}
					});	
			});
				
			}
			$.ajax({
				url: "<? echo base_url(); ?>Draft_Live/update_autodraft/<? echo $team_id; ?>/<? echo $draft_id; ?>/" + autodraft,
				type: 'POST',
				success: function(data) {
					
				}
			});	
			
		});
		
		//start timer
		window.pause = <? echo $pause; ?>;
		window.remaining_time = <? echo $remaining_time; ?>;
		window.notification=0;
		
		});
		
		function filter(position){
			$("."+position).toggle();
			if($("#inactive_filter").attr('data-inactives')==0){
				$(".inactive").hide();	
			}
						
		}
		
		function filter_inactive(current_state){
			if(current_state==0){
				$(".inactive").show();
				$.each(['QB','RB','WR','TE','K'], function(index, value){
					if($("#"+value+"_filter").hasClass('fade')){
						$("."+value).hide();	
					}
				});
				$("#inactive_filter").attr('data-inactives',1);
			}
			else{
				$(".inactive").hide();
				$("#inactive_filter").attr('data-inactives',0);
			}
		}
		filter('inactive');
		
		
	</script>
    <!--load audio-->
     <audio class="audio-update" preload="auto"> 
       <source src="<? echo base_url(); ?>assets/audio/update.mp3" type="audio/mpeg">
    </audio>
    <audio class="audio-notification" preload="auto"> 
       <source src="<? echo base_url(); ?>assets/audio/notification.mp3" type="audio/mpeg">
    </audio>
    
    <div id="alerts" class="alert alert-danger" role="alert" style="display:none"></div>
    <div data-status=<? echo $status; ?> id="draft_status"></div>
    <div ><h3><strong><span id="latest_pick"><? if($pause==0) { echo $year.' FFFL Drafts'; } else { echo "Please Wait. We'll Continue Shortly."; } ?></span></strong></h3></div>
   <div id="test_area"></div>
   	<div class="row"><!--results slider-->
    	<div class="col-xs-24" >
            <div id="slider_container">
            	<!--single slider-->
                <div class="single-slider hidden-md hidden-sm hidden-lg col-xs-24" >
                	<div class="slide " id="" style="margin:2px;height:50px;">
                    	<div class=" row-eq-height" >
                        	
                            <div class="ellipses text-center col-xs-24 vertical_center " style="font-weight:bold"><strong><? echo $year; ?> FFFL Drafts</strong></div>
                        </div>
                    </div>
                    <? foreach($draft_picks as $pick_number => $pick_data){ ?>
                        <div class="slide"  style="margin:2px;height:50px;">
                        	<? if($pick_data['fffl_player_id']>0) { $style = 'border-color:#223a73;'; $on_the_clock=""; }
							elseif($current_pick ==$pick_number){ $style = 'border-color:#b01f24;'; $on_the_clock="On The Clock";} else { $style='border-color:#ddd'; $on_the_clock='&nbsp;';} ?>
                            <div class="draft_pick row-eq-height" id="single_pick_<? echo $pick_number; ?>" style=" <? echo $style; ?> ">
                                <div class="ellipses text-center col-xs-24  " style="font-weight:bold">
                                    <small><span id="single_pick_<? echo $pick_number; ?>_team"><? echo $pick_number.'. '.team_name_no_link($pick_data['team_id'],false); ?></span></small>
                                    <br><small> 
                                    <span id="single_pick_<? echo $pick_number; ?>_player">
                                    
									<? if($pick_data['fffl_player_id']>0){
                                                    echo player_name_no_link($pick_data['fffl_player_id']); 
                                                }
                                                elseif($pick_data['fffl_player_id']==-1){
                                                    echo 'Time Expired';	
                                                }
                                                else{
                                                    echo $on_the_clock;
                                                }
                                    ?>
                                    </span></small>
                                </div>
							</div>
                        </div>
                    <? } ?>
				</div>
                <div class="slider hidden-xs col-md-24" >
                	<div class="slide " id="" style="margin:2px;height:50px;">
                    	<div class=" row-eq-height" >
                        	<div class="hidden-xs col-md-7 vertical_center text-right"><img src="<? echo base_url(); ?>assets/img/logos/fffl_logo.gif" width="31px" height="40px"></div>
                            <div class="ellipses text-left col-xs-24 col-md-17 vertical_center " style="font-weight:bold"><strong><? echo $year; ?> FFFL Drafts</strong></div>
                        </div>
                    </div>
                    <? foreach($draft_picks as $pick_number => $pick_data){ ?>
                        <div class="slide"  style="margin:2px;height:50px;">
                        	<? if($pick_data['fffl_player_id']>0) { $style = 'border-color:#223a73;'; $on_the_clock=""; }
							elseif($current_pick ==$pick_number){ $style = 'border-color:#b01f24;'; $on_the_clock="On The Clock";} else { $style='border-color:#ddd'; $on_the_clock='&nbsp;';} ?>
                            <div class="draft_pick row-eq-height" id="pick_<? echo $pick_number; ?>" style=" <? echo $style; ?> ">
                                <div class="hidden-xs col-md-7 vertical_center"><img style="max-width:40px;max-height:40px;" src="<? echo $pick_data['logo_path']; ?>"  ></div>
                                <div class="ellipses text-center col-xs-24 col-md-17 " style="font-weight:bold">
                                    <small><span id="pick_<? echo $pick_number; ?>_team"><? echo $pick_number.'. '.team_name_no_link($pick_data['team_id'],false); ?></span></small>
                                    <br><small> 
                                    <span id="pick_<? echo $pick_number; ?>_player">
                                    
									<? if($pick_data['fffl_player_id']>0){
                                                    echo player_name_no_link($pick_data['fffl_player_id']); 
                                                }
                                                elseif($pick_data['fffl_player_id']==-1){
                                                    echo 'Time Expired';	
                                                }
                                                else{
                                                    echo $on_the_clock;
                                                }
                                    ?>
                                    </span></small>
                                </div>
							</div>
                        </div>
                    <? } ?>
				</div>
			</div>
        </div>
    </div><!-- end results slider row-->
    
    <div class="row">
		
        <div class="col-xs-24 col-md-12"><!-- current timer -->
        	<? if (isset($draft_picks[$current_pick]) && $team_id==$draft_picks[$current_pick]['team_id']){ $panel='red'; } else { $panel='blue'; } ?>
        	<div id="current_pick_primary" class="panel  panel-primary <? echo $panel; ?>_panel-primary " >
                <div id="current_pick_heading" class="panel-heading current_pick <? echo $panel; ?>_panel-heading">
                    <h4 id="current_pick_title" class="panel-title current_pick <? echo $panel; ?>_panel-title text-center"><small>On the Clock</small></h4>
                </div>
                <div class="panel-body" >
                	<div class="row row-eq-height">
                    	<? if($status!=0 && $status!=3){ ?>
                            <div class="col-xs-7 text-center vertical_center" id="countdown"></div>
                            <? $hidden = ''; ?>
                        <? } 
						else{ ?>
							<div class="col-xs-24 text-center vertical_center">
                                <h2 style="margin:3px;" id="countdown"></h2>
                            </div>
                            <? $hidden='display:none'; ?>
						<? } ?>
                        <div class="col-xs-6 text-center vertical_center" style=" <? echo $hidden; ?>; margin-top:3px; " id="current_logo">
                            <img  src="<? if(isset($draft_picks[$current_pick])){ echo $draft_picks[$current_pick]['logo_path']; } ?>"  class="img-responsive">
                        </div>
                        <div id="current_pick_display" class="col-xs-11 text-center vertical_center ellipses" style=" <? echo $hidden; ?>">
                            <h5 style="margin:3px;"><strong>
                                <span id="current_pick_team"><? echo team_name_link($draft_picks[$current_pick]['team_id']); ?></span><br>
                                Rd <span id="round_number"><? echo $draft_picks[$current_pick]['round']; ?></span> #<span id="pick_number"><? echo $current_pick; ?></span>
                                </strong>
                            </h5>
                        
                        </div>
                    </div>
                </div>
            </div>
        </div><!--end current timer panel-->
        
        <div class="col-xs-24 col-md-12" style="float:right"><!--player selection area -->
        	<div class="panel panel-primary blue_panel-primary " >
                <div class="panel-heading blue_panel-heading">
                    <h4 class="panel-title blue_panel-title text-center"><small>Make a Selection</small></h4>
                </div>
                <div class="panel-body" >
                	<div class="row">
                    <div class="col-xs-12 col-md-12" nowrap style="text-wrap:none">
                    	<!--<div style="display:inline" class="text-center"><small><strong>Autodraft: </strong></small></div>
                    	<div class="switch-wrapper" nowrap style="text-wrap:none">
                          <input type="checkbox" id="autodraft" name="autodraft" value="0" >
                        </div>-->
                    </div>
                    <div style="margin-bottom:5px;" class="col-xs-12 col-md-12">
                        <button type="button" data-status="<? echo $autodraft; ?>" class="btn btn-primary btn-sm " id="submit_pick_button">
                          Submit Pick
                        </button>
                    </div>
                	<table class="table table-condensed " id="available_players_table">
                    	<tr>
                        	<td class="col-xs-12" style="padding:0px">
                            	<div class="text-center">Available Players</div>
                                <? $positions = array('QB','RB','WR','TE','K');
								foreach($positions as $position){ ?>
									<button type="button" class="btn btn-primary btn-sm" id="<? echo $position; ?>_filter" onClick="filter('<? echo $position; ?>'); $(this).toggleClass('fade');" style="margin-bottom:3px;">
									  <? echo $position; ?>
									</button>
								<? } ?>
                                <button type="button" data-inactives=0 class="fade btn btn-primary btn-sm" id="inactive_filter" onClick="filter_inactive($(this).attr('data-inactives')); $(this).toggleClass('fade');" style="margin-bottom:3px;">
									  Inactives
									</button>
                                <div>
                                	<? 
										
										
										//$data = array(
											//	'id' => 'name',
											//	'value' => '',
											//	'class' => 'filters form-control',
											//	'style' => 'width:100%'
												
									//	);
												
												
										//echo form_input($data);
									?>
                                </div>
                                
                            	<div  id="available_players_table" style='overflow:auto; max-height:400px'>
                					 
									<?
                                  
                                    foreach($draftable as $fffl_player_id=>$position){
										
										if(in_array($fffl_player_id,$team_draft_list)){ $hide="display:none"; } else { $hide='';}
                                        ?>
                                        <div class='<? echo $position; ?>' id="available_<? echo $fffl_player_id; ?>" style=" <? echo $hide; ?>"> 
                                            <div class="text-left col-xs-24 " style="margin-top:3px;">
                                                <small>
                                            	<div style="float:left" class="pointer" >
                                                	<span  class="glyphicon glyphicon-plus-sign blue_font add_selection_button" id="add_<? echo $fffl_player_id; ?>" aria-hidden="true" data-id="<? echo $fffl_player_id; ?>" ></span> &nbsp;
                                                </div>
                                        		<div class="text-left "  id="available_name_<? echo $fffl_player_id; ?>" style="padding:0px"><strong><? echo player_name_no_link($fffl_player_id); ?></strong></div></small>
                                            </div>
                                  		</div>

                                    <?
                                    }
                                    
                                    ?>
                                
                                	
                                </div>
                            </td>
                            <td class="col-xs-12" style="padding:0px">
                            	<div class="text-center">Your Next Pick</div>
                            	<ul id="sortable" class="table table-striped table-condensed table-hover" style="padding:0px;overflow:auto; max-height:400px">
                        	
									<?php
                                    
                                    
                                    foreach ($team_draft_list as $fffl_player_id) {
                                        
                                    ?> 
                                        <li id="list_<?php echo $fffl_player_id; ?>" data-id="<? echo $fffl_player_id; ?>" class="text-left row" style="padding:0px;margin:0px;">
                                            
                                            <div class="text-left vertical_center col-xs-24 "  style="margin-top:3px;" >
                                            	<small>
                                                	<div style="float:left" class="pointer">
                                            			<span class="glyphicon glyphicon-remove-sign red_font remove_selection_button " id="remove_<? echo $fffl_player_id; ?>" aria-hidden="true" data-id="<? echo $fffl_player_id; ?>" style=" display:inline;" ></span>
                                                    </div>
                                                    
													<section>
                                    					<small><div class="glyphicon glyphicon-menu-hamburger" aria-hidden="true" style="display:inline; color:#aaa;"></div></small>
                                            			<strong><? echo player_name_no_link($fffl_player_id); ?></strong>
                                                	</section>
                                                </small>
                                            </div>
                                                
                                            
                                        </li>
                                    <?php
                                        
                                    }
                                   ?>
                                  
                                </ul>
                            </td>
                        </tr>
                    
                    </table>
                    </div>
                </div>
            </div>
        </div><!--end player selection area-->

        <div class="col-xs-24 col-md-12"><!-- chat -->
        	<div class="panel panel-primary blue_panel-primary " >
                <div class="panel-heading blue_panel-heading">
                    <h4 class="panel-title blue_panel-title text-center"><small>Chat</small></h4>
                </div>
                <div class="panel-body" >
                	<input class="create-comment" placeholder="Add a Comment" style="display:table-cell; width:80%">
                    <button class="submit-comment"><span class="glyphicon glyphicon-send" aria-hidden="true"></span></button>
                    <div class="chat_area text-left" style="border-top:1px #ddd solid; margin-top:10px;height:250px;overflow:auto">
                    <? foreach($chat_array as $message_data){
						echo '<small><strong>'.$message_data['chat_team'].':</strong> '.$message_data['message'].'</small><br>';
						
					}?>
                    </div>
                </div>
            </div>
        </div><!--end current timer panel-->
      <? if($security_level==3) { ?>
      	<script>
			

          //every 20 sec ajax to controller to check if draft is started and then check for a pick by current picker and then check if timer has expired
			function update_check() {
				$.ajax({
					url: "<? echo base_url(); ?>Draft_Live/pick_check/<? echo $draft_id; ?>",
					type: 'POST',
					success: function(data) {
						window.last_check = data;
						
					}
				});
				setTimeout(update_check,20000);
			}
			setTimeout(update_check,1000);
          	
          	function last_check_timer(){
				difference = Math.floor(($.now()/1000))-window.last_check;
				$("#time_since").text(difference);
				setTimeout(last_check_timer,2000);
			}
			setTimeout(last_check_timer,1000);
          //bind pause button to ajax pause the draft
          
          function pause_draft(){
				var status = $("#pause").attr('data-pause_status');
				$.ajax({
						url: "<? echo base_url(); ?>Draft_Live/pause_draft/<? echo $draft_id; ?>/" + status,
						type: 'POST',
						success: function(data) {
							if(status==0){//unpause
								$("#pause").text('Pause');
								$("#pause").attr('data-pause_status',1);
							}
							else {
								$("#pause").text('Resume');	
								$("#pause").attr('data-pause_status',0);
							}
						}
					});	
				
		  }
		  
		  function start_end_draft(status){
				
				$.ajax({
						url: "<? echo base_url(); ?>Draft_Live/start_end_draft/<? echo $draft_id; ?>/" + status,
						type: 'POST',
						success: function() {
							
						}
					});	
				
		  }
		  
		  $(".click_name").on("click",function() {
				window.open('<? echo base_url(); ?>Draft_Live/load_absentee_pick/<? echo $draft_id; ?>/'+$(this).attr('id'));
  				return false;
				
			});
          
      	</script>
        <div class="col-xs-24"><!-- commisioner control -->
        	<div class="panel panel-primary blue_panel-primary " >
                <div class="panel-heading blue_panel-heading">
                    <h4 class="panel-title blue_panel-title text-center"><small>Commisioner Control</small></h4>
                </div>
                <div class="panel-body" >
                	<div class="col-xs-8">
             			Time since last check: <span id="time_since"></span>s
                    </div>
                    <div class="col-xs-8">
             			Pause Draft: <button type="button" class="btn btn-primary btn-sm" data-pause_status=<? if($pause==1){ echo 0; } else { echo 1; } ?> id="pause" onClick="pause_draft();">
									  <? if($pause==1){ echo 'Resume'; } else { echo 'Pause'; } ?>
									</button>
                                    <br>
                                   <button type="button" class="btn btn-primary btn-sm" id="start" onClick="start_end_draft(1);">
									  Start Draft
									</button>&nbsp;&nbsp;
                                    <button type="button" class="btn btn-primary btn-sm" id="end" onClick="start_end_draft(3);">
									  End Draft
									</button>
                    </div>
                    <div class="col-xs-8">
             			<div id="" class="dropdown" style="margin:5px;" >
                            <div class="btn-group dropup">
                                <button class="btn btn-default dropdown-toggle" type="button" id="team_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <span id="dropdown_teams">Select Team</span>
                                    <span class="caret"></span>
                                </button>
                                
                                <ul class="dropdown-menu"  aria-labelledby="dropdownMenu1" >
                                   <? 
                                       foreach($all_teams_id_name as $team_id_ ){//team_id is already used for the viewer; added _ to differentiate
                                            echo '<li><a href="#" id="'.$team_id_.'" class="click_name"><small>'.team_name_no_link($team_id_).'</small></a></li>';   
                                           
                                       }
                                    ?>
                                </ul>
                            </div>      
                        </div>
                    </div>
                </div>
            </div>
        </div><!--end commissioner control panel-->
      <? } ?>
        
    </div>
 	<script>
		
	 var pusher = new Pusher('524300fc2161d2996a58');
	 
	 //New pick pusher

	var pick_made = pusher.subscribe('pick_made');
	
	pick_made.bind('new_pick', function(pick_data){
		var player_id = pick_data.player_id;
		var pick_team_id = pick_data.pick_team_id;
		var pick_number = pick_data.pick_number;
		var current_pick = pick_data.current_pick;
		var player = pick_data.player_name;
		var current_logo = pick_data.current_logo;
		var timer_expiration = pick_data.timer_expiration;
		var round_number = pick_data.round_number;
		var current_pick_team = pick_data.current_pick_team;
		var current_pick_team_id = pick_data.current_pick_team_id;
		var makeup_picks_array = pick_data.makeup_picks;
			//update autodtraft if viewing team made pick
			
			if(pick_team_id == <? echo $team_id; ?>){
				
					$("#submit_pick_button").attr('data-status',0);	
					$("#submit_pick_button").text('Submit Pick').addClass('btn-primary').removeClass('btn-danger');;
				
				
				
			}
			
			//play audio
			
			$(".audio-update").trigger('play');
			
			//remove from lists
			if(player_id>-1){
				$('#available_'+player_id).remove();
				$('#list_'+player_id).remove();
			}
			
			//add alert if the team has a make up pck
			var has_alerts=0;
			$.each(makeup_picks_array,function(index,value){
				if(value==<? echo $team_id; ?>) { $("#alerts").text('You have a passed pick. Submit a pick to make the selection.'); $("#alerts").slideDown(1500); has_alerts=1; return false; }
				
			});
			if(has_alerts==0 && $("#alerts").is(':visible')) { $("#alerts").slideToggle(1500); }
			
			// Manually refresh positioning of slick
			$('.slider').slick('slickGoTo',(current_pick-1),1);
			$('.single-slider').slick('slickGoTo',(current_pick),1);
			$("#pick_"+pick_number+"_player").text(player);
			$("#psingle_pick_"+pick_number+"_player").text(player);
			if(player != 'Time Expired'){
				$("#pick_"+pick_number).css("border-color",'#223a73');
				$("#single_pick_"+pick_number).css("border-color",'#223a73');
			}
			else{
				$("#pick_"+pick_number).css("border-color",'#ddd');
				$("#single_pick_"+pick_number).css("border-color",'#ddd');
			}
			
			
			if(current_pick_team_id == <? echo $team_id; ?>){
				//play audio
				window.notification = 1;
				$(".audio-update").on('ended', function(){
					$(".audio-notification").delay(2000).trigger('play').delay(3000).queue(function(){ 
						$(".audio-update").off('ended'); 
					});
				});
				
				$("#current_pick_heading").addClass('red_panel-heading');
				$("#current_pick_title").addClass('red_panel-title');
				$("#current_pick_primary").addClass('red_panel-primary');
				$("#current_pick_heading").removeClass('blue_panel-heading');
				$("#current_pick_title").removeClass('blue_panel-title');
				$("#current_pick_primary").removeClass('blue_panel-primary');
			}
			else{
				window.notification = 0;
				$("#current_pick_heading").addClass('blue_panel-heading');
				$("#current_pick_title").addClass('blue_panel-title');
				$("#current_pick_primary").addClass('blue_panel-primary');
				$("#current_pick_heading").removeClass('red_panel-heading');
				$("#current_pick_title").removeClass('red_panel-title');
				$("#current_pick_primary").removeClass('red_panel-primary');
			}
			$("#pick_"+current_pick).css("border-color",'#b01f24');
			$("#single_pick_"+current_pick).css("border-color",'#b01f24');
			difference = timer_expiration - Math.floor(($.now()/1000));
			window.remaining_time = difference;
			$('#current_logo').children().attr('src',current_logo);
			$('#pick_number').text(current_pick);
			$('#round_number').text(round_number);
			$('#current_pick_team').text(current_pick_team);
			$("#latest_pick").text($("#pick_"+pick_number+"_team").text()+" - "+$("#pick_"+pick_number+"_player").text()).fadeIn(1000).delay(200).fadeOut(700).fadeIn(700).delay(200).fadeOut(700).fadeIn(700).delay(200).fadeOut(700).fadeIn(700).fadeOut(700).fadeOut(700).fadeIn(700).fadeOut(700).fadeIn(700).fadeIn(700).delay(7000).fadeOut(200,function(){$("#latest_pick").text('<? echo$year; ?> FFFL Drafts').fadeIn(700);});
			//$("#test_area").text(pick_number);
	});
	
	
	 //pause pusher
	
	var pause = pusher.subscribe('pauser');
	
	pause.bind('pause', function(pause_data){
		
		var message = pause_data.message;
		var pause_status = pause_data.pause_status;
		
		if(pause_status==0){//add the time back
			$("#countdown").css('color','black');
			$("#latest_pick").text(message).delay(7000).fadeOut(700,function(){$("#latest_pick").text('<? echo$year; ?> FFFL Drafts').fadeIn(700);});
			var timer_expiration = pause_data.timer_expiration;
			difference = timer_expiration - Math.floor(($.now()/1000));
			window.remaining_time = difference;
		} 
		else{
			$("#latest_pick").text(message);
			$("#countdown").css('color','white');
			
		}
	});
	
	 //start end pusher
	
	var start_end = pusher.subscribe('start_end');
	
	start_end.bind('start_end', function(status_data){
		
		var message = status_data.message;
		var timer_expiration = status_data.timer_expiration;
		var status = status_data.status;
		
		if(status==1){//start the draft
			
			$("#latest_pick").text(message).delay(7000).fadeOut(700,function(){$("#latest_pick").text('<? echo $year; ?> FFFL Drafts').fadeIn(700);});
			$("#draft_status").attr('data-status',1);
			difference = timer_expiration - Math.floor(($.now()/1000));
			window.remaining_time = difference;
			
			$('#current_logo').show();
			$('#current_pick_display').show();
			
			
		} 
		else{//end the draft
			$("#draft_status").attr('data-status',3);
			$("#latest_pick").text(message);
			window.remaining_time = 0;
			
			
		}
		change_content('<? echo base_url(); ?>Draft_Live/live_draft/<? echo $draft_id; ?>/<? echo $team_id; ?>','');
	});
	
	
	 //Chat pusher
	$(".create-comment").keyup(function(event){
		if(event.keyCode == 13){
			$(".submit-comment").click();
		}
	});
	
	
	var chats = pusher.subscribe('chats');
	chats.bind('new_chat', function(chat){
		
		var chat_team = chat.chat_team;
		var message = chat.message;
		$("div.chat_area").html("<small><strong>" + chat_team + ":</strong> " + message + "</small><br>" + $("div.chat_area").html());
	});
	
	var sendChat = function(){
		var text = $('input.create-comment').val();
		$.post('http://fantasy.thefffl.com/Draft_Live/chat_update/', {message: text}).success(function(){
			console.log('chat sent');
		});
		$('input.create-comment').val('');
	};
	
	$('button.submit-comment').on('click', sendChat);
	
	</script>



<!--<div id="drafts">
   <div class="row">
        <div class="text-center col-xs-24" id="test">
            
        </div>
	</div>
       
</div> <!--drafts-->    

</div>


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/