<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//d($this->_ci_cached_vars);
?>

<!DOCTYPE HTML >
<html>
	<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="http://thefffl.com/favicon.ico">
        
    	<!--CSS LOADS-->
        	<!-- menu  CSS -->
    		<link href="<?php echo base_url(); ?>assets/css/menu/component.css" rel="stylesheet">
            
        	<!-- Bootstrap core CSS -->
    		<link href="<?php echo base_url(); ?>assets/css/bootstrap/css/bootstrap.min.css" rel="stylesheet">

            
            <!-- Local CSS -->
    		<link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet">
            
            
		<!--Javascript loads -->
        <script src="<?php echo base_url(); ?>assets/js/bootstrap/bootstrap.min.js"></script>
      
        	<!--JQUERY CDN then locol if CDN Down-->
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
            <script>
				if (typeof jQuery === 'undefined') 
					{ 
						document.write('<script src="<?php echo base_url(); ?>assets/js/jquery-2.1.4.min.js"<\/script>');
					}
			</script>
          
  			
  			<script src="<?php echo base_url(); ?>assets/js/jquery.countdown.min.js"></script>
			<script src="<?php echo base_url(); ?>assets/js/menu/modernizr.custom.js"></script>
          
            
            <!--SITE JS-->
			<script src="<?php echo base_url(); ?>assets/js/global.js"></script>
            <script src="<?php echo base_url(); ?>assets/js/ajaxfileupload.js"></script>
            
		<!--SCRIPTS--> 
        	
            
		<!-- END SCRIPTS -->
        
	</head>
    
	<body>
    <? //if($team_id==56){d($this->_ci_cached_vars);}?>
    	<div class="menu_container" >
            <!-- Menu Wrapper -->
            <div class="mp-pusher" id="mp-pusher" >
				<!-- mp-menu -->
                <nav id="mp-menu" class="mp-menu" >
                
                    <div class="mp-level">	<!--MAIN-->
                        <h2 class="icon icon-world menu_label">The FFFL</h2>
                        <ul> <!--MAIN : links-->
                        	<?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']==1) { 
																		?>
                            <li class="icon icon-arrow-left"><!--MAIN: link 1: Personal Team PAge-->
                                <a class="" href="#"><?php echo $team_name_first_nickname; ?></a>
                                <div class="mp-level"> <!-- MAIN > XXNAME OF TEAMXX: -->
                                    <h2 class="menu_label"><?php echo $team_name_first_nickname; ?></h2><!--MAIN > XXNAME OF TEAMXX: label-->
                                    <a class="mp-back" href="#">back</a>
                                    <ul><!--MAIN > XXNAME OF TEAMXX: links -->
                                    	<li class="icon icon-arrow-left"> <!--MAIN > XXNAME OF TEAMXX: link 1: Manage FA Draft-->
                                        	<a class="" href="<?php echo base_url(); ?>Team/id/<? echo $team_id; ?>/roster">
												Team Page
											</a>
										</li> <!--MAIN > XXNAME OF TEAMXX: link 1: Manage FA Draft-->
                                        <li class="icon icon-arrow-left"> <!--MAIN > XXNAME OF TEAMXX: link 2: Manage FA Draft-->
                                        	<a class="" href="<? echo base_url(); ?>Free_Agent/request">
												Manage Free Agent Draft
											</a>
										</li> <!--MAIN > XXNAME OF TEAMXX: link 1: Manage FA Draft-->
                                    	
                                        <li class="icon icon-arrow-left" > <!--MAIN > XXNAME OF TEAMXX: link 7: Sup Draft-->
                                        	<a class="" href="<? echo base_url(); ?>Supplemental/select">
												Supplemental Draft Selection
											</a>
										</li> <!--MAIN > XXNAME OF TEAMXX: link 6: Sup Draft-->
                                        <li class="icon icon-arrow-left" > <!--MAIN > XXNAME OF TEAMXX: link 8: Pro Bowl-->
                                        	<a class="" href="<? echo base_url(); ?>Probowl/selection">
												Submit a Pro Bowl Roster
											</a>
										</li> <!--MAIN > XXNAME OF TEAMXX: link 7: Pro Bowl-->
                                        <li class="icon icon-arrow-left" > <!--MAIN > XXNAME OF TEAMXX: link 9: Update Team Pic-->
                                        	<a class="" href="#">
												Update Team Logo <span class="label label-default">Soon</span>
											</a>
										</li> <!--MAIN > XXNAME OF TEAMXX: link 8: Team logo-->
                                        <li class="icon icon-arrow-left" > <!--MAIN > XXNAME OF TEAMXX: link 10: Update PRofile-->
                                        	<a class="" href="<?PHP echo base_url(); ?>Account/update">
												Update Profile
											</a>
										</li> <!--MAIN > XXNAME OF TEAMXX: link 9: Update Profile-->
													<?php if($_SESSION['security_level']>1) { ?>
                                        <li class="icon icon-arrow-left" > <!--MAIN > XXNAME OF TEAMXX: link 11: Admin Dashboard-->
                                        	<a class="" href="<?PHP echo base_url(); ?>Admin">
												Admin Panel
											</a>
										</li> <!--MAIN > XXNAME OF TEAMXX: link 10: Admin Dashboard-->
												<?php } //end if security level >1 ?>
                                    </ul><!--MAIN > XXNAME OF TEAMXX: -->    
                                </div> <!-- MAIN > XXNAME OF TEAMXX -->        
                            </li ><!--MAIN: link 1: XXNAME OF TEAMXX-->
                            
													<?php }//end if logged_in ?>
                            <li class="icon icon-arrow-left"><!--MAIN: link 1: Teams-->
                                <a class="" href="#">Teams</a>
                                <div class="mp-level"> <!-- MAIN > TEAMS: lists AFC and NFC -->
                                    <h2 class="menu_label">Teams</h2><!--MAIN > TEAMS: label-->
                                    <a class="mp-back" href="#">back</a>
                                    <ul><!--MAIN > TEAMS: links (AFC AND NFC)-->
                                    	<? //now list the conferences->divisions->teams using foreach to do all 
										foreach($all_teams as $conference =>$divisions){ ?>
                                            <li class=""> <!--MAIN > TEAMS: link 1: AFC-->
                                                <a class="" href="#">
                                                    <?php 
                                                        $image_properties = array(
                                                            'src'   => base_url().'assets/img/logos/'.strtolower($conference).'logo.gif',
                                                            'width' => '25',
                                                        );
                                                        echo img($image_properties).'&nbsp;'; 
                                                    echo $conference; ?>
                                                </a>
                                                <div class="mp-level"><!-- MAIN > TEAMS > AFC  -->
                                                    <h2 class="icon icon-display menu_label"><? echo $conference; ?> Teams</h2><!--MAIN > TEAMS > AFC: label-->
                                                    <a class="mp-back" href="#">back</a>
                                                    <ul> <!--MAIN > TEAMS > AFC: links-->
                                                       
                                                        <? 	ksort($divisions);
															foreach($divisions as $division => $teams){ ?>
                                                            <li class="icon icon-arrow-left">
                                                                <h2 class="icon icon-display black">
                                                                    <? echo $division; ?>
                                                                </h2>
                                                            </li>
                                                            <? asort($teams);
                                                                foreach($teams as $team => $team_name){
                                                            ?>	<li class="icon icon-arrow-left">
                                                                    <a href="<? echo base_url().'Team/id/'.$team; ?>"><? echo $team_name; ?></a>
                                                                </li>
                                                            <? }//teams ?>
                                                            
                                                        <? } //conference as division ?>
                                                    </ul><!--MAIN > TEAMS > AFC: links -->
                                                </div><!--MAIN > TEAMS > AFC -->
                                            </li> <!--MAIN > TEAMS: link 1: AFC-->
                                        <? } //end all_teams array ?>
                                    		<li class=""> <!--MAIN > TEAMS: link 3: Depth Charts-->
                                                <a class="" href="<? echo base_url(); ?>Team/depth">
                                                    Depth Charts
                                                </a>
                                            </li> <!--MAIN > TEAMS: link 3: Depth Charts-->
                                    </ul><!--MAIN > TEAMS: links (AFC AND NFC)-->    
                                </div> <!-- MAIN > TEAMS: lists AFC and NFC -->        
                            </li ><!--MAIN: link 1: Teams-->        
                            
                            <li class="icon icon-arrow-left"><!--MAIN: link 2: Standings-->
                                <a class="" href="<? echo base_url().'Standing/year/'; ?>">Standings</a>
                                        
                            </li ><!--MAIN: link 2: Standings-->    
							
                            <li class="icon icon-arrow-left"><!--MAIN: link 3: Players-->
                                <a class="" href="#">Players</a>
                                <div class="mp-level"> <!-- MAIN > Players: -->
                                    <h2 class="menu_label">Players</h2><!--MAIN > Players: label-->
                                    <a class="mp-back" href="#">back</a>
                                    <ul><!--MAIN > Players: links -->
                                    	<li class="icon icon-arrow-left"> <!--MAIN > Players: link 1: Search-->
                                        	<a class="" href="<?php echo base_url(); ?>Player/search">
												Search
											</a>
										</li> <!--MAIN > Players: link 1: Search-->
                                    	<li class="icon icon-arrow-left"> <!--MAIN > Players: link 2: Position Rankings-->
                                        	<a class="" href="<?php echo base_url(); ?>Player/rankings/">
												Position Rankings
											</a>
										</li> <!--MAIN > Players: link 2: Position Rankings-->
                                        <li class="icon icon-arrow-left" > <!--MAIN > Players: link 3: Inactive Players-->
                                        	<a class="" href="#">
												Inactive Players <span class="label label-default">Soon</span>
											</a>
										</li> <!--MAIN > Players: link 3: Inactive Players-->
                                        <li class="icon icon-arrow-left" > <!--MAIN > Standings: link 4: FFFL Depth Charts-->
                                        	<a class="" href="<? echo base_url(); ?>Team/depth">
												FFFL Depth Charts
											</a>
										</li> <!--MAIN > Players: link 4: FFFL Depth Charts-->
                                    </ul><!--MAIN > Players: -->    
                                </div> <!-- MAIN > Players -->        
                            </li ><!--MAIN: link 3: Players--> 
                            
                            <li class="icon icon-arrow-left"><!--MAIN: link 4: Transactions-->
                                <a class="" href="#">Transactions</a>
                                <div class="mp-level"> <!-- MAIN > Transactions: -->
                                    <h2 class="menu_label">Transactions</h2><!--MAIN > Transactions: label-->
                                    <a class="mp-back" href="#">back</a>
                                    <ul><!--MAIN > Players: links -->
                                    	<li class="icon icon-arrow-left"> <!--MAIN > Transactions: link 2: FA -->
                                        	<a class="" href="<?php echo base_url(); ?>Transaction">
												Transactions
											</a>     
										</li> <!--MAIN > Transactions >  link 2: FA -->
                                    	<li class="icon icon-arrow-left"> <!--MAIN > Transactions: link 1: Trades-->
                                        	<a class="" href="<?php echo base_url(); ?>Trade">
												Trades
											</a>
										</li> <!--MAIN > Transactions: link 1: Trades-->
                                    	<li class="icon icon-arrow-left"> <!--MAIN > Transactions: link 2: FA -->
                                        	<a class="" href="#">
												Free Agency
											</a> 
                                            <div class="mp-level"> <!-- MAIN > Transactions > Free Agency:-->
                                                <h2 class="menu_label">Free Agency</h2><!--MAIN > Transactions > Free Agency: label-->
                                                <a class="mp-back" href="#">back</a>
                                                <ul><!--MAIN > Transactions > Free Agency: -->
                                                    <li class="icon icon-arrow-left"> <!--MAIN > Transactions > Free Agency: link 1: Draft Results-->
                                                        <a class="" href="<? echo base_url(); ?>Free_Agent/results">
                                                            FA Draft Results
                                                        </a>
                                                    </li> <!--MAIN > Transactions > Free Agency: link 1: Draft Results-->                        
                                                    <li class="icon icon-arrow-left"> <!--MAIN > Transactions > Free AGency: link 2: order-->
                                                        <a class="" href="<? echo base_url() ?>Free_Agent/order">
                                                            FA Draft Order
                                                        </a>
                                                    </li> <!--MAIN > Transactions > Draft: link 2: order-->
                                                </ul><!--MAIN > Transactions > Free Agency-->    
                                            </div> <!-- MAIN > Transactions > Free Agency -->      
										</li> <!--MAIN > Transactions >  link 2: FA -->
                                    	<li class="icon icon-arrow-left"> <!--MAIN > Transactions: link 3: Draft -->
                                        	<a class="" href="#">
												Draft
											</a>
                                            <div class="mp-level"> <!-- MAIN > Transactions > Draft:-->
                                                <h2 class="menu_label">Draft</h2><!--MAIN > Transactions > Draft: label-->
                                                <a class="mp-back" href="#">back</a>
                                                <ul><!--MAIN > Transactions > Draft: -->
                                                    <li class="icon icon-arrow-left"> <!--MAIN > Transactions > Draft: link 1: Draft Order-->
                                                        <a class="" href="<? echo base_url().'Draft/'; ?>">
                                                            Draft Results
                                                        </a>
                                                    </li> <!--MAIN > Transactions > Draft: link 1: Draft Order-->                        
                                                    <li class="icon icon-arrow-left"> <!--MAIN > Transactions > Draft: link 3: Live Draft-->
                                                        <a class="" href="<? echo base_url() ?>Draft_Live/live">
                                                            Live Draft Center
                                                        </a>
                                                    </li> <!--MAIN > Transactions > Draft: link 3: Live Draft-->
                                                </ul><!--MAIN > Transactions > Draft-->    
                                            </div> <!-- MAIN > Transactions > Draft -->        
										</li> <!--MAIN > Transactions >  link 3: Draft -->
                                    </ul><!--MAIN > Transactions: -->    
                                </div> <!-- MAIN > Transactions -->        
                            </li ><!--MAIN: link 4: Transactions--> 
                            
                            <li class="icon icon-arrow-left"><!--MAIN: link 5: Scores-->
                                <a class="" href="<? echo base_url(); ?>Game/scores">Scores</a>
                                   
                            </li ><!--MAIN: link 5: Scores--> 
							
                            <li class="icon icon-arrow-left"><!--MAIN: link 6: League-->
                                <a class="" href="#">League </a>
                                <div class="mp-level"> <!-- MAIN > League: -->
                                    <h2 class="menu_label">League</h2><!--MAIN > League: label-->
                                    <a class="mp-back" href="#">back</a>
                                    <ul><!--MAIN > League: links -->
                                    	<li class="icon icon-arrow-left"> <!--MAIN > LEague: link 1: Rule Book-->
                                        	<a class="" href="<? echo base_url().'League/rules'; ?>">
												Rule Book
											</a>
										</li> <!--MAIN > League: link 1: Rule Book-->
                                    	<li class="icon icon-arrow-left"> <!--MAIN > LEague: link 2: Record Book-->
                                        	<a class="" href="<? echo base_url().'League/records'; ?>">
												Record Book
											</a>
										</li> <!--MAIN > LEague: link 2: Record Book-->
                                        <li class="icon icon-arrow-left" > <!--MAIN > LEague: link 3: Franchise Statistics-->
                                        	<a class="" href="#">
												Franchise Statistics<span class="label label-default">Soon</span>
											</a>
										</li> <!--MAIN > League: link 3: Franchise Stats-->
                                        <li class="icon icon-arrow-left" > <!--MAIN > League: link 4: Past Seasons-->
                                        	<a class="" href="#">
												Past Seasons<span class="label label-default">Soon</span>
											</a>
										</li> <!--MAIN > League: link 4: Past Seasons-->
                                        <li class="icon icon-arrow-left" > <!--MAIN > League: link 5: FFFL Trophies-->
                                        	<a class="" href="#">
												FFFL Trophies<span class="label label-default">Soon</span>
											</a>
										</li> <!--MAIN > League: link 5: FFFL Trophies-->
                                        <li class="icon icon-arrow-left" > <!--MAIN > League: link 6: FFFL HOF-->
                                        	<a class="" href="#">
												FFFL Hall of Fame<span class="label label-default">Soon</span>
											</a>
										</li> <!--MAIN > League: link 6: FFFL HOF-->
                                        <li class="icon icon-arrow-left" > <!--MAIN > League: link 7: NFL TEam Resources-->
                                        	<a class="" href="#">
												NFL Team Resources<span class="label label-default">Soon</span>
											</a>
										</li> <!--MAIN > League: link 7: NFL Team Resources-->
                                        <li class="icon icon-arrow-left" > <!--MAIN > League: link 8: Open Dates-->
                                        	<a class="" href="#">
												Open Dates<span class="label label-default">Soon</span>
											</a>
										</li> <!--MAIN > League: link 8: Open Dates-->
                                        <li class="icon icon-arrow-left" > <!--MAIN > League: link 9: Injury Reports-->
                                        	<a class="" href="#">
												Injury Report<span class="label label-default">Soon</span>
											</a>
										</li> <!--MAIN > League: link 9: Injury Reports-->
                                    </ul><!--MAIN > League: -->    
                                </div> <!-- MAIN > League -->        
                            </li ><!--MAIN: link 6: League--> 
						</ul>
					</div>
                  <div style="position:absolute;bottom:0;padding:10px;text-align:center;"><a href="https://www.facebook.com/groups/225478930827401/"><img width="40px" src="<?php echo base_url();?>assets/img/logos/facebook.png"> <strong>Discuss on Facebook</strong></a></div>
				</nav>
            	<!-- /mp-menu -->
    
                <div class="scroller"><!-- this is for emulating position fixed of the nav -->
                    <div class="scroller-inner">
                        
                        <div class="content clearfix">
							<div style="clear:both; height:25px;" >
                                <div class="top_header navbar navbar-fixed-top"  ><!--container for the mmenu button-->
                    
                                    <a href="#" id="trigger" class="blue_links">
                                        <?php $image_properties = array(
                                                'src'   => base_url().'assets/img/logos/fffl_logo.gif',
                                                'width' => '55',
                                            );
                                            echo '<div style="float:left;overflow:visible">'.img($image_properties).'</div>';
                                        ?>
                                        <span class="glyphicon glyphicon-menu-hamburger " style="font-size:17px;" aria-hidden="true"></span>
                                        <span style="font-size:20px;"  >FFFL</span>
                                        
                                    </a>
                    				<div style="float:right;">
                                        
                                    	<a class="blue_links" href="<?php echo base_url(); ?>">Home</a> | 
                                        <?php
											if($logged_in ==TRUE) {?>
                                    			<a class="blue_links" href="<?php echo base_url(); ?>Account/Logout/">Logout</a>
                                            <? } else { ?>
                                            	<a class="blue_links" href="<?php echo base_url(); ?>Account/Login/">Login</a>
                                             <? } ?>
                                   	</div>
                                    
                                   
                                </div>
                            </div> 
                            		
                                
                                    <?
									//add alerts here
									 if(isset($alerts) && !empty($alerts)){
										
											foreach($alerts as $alert => $data){
												if($data === end($alerts)) { $margin=''; } else {$margin = 'margin-bottom:0px;';}
												echo '<div style="height:35px; line-height:16px; '.$margin.'; border-radius:0px;" class="text-center alert alert-'.$data['alert_type'].'" role="alert"><strong>'.$data['message'].'</strong></div>';	
												
											}
											?>
                                             <script type="text/javascript">
												$("#clock")
													.countdown($('#clock').attr('time'), function(event) {
														$(this).text(
															event.strftime('%Dd %H:%M:%S')
														);
													});
                                            </script>
                                          <?
											
										}
									
										
										else{ ?>
                                        <div class="text-center white_font champs hidden-xs " >
                                   	 		<span >2016</span> Ben McDaniel<? echo nbs(2); ?>|<? echo nbs(2); ?><span >2015</span> Dave McCray<? echo nbs(2); ?><span>|<? echo nbs(2); ?>2014</span> Dave McCray<span ><? echo nbs(2); ?>|<? echo nbs(2); ?>2013</span> Eric Burleson<span ><? echo nbs(2); ?>|<? echo nbs(2); ?>2012</span> Ron Cole
                                    	</div>
									
                            
                            <div class="hidden-sm hidden-md hidden-lg champs_hidden"></div> 
							<? } ?>
                            <div class="container"  ><!--bootstrap container-->
		
		

<!-- ----------------------------------------------------------------------------------------------- 
END header.php
-------------------------------------------------------------------------------------------------- -->