<?PHP
	/**
	 * scoreboard view.
	 *
	 * through ajax will display scores by week	 */
	 
	d($this->_ci_cached_vars);
	//$this->output->enable_profiler(TRUE);
?>
	<script type="text/javascript">
		$(document).ready(function(){
    		$('[data-toggle="popover"]').popover(); 
		});
		
		//stats popover
		$('.stats_button').popover({
			html: true,
			trigger: 'click',
			placement: 'top',
          	title: '' + '',
			container: 'body',
			
			content: function() {
				var id = $(this).attr('data-player');
				var year = <? echo $year; ?>;
				var week = <? echo $week; ?>;
			  return $.ajax({url: 'http://fantasy.thefffl.com/Player/stats_info/'+id+'/'+year+'/'+week,
							 dataType: 'html',
							 async: false}).responseText;
			}
		  }).click(function(e) {
			
			$(this).popover('toggle');
			//$("#open_player").attr("data-player",$(this).attr("data-player"));
			//$("#open_player").attr("data-team",$(this).attr("data-team"));
         	
        });
      
		$(document).on("click",function() {	
			$('body').on('click', function (e) {
				
				$('[data-original-title]').each(function () { 
					//the 'is' for buttons that trigger popups
					//the 'has' for icons within a button that triggers a popup
					if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
						$(this).popover('hide');
						if (!$(e.target).is("a")){
							//$("#open_player").attr("data-player",0);
							//$("#open_player").attr("data-team",0);
						}
					}
				});
			});
		});
		
	//emd stats popover
		
		
	</script>
    <style>
		.link_color{
			color:white;
		}
	</style>
    	<div class="row" >
            <div class="col-xs-24 col-sm-12 ">
                <div class=" bg-warning col-xs-8 " ><small><small>In Progress</small></small></div>
                
                <div class=" bg-info col-xs-8 " ><small><small>Possession</small></small></div>
                
                <div class=" bg-danger col-xs-8 " ><small><small>Redzone</small></small></div>
            </div>
        </div>
        <? 
		$align='left';
		
		//display the data for each game in a separate panel
		foreach($games_array as $game_data){  
			if($game_data['priority']==1) { $panel_color='red'; } else { $panel_color='blue'; } ?>
        	<div class="panel panel-primary <? echo $panel_color; ?>_panel-primary " >
            	<div class="panel-heading <? echo $panel_color; ?>_panel-heading">
                    <h4 class="panel-title <? echo $panel_color; ?>_panel-title">
						<div class="row">
                            <div class="col-xs-11 hidden-sm hidden-md hidden-lg text-right">
                            	<strong><? echo '<small><span class="link_color" id="'.$game_data['priority'].'_record_a">'.$game_data['record_a'].'</span></small><small>&nbsp;&nbsp;<span id="'.$game_data['priority'].'_team_a">'.team_name_link($game_data['opponent_a'],TRUE,FALSE).'</span>'; ?></small></strong>
                            </div>
                            <div class="col-sm-11 hidden-xs text-right">
                            	<strong><? echo '<small><span class="link_color" id="'.$game_data['priority'].'_record_a">'.$game_data['record_a'].'</span></small>&nbsp;&nbsp;<span id="'.$game_data['priority'].'_team_a">'.team_name_link($game_data['opponent_a'],TRUE,TRUE).'</span>'; ?></strong>
                            </div>
                            <div class="col-xs-2">|</div>
                            <div class="col-xs-11 hidden-sm hidden-md hidden-lg  text-left">
                            	<strong><small><span id="<? echo $game_data['priority'].'_team_b'; ?>"<? echo team_name_link($game_data['opponent_b'],TRUE,FALSE).'&nbsp;&nbsp;</small><small><span class="link_color" id="'.$game_data['priority'].'_record_b">'.$game_data['record_b'].'</span></small>'; ?></strong>
                            </div>
                            <div class="col-sm-11 hidden-xs  text-left">
                            	<strong><? echo '<span id="'.$game_data['priority'].'_team_b">'.team_name_link($game_data['opponent_b'],TRUE,TRUE).'</span>&nbsp;&nbsp;<small><span class="link_color" id="'.$game_data['priority'].'_record_b">'.$game_data['record_b'].'</span></small>'; ?></strong>
                            </div>
                    	</div>
					</h4>
                </div>
                
                <!-- a new row, each tema will ahve a column -->
               
                <div class="row ">
                	
                        <div class="visible-xs row">
                            <span class="pointer" id="<? echo $game_data['opponent_a']; ?>" onClick=" $('.<? echo $game_data['opponent_a']; ?>').toggleClass('hidden-xs'); $(this).toggle(); $(this).next().toggle(); "><small>Show Rosters</small></span>
                            <span class="pointer" onClick="$('.<? echo $game_data['opponent_a']; ?>').toggleClass('hidden-xs'); $(this).toggle();  $(this).prev().toggle(); " style="display:none"><small>Hide Rosters</small></span>
                        </div>
                    
                	<? 
					//make a table for each team's roster and socres
					foreach(array("a","b") as $letter){ 
					if($align=='right'){$align='left';}else{$align='right';}?>
					<div class="col-xs-24 col-sm-12">
                    	<!--team logo and scores -->
                            	<div class="text-center col-xs-24">
                            		<h2 style="display:inline"><strong>
									<? 
                                    if($letter=='b'){ echo ' <span id="'.$game_data['priority'].'_team_score_b">'.$game_data['opponent_b_score'].'</span> '; }
                                        
                                        $image_properties = array(
                                            'src' => $game_data['logo_'.$letter],
                                            'class' => 'img-responsive',
                                            
                                            'style' => 'display:inline;height:30px'
                                        );
                                    echo img($image_properties).'';
                                    
                                    if($letter=='a'){ echo ' <span id="'.$game_data['priority'].'_team_score_a">'.$game_data['opponent_a_score'].'</span>'; }
                                    ?>
                                    </strong></h2> 
                                </div>
                                
                                <table class="table table-hover table-condensed col-xs-24 hidden-xs <? echo $game_data['opponent_a']; ?>" id="">
                                    <tbody > 
                                        <?
										$tie_breaker=0;
                                        //for each player in the starting lineup
										$player_count = 1;
                                        foreach($game_data['starters_'.$letter] as $fffl_player_id=>$data){ 
											$tie_breaker=$tie_breaker+$data['decimal'];
											//d($data);
											if(is_array($data['team'])){
												$team = $data['team']['current_team'];	
											}
											else {
												$team = $data['team'];	
											}
											?>
                                        
                                            <tr class="row <? if(in_array($data['team'],$redzone)){ 
                                                                echo 'bg-danger'; 
                                                            }
                                                            elseif(in_array($data['team'],$possession)) { 
																
                                                                echo 'bg-info'; 
                                                            }
                                                            elseif(!is_null($data['score']) && strpos($data['status'],'Final')==FALSE) {
                                                                echo 'bg-warning'; 
                                                            }  ?> ">
                                                <td>
                                                    <div class="row">
                                                        <div class="text-left  col-xs-13 col-sm-13 ellipses "><small><span class="player_<? echo $player_count; ?>_name"><? echo player_name_link($fffl_player_id,TRUE,FALSE).'</span> <span class="player_'.$player_count.'_team">'.$team; ?></span> </small></div>
                                                        <div class="text-right  col-xs-11 col-sm-11"><small><small><span class="player_<? echo $player_count; ?>_status"><? 
														if(isset($data['status']['1']) && strpos($data['status'],'Final')==FALSE)
														{  
															$data['status']=explode(' ',$data['status'],2); 
															echo $data['status']['1']; 
														} 
														?></span></small></small> <strong><? if(!is_null($data['score'])){ ?><small>&nbsp;
														<a role="button" 
                                                            href="#" 
                                                            tabindex="0" 
                                                            class="stats_button" 
                                                           	id="<? echo $fffl_player_id; ?>_<? echo $game_data['opponent_'.$letter]; ?>" 
                                                            data-team="<? echo $game_data['opponent_'.$letter]; ?>" 
                                                            data-player="<? echo $fffl_player_id; ?>" 
                                                            >
														<span class="player_<? echo $player_count; ?>_score"><? echo $data['score'];  ?></span></a></small></strong><? } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <? $player_count++;
										 }//end starters ?>
											<tr class="row">
												<td class="text-right"><small>Tiebreaker: <strong><? echo number_format($tie_breaker,1); ?></strong></small></td>
											</tr>
                                            
                                            <tr class="row">
												<td class="text-center">
                                                	<span class="pointer" id="bench_<? echo $game_data['opponent_a']; ?>" onClick=" $('.bench_<? echo $game_data['opponent_a']; ?>').toggle(); $(this).toggle(); $(this).next().toggle(); "><small>View Bench</small></span>
                            						<span class="pointer" onClick="$('.bench_<? echo $game_data['opponent_a']; ?>').toggle(); $(this).toggle();  $(this).prev().toggle(); " style="display:none"><small>Hide Bench</small></span>
                            					</td>
											</tr>
                                            
                                        <? //bench players 
										foreach($game_data['bench_'.$letter] as $fffl_player_id=>$data){ 
											$tie_breaker=$tie_breaker+$data['decimal'];
											//d($data);
											if(is_array($data['team'])){
												$team = $data['team']['current_team'];	
											}
											else {
												$team = $data['team'];	
											}
											?>
                                        
                                            <tr style="display:none" class="bench_<? echo $game_data['opponent_a']; ?> row <? if(in_array($data['team'],$redzone)){ 
                                                                echo 'bg-danger'; 
                                                            }
                                                            elseif(in_array($data['team'],$possession)) { 
																
                                                                echo 'bg-info'; 
                                                            }
                                                            elseif(!is_null($data['score']) && strpos($data['status'],'Final')==FALSE) {
                                                                echo 'bg-warning'; 
                                                            }  ?> ">
                                                <td>
                                                    <div class="row">
                                                        <div class="text-left  col-xs-13 col-sm-13 ellipses "><small><? echo player_name_link($fffl_player_id,TRUE,FALSE).' '.$team; ?> </small></div>
                                                        <div class="text-right  col-xs-11 col-sm-11"><small><small><? 
														if(strpos($data['status'],'Final')==FALSE && isset($data['status']['1']))
														{ 
															$data['status']=explode(' ',$data['status'],2); 
															echo $data['status']['1']; 
														} 
														?></small></small> <strong><? if(!is_null($data['score'])){ ?><small>&nbsp;
														<a role="button" 
                                                            href="#" 
                                                            tabindex="0" 
                                                            class="stats_button" 
                                                           	id="<? echo $fffl_player_id; ?>_<? echo $game_data['opponent_'.$letter]; ?>" 
                                                            data-team="<? echo $game_data['opponent_'.$letter]; ?>" 
                                                            data-player="<? echo $fffl_player_id; ?>" 
                                                            >
														<? if(isset($data['score'])){ echo $data['score']; } ?></a></small></strong><? } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <? }//end bench ?>
                                        
                                    </tbody>
                                </table> 
                        	
                        <!--each team's column-->
                    </div>
                  	<? } //end the table for each team 
					?>
               </div><!--end teams row-->
               
            </div>
        <? } ?>
	
        
       

       


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/