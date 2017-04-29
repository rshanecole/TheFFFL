<?PHP
	/**
	 * nfl games view.
	 *
	 * through ajax will display nfl game stats */
	 
	//d($this->_ci_cached_vars);
	//$this->output->enable_profiler(TRUE);
?>
	<script type="text/javascript">
		
	</script>
    <style>
		.link_color{
			color:white;
		}
	</style>
		
        <? 
		$align='left';
		//display the data for each game in a separate panel
		foreach($games_array as $game_data){ ?>
		<div class="row">
        	<div class="panel panel-primary black_panel-primary " >
            	<div class="panel-heading black_panel-heading">
                    <h4 class="panel-title black_panel-title">
						<div class="row">
                            
                            <div class="col-md-11 hidden-xs hidden-sm text-right">
                                <strong><?
									$to_score=''; 
                                    if($game_data['status_flag']>0){
                                        
                                        $to = 1;
                                        while($to<=$game_data['home_to'] && $game_data['status_flag']==1){
                                            $to_score .='&bull;';
                                            $to++;	
                                        }
										$to_score .= ' '.$game_data['home_score'].' ';
                                    }
                                        
                                echo $to_score.' '.$game_data['home_team']; ?></strong>
                            </div>
                            <div class="col-md-2"> | </div>
                            <div class="col-md-11 hidden-xs hidden-sm text-left">
                                <strong><? 
									$score_to='';
                                    if($game_data['status_flag']>0){
                                        $score_to = ' '.$game_data['away_score'].' ';
                                        $to = 1;
                                        while($to<=$game_data['away_to'] && $game_data['status_flag']==1){
                                            $score_to .='&bull;';
                                            $to++;	
                                        }
                                    }
                                        
                                echo $game_data['away_team'].' '.$score_to; ?></strong>
                            </div>
                    	</div>
                        
                        <div class="row">
                        	 <div class="col-md-24 hidden-xs hidden-sm text-center">
                                <strong><small><span class="white_font"><? 
									
                                    if($game_data['status_flag']==1){
										$quarter='';
										if($game_data['quarter']==1){
											$quarter='1st';
										}
										if($game_data['quarter']==2){
											$quarter='2nd';
										}
										if($game_data['quarter']==3){
											$quarter='3rd';
										}
										if($game_data['quarter']==4){
											$quarter='4th';
										}
										if($game_data['quarter']==5){
											$quarter='OT';
										}
										
										
                                        echo $game_data['clock'].' '.$quarter;
										
										$down='';
										if($game_data['down']==1){
											$down = '<br>'.$game_data['possession'].' at '.$game_data['yard_line'].' '.$game_data['down'].'st'.' & '.$game_data['to_go'];
										}
										if($game_data['down']==2){
											$down = '<br>'.$game_data['possession'].' at '.$game_data['yard_line'].' '.$game_data['down'].'nd'.' & '.$game_data['to_go'];
										}
										if($game_data['down']==3){
											$down = '<br>'.$game_data['possession'].' at '.$game_data['yard_line'].' '.$game_data['down'].'rd'.' & '.$game_data['to_go'];
										}
										if($game_data['down']==4){
											$down = '<br>'.$game_data['possession'].' at '.$game_data['yard_line'].' '.$game_data['down'].'th'.' & '.$game_data['to_go'];
										}
										echo $down;
                                    }
									elseif($game_data['status_flag']==2){
                                        echo $game_data['quarter'];
                                    }
									else{
										echo date('D g:ia',$game_data['start_time']);
									}
                                        
                                 ?>
                                </span></small></strong>
                            </div>
                        
                        </div>
                        
					</h4>
                </div>
                <? if($game_data['status_flag']>0){ ?>
                    <div class="row text-left" style="padding:5px;">
                    	<? if($game_data['status_flag']==1) { ?>
                        <div class="col-md-24" style="height:130px; overflow-y:auto">
                            <small><? 
                                if(isset($game_data['plays']) && $game_data['status_flag']!=2){
                                    
                                    foreach($game_data['plays'] as $play){
                                        echo $play.'<br>';
                                    }
                                }
                                ?></small>
                        </div>
                        <? } ?>
                        <div class="col-md-24">
                        	<div class="row">
                                <div class="text-center pointer col-md-24" >
                                    <span id="<? echo $game_data['home_team']; ?>" onClick=" $(this).parent().next().show(); $(this).toggle(); $(this).next().toggle(); "><small>Show Stats</small></span>
                                    <span onClick="$(this).parent().next().hide(); $(this).toggle();  $(this).prev().toggle(); " style="display:none"><small>Hide Stats</small></span>
                                </div>
                                
                                
                            	
                                <div class=" col-md-24" style="display:none; margin-top:6px;">
                                	
                                    <!-- passing -->
                                    <div class="stat_group passing"> 
                                        <div class="col-md-11 text-right pointer" onClick="$(this).parent().hide(); $(this).parent().nextAll('.kicking').show();"><small><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span> Kicking</small></div> 
                                        <div class="col-md-2 text-center"> | </div>
                                        <div class="col-md-11 text-left pointer" onClick="$(this).parent().hide(); $(this).parent().next('.rushing').show();"><small>Rushing <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></small></div>
                                    
                                    
                                        <div class="text-center col-md-24" style="margin-top:6px;"><small><? echo $game_data['home_team']; ?> Passing</small></div>
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
                                               <tbody>
                                                <? foreach($game_data['home_passing'] as $player){
                                                    echo '<tr>';
                                                        echo '<td class="text-left"><small>'.$player['name'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['cmp'].'/'.($player['cmp']+$player['att']).'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['yds'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['tds'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['ints'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['twoptm'].'/'.($player['twoptm']+$player['twopta']).'</small></td>';
                                                    echo '</tr>';
                                                } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center col-md-24"><small><? echo $game_data['away_team']; ?> Passing</small></div>
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
                                               <tbody>
                                                <? foreach($game_data['away_passing'] as $player){
                                                    echo '<tr>';
                                                        echo '<td class="text-left"><small>'.$player['name'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['cmp'].'/'.($player['cmp']+$player['att']).'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['yds'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['tds'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['ints'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['twoptm'].'/'.($player['twoptm']+$player['twopta']).'</small></td>';
                                                    echo '</tr>';
                                                } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <!-- rushing -->
                                    <div class="stat_group rushing" style="display:none"> 
                                        <div class="col-md-11 text-right pointer" onClick="$(this).parent().hide(); $(this).parent().prevAll('.passing').show();"><small><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span> Passing</small></div> 
                                        <div class="col-md-2 text-center"> | </div>
                                        <div class="col-md-11 text-left pointer" onClick="$(this).parent().hide(); $(this).parent().next('.receiving').show();"><small>Receiving <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></small></div>
                                    
                                    
                                        <div class="text-center col-md-24" style="margin-top:6px;"><small><? echo $game_data['home_team']; ?> Rushing</small></div>
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
                                               <tbody>
                                                <? foreach($game_data['home_rushing'] as $player){
                                                    echo '<tr>';
                                                        echo '<td class="text-left"><small>'.$player['name'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['att'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['yds'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['tds'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['lng'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['twoptm'].'/'.($player['twoptm']+$player['twopta']).'</small></td>';
                                                    echo '</tr>';
                                                } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center col-md-24"><small><? echo $game_data['away_team']; ?> Rushing</small></div>
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
                                               <tbody>
                                                <? foreach($game_data['away_rushing'] as $player){
                                                    echo '<tr>';
                                                        echo '<td class="text-left"><small>'.$player['name'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['att'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['yds'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['tds'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['lng'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['twoptm'].'/'.($player['twoptm']+$player['twopta']).'</small></td>';
                                                    echo '</tr>';
                                                } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <!-- receiving -->
                                    <div class="stat_group receiving" style="display:none"> 
                                        <div class="col-md-11 text-right pointer" onClick="$(this).parent().hide(); $(this).parent().prevAll('.rushing').show();"><small><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span> Rushing</small></div> 
                                        <div class="col-md-2 text-center"> | </div>
                                        <div class="col-md-11 text-left pointer" onClick="$(this).parent().hide(); $(this).parent().next('.fumbles').show();"><small>Fumbles <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></small></div>
                                    
                                    
                                        <div class="text-center col-md-24" style="margin-top:6px;"><small><? echo $game_data['home_team']; ?> Receiving</small></div>
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
                                               <tbody>
                                                <? foreach($game_data['home_receiving'] as $player){
                                                    echo '<tr>';
                                                        echo '<td class="text-left"><small>'.$player['name'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['rec'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['yds'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['tds'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['lng'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['twoptm'].'/'.($player['twoptm']+$player['twopta']).'</small></td>';
                                                    echo '</tr>';
                                                } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center col-md-24"><small><? echo $game_data['away_team']; ?> Receiving</small></div>
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
                                               <tbody>
                                                <? foreach($game_data['away_receiving'] as $player){
                                                    echo '<tr>';
                                                        echo '<td class="text-left"><small>'.$player['name'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['rec'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['yds'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['tds'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['lng'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['twoptm'].'/'.($player['twoptm']+$player['twopta']).'</small></td>';
                                                    echo '</tr>';
                                                } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <!-- fumbles -->
                                    <div class="stat_group fumbles" style="display:none"> 
                                        <div class="col-md-11 text-right pointer" onClick="$(this).parent().hide(); $(this).parent().prevAll('.receiving').show();"><small><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span> Receiving</small></div> 
                                        <div class="col-md-2 text-center"> | </div>
                                        <div class="col-md-11 text-left pointer" onClick="$(this).parent().hide(); $(this).parent().next('.kicking').show();"><small>Kicking <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></small></div>
                                    
                                    
                                        <div class="text-center col-md-24" style="margin-top:6px;"><small><? echo $game_data['home_team']; ?> Fumbles</small></div>
                                       	<div>
                                            <table class='table table-condensed table-striped'>
                                               <thead>
                                                	<tr>
                                                    <td></td>
                                                    <td class="text-center"><small>lost/tot</small></td>
                                                    </tr>
                                               </thead>
                                               <tbody>
                                                <? foreach($game_data['home_fumbles'] as $player){
                                                    if($player['tot']>0){
														echo '<tr>';
															echo '<td class="text-left"><small>'.$player['name'].'</small></td>';
															echo '<td class="text-center"><small>'.$player['lost'].'/'.$player['tot'].'</small></td>';
															
														echo '</tr>';
													}
													else{
														echo '<tr>';
															echo '<td class="text-left"><small>None</small></td>';
															echo '<td class="text-center"><small></small></td>';
															
														echo '</tr>';
													}
                                                } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center col-md-24"><small><? echo $game_data['away_team']; ?> Fumbles</small></div>
                                        <div>
                                            <table class='table table-condensed table-striped'>
                                               <thead>
                                                	<tr>
                                                    <td></td>
                                                    <td class="text-center"><small>lost/tot</small></td>
                                                    </tr>
                                               </thead>
                                               <tbody>
                                                <? foreach($game_data['away_fumbles'] as $player){
													 if($player['tot']>0){
														echo '<tr>';
															echo '<td class="text-left"><small>'.$player['name'].'</small></td>';
															echo '<td class="text-center"><small>'.$player['lost'].'/'.$player['tot'].'</small></td>';
															
														echo '</tr>';
													}
													else{
														echo '<tr>';
															echo '<td class="text-left"><small>None</small></td>';
															echo '<td class="text-center"><small></small></td>';
															
														echo '</tr>';
													}
                                                } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <!-- kicking -->
                                    <div class="stat_group kicking" style="display:none"> 
                                        <div class="col-md-11 text-right pointer" onClick="$(this).parent().hide(); $(this).parent().prevAll('.fumbles').show();"><small><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span> Fumbles</small></div> 
                                        <div class="col-md-2 text-center"> | </div>
                                        <div class="col-md-11 text-left pointer" onClick="$(this).parent().hide(); $(this).parent().prevAll('.passing').show();"><small>Passing <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></small></div>
                                    
                                    
                                        <div class="text-center col-md-24" style="margin-top:6px;"><small><? echo $game_data['home_team']; ?> Kcking</small></div>
                                       	<div>
                                            <table class='table table-condensed table-striped'>
                                               <thead>
                                                	<tr>
                                                    <td></td>
                                                    <td class="text-center"><small>fg</small></td>
                                                    <td class="text-center"><small>xp</small></td>
                                                    </tr>
                                               </thead>
                                               <tbody>
                                                <? foreach($game_data['home_kicking'] as $player){
                                                    echo '<tr>';
                                                        echo '<td class="text-left"><small>'.$player['name'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['fgm'].'/'.$player['fga'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['xpmade'].'/'.$player['xpa'].'</small></td>';
                                                       
                                                    echo '</tr>';
                                                } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center col-md-24"><small><? echo $game_data['away_team']; ?> Kicking</small></div>
                                        <div>
                                           <table class='table table-condensed table-striped'>
                                               <thead>
                                                	<tr>
                                                    <td></td>
                                                    <td class="text-center"><small>fg</small></td>
                                                    <td class="text-center"><small>xp</small></td>
                                                    </tr>
                                               </thead>
                                               <tbody>
                                                <? foreach($game_data['away_kicking'] as $player){
                                                    echo '<tr>';
                                                        echo '<td class="text-left"><small>'.$player['name'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['fgm'].'/'.$player['fga'].'</small></td>';
                                                        echo '<td class="text-center"><small>'.$player['xpmade'].'/'.$player['xpa'].'</small></td>';
                                                       
                                                    echo '</tr>';
                                                } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                        	</div>
                        </div>
                    </div>
                <? } ?>
            </div>
        </div>
        <? } ?>
	
        
       

       


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/