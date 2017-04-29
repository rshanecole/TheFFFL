<?PHP
	/**
	 * roster view.
	 *
	 * through ajax will display roster, schedule, drafts, franchise, and team history
	 */
	//d($this->_ci_cached_vars);
?>
	<script type="text/javascript">
		$(document).ready(function(){
    		$('[data-toggle="popover"]').popover(); 
		});
		
		//add a player to lineup
		$("#Bench_table .glyphicon-plus-sign").on("click",function(){
			$(this).parent().html('<? $image_properties = array('src' => base_url().'assets/img/loading.gif', 'height'=>'11'); echo img($image_properties); ?>');
			path = 	'<? echo base_url().'Team/add_player_starting_lineup/'.$team_id.'/'.$year.'/'.$week.'/'; ?>' + this.id;
			change_content(path,'Roster');
		});
		
		//remove a player from lineup
		$("#Starters_table .glyphicon-remove-sign").on("click",function(){
			$(this).parent().html('<? $image_properties = array('src' => base_url().'assets/img/loading.gif', 'height'=>'11'); echo img($image_properties); ?>');
			path = 	'<? echo base_url().'Team/remove_player_starting_lineup/'.$team_id.'/'.$year.'/'.$week.'/'; ?>' + this.id;
			change_content(path,'Roster');
		});
		
		//activate pup
		$("#activate_pup").on("click",function(){
			$(this).hide();
			$("#pup_confirm").show();
			$("#pup_confirm").html('Confirm');
			$("#pup_dismiss").show();
			$("#pup_dismiss").html('Dismiss');
		});
		
		$("#pup_dismiss").on("click",function(){
			$("#pup_confirm").hide();
			$("#activate_pup").show();
			$(this).hide();	
		});
		
		$("#pup_confirm").on("click",function(){
                        $('#pup_confirm').off("click");
			$(this).html('<? $image_properties = array('src' => base_url().'assets/img/loading.gif', 'height'=>'11'); echo img($image_properties); ?>');
			path = 	'<? echo base_url().'Team/activate_pup/'.$team_id.'/'; ?>' + $("#pup_confirm").closest("div").attr("id");
			change_content(path,'Roster');
			
		});
		
		//scoring popover
		$('.scoring_button').popover({
			html: true,
			trigger: 'click',
			placement: 'top',
          title: '' + '',
			
			content: function() {
				var id = $(this).attr('fffl_player_id');
				var year = $(this).attr('year');
			  return $.ajax({url: 'http://fantasy.thefffl.com/Player/scoring_info/'+id+'/'+year,
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
	//emd scoring popover
	</script>

<? 
	function create_roster_table($area,$data,$team_id,$open_positions=array(),$week,$league_weeks_on_pup,$franchise_players,$year) {

		//create the outer container of the table first
		?>

            <table class="table table-hover table-condensed" id="<? echo $area; ?>_table">
                <tbody > 
                    <tr class="row">
                        <td class="text-center  col-xs-12"><small></small></td>
                        <td class="text-center  col-xs-10"><small>Opp</small></td>
                        <td class="text-center  col-xs-1"><small>Sal</small></td>
                    </tr>
		<?
        
		//add rows for each individual player               
		foreach($data as $fffl_player_id => $data_array){
			//set alert styles for rows. danger for bye weeks and out. info for injuries other than P or Out.
			//if more are added be sure to prioritze with the most important first
			
			//franchise player turns blue
			if($team_id==$_SESSION['team_id'] && in_array($fffl_player_id,$franchise_players)) {
				$alert = 'info';	
			}
			//out, suspended, bye week turns red
			elseif($data_array['bye_week']==$week || in_array($data_array['nfl_injury_game_status'],array('OUT','SUSPENDED','IR','PUP'))){
				$alert = 'danger';
			}
			//prob, quest, or doubt turns yellow
			elseif(in_array($data_array['nfl_injury_game_status'],array('PROBABLE','QUESTIONABLE','DOUBTFUL','NFI')) ){
				$alert = 'warning';
				
			} 
			else {
				$alert='';
			}
			
			//get first letter of injry status
			if($data_array['nfl_injury_game_status']=='PUP'){$injury_letter='PUP';}
			elseif($data_array['nfl_injury_game_status']=='IR'){ $injury_letter='IR';}
			elseif($data_array['nfl_injury_game_status']=='NFI'){ $injury_letter='NFI';}
			else { $injury_letter = substr($data_array['nfl_injury_game_status'],0,1); }
			$injury_letter = '<sup><strong><span class="red_font">'.$injury_letter.'</span></strong></sup>';
			
			//what type of icon for moving in lineup
			if(in_array($area,array('Starters','Bench')) && $data_array['is_player_locked']==1 && $_SESSION['security_level']!=3){
				$rostering_icon = '<small><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></small>';
			}
			else if($area =='Starters' && ($team_id==$_SESSION['team_id'] || $_SESSION['security_level']==3)){
				$rostering_icon = '<span class="glyphicon glyphicon-remove-sign red_font" id="'.$fffl_player_id.'" aria-hidden="true" ></span>';
				
			} 
			else if($area=='Bench' && ($team_id==$_SESSION['team_id'] || $_SESSION['security_level']==3)) {
				//if the position of the player isn't an open one, you cant add it
				if(isset($open_positions) && in_array($data_array['position'],$open_positions)){
					$rostering_icon = '<span class="glyphicon glyphicon-plus-sign blue_font" id="'.$fffl_player_id.'" aria-hidden="true" ></span>';
				}
				else{
					//add a dummy just for spacing for viewer not owner of this team
					$rostering_icon='<span class="glyphicon glyphicon-ok-circle white_font " style="visibility:hidden" aria-hidden="true"></span>';
				}
			}
			else {
				//add a dummy just for spacing for pup and ps
				$rostering_icon='<span class="glyphicon glyphicon-ok-circle white_font " style="visibility:hidden" aria-hidden="true"></span>';
			}
			//get headline icon
			if(count($data_array['headlines'])>0) {
				//there's a story
				$headlines = ' <a href="#" 
								tabindex="0" 
								class="" 
								role="button" 
								data-toggle="popover" 
								data-container="#popover_container" 
								data-html="true" 
								data-trigger="focus" 
								title="'.$data_array['headlines']['0']['title'].' '.date('M d',$data_array['headlines']['0']['date']).'" 
								data-content="'.str_replace('"','&quot;',$data_array['headlines']['0']['description']).'...<a href=&quot;'.$data_array['headlines']['0']['link'].'&quot; target=&quot;_new&quot;>Read More</a>" 
								data-placement="top">
									<small><sup><span class="glyphicon glyphicon-file blue_font" aria-hidden="true"></span></sup></small>
								</a> ';
			} 
			else{
				$headlines='';	
			}
			
			//create the display table
			echo '<tr class="'.$alert.'" >';
				//the first column will be id of player id to pass to javascript to eliminate or add player in rostering
				echo '<td class="text-left col-xs-6 col-sm-3"  >';
					
						echo '<small >'.$rostering_icon.'</small> ';
				
					echo '<small>'.$data_array['position'].'</small>';
				echo '</td>';
				echo '<td class="text-left  col-xs-8 col-sm-9"><div class="row">';
					//create a separate display of name on top info on bottom for xs and sm
					echo '<div class="hidden-sm hidden-md hidden-lg ">';
						echo '<strong><small>'.player_name_link($fffl_player_id,FALSE,FALSE).$headlines.' </small>';
					echo '</div>';
					echo '<div class="hidden-sm hidden-md hidden-lg ">';
						echo '<small><a href="#" tabindex="0" class="scoring_button" role="button" fffl_player_id="'.$fffl_player_id.'" year="'.$year.'">'.$injury_letter.' '.number_format($data_array['score_average'],1).'</a> | '.$data_array['current_team'].' | '.$data_array['bye_week'].'</small>';
					echo '</div>';
					//createa a display of one line for md and up
					echo '<div class="hidden-xs ">';
						echo '<strong><small>'.player_name_link($fffl_player_id,FALSE,FALSE).$headlines.'</small></strong> <small><a href="#" tabindex="0" class="scoring_button" role="button" fffl_player_id="'.$fffl_player_id.'" year="'.$year.'">'.$injury_letter.' '.number_format($data_array['score_average'],1).'</a> | '.$data_array['current_team'].' | '.$data_array['bye_week'].'</small>';
					echo '</div>';
					if($area=='PUP') {
						echo '<div class="text-center" id="'.$fffl_player_id.'">';
						if($data_array['weeks_on_pup']>=$league_weeks_on_pup) {
							if($team_id==$_SESSION['team_id'] || $_SESSION['security_level']==3){
								echo '<span id="activate_pup" class="blue_font pointer" ><strong>Activate</strong></span><span id="pup_confirm" class="alert alert-warning pointer" role="alert" style="padding:1px 4px 1px 4px;display:none"></span>&nbsp;&nbsp;&nbsp;&nbsp;<span id="pup_dismiss" class="alert alert-danger pointer" role="alert" style="padding:1px 4px 1px 4px;display:none"></span>';
							}
							else{
								echo '<span class="blue_font"><strong><small>Available</small></strong></span>';
							}
						}
						else {
							$week_available = $league_weeks_on_pup-$data_array['weeks_on_pup'] + $week;
							echo '<span class="red_font"><strong><small>Available Week '.$week_available.'</small></strong></span>';
						}
					echo '</div>';
					}
				echo '</div></td>';
				echo '<td class="text-center col-xs-10">';
					echo '<small>'.$data_array['next_game_status'].'</small>';
				echo '</td>';
				echo '<td class="col-xs-1" >';
					echo '<small>'.$data_array['current_salary'].'</small>';
				echo '</td>';
				
			echo '</tr>';
			
		}//end foreach of each individual player's row
		
		//close table container
		?>
                </tbody>
            </table>
        
		<?
	}
?>


	<script type="text/javascript">
		
			jQuery(function() { 

				$("#release_modal_button").on("click",function(){
					var modal_release = "<!--release modal -->\
							 <!--data loaded into elements by the button that launches this on the actual release page-->\
							<div class='modal fade' id='release_modal' tabindex='-1' role='dialog' aria-labelledby='release_modal_Label'>\
							  <div class='modal-dialog' role='document'>\
								<div class='modal-content'>\
								  <div class='modal-header'>\
									<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>\
									<h4 class='modal-title' id='release_modal_Label'>Release Players</h4>\
								  </div>\
								  <div class='modal-body'><div id='test_area'></div>\
									<div class='text-center'>You must release <span id='number_to_release'></span>.</div>\
								  </div>\
								  <div class='col-xs-24' id='release_list'>\
								  </div>\
								  <div class='modal-footer'>\
								  </div>\
								</div>\
							  </div>\
							</div>";
					$('#modal_area').html(modal_release);
					//display the number of players over in number_to_release in modal
					$("#number_to_release").html('<? echo $number_to_release; if($number_to_release==1){ echo ' player'; } else { echo ' players'; }?>');
					//load the release_list
					var path = '<? echo base_url().'Team/load_release_list/'.$team_id.'/'.$number_to_release; ?>';
					$('#release_list').load(path);
					
				});
				
				$("#sub_modal_button").on("click",function(){
					var modal_sub = "<!--sub modal -->\
							 <!--data loaded into elements by the button that launches this on the actual sub page-->\
							<div class='modal fade' id='sub_modal' tabindex='-1' role='dialog' aria-labelledby='sub_modal_Label'>\
							  <div class='modal-dialog' role='document'>\
								<div class='modal-content'>\
								  <div class='modal-header'>\
									<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>\
									<h4 class='modal-title' id='sub_modal_Label'>Substitute Priority</h4>\
								  </div>\
								  <div class='modal-body'><div id='test_area'></div>\
									<div class='text-center'><small>Set your substitute order for all players, even those currently in your starting lineup.  If a player whose kickoff time is between 9 a.m. and 1 p.m. is ruled out for his game, the first player on this list who would meet the starting lineup requirements and is not already in the starting lineup, will replace the player.</small></div>\
								  </div>\
								  <div class='col-xs-24' id='sub_list'>\
								  </div>\
								  <div class='modal-footer'>\
								  </div>\
								</div>\
							  </div>\
							</div>";
					$('#modal_area').html(modal_sub);
					
					//load the sub_list
					var path = '<? echo base_url().'Team/load_sub_list/'.$team_id; ?>'
					$('#sub_list').load(path);
					
				});
		
		});

			
	</script>
	<div id="popover_container">
    
   <?  if($number_to_release>0){  ?>
    <div class="row text-center" style="margin-bottom:5px;">
    	 <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#release_modal" id="release_modal_button">
              Release Players
         </button>
    </div>
    <? }
	elseif($team_id==$_SESSION['team_id'] || $_SESSION['security_level']==3) { //wont' be able to adjust subs if players need to be released ?>
        <div class="row text-right" style="margin-bottom:5px;">
        	<div class="col-xs-24">
                 <a href="#" class="" data-toggle="modal" data-target="#sub_modal" id="sub_modal_button">
                      Set Substitutes
                 </a>
        	</div>
        </div>
    <? } ?>

    <div class="row " style="padding:0px; " >
    	<div id="starters" class="col-xs-24 col-md-12"  >
        	<div class="panel panel-primary blue_panel-primary" >
            	<div class="panel-heading blue_panel-heading">
                    <span class="panel-title blue_panel-title"><small><?php 
                        if($week<17 && $week>0)
                        {
                            echo $year.' Week '.$week.' '; 
                        }	
                        ?>Starters</small></strong>
                    </span>
                </div>
                <?
                    create_roster_table('Starters',$starters,$team_id,array(),$week,$league_weeks_on_pup,$franchise_players,$scores_year);
                ?>
            </div>
		</div>
        <div id="bench" class="col-xs-24 col-md-12"  >
        	<div class="panel panel-primary blue_panel-primary" >
                <div class="panel-heading blue_panel-heading">
                    <span class="panel-title blue_panel-title"><small>Bench</small></span>
                </div>
                <?
					if(isset($bench)){
                    	create_roster_table('Bench',$bench,$team_id,$open_positions,$week,$league_weeks_on_pup,$franchise_players,$scores_year);
					}
                ?>
			</div>
        </div>
        <div class="clearfix visible-md-block visible-lg-block"></div>
        <div id="pup" class="col-xs-24 col-md-12"  >
        	<div class="panel panel-primary blue_panel-primary" >
                <div class="panel-heading blue_panel-heading">
                    <span class="panel-title blue_panel-title"><small>Physically Unable to Perform (PUP)</small></span>
                </div>
                <script type="text/javascript">
			
			jQuery(function() { 
		
		
			
				$("#pup_modal_button").on("click",function(){
					var modal_pup = "<!--PUP modal -->\
						 <!--data loaded into elements by the button that launches this on the actual roster page-->\
						<div class='modal fade' id='pup_modal' tabindex='-1' role='dialog' aria-labelledby='pup_modal_Label'>\
						  <div class='modal-dialog' role='document'>\
							<div class='modal-content'>\
							  <div class='modal-header'>\
								<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>\
								<h4 class='modal-title' id='pup_modal_Label'>Select Player for PUP List</h4>\
							  </div>\
							  <div class='modal-body'><div id='test_area'></div>\
								Any player added to the pup list will remain there for <span id='pup_weeks'></span> weeks. Only injured players are eligible. Players who are suspended or have a non-football injury (NFI) are not eligible.\
							  </div>\
							  <div class='col-xs-24' id='pup_list'>\
							  </div>\
							  <div class='modal-footer'>\
							  </div>\
							</div>\
						  </div>\
						</div>";
					$('#modal_area').html(modal_pup);
					//display the league's minimum pup weeks in the instructions
					$("#pup_weeks").html('<? echo $league_weeks_on_pup; ?>');
					//load the pup eleigible players
					var path = '<? echo base_url().'Team/load_pup_list/'.$team_id; ?>';
					$('#pup_list').load(path);
					
				});
			
			});

		</script>
                <?
                    if(isset($inactives['PUP'])){
						create_roster_table('PUP',$inactives['PUP'],$team_id,array(),$week,$league_weeks_on_pup,$franchise_players,$scores_year);
					} 
					else {
						echo '<table class=" "  >
								<tbody > 
									<tr class="row">
										<td class="col-xs-24"></td>
									</tr>
										<tr>
											<td style="padding:10px"  class="col-xs-24 pointer blue_font"><div  data-toggle="modal" data-target="#pup_modal" id="pup_modal_button"><strong>Add Player to PUP</strong></button></td>
										</tr>
									</tbody>
								</table>
									
							';	
					}
				?>
                
			</div>
        </div>
        <div id="ps" class="col-xs-24 col-md-12"  >
            <div class="panel panel-primary blue_panel-primary" >
                <div class="panel-heading blue_panel-heading">
                    <span class="panel-title blue_panel-title"><small>Practice Squad (PS)</small></span>
                </div>
                <script type="text/javascript">
					
					jQuery(function() { });
				
				
				
					$("#ps_modal_button").on("click",function(){
						var modal_ps = "<!--PS modal -->\
								 <!--data loaded into elements by the button that launches this on the actual roster page-->\
								<div class='modal fade' id='ps_modal' tabindex='-1' role='dialog' aria-labelledby='ps_modal_Label'>\
								  <div class='modal-dialog' role='document'>\
									<div class='modal-content'>\
									  <div class='modal-header'>\
										<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>\
										<h4 class='modal-title' id='ps_modal_Label'>Select Player for PS List</h4>\
									  </div>\
									  <div class='modal-body'><div id='test_area'></div>\
										Any player added to the PS list will remain there for the remainder of the season. Only rookies are eligible.\
									  </div>\
									  <div class='col-xs-24' id='ps_list'>\
									  </div>\
									  <div class='modal-footer'>\
									  </div>\
									</div>\
								  </div>\
								</div>";
						$('#modal_area').html(modal_ps);
						//load the ps eleigible players
						var path = '<? echo base_url().'Team/load_ps_list/'.$team_id; ?>';
						$('#ps_list').load(path);
						
					});
		
				</script>
                <?
                    if(isset($inactives['PS'])){
						create_roster_table('PS',$inactives['PS'],$team_id,array(),$week,$league_weeks_on_pup,$franchise_players,$scores_year);
					} 
					elseif($ps_open==TRUE) {//***NI*** createa  means to check the actual ps deadline here
						echo '<table class=" "  >
								<tbody > 
									<tr class="row">
										<td class="col-xs-24"></td>
									</tr>
										<tr>
											<td style="padding:10px" class="col-xs-24 pointer blue_font"><div data-toggle="modal" data-target="#ps_modal" id="ps_modal_button"><strong>Add Player to PS</strong></div></td>
										</tr>
									</tbody>
								</table>
									
							';	
					}
                ?>
            </div>
        </div>
	</div>
    </div>


<?PHP
/*End of file roster.php*/
/*Location: ./application/veiws/Team/roster.php*/