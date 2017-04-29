<?PHP
	/**
	 * nfl games view.
	 *
	 * through ajax will display nfl game stats */
	 
	//d($this->_ci_cached_vars);
	//$this->output->enable_profiler(TRUE);
?>
	<script type="text/javascript">
		function load_nfl_games(){
				
				$.ajax({
					type: "POST",
					url: "<? echo base_url(); ?>Game/nfl_games2/<? echo $year; ?>/<? echo $week; ?>",
					async: false
				}).success(function(data){
					
					var game=1;
					
					//update info for each game
					$.each(data.games_array, function(index, val) {
						
						$("."+game+"_home_team").html(val['home_team']);
						$("."+game+"_away_team").html(val['away_team']);
						
						$("#"+game+"_home_to_score").html(val['home_team_score']);
						$("#"+game+"_away_to_score").html(val['away_team_score']);
						
						$("."+game+"_time").html(val['time']);
						$("."+game+"_down").html(val['down']);
						
						if(val['status_flag']>0){
							$("#"+game+"_in_progress").removeClass("hidden");
							if(val['status_flag']!=1){
								$("#"+game+"_in_progress").addClass("hidden");
							}
						}
						else {
							$("#"+game+"_in_progress").addClass("hidden");
						}
						$("#"+game+"_plays").html(val.plays);
						
						$("#"+game+"_home_passing").html(val['home_passing']);
						$("#"+game+"_away_passing").html(val['away_passing']);
						
						$("#"+game+"_home_rushing").html(val['home_rushing']);
						$("#"+game+"_away_rushing").html(val['away_rushing']);
						
						$("#"+game+"_home_receiving").html(val['home_receiving']);
						$("#"+game+"_away_receiving").html(val['away_receiving']);
						
						$("#"+game+"_home_fumbles").html(val['home_fumbles']);
						$("#"+game+"_away_fumbles").html(val['away_fumbles']);
						
						$("#"+game+"_home_kicking").html(val['home_kicking']);
						$("#"+game+"_away_kicking").html(val['away_kicking']);
						
						game++;
					});
					
				}).complete(function(){
					
					setTimeout(function(){load_nfl_games();}, 47000);
				})
			};
		
			load_nfl_games();
	</script>
    <style>
		.link_color{
			color:white;
		}
	</style>
		<div id = "test_area_nfl"></div>
        <? 
		$align='left';
		//display the data for each game in a separate panel
		$game = 1; 
		$number_games = 16; //remove send from controller
		while($game <= $number_games){ 
		?>
		<div class="row">
        	<div class="panel panel-primary black_panel-primary " >
            	<div class="panel-heading black_panel-heading">
                    <h4 class="panel-title black_panel-title">
						<div class="row">
                            
                            <div class="col-xs-11 text-right">
                                <strong>
									<span id="<? echo $game; ?>_home_to_score"></span>
                                </strong>
                            </div>
                            <div class="col-xs-2"> | </div>
                            <div class="col-xs-11 text-left">
                                <strong>
								 	<span id="<? echo $game; ?>_away_to_score"></span>
								</strong>
                            </div>
                    	</div>
                        
                        <div class="row">
                        	 <div class="col-xs-24 text-center">
                                <strong><small>
                                    <span class="white_font <? echo $game; ?>_time"></span>
                                    <span class="white_font <? echo $game; ?>_down"></span>
                                </small></strong>
                            </div>
                        
                        </div>
                        
					</h4>
                </div>
                <div class="row text-left hidden" id="<? echo $game; ?>_in_progress" style="padding:5px;">
					
                    <div class="col-xs-24" style="height:130px; overflow-y:auto">
                        <small><span id="<? echo $game; ?>_plays"></span></small>
                    </div>
                    <div class="col-xs-24">
                        <div class="row">
                            <div class="text-center pointer col-xs-24" >
                                <span onClick=" $(this).parent().next().show(); $(this).toggle(); $(this).next().toggle(); "><small>Show Stats</small></span>
                                <span onClick="$(this).parent().next().hide(); $(this).toggle();  $(this).prev().toggle(); " style="display:none"><small>Hide Stats</small></span>
                            </div>

                            <div class=" col-xs-24" style="display:none; margin-top:6px;">
                                
                                <!-- passing -->
                                <div class="stat_group passing"> 
                                    <div class="col-xs-11 text-right pointer" onClick="$(this).parent().hide(); $(this).parent().nextAll('.kicking').show();"><small><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span> Kicking</small></div> 
                                    <div class="col-xs-2 text-center"> | </div>
                                    <div class="col-xs-11 text-left pointer" onClick="$(this).parent().hide(); $(this).parent().next('.rushing').show();"><small>Rushing <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></small></div>
                                
                                
                                    <div class="text-center col-xs-24" style="margin-top:6px;"><small><span class="<? echo $game; ?>_home_team"></span> Passing</small></div>
                                    <div>
                                        <table class='table table-condensed table-striped'>
                                           <thead>
                                                <tr>
                                                <td></td>
                                                <td class="text-center"><small>a/c</small></td>
                                                <td class="text-center"><small>yds</small></td>
                                                <td class="text-center"><small>tds</small></td>
                                                <td class="text-center"><small>ints</small></td>
                                                <td class="text-center"><small>2pt</small></td>
                                                </tr>
                                           </thead>
                                           <tbody id="<? echo $game; ?>_home_passing">
                                            
                                           </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center col-xs-24"><small><span class="<? echo $game; ?>_away_team"></span> Passing</small></div>
                                    <div>
                                        <table class='table table-condensed table-striped'>
                                            <thead>
                                                <tr>
                                                <td></td>
                                                <td class="text-center"><small>a/c</small></td>
                                                <td class="text-center"><small>yds</small></td>
                                                <td class="text-center"><small>tds</small></td>
                                                <td class="text-center"><small>ints</small></td>
                                                <td class="text-center"><small>2pt</small></td>
                                                </tr>
                                           </thead>
                                           <tbody id="<? echo $game; ?>_away_passing">
                                            
                                           </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- rushing -->
                                <div class="stat_group rushing" style="display:none"> 
                                    <div class="col-xs-11 text-right pointer" onClick="$(this).parent().hide(); $(this).parent().prevAll('.passing').show();"><small><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span> Passing</small></div> 
                                    <div class="col-xs-2 text-center"> | </div>
                                    <div class="col-xs-11 text-left pointer" onClick="$(this).parent().hide(); $(this).parent().next('.receiving').show();"><small>Receiving <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></small></div>
                                
                                
                                    <div class="text-center col-xs-24" style="margin-top:6px;"><small><span class="<? echo $game; ?>_home_team"></span> Rushing</small></div>
                                    <div>
                                        <table class='table table-condensed table-striped'>
                                           <thead>
                                                <tr>
                                                <td></td>
                                                <td class="text-center"><small>att</small></td>
                                                <td class="text-center"><small>yds</small></td>
                                                <td class="text-center"><small>tds</small></td>
                                                <td class="text-center"><small>lng</small></td>
                                                <td class="text-center"><small>2pt</small></td>
                                                </tr>
                                           </thead>
                                           <tbody id="<? echo $game; ?>_home_rushing">
                                            
                                           </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center col-xs-24"><small><span class="<? echo $game; ?>_away_team"></span> Rushing</small></div>
                                    <div>
                                        <table class='table table-condensed table-striped'>
                                            <thead>
                                                <tr>
                                                <td></td>
                                                <td class="text-center"><small>att</small></td>
                                                <td class="text-center"><small>yds</small></td>
                                                <td class="text-center"><small>tds</small></td>
                                                <td class="text-center"><small>lng</small></td>
                                                <td class="text-center"><small>2pt</small></td>
                                                </tr>
                                           </thead>
                                           <tbody id="<? echo $game; ?>_away_rushing">
                                            
                                           </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- receiving -->
                                <div class="stat_group receiving" style="display:none"> 
                                    <div class="col-xs-11 text-right pointer" onClick="$(this).parent().hide(); $(this).parent().prevAll('.rushing').show();"><small><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span> Rushing</small></div> 
                                    <div class="col-xs-2 text-center"> | </div>
                                    <div class="col-xs-11 text-left pointer" onClick="$(this).parent().hide(); $(this).parent().next('.fumbles').show();"><small>Fumbles <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></small></div>
                                
                                
                                    <div class="text-center col-xs-24" style="margin-top:6px;"><small><span class="<? echo $game; ?>_home_team"></span> Receiving</small></div>
                                    <div>
                                        <table class='table table-condensed table-striped'>
                                           <thead>
                                                <tr>
                                                <td></td>
                                                <td class="text-center"><small>rec</small></td>
                                                <td class="text-center"><small>yds</small></td>
                                                <td class="text-center"><small>tds</small></td>
                                                <td class="text-center"><small>lng</small></td>
                                                <td class="text-center"><small>2pt</small></td>
                                                </tr>
                                           </thead>
                                           <tbody id="<? echo $game; ?>_home_receiving">
                                            
                                           </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center col-xs-24"><small><span class="<? echo $game; ?>_away_team"></span> Receiving</small></div>
                                    <div>
                                        <table class='table table-condensed table-striped'>
                                            <thead>
                                                <tr>
                                                <td></td>
                                                <td class="text-center"><small>rec</small></td>
                                                <td class="text-center"><small>yds</small></td>
                                                <td class="text-center"><small>tds</small></td>
                                                <td class="text-center"><small>lng</small></td>
                                                <td class="text-center"><small>2pt</small></td>
                                                </tr>
                                           </thead>
                                           <tbody id="<? echo $game; ?>_away_receiving">
                                            
                                           </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- fumbles -->
                                <div class="stat_group fumbles" style="display:none"> 
                                    <div class="col-xs-11 text-right pointer" onClick="$(this).parent().hide(); $(this).parent().prevAll('.receiving').show();"><small><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span> Receiving</small></div> 
                                    <div class="col-xs-2 text-center"> | </div>
                                    <div class="col-xs-11 text-left pointer" onClick="$(this).parent().hide(); $(this).parent().next('.kicking').show();"><small>Kicking <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></small></div>
                                
                                
                                    <div class="text-center col-xs-24" style="margin-top:6px;"><small><span class="<? echo $game; ?>_home_team"></span> Fumbles</small></div>
                                    <div>
                                        <table class='table table-condensed table-striped'>
                                           <thead>
                                                <tr>
                                                <td></td>
                                                <td class="text-center"><small>lost/tot</small></td>
                                                </tr>
                                           </thead>
                                           <tbody id="<? echo $game; ?>_home_fumbles">
                                            
                                           </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center col-xs-24"><small><span class="<? echo $game; ?>_away_team"></span> Fumbles</small></div>
                                    <div>
                                        <table class='table table-condensed table-striped'>
                                           <thead>
                                                <tr>
                                                <td></td>
                                                <td class="text-center"><small>lost/tot</small></td>
                                                </tr>
                                           </thead>
                                           <tbody id="<? echo $game; ?>_away_fumbles">
                                            
                                           </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- kicking -->
                                <div class="stat_group kicking" style="display:none"> 
                                    <div class="col-xs-11 text-right pointer" onClick="$(this).parent().hide(); $(this).parent().prevAll('.fumbles').show();"><small><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span> Fumbles</small></div> 
                                    <div class="col-xs-2 text-center"> | </div>
                                    <div class="col-xs-11 text-left pointer" onClick="$(this).parent().hide(); $(this).parent().prevAll('.passing').show();"><small>Passing <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></small></div>
                                
                                
                                    <div class="text-center col-xs-24" style="margin-top:6px;"><small><span class="<? echo $game; ?>_home_team"></span> Kicking</small></div>
                                    <div>
                                        <table class='table table-condensed table-striped'>
                                           <thead>
                                                <tr>
                                                <td></td>
                                                <td class="text-center"><small>fg</small></td>
                                                <td class="text-center"><small>xp</small></td>
                                                </tr>
                                           </thead>
                                           <tbody id="<? echo $game; ?>_home_kicking">
                                            
                                           </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center col-xs-24"><small><span class="<? echo $game; ?>_away_team"></span> Kicking</small></div>
                                    <div>
                                       <table class='table table-condensed table-striped'>
                                           <thead>
                                                <tr>
                                                <td></td>
                                                <td class="text-center"><small>fg</small></td>
                                                <td class="text-center"><small>xp</small></td>
                                                </tr>
                                           </thead>
                                           <tbody id="<? echo $game; ?>_away_kicking">
                                            
                                           </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
        <? 
			$game++;
		} ?>
	
        
       

       


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/