<?PHP
	/**
	 * supplemental selection view.
	 *
	 * through ajax will reorder supplemental selections
	 */
	d($this->_ci_cached_vars);
	//$this->output->enable_profiler(TRUE);
?>
	<script type="text/javascript" src="<? echo base_url(); ?>assets/js/jquery-ui.min.js"></script>
    <script src="<? echo base_url(); ?>assets/js/jquery.ui.touch-punch.min.js"></script>
    <link rel="stylesheet" href="<? echo base_url(); ?>assets/css/jquery-ui.min.css" />
    <style>
    .hanging_indent{
    	padding-left:22px;
		text-indent:-22px;
    }
	</style>
	<script type="text/javascript">
		
	</script>
		
        <div id="" class="col-xs-24 col-sm-12" >
        	<div class="panel panel-primary blue_panel-primary " >
            	<div class="panel-heading blue_panel-heading">
                    <h3 class="panel-title blue_panel-title">
						<strong>Super Bowls</strong></h3><h5></h5>
                </div>
                <div class = "panel-body">
                	<table class="table table-hover table-condensed table-striped " id="">
                    <tbody>
						<?php
                        $number = $total_superbowls;
                        foreach ($superbowls as $year => $data) {
                            
                        ?> 
                         <tr class="row" >	
                        	<td class="" style="padding:0px;margin:0px;">     
                            	<div class="hanging_indent">  
                                    <div class="col-xs-8 col-sm-6 col-md-5" ><small><? echo $year.' '.roman_numeral($number); ?>: </small></div>
                                    <div class="col-xs-16 col-sm-18 col-md-19" >
                                    	<small>
                                            <? 
											if($data['0']['winner']>0){
												echo '<strong>'.team_name_link($data['0']['winner']).'</strong> '.$data['0']['opponent_a_score'].' vs '.$data['0']['opponent_b_score'].' '.team_name_link($data['0']['loser']); 
											}
											else{
												echo '&nbsp;';	
											}
											?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                    	</tr>          
							<?php
								$number--;
								
                            }
							
						
                            ?>
                            
                    </tbody>
                    </table>
					
               </div>
            </div>
		</div>
    	<div id="" class="col-xs-24 col-sm-12" >
        	<div class="panel panel-primary blue_panel-primary " >
            	<div class="panel-heading blue_panel-heading">
                    <h3 class="panel-title blue_panel-title">
						<strong>Toilet Bowls</strong></h3><h5></h5>
                </div>
                <div class = "panel-body">
              		<table class="table table-hover table-condensed table-striped " id="">
                        <tbody>
                            
							<?php
							$number = $total_toilet_bowls;
                            foreach ($toilet_bowls as $year => $data) {
								
                            ?> 
                            <tr class="row" >	
                                <td class="" style="padding:0px;margin:0px;">     
                                    <div class="hanging_indent">   
                                        <div class="col-xs-8 col-sm-6 col-md-5" ><small><? echo $year.' '.roman_numeral($number); ?>: </small></div>
                                        <div class="col-xs-16 col-sm-18 col-md-19" >
                                            <small>
                                                <? 
                                                if($data['winner']>0){
                                                    echo '<strong>'.team_name_link($data['winner']).'</strong> '.$data['winner_score'].' vs '.$data['loser_score'].' '.team_name_link($data['loser']); 
                                                }
                                                else{
                                                    echo '&nbsp;';	
                                                }
                                                ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                                      
							<?php
								$number--;
								
                            }
							
						
                            ?>
                    	</tbody>
					</table>
               	</div>
            </div>
		</div>
        <div id="" class="col-xs-24 col-sm-12" >
        	<div class="panel panel-primary blue_panel-primary " >
            	<div class="panel-heading blue_panel-heading">
                    <h3 class="panel-title blue_panel-title">
						<strong>Pro Bowls</strong></h3><h5></h5>
                </div>
                <div class = "panel-body">
              		<table class="table table-hover table-condensed table-striped " id="">
                        <tbody>
                            <?php
							$number = $total_pro_bowls;
                            foreach ($pro_bowls as $year => $data) {
								
                            ?> 
							
                            <tr class="row" >	
                                <td class="" style="padding:0px;margin:0px;">     
                                    <div class="hanging_indent">	

                                        <div class="col-xs-8 col-sm-6 col-md-5" ><small><? echo $year.' '.roman_numeral($number); ?>: </small></div>
                                        <div class="col-xs-16 col-sm-18 col-md-19" >
                                            <small>
                                                <? 
                                                if($data['winner']>0){
                                                    echo '<strong>'.team_name_link($data['winner']).'</strong> - '.$data['winner_score'].' pts.'; 
                                                }
                                                else{
                                                    echo '&nbsp;';	
                                                }
                                                ?>
                                            </small>
                                        </div>
                                	</div>
                            	</td>
                            </tr>
							<?php
								$number--;
								
                            }
							?>
						</tbody>
                            
					</table>
               </div>
            </div>
		</div>
        <div id="" class="col-xs-24 col-sm-12" >
        	<div class="panel panel-primary blue_panel-primary " >
            	<div class="panel-heading blue_panel-heading">
                    <h3 class="panel-title blue_panel-title">
						<strong>Season: Wins</strong></h3><h5></h5>
                </div>
                <div class = "panel-body">
                	<table class="table table-hover table-condensed table-striped" id="">
                    	<tbody>
                    		<tr class="row" >	
                                <td class="" style="padding:0px;margin:0px;">     
                                    <div class="hanging_indent">
                        		<!--Most wins regular saeason-->
                                        <div class="col-xs-24 col-sm-12 col-md-10" ><small>Most Wins in a Regular Season:</small></div>
                                        <div class="col-xs-24 col-sm-12 col-md-14" >
                                            
                                            
                                                <small><strong>
                                                <span class="visible-xs-inline">&nbsp;&nbsp;</span>
                                                <? 
                                                $counter=1;
                                                echo $max_wins_reg;
                                                ?></strong> - <?
                                                foreach($max_wins_reg_array as $data){
                                                    
                                                    if($counter > 1){ echo ', '; }
                                                    echo team_name_link($data['team_id']).' (';
                                                    $counter2=1;
                                                    foreach($data['years'] as $year){
                                                        if($counter2 > 1){ echo ', '; }
                                                        echo $year;
                                                        $counter2++;	
                                                    }
                                                    echo ')';
                                                    $counter++;
                                                }
                                                ?>
                                            
                                            </small>
                                        </div>
                                	</div>
                            	</td>     
                               
                        	</tr>
                        	<tr class="row">
                                <td class="" style="padding:0px;margin:0px;">
                                	<div class="hanging_indent">	
                                        <!--Most wins regular saeason-->
                                        <div class="col-xs-24 col-sm-12 col-md-10" ><small>Most Wins in a Season:</small></div>
                                        <div class="col-xs-24 col-sm-12 col-md-14" >
                                            
                                            
                                                <small><strong>
                                                <span class="visible-xs-inline">&nbsp;&nbsp;</span>
                                            
                                                <? 
                                                $counter=1;
                                                echo $max_wins_all;
                                                ?></strong> - <?
                                                foreach($max_wins_all_array as $data){
                                                    
                                                    if($counter > 1){ echo ', '; }
                                                    echo team_name_link($data['team_id']).' (';
                                                    $counter2=1;
                                                    foreach($data['years'] as $year){
                                                        if($counter2 > 1){ echo ', '; }
                                                        echo $year;
                                                        $counter2++;	
                                                    }
                                                    echo ')';
                                                    $counter++;
                                                }
                                                ?>
                                            
                                            </small>
                                        </div> 
                                	</div>		    
                                </td>   
                        	</tr>
                            <tr class="row">
                                <td class="" style="padding:0px;margin:0px;">
                                	<div class="hanging_indent">	
                                        <!--Most wins regular saeason-->
                                        <div class="col-xs-24 col-sm-12 col-md-10" ><small>Fewest Losses in a Regular Season:</small></div>
                                        <div class="col-xs-24 col-sm-12 col-md-14" >
                                            
                                            
                                                <small><strong>
                                                <span class="visible-xs-inline">&nbsp;&nbsp;</span>
                                            
                                                <? 
                                                $counter=1;
                                                echo $min_losses_reg;
                                                ?></strong> - <?
                                                foreach($min_losses_reg_array as $data){
                                                    
                                                    if($counter > 1){ echo ', '; }
                                                    echo team_name_link($data['team_id']).' (';
                                                    $counter2=1;
                                                    foreach($data['years'] as $year){
                                                        if($counter2 > 1){ echo ', '; }
                                                        echo $year;
                                                        $counter2++;	
                                                    }
                                                    echo ')';
                                                    $counter++;
                                                }
                                                ?>
                                            
                                            </small>
                                        </div> 
                                	</div>		    
                                </td>   
                        	</tr>
                            <tr class="row">
                                <td class="" style="padding:0px;margin:0px;">
                                	<div class="hanging_indent">	
                                        <!--Most wins regular saeason-->
                                        <div class="col-xs-24 col-sm-12 col-md-10" ><small>Fewest Losses in a Season with Playoffs:</small></div>
                                        <div class="col-xs-24 col-sm-12 col-md-14" >
                                            
                                            
                                                <small><strong>
                                                <span class="visible-xs-inline">&nbsp;&nbsp;</span>
                                            
                                                <? 
                                                $counter=1;
                                                echo $min_losses_all;
                                                ?></strong> - <?
                                                foreach($min_losses_all_array as $data){
                                                    
                                                    if($counter > 1){ echo ', '; }
                                                    echo team_name_link($data['team_id']).' (';
                                                    $counter2=1;
                                                    foreach($data['years'] as $year){
                                                        if($counter2 > 1){ echo ', '; }
                                                        echo $year;
                                                        $counter2++;	
                                                    }
                                                    echo ')';
                                                    $counter++;
                                                }
                                                ?>
                                            
                                            </small>
                                        </div> 
                                	</div>		    
                                </td>   
                        	</tr>
                            <tr class="row">
                                <td class="" style="padding:0px;margin:0px;">
                                	<div class="hanging_indent">	
                                        <!--loginest winning streak-->
                                        <div class="col-xs-24 col-sm-12 col-md-10" ><small>Longest Winning Streak:</small></div>
                                        <div class="col-xs-24 col-sm-12 col-md-14" >
                                            
                                            
                                                <small><strong>
                                                <span class="visible-xs-inline">&nbsp;&nbsp;</span>
                                            
                                                <? 
                                                $counter=1;
                                                echo $max_streak_all;
                                                ?></strong> - <?
                                                foreach($max_streak_all_array as $data){
                                                    
                                                    if($counter > 1){ echo ', '; }
                                                    echo team_name_link($data['team_id']).' (';
                                                    $counter2=1;
                                                    foreach($data['years'] as $year){
                                                        if($counter2 > 1){ echo ', '; }
                                                        echo $year;
                                                        $counter2++;	
                                                    }
                                                    echo ')';
                                                    $counter++;
                                                }
                                                ?>
                                            
                                            </small>
                                        </div> 
                                	</div>		    
                                </td>   
                        	</tr>
							<tr class="row">
                                <td class="" style="padding:0px;margin:0px;">
                                	<div class="hanging_indent">	
                                        <!--loginest winning streak-->
                                        <div class="col-xs-24 col-sm-12 col-md-10" ><small>Longest Winning Streak in Single Season:</small></div>
                                        <div class="col-xs-24 col-sm-12 col-md-14" >
                                            
                                            
                                                <small><strong>
                                                <span class="visible-xs-inline">&nbsp;&nbsp;</span>
                                            
                                                <? 
                                                $counter=1;
                                                echo $max_streak_season;
                                                ?></strong> - <?
                                                foreach($max_streak_season_array as $data){
                                                    
                                                    if($counter > 1){ echo ', '; }
                                                    echo team_name_link($data['team_id']).' (';
                                                    $counter2=1;
                                                    foreach($data['years'] as $year){
                                                        if($counter2 > 1){ echo ', '; }
                                                        echo $year;
                                                        $counter2++;	
                                                    }
                                                    echo ')';
                                                    $counter++;
                                                }
                                                ?>
                                            
                                            </small>
                                        </div> 
                                	</div>		    
                                </td>   
                        	</tr>
						</tbody>
                    </table>
               </div>
            </div>
		</div>
        
        <!-- SEASON: Scoring -->
        <div id="" class="col-xs-24 col-sm-12" >
        	<div class="panel panel-primary blue_panel-primary " >
            	<div class="panel-heading blue_panel-heading">
                    <h3 class="panel-title blue_panel-title">
						<strong>Season: Scoring</strong></h3><h5></h5>
                </div>
                <div class = "panel-body">
                	<table class="table table-hover table-condensed table-striped" id="">
                    	<tbody>
                    		<tr class="row" >	
                                <td class="" style="padding:0px;margin:0px;">     
                                    <div class="hanging_indent">
                        		<!--Most points regular saeason-->
                                        <div class="col-xs-24 col-sm-12 col-md-10" ><small>Most Points in a Regular Season:</small></div>
                                        <div class="col-xs-24 col-sm-12 col-md-14" >
                                            
                                            
                                                <small><strong>
                                                <span class="visible-xs-inline">&nbsp;&nbsp;</span>
                                                <? 
                                                $counter=1;
                                                echo $max_points_reg;
                                                ?></strong> - <?
                                                foreach($max_points_reg_array as $data){
                                                    
                                                    if($counter > 1){ echo ', '; }
                                                    echo team_name_link($data['team_id']).' (';
                                                    $counter2=1;
                                                    foreach($data['years'] as $year){
                                                        if($counter2 > 1){ echo ', '; }
                                                        echo $year;
                                                        $counter2++;	
                                                    }
                                                    echo ')';
                                                    $counter++;
                                                }
                                                ?>
                                            
                                            </small>
                                        </div>
                                	</div>
                            	</td>     
                               
                        	</tr>
                        	<tr class="row">
                                <td class="" style="padding:0px;margin:0px;">
                                	<div class="hanging_indent">	
                                        <!--Most points in full saeason-->
                                        <div class="col-xs-24 col-sm-12 col-md-10" ><small>Most Points in a Season:</small></div>
                                        <div class="col-xs-24 col-sm-12 col-md-14" >
                                            
                                            
                                                <small><strong>
                                                <span class="visible-xs-inline">&nbsp;&nbsp;</span>
                                            
                                                <? 
                                                $counter=1;
                                                echo $max_points_all;
                                                ?></strong> - <?
                                                foreach($max_points_all_array as $data){
                                                    
                                                    if($counter > 1){ echo ', '; }
                                                    echo team_name_link($data['team_id']).' (';
                                                    $counter2=1;
                                                    foreach($data['years'] as $year){
                                                        if($counter2 > 1){ echo ', '; }
                                                        echo $year;
                                                        $counter2++;	
                                                    }
                                                    echo ')';
                                                    $counter++;
                                                }
                                                ?>
                                            
                                            </small>
                                        </div> 
                                	</div>		    
                                </td>   
                        	</tr>
                            <tr class="row">
                                <td class="" style="padding:0px;margin:0px;">
                                	<div class="hanging_indent">	
                                        <!--Most points in the playoffs-->
                                        <div class="col-xs-24 col-sm-12 col-md-10" ><small>Most Points in a Playoff:</small></div>
                                        <div class="col-xs-24 col-sm-12 col-md-14" >
                                            
                                            
                                                <small><strong>
                                                <span class="visible-xs-inline">&nbsp;&nbsp;</span>
                                            
                                                <? 
                                                $counter=1;
                                                echo $max_points_playoffs;
                                                ?></strong> - <?
                                                foreach($max_points_playoffs_array as $data){
                                                    
                                                    if($counter > 1){ echo ', '; }
                                                    echo team_name_link($data['team_id']).' (';
                                                    $counter2=1;
                                                    foreach($data['years'] as $year){
                                                        if($counter2 > 1){ echo ', '; }
                                                        echo $year;
                                                        $counter2++;	
                                                    }
                                                    echo ')';
                                                    $counter++;
                                                }
                                                ?>
                                            
                                            </small>
                                        </div> 
                                	</div>		    
                                </td>   
                        	</tr>
                            <tr class="row">
                                <td class="" style="padding:0px;margin:0px;">
                                	<div class="hanging_indent">	
                                        <!--Most wins regular saeason-->
                                        <div class="col-xs-24 col-sm-12 col-md-10" ><small>Most Points in a Single Game:</small></div>
                                        <div class="col-xs-24 col-sm-12 col-md-14" >
                                            
                                            
                                                <small><strong>
                                                <span class="visible-xs-inline">&nbsp;&nbsp;</span>
                                            
                                                <? 
                                                $counter=1;
                                                echo $max_points_game;
                                                ?></strong> - <?
                                                foreach($max_points_game_array as $data){
                                                    
                                                    if($counter > 1){ echo ', '; }
                                                    echo team_name_link($data['team_id']).' (';
                                                    $counter2=1;
                                                    foreach($data['years'] as $year){
                                                        if($counter2 > 1){ echo ', '; }
                                                        echo $year;
                                                        $counter2++;	
                                                    }
                                                    echo ')';
                                                    $counter++;
                                                }
                                                ?>
                                            
                                            </small>
                                        </div> 
                                	</div>		    
                                </td>   
                        	</tr>
                            <tr class="row">
                                <td class="" style="padding:0px;margin:0px;">
                                	<div class="hanging_indent">	
                                        <!--most points in a superbowl-->
                                        <div class="col-xs-24 col-sm-12 col-md-10" ><small>Most Points in a Superbowl:</small></div>
                                        <div class="col-xs-24 col-sm-12 col-md-14" >
                                            
                                            
                                                <small><strong>
                                                <span class="visible-xs-inline">&nbsp;&nbsp;</span>
                                            
                                                <? 
                                                $counter=1;
                                                echo $max_points_superbowl;
                                                ?></strong> - <?
												
                                                foreach($max_points_superbowl_array as $data){
                                                    
                                                    if($counter > 1){ echo ', '; }
                                                    echo team_name_link($data['team_id']).' (';
                                                    $counter2=1;
                                                    foreach($data['years'] as $year){
                                                        if($counter2 > 1){ echo ', '; }
                                                        echo $year;
                                                        $counter2++;	
                                                    }
                                                    echo ')';
                                                    $counter++;
                                                }
                                                ?>
                                            
                                            </small>
                                        </div> 
                                	</div>		    
                                </td>   
                        	</tr>
							<tr class="row">
                                <td class="" style="padding:0px;margin:0px;">
                                	<div class="hanging_indent">	
                                        <!--most points in a superbowl-->
                                        <div class="col-xs-24 col-sm-12 col-md-10" ><small>Most Points in a Probowl:</small></div>
                                        <div class="col-xs-24 col-sm-12 col-md-14" >
                                            
                                            
                                                <small><strong>
                                                <span class="visible-xs-inline">&nbsp;&nbsp;</span>
                                            
                                                <? 
                                                $counter=1;
                                                echo $max_points_pro_bowls;
                                                ?></strong> - <?
												
                                                foreach($max_points_pro_bowls_array as $data){
                                                    
                                                    if($counter > 1){ echo ', '; }
                                                    echo team_name_link($data['team_id']).' (';
                                                    $counter2=1;
                                                    foreach($data['years'] as $year){
                                                        if($counter2 > 1){ echo ', '; }
                                                        echo $year;
                                                        $counter2++;	
                                                    }
                                                    echo ')';
                                                    $counter++;
                                                }
                                                ?>
                                            
                                            </small>
                                        </div> 
                                	</div>		    
                                </td>   
                        	</tr>
						</tbody>
                    </table>
               </div>
            </div>
		</div>
        
     </div> 


       


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/