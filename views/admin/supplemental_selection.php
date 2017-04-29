<?PHP
	/**
	 * supplemental selection view.
	 *
	 * through ajax will reorder supplemental selections
	 */
	//d($this->_ci_cached_vars);
	//$this->output->enable_profiler(TRUE);
?>
	<script type="text/javascript" src="<? echo base_url(); ?>assets/js/jquery-ui.min.js"></script>
    <script src="<? echo base_url(); ?>assets/js/jquery.ui.touch-punch.min.js"></script>
    <link rel="stylesheet" href="<? echo base_url(); ?>assets/css/jquery-ui.min.css" />
	<script type="text/javascript">
		//add a player to table
		
		$(document).ready(function(){
    		$('[data-toggle="popover"]').popover(); 
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
	//end scoring popover
		
		$(function() {
			$(".add_selection_button").on("click",function(){
				$(this).parent().html('<? $image_properties = array('src' => base_url().'assets/img/loading.gif', 'height'=>'11'); echo img($image_properties); ?>');
				change_content('<? echo base_url(); ?>Supplemental/add_selection/'+$(this).attr('id'),'');
				
			});
			
			$(".remove_selection_button").on("click",function(){
				change_content('<? echo base_url(); ?>Supplemental/remove_selection/'+$(this).parent().parent().attr('id'),'');
				$(this).parent().html('<div class="col-xs-1"><? $image_properties = array('src' => base_url().'assets/img/loading.gif', 'height'=>'11'); echo img($image_properties); ?></div>');
				
				
			});
			
			//sortable selections
			$('#sortable').sortable({
				axis: 'y',
				opacity: 0.7,
				handle: 'span',
				update: function(event, ui) {
					$(this).find(".priority").html('<? $image_properties = array('src' => base_url().'assets/img/loading.gif', 'height'=>'11'); echo img($image_properties); ?>');
					var list_sortable = $(this).sortable('toArray').toString();
					// change order in the database using Ajax
					$.ajax({
						url: "<?php echo base_url(); ?>Supplemental/update_selections/<? echo $team_id; ?>",
						type: 'POST',
						data: {list_order:list_sortable},
						success: function(data) {
							change_content('<? echo base_url(); ?>Supplemental/selections/','');
						}
					});
				}
			}); // fin sortable
			
			<? if($team_pick==$number_selections){ ?>
				$(".add_selection_button").hide();
				
			<? } ?>
		});
	</script>
		
        <div id="selections" class="col-xs-24 col-sm-12" >
        	<div class="panel panel-primary blue_panel-primary " >
            	<div class="panel-heading blue_panel-heading">
                    <h3 class="panel-title blue_panel-title">
						<strong>Selections</strong></h3><h5></h5>
                </div>
                
                        <ul id="sortable" class="table table-striped" style="padding-left:0px;">
                        	
							<?php
							
							$priority=0;
                            foreach ($current_selections as $fffl_player_id) {
								$priority++;
                            ?> 
                                <li id="<?php echo $fffl_player_id; ?>" class="text-left row" >
                                	
                                	<small><div class="col-xs-1 glyphicon glyphicon-remove-sign red_font remove_selection_button" style="display:inline;padding-top:4px;" aria-hidden="true" ></div></small>
                                    <div class="col-xs-1 priority" ><? echo $priority; ?></div>
                                    <div class="col-xs-16" >
                                    	<span>
                                    		<small><small><div class="glyphicon glyphicon-menu-hamburger" aria-hidden="true" style="display:inline; color:#aaa;"></div></small></small>
                                            <div style="font-family:Arial, Helvetica, sans-serif;display:inline"><small><strong>
                                            <? echo player_name_no_link($fffl_player_id); ?>
                                            </strong></small>
                                            </div>
                                        </span>
                                    </div>
                                    
                                        
                                    
                                </li>
                            <?php
								
                            }
							while(($team_pick-$priority)>0){
								?><div class="text-left"  style="padding-left:30px;"><? $priority++; echo $priority;  ?></div><?
							}
                            ?>
                        </ul>

               
            </div>
		</div>
    	<div id="available_players" class="col-xs-24 col-sm-12" >
        	<div class="panel panel-primary blue_panel-primary " >
            	<div class="panel-heading blue_panel-heading">
                    <h3 class="panel-title blue_panel-title">
						<strong>Available Players</strong></h3><h5></h5>
                </div>
                
                <table class="table table-hover table-condensed" id="available_players_table">
                <tbody > 
                    <?
					foreach($available_players as $data){
						$fffl_player_id = $data['fffl_player_id'];
						$salary = $data['salary'];
						//compile player data for view
						//franchise player turns blue
						
						//out, suspended, bye week turns red
						if(in_array($all_players[$fffl_player_id]['nfl_injury_game_status'],array('OUT','SUSPENDED','IR','PUP'))){
							$alert = 'danger';
						}
						//prob, quest, or doubt turns yellow
						elseif(in_array($all_players[$fffl_player_id]['nfl_injury_game_status'],array('PROBABLE','QUESTIONABLE','DOUBTFUL','NFI')) ){
							$alert = 'warning';
							
						} 
						else {
							$alert='';
						}
						
						//get first letter of injry status
						if($all_players[$fffl_player_id]['nfl_injury_game_status']=='PUP'){$injury_letter='PUP';}
						elseif($all_players[$fffl_player_id]['nfl_injury_game_status']=='IR'){ $injury_letter='IR';}
						elseif($all_players[$fffl_player_id]['nfl_injury_game_status']=='NFI'){ $injury_letter='NFI';}
						else { $injury_letter = substr($all_players[$fffl_player_id]['nfl_injury_game_status'],0,1); }
						$injury_letter = '<sup><strong><span class="red_font">'.$injury_letter.'</span></strong></sup>';
						//get headline icon
						if(count($all_players[$fffl_player_id]['headlines'])>0) {
							//there's a story
							$headlines = ' <a href="#" 
											tabindex="0" 
											class="" 
											role="button" 
											data-toggle="popover" 
											data-html="true" 
											data-trigger="focus" 
											title="'.$all_players[$fffl_player_id]['headlines']['0']['title'].' '.date('M d',$all_players[$fffl_player_id]['headlines']['0']['date']).'" 
											data-content="'.str_replace('"','&quot;',$all_players[$fffl_player_id]['headlines']['0']['description']).'...<a href=&quot;'.$all_players[$fffl_player_id]['headlines']['0']['link'].'&quot; target=&quot;_new&quot;>Read More</a>" 
											data-placement="top">
												<small><sup><span class="glyphicon glyphicon-file blue_font" aria-hidden="true"></span></sup></small>
											</a> ';
						} 
						else{
							$headlines='';	
						}
						
						?>
                            <tr class="row <? echo $alert; ?> ">
                                <td class="text-center vertical_center col-xs-2"><small>
                                
                                    <span class="glyphicon glyphicon-plus-sign blue_font add_selection_button" id="<? echo $fffl_player_id; ?>" aria-hidden="true" ></span>
                                
                                </small></td>
                                <td class="text-left  col-xs-22" ><strong><small><? echo player_name_link($fffl_player_id).' '.$headlines.'<a href="#" tabindex="0" class="scoring_button" role="button" fffl_player_id="'.$fffl_player_id.'" year="'.($year-1).'">'.$injury_letter.' '.$all_players[$fffl_player_id]['score_average'].' '.$salary.'</a>'; ?></small></strong></td>
                                
                            
                            </tr>	
						
					<?
					}
					
					?>
                
                </tbody>
               </table> 
               
            </div>
		</div>
        
        
     </div> 


       


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/