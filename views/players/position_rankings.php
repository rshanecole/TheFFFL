<?PHP
	/**
	 * players search view.
	 *
	 * includes list of all players
	 */
		//d($this->_ci_cached_vars);
?>

	<script type="text/javascript">
	$(document).ready(function(){
    		$('[data-toggle="popover"]').popover(); 
		});
	
	$(".greater_than_20").hide();
	
	$(".load_btn").on("click",function() {
		$(this).parent().parent().find('.load_btn').toggle();
		$(this).parent().parent().find('.greater_than_20').toggle();
	});
			
	//scoring popover
		$('.scoring_button').popover({
			html: true,
			trigger: 'click',
			placement: 'top',
          title: '' + '',
			container: '#popover_container',
			content: function() {
				var id = $(this).attr('fffl_player_id');
				var year = $(this).attr('year');
			  return $.ajax({url: '<? echo base_url(); ?>Player/scoring_info/'+id+'/'+year,
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
	</script>

<?
 	function create_position_table($position, $rankings_array,$team_id,$year){ ?>
		<div class="col-xs-24 col-sm-12 col-md-8" id="<? echo $position; ?>_table">
             <div class="panel panel-primary blue_panel-primary" >
                <div class="panel-heading blue_panel-heading">
                    <h4 class="panel-title blue_panel-title text-center"><small><? echo $position; ?></small></h4>
                </div>
                <table class="table table-condensed table-striped" id="<? echo $position; ?>_table">
                    <thead class="" >
                        <th class="text-center" colspan="2"><small>Player</small></th><th class="text-center" ><small>Average</small></th>
                    </thead>
                    <tbody>
                        <? 
						$count=0;
                        foreach($rankings_array as $rank => $data){
							
							if($data['average']>0){
								//get the headline data
								if(count($data['headlines'])>0) {
									//there's a story
									$headlines = ' <a href="#" 
													tabindex="0" 
													class="" 
													role="button" 
													data-toggle="popover" 
													data-html="true" 
													data-trigger="focus" 
													title="'.$data['headlines']['0']['title'].' '.date('M d',$data['headlines']['0']['date']).'" 
													data-content="'.str_replace('"','&quot;',$data['headlines']['0']['description']).'...<a href=&quot;'.$data['headlines']['0']['link'].'&quot; target=&quot;_new&quot;>Read More</a>" 
													data-placement="top">
														<small><sup><span class="glyphicon glyphicon-file blue_font" aria-hidden="true"></span></sup></small>
													</a> ';
								} 
								else{
									$headlines='';	
								}
								
								$count++;
								if($count<=20){ $hidden_class=""; } else {$hidden_class="greater_than_20";}
								//team players light up blue
								if(in_array($team_id,$data['fffl_teams'])){ $alert = 'info'; } 
								//out, suspended, bye week turns red
								elseif(in_array($data['injury']['nfl_injury_game_status'],array('OUT','SUSPENDED','IR','PUP'))){
									$alert = 'danger';
								}
								//prob, quest, or doubt turns yellow
								elseif(in_array($data['injury']['nfl_injury_game_status'],array('PROBABLE','QUESTIONABLE','DOUBTFUL','NFI')) ){
									$alert = 'warning';
									
								} 
								else {
									$alert='';
								}
								//get first letter of injry status
								if($data['injury']['nfl_injury_game_status']=='PUP'){$injury_letter='PUP';}
								elseif($data['injury']['nfl_injury_game_status']=='IR'){ $injury_letter='IR';}
								elseif($data['injury']['nfl_injury_game_status']=='NFI'){ $injury_letter='NFI';}
								else { $injury_letter = substr($data['injury']['nfl_injury_game_status'],0,1); }
								$injury_letter = '<sup><strong><span class="red_font">'.$injury_letter.'</span></strong></sup>';
								
								//get if FA
								//d($data['fffl_teams'],count($data['fffl_teams']));
								if(count($data['fffl_teams'])<2){ $fa = '<sup><strong><span class="red_font">FA</span></strong></sup>'; } else { $fa=''; }
								echo '<tr class="'.$alert.' '.$hidden_class.'" >
										<td class="text-center"><small>'.($rank+1).'</small></td><td class="text-left"><small>'.player_name_link($data['fffl_player_id'],TRUE,FALSE).' '.$data['team'].' '.$fa.' '.$headlines.' '.$injury_letter.'</small></td><td class="text-center"><small><a href="#" tabindex="0" class="scoring_button" role="button" fffl_player_id="'.$data['fffl_player_id'].'" year="'.$year.'">'.$data['average'].'</a></small></td>
									</tr>';
							}
                                
                        }
                        ?>
                        <tr class = "load_btn pointer" id="load_<? echo $position; ?>">
                        	<td colspan="3" onClick="load_collapse()"><small><strong>Load More +</strong></small></td>
                        </tr>
                        <tr class = "load_btn pointer" id="collapse_<? echo $position; ?>" style="display:none">
                        	<td colspan="3"><small><strong>Less -</strong></small></td>
                        </tr>
					</tbody>
                </table>
             </div>
         </div>
                                        
                                    
            <?php
    } //end funciton
            ?>  
        <div id="popover_container">
			<div class="row" >
            	<div class="col-xs-24">
                	<h4><strong><? echo $year; ?> Rankings</strong></h4>
                </div>
            	<?
				foreach(array('QB','RB','WR','TE','K') as $position){
					
					create_position_table($position, $rankings[$position],$team_id,$year)	;
					
				}
				?>
            
            </div>
 
            </div>
        </div> <!-- end content_area div -->
	</div>



<?PHP 
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/