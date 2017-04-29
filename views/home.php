<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//d($this->_ci_cached_vars);

?>     
			
			<div class="row row-eq-height" style="margin-top:0px;margin-bottom:5px; background-color:white;border-top-left-radius:10px;border-top-right-radius:10px;border-bottom-left-radius:0px; border-bottom-right-radius:0px;padding:15px;">
				<!--container for league headlines and cbs-->	
               	<div class="col-xs-24 col-sm-16"> 
               		<div class="row">
                    	<!--League Headlines-->
                        <div class="col-xs-24 " style="margin-bottom:5px;padding-right:5px;height:140px;border:solid #ccc 1px; border-radius:5px;overflow:hidden">
                       
                            
                             <? 
							 if($week>0 && $week<14){ //in season display ?>
                             		<div class="row">
                                        <h4 class="text-center hidden-xs">Game of the Week</h4>
                                        <h6 class="text-center hidden-sm hidden-md hidden-lg">Game of the Week</h6>
                                    </div>
                                    <div class="row" style="padding:5px;">
                                        <div class="col-xs-4 text-center">
                                            <a href="">
                                                <img class="img-responsive " style="max-height:75px"  src="<? echo $gow['0']['logo_a_path']; ?>">
                                            </a>
                                        </div>
                                        <div class="col-xs-7 text-center">
                                         	<? echo '<strong><small>'.team_name_link($gow['0']['opponent_a']).'</small></strong>'; ?>
                                        	<? echo '<br><small>('.$gow['0']['record_a']['wins'].'-'.$gow['0']['record_a']['losses'].', '.$gow['0']['points_a'].')</small>';
                                             ?>
                                        </div>
                                        <div class="col-xs-2 text-center">
                                         	VS
                                        </div>
                                        <div class="col-xs-7 text-center">
                                         	<? echo '<strong><small>'.team_name_link($gow['0']['opponent_b']).'</small></strong>'; ?>
                                        	<? echo '<br><small>('.$gow['0']['record_b']['wins'].'-'.$gow['0']['record_b']['losses'].', '.$gow['0']['points_b'].')</small>';
                                             ?>
                                        </div>
                                       
                                        <div class="col-xs-4 text-center">
                                            <a href="">
                                                <img class="img-responsive " style="max-height:75px"  src="<? echo $gow['0']['logo_b_path']; ?>">
                                            </a>
                                        </div>
                                        
                                    </div>
                                    
                             <? } 
							 elseif($week==14){ //first round playoffs ?>
                             		<div class="row">
                                        <h4 class="text-center hidden-xs">Divisional Playoffs</h4>
                                        <h6 class="text-center hidden-sm hidden-md hidden-lg">Divisional Playoffs</h6>
                                    </div>
                                    <div class="row" style="padding:5px;">
                                    <? foreach($gow as $game){
										 ?>
                                    
                                    	<div class="col-xs-24 col-sm-12">
                                        	<div class="row">
                                                <div class="col-xs-4 text-center">
                                                    <a href="">
                                                        <img class="img-responsive " style="max-height:18px"  src="<? echo $game['logo_a_path']; ?>">
                                                    </a>
                                                </div>
                                                <div class="col-xs-6 text-center">
                                                    <? echo '<strong><small>'.team_name_link($game['opponent_a'],TRUE,FALSE).'</small></strong>'; ?>
                                                    
                                                </div>
                                                <div class="col-xs-2 text-center">
                                                    <small><small>VS</small></small>
                                                </div>
                                                <div class="col-xs-6 text-center">
                                                    <? echo '<strong><small>'.team_name_link($game['opponent_b'],TRUE,FALSE).'</small></strong>'; ?>
                                                 
                                                </div>
                                               
                                                <div class="col-xs-4 text-center">
                                                    <a href="">
                                                        <img class="img-responsive " style="max-height:18px"  src="<? echo $game['logo_b_path']; ?>">
                                                    </a>
                                                </div>
                                        	</div>
                                            
                                        </div>
                                        <? 
                                        } ?>
                                        
                                    </div>
                                    
                             <? }
							 elseif($week==15){ //second round playoffs and tb?>
                             		<div class="row">
                                        <h4 class="text-center hidden-xs">Conference Championships</h4>
                                        <h6 class="text-center hidden-sm hidden-md hidden-lg">Conference Championships</h6>
                                    </div>
                                    <div class="row" style="padding:5px;">
                                    <? foreach($gow as $game){
										if($game['is_playoff']==0){ ?>
											<div class="col-xs-24 ">
                                                <div class="row">
                                                    <div class="col-xs-24 text-center">
                                                        <h4 class="text-center hidden-xs">Toilet Bowl</h4>
                                        				<h6 class="text-center hidden-sm hidden-md hidden-lg">Toilet Bowl</h6>
                                                    </div>
                                                   
                                                </div>
                                        	</div>
										<? }
										 ?>
                                    
                                    	<div class="col-xs-24 col-sm-12 <? if($game['is_playoff']==0){ ?> col-sm-offset-6 <? } ?>">
                                        	<div class="row">
                                                <div class="col-xs-4 text-center">
                                                    <a href="">
                                                        <img class="img-responsive " style="max-height:18px"  src="<? echo $game['logo_a_path']; ?>">
                                                    </a>
                                                </div>
                                                <div class="col-xs-6 text-center">
                                                    <? echo '<strong><small>'.team_name_link($game['opponent_a'],TRUE,FALSE).'</small></strong>'; ?>
                                                    
                                                </div>
                                                <div class="col-xs-2 text-center">
                                                    <small><small>VS</small></small>
                                                </div>
                                                <div class="col-xs-6 text-center">
                                                    <? echo '<strong><small>'.team_name_link($game['opponent_b'],TRUE,FALSE).'</small></strong>'; ?>
                                                 
                                                </div>
                                               
                                                <div class="col-xs-4 text-center">
                                                    <a href="">
                                                        <img class="img-responsive " style="max-height:18px"  src="<? echo $game['logo_b_path']; ?>">
                                                    </a>
                                                </div>
                                        	</div>
                                            
                                        </div>
                                        <? 
                                        } ?>
                                        
                                    </div>
                                  <? }
							 elseif($week==16){ //SUperbowl> ?>
                             		<div class="row">
                                        <h4 class="text-center hidden-xs">Super Bowl XIX</h4>
                                        <h6 class="text-center hidden-sm hidden-md hidden-lg">Super Bowl XIX</h6>
                                    </div>
                                    <div class="row" style="padding:5px;">
                                    <? foreach($gow as $game){
										
										 ?>
                                    
                                    	<div class="col-xs-24 col-sm-12 <? if($game['is_playoff']==0){ ?> col-sm-offset-6 <? } ?>">
                                        	<div class="row">
                                                <div class="col-xs-4 text-center">
                                                    <a href="">
                                                        <img class="img-responsive " style="max-height:18px"  src="<? echo $game['logo_a_path']; ?>">
                                                    </a>
                                                </div>
                                                <div class="col-xs-6 text-center">
                                                    <? echo '<strong><small>'.team_name_link($game['opponent_a'],TRUE,FALSE).'</small></strong>'; ?>
                                                    
                                                </div>
                                                <div class="col-xs-2 text-center">
                                                    <small><small>VS</small></small>
                                                </div>
                                                <div class="col-xs-6 text-center">
                                                    <? echo '<strong><small>'.team_name_link($game['opponent_b'],TRUE,FALSE).'</small></strong>'; ?>
                                                 
                                                </div>
                                               
                                                <div class="col-xs-4 text-center">
                                                    <a href="">
                                                        <img class="img-responsive " style="max-height:18px"  src="<? echo $game['logo_b_path']; ?>">
                                                    </a>
                                                </div>
                                        	</div>
                                            
                                        </div>
                                        <? 
                                        } ?>
                                        
                                    </div>  
                             <? 
							 }
							 elseif($week==17){ //SUperbowl> ?>
                             		<div class="row">
                                     <img class="img-responsive  " style="max-height:50px;display:block; margin:auto" src="<? echo base_url().'assets/img/logos/probowl.jpg'; ?>">
                                        <h4 class="text-center hidden-xs"><? echo $year; ?> Pro Bowl</h4>
                                        <h6 class="text-center hidden-sm hidden-md hidden-lg"><? echo $year; ?> Pro Bowl</h6>
                                    </div>
                                    <div class="row text-center" style="padding:5px;">
                                    Rosters Due Sunday Morning 9 a.m.
                                        
                                    </div>  
                             <? }
                                else {//preseason display
                                    echo '<h4 class="text-center hidden-xs">Preseason Schedule</h4>
                                            <h6 class="text-center hidden-sm hidden-md hidden-lg">Preseason Schedule</h6>';
                                    $current=0; $count=0; $draft=0;
                                    foreach($calendar as $time => $event){
                                        $count++;
                                        if($count==7){ echo '<div class="alert text-center" role="alert" style="color:#999; background-color:#ddd; padding:1px; margin:1px;" ><small>Supplemental Draft Rd 2</small></div>'; continue;}
                                        elseif($count>7){ continue; }
                                        if($event['short_name']=='FFFL Drafts' && $draft==1){continue;}
                                        if($time==$prev_event_time && $current==0){
                                            echo '<div class="alert alert-info text-center" role="alert" style="padding:1px; margin:1px;"><small>'.$event['short_name'].'</small></div>';
                                            $current=1;
                                        }
                                        else{
                                            echo '<div class="alert text-center" role="alert" style="color:#999; background-color:#ddd; padding:1px; margin:1px;" ><small>'.$event['short_name'].'</small></div>';
                                        }
                                        if($event['short_name']=='FFFL Drafts'){$draft=1;}
                                    }
                                    
                                }?>
                           
                            
                        
                        </div>
                        <!--league Headline-->
                        
                        <div class="col-xs-24 vertical_center" style="padding-right:5px;height:60px;border:solid #ccc 1px; border-radius:5px;overflow:hidden;margin-bottom:5px">
                            
                        	<div class="row " style="padding:5px;">
                            	<div class="col-xs-8 " style="margin-top:5px;margin-bottom:5px;">
                                     			
                                                
                                                    <img class="img-responsive img-rounded " style="max-height:50px;display:block; margin:auto" src="<? echo base_url().'assets/img/logos/super19.png'; ?>">
                                                
                                           
                                                
                                </div>           
                                <div class="col-xs-16 text-center">
                                            
                                                    <h4 class="text-center hidden-xs">Super Bowl XIX<br>Ben Wins Fourth Championship</h4>
                                                    <h6 class="text-center hidden-sm hidden-md hidden-lg">Super Bowl XIX<br>Ben Wins Fourth Championship</h6>
                                               
                                                
                                </div>
                                   
                               	
                            </div>
                            
                            
                        </div>
                        <div class="col-xs-24 vertical_center" style="padding-right:5px;height:60px;border:solid #ccc 1px; border-radius:5px;overflow:hidden;">
                            
                        	<div class="row " style="padding:5px;">
                            	<div class="col-xs-16 " >
                                     			 <a href="<? echo $marquee['link']; ?>">
                                                    <h4 class="text-center hidden-xs"><? echo str_replace(' By','<br>By',$marquee['text']); ?></h4>
                                                    <h6 class="text-center hidden-sm hidden-md hidden-lg"><? echo str_replace(' By','<br>By',$marquee['text']); ?></h6>
                                                </a>
                                               
                                            	
                                                
                                </div>           
                                <div class="col-xs-8 text-center" style="margin-top:5px;margin-bottom:5px;">
                                               
                                                <a href="<? echo $marquee['link']; ?>">
                                                    <img class="img-responsive img-rounded "   style="max-height:50px;display:block; margin:auto" src="<? echo $marquee['img']; ?>">
                                                </a>
                                                
                                </div>
                                   
                               	
                            </div>
                            
                            
                        </div>
                <!--next 2 /div close container for cbs and league -->    
                    </div>
                 
             	</div>   
                
                    
            <!--NFL Headlines-->
                
                <div class="hidden-xs col-sm-8 col-md-8" style="padding-right:5px;">
                    <div class="col-xs-24 text-center" style="padding:5px;padding-bottom:0px;border:solid #ccc 1px; border-radius:5px;max-height:265px;overflow:auto">
                        <h4 class="text-center hidden-xs"><img src="<? echo base_url(); ?>assets/img/nfl_logo.png" height="35px"> Headlines</h4>
                            <table class="table table-condensed table-striped table-hover" style="padding-bottom:0px;">
                                <? foreach($headlines as $headline_data){ ?>
                                    
                                    <tr>
                                        <td class="text-left"><small><? echo '<a href="'.$headline_data['link'].'" target="_new">'.$headline_data['title'].'</a>'; ?>
                                        </small></td>
                                    </tr>
                                <? } ?>
                            </table>
                        
                    </div>
                </div>
             </div>
             
             <div class="row " >
             <div class="container-fluid" style="padding-top:20px; border-radius:0px;" id="container"><!--creates white box around content with red border-->
				<div class="hidden-xs " >
                    <!-----------Best Record -->
                    <div class="hidden-xs hidden-xs col-sm-3  "  >
                        
                        <div class=" hidden-xs panel panel-primary blue_panel darker_shadow"  > 
                            <div class="hidden-xs panel-heading blue_panel-heading text-center" style="padding:3px;"> 
                                <span class="panel-title blue_panel-title " ><small>Record</small></span>
                            </div> 
                            <div class="hidden-xs panel-body"  style="padding:5px;height:120px;"> 
                                <div class="row">
                                    <div class="hidden-xs col-sm-24 text-center ellipses">
                                    	<div class="row">
                                            <div class="hidden-xs col-sm-24" style="width:100%;">
                                                <? $image_properties = array(
                                                    'src'   => $best_record['team_logo_path'],
                                                    'height' => '53px',
													'style' => 'max-width:100%'
                                                                                           
                                                ); 
                                                echo img($image_properties); ?>
                                            </div>
                                            <div class="col-sm-24 ellipses" style="width:100%">
                                                <small><strong><? $team_names = explode(' ',team_name_no_link($best_record['team_id']),2); echo $team_names['0'].'<br>'.$team_names['1'].'</strong><br>'.$best_record['record']; ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                     <!-----------Best Scoring -->
                    <div class="hidden-xs col-sm-3 " >
                        
                        <div class="hidden-xs  panel panel-primary blue_panel darker_shadow"  > 
                            <div class="hidden-xs panel-heading blue_panel-heading text-center" style="padding:3px;"> 
                                <span class="panel-title blue_panel-title " ><small>Scoring</small></span>
                            </div> 
                            <div class="hidden-xs panel-body"  style="padding:5px;height:120px;"> 
                                <div class="row">
                                    <div class="hidden-xs col-sm-24 text-center ellipses">
                                    	<div class="row">
                                            <div class="hidden-xs col-sm-24" style="width:100%;">
                                                <? $image_properties = array(
                                                    'src'   => $best_scoring['team_logo_path'],
                                                    'height' => '53px',
                                                    'style' => 'max-width:100%'                                 
                                                ); 
                                                echo img($image_properties); ?>
                                            </div>
                                            <div class="col-sm-24 ellipses" style="width:100%">
                                                <small><strong><? $team_names = explode(' ',team_name_no_link($best_scoring['team_id']),2); echo $team_names['0'].'<br>'.$team_names['1'].'</strong><br>'.$best_scoring['points']; ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                     <!-----------Best week -->
                    <div class="hidden-xs col-sm-3 " >
                        
                        <div class="hidden-xs  panel panel-primary blue_panel darker_shadow"  > 
                            <div class="hidden-xs panel-heading blue_panel-heading text-center" style="padding:3px;"> 
                                <span class="panel-title blue_panel-title " ><small>Week</small></span>
                            </div> 
                            <div class="hidden-xs panel-body"  style="padding:5px;height:120px;"> 
                                <div class="row">
                                    <div class="hidden-xs col-sm-24 text-center ellipses">
                                    	<div class="row">
                                            <div class="hidden-xs col-sm-24" style="width:100%;">
                                                <? $image_properties = array(
                                                    'src'   => $best_week['team_logo_path'],
                                                    'height' => '53px',
                                                    'style' => 'max-width:100%'                                       
                                                ); 
                                                echo img($image_properties); ?>
                                            </div>
                                            <div class="col-sm-24 ellipses" style="width:100%">
                                                <small><strong><? $team_names = explode(' ',team_name_no_link($best_week['team_id']),2); echo $team_names['0'].'<br>'.$team_names['1'].'</strong><br>'.$best_week['score'].' ('.$best_week['week'].')'; ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-----------Best players -->
                    <? foreach(array('QB','RB','WR','TE','K') as $position){ 
					$array_name = 'best_'.$position; 
					?>
                    <div class="hidden-xs col-sm-3  "  >
                        
                        <div class=" panel panel-primary blue_panel darker_shadow"  > 
                            <div class="hidden-xs panel-heading blue_panel-heading text-center" style="padding:3px;"> 
                                <span class="panel-title blue_panel-title " ><small><? echo $position; ?></small></span>
                            </div> 
                            <div class="hidden-xs panel-body"  style="padding:5px;height:120px;"> 
                                <div class="row">
                                    <div class="hidden-xs col-sm-24 text-center ellipses">
                                    	<div class="row">
                                            <div class="hidden-xs col-sm-24" style="width:100%;">
                                                <? $data = $$array_name;
												 $image_properties = array(
                                                    'src'   => $data['picture_path'],
                                                    'height' => '53px',
													'style' => 'max-width:100%'
                                                                                           
                                                ); 
                                                echo img($image_properties); ?>
                                            </div>
                                            <div class="col-sm-24 ellipses" style="width:100%">
                                                <small><strong><? $player_names = explode(' ',player_name_no_link($data['fffl_player_id'],FALSE,FALSE),2); echo $player_names['0'].'<br>'.$player_names['1'].'</strong><br>'.$data['average']; ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <? } ?>
                    
				</div>
				
                <!-----------Standings -->
                <div class="hidden-xs col-sm-24 col-md-16">
                    <div class=" panel panel-primary blue_panel darker_shadow"   > 
                        <div class="panel-heading blue_panel-heading"> 
                            <h3 class="panel-title blue_panel-title" >Standings</h3> 
                        </div> 
                        <div class=panel-body> 
                            <div class="row">
                                <div class="col-sm-24 col-md-12 ">
                                    <h5 class="text-center">AFC East</h5>
                                    <table class="table table-hover table-condensed" id="">
                                         <tbody >
                                    <?
                                      foreach($afc_east_standings as $data){ ?>
                                         <tr class="row <? if ($data['playoffs']==TRUE) { echo 'bg-info'; } ?> ">
                                            <td class="text-center  col-xs-3"><small>
                                            <?
                                                $image_properties = array(
                                                    'src'   => $data['team_logo_path'],
                                                  
													'height' => '20px'
													
                                                );
                                                echo img($image_properties);
                                            ?>
                                            </small></td>
                                            <td class="text-left  col-xs-13 ellipses"><small><? echo team_name_link($data['team_id']); ?> </small></td>
                                            <td class="text-center  col-xs-2"><small><? echo $data['wins']; ?></small></td>
                                            <td class="text-center  col-xs-2"><small><? echo $data['losses']; ?></small></td>
                                            <td class="text-center  col-xs-4"><small><? echo $data['points']; ?></small></td>
                                            
                                        
                                        </tr>
                                     <?  }
                                    ?>
                                        </tbody>
                                    </table>
                                    <h5 class="text-center">AFC West</h5>
                                    <table class="table table-hover table-condensed" id="">
                                         <tbody >
                                    <?
                                      foreach($afc_west_standings as $data){ ?>
                                         <tr class="row <? if ($data['playoffs']==TRUE) { echo 'bg-info'; } ?> ">
                                            <td class="text-center  col-xs-3"><small>
                                            <?
                                                $image_properties = array(
                                                    'src'   => $data['team_logo_path'],
                                                    'height' => '20px'
													
                                                );
                                                echo img($image_properties);
                                            ?>
                                            </small></td>
                                            <td class="text-left  col-xs-13 ellipses"><small><? echo team_name_link($data['team_id']); ?> </small></td>
                                            <td class="text-center  col-xs-2"><small><? echo $data['wins']; ?></small></td>
                                            <td class="text-center  col-xs-2"><small><? echo $data['losses']; ?></small></td>
                                            <td class="text-center  col-xs-4"><small><? echo $data['points']; ?></small></td>
                                            
                                        
                                        </tr>
                                     <?  }
                                    ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-24 col-md-12 ">
                                    <h5 class="text-center">NFC East</h5>
                                    <table class="table table-hover table-condensed" id="">
                                         <tbody >
                                    <?
                                      foreach($nfc_east_standings as $data){ ?>
                                         <tr class="row <? if ($data['playoffs']==TRUE) { echo 'bg-info'; } ?> ">
                                            <td class="text-center  col-xs-3"><small>
                                            <?
                                                $image_properties = array(
                                                    'src'   => $data['team_logo_path'],
                                                    
													'height' => '20px'
													
                                                );
                                                echo img($image_properties);
                                            ?>
                                            </small></td>
                                            <td class="text-left  col-xs-13 ellipses"><small><? echo team_name_link($data['team_id']); ?> </small></td>
                                            <td class="text-center  col-xs-2"><small><? echo $data['wins']; ?></small></td>
                                            <td class="text-center  col-xs-2"><small><? echo $data['losses']; ?></small></td>
                                            <td class="text-center  col-xs-4"><small><? echo $data['points']; ?></small></td>
                                            
                                        
                                        </tr>
                                     <?  }
                                    ?>
                                        </tbody>
                                    </table>
                                    <h5 class="text-center">NFC West</h5>
                                    <table class="table table-hover table-condensed" id="">
                                         <tbody >
                                    <?
                                      foreach($nfc_west_standings as $data){ ?>
                                         <tr class="row <? if ($data['playoffs']==TRUE) { echo 'bg-info'; } ?> ">
                                            <td class="text-center  col-xs-3"><small>
                                            <?
                                                $image_properties = array(
                                                    'src'   => $data['team_logo_path'],
                                                    'height' => '20px'
													
                                                );
                                                echo img($image_properties);
                                            ?>
                                            </small></td>
                                            <td class="text-left  col-xs-13 ellipses"><small><? echo team_name_link($data['team_id']); ?> </small></td>
                                            <td class="text-center  col-xs-2"><small><? echo $data['wins']; ?></small></td>
                                            <td class="text-center  col-xs-2"><small><? echo $data['losses']; ?></small></td>
                                            <td class="text-center  col-xs-4"><small><? echo $data['points']; ?></small></td>
                                            
                                        
                                        </tr>
                                     <?  }
                                    ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
                <!-----------Calendar Items -->
                <div class=" col-xs-24 col-sm-12 col-md-8 ">
                    <div class="panel panel-primary blue_panel darker_shadow"  > 
                        <div class="panel-heading blue_panel-heading" > 
                            <h3 class="panel-title blue_panel-title" >League Schedule</h3> 
                        </div> 
                        <div class=panel-body><small> 
                            <?
								$completed_events=array();
                              foreach($calendar as $time => $event){
                                  if($time>now()){
                                    echo ''.date('M j - ',$time).$event['long_name'].'<br>';
                                  }
								  else{
									$completed_events[] =  ''.date('M j - ',$time).$event['long_name'].'<br>';
								  }
                              }
							  echo '<div style="width:100%;border-top:1px solid #999;"></div><span style="color:#999">';
							  foreach($completed_events as $event){
								  echo $event;
								  
							  }
							  echo '</span></small>';
                            ?>
                        </div> 
                    </div>
                </div>
           </div>  <!-- /row -->       

        </div>