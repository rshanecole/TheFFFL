<?PHP
	/**
	 * scoreboard view.
	 *
	 * through ajax will display scores by week	 */
	 
	//d($this->_ci_cached_vars);
	//$this->output->enable_profiler(TRUE);
?>
	<script type="text/javascript">
		function load_league_games(){
				
				$.ajax({
					type: "POST",
					url: "<? echo base_url(); ?>Game/league_games/<? echo $league_id; ?>/<? echo $year; ?>/<? echo $week; ?>",
					async: false
				}).success(function(data){
					var player=1; //declare for later
					var game=1;
					var is_playoffs = false;
					
					//update info for each game
					$.each(data.games_array, function(index, val) {
						if(val["is_playoff"]==1){ 
							is_playoffs=true; 
							$("#playoffs").html("<img src='<? echo base_url(); ?>assets/img/logos/playoffs.gif'>"); 
							
						}
						else {
							if(val["week"]==17){
								$("#playoffs").html("<img src='<? echo base_url(); ?>assets/img/logos/probowl.jpg'>");
							}
							
							is_playoffs=false;	
						}
						//go through the a side of the panel, then b
						$.each(["a","b"], function(letter_index, letter){
							
							$("."+game+"_team_"+letter).html(val["opponent_"+letter]); //Team Name
							$("."+game+"_team_"+letter+"_small").html(val["opponent_"+letter+"_small"]); //Team Name Sm (first name only)
							$("."+game+"_team_"+letter+"_record").html(val["record_"+letter]); // Team record
							$("."+game+"_team_"+letter+"_logo").attr("src",val["logo_"+letter]); // Team Logo
							$("#"+game+"_team_"+letter+"_score").html(val["opponent_"+letter+"_score"]); // TEam Score
							$("#"+game+"_team_"+letter+"_tiebreaker").html(val["tiebreaker_"+letter]); // TEam Tiebreaker
							
							//starters
							player=1;
							$.each(val["starters_"+letter], function( fffl_player_id, player_data ){
								
								$("#"+game+"_team_"+letter+"_"+player+"_name").html(player_data["name"]);//Player Pos, Name, Team
								//if the player game is final, just score, otherwise time and score
								if (~player_data["status"].indexOf("Final")){
									$("#"+game+"_team_"+letter+"_"+player+"_status").html("");
								}
								else {
									$("#"+game+"_team_"+letter+"_"+player+"_status").html(player_data["status"]);
									
									$("#"+game+"_team_"+letter+"_"+player+"_background").removeClass("bg-danger");
									$("#"+game+"_team_"+letter+"_"+player+"_background").removeClass("bg-info");
									$("#"+game+"_team_"+letter+"_"+player+"_background").removeClass("bg-warning");
									
									if(jQuery.inArray(player_data["team"], data.redzone)>-1){//in the redzone
										$("#"+game+"_team_"+letter+"_"+player+"_background").addClass("bg-danger");
									}
									else if(jQuery.inArray(player_data["team"], data.possession)>-1){//in the redzone
										$("#"+game+"_team_"+letter+"_"+player+"_background").addClass("bg-info");
									}
									else if(player_data["score"] != null){//if the player's score is not null 
										$("#"+game+"_team_"+letter+"_"+player+"_background").addClass("bg-warning");
									}
									
								}
								
								$("."+game+"_team_"+letter+"_"+player+"_score").html(player_data["score"]);//player score
								$("#"+game+"_team_"+letter+"_"+player+"_stats").attr("data-player",player_data["fffl_player_id"]);//player score
								
								
								player++;
							});
							//in case team doesn't have a full starting lineup, empty additional players
							while(player <= <? echo $number_starters; ?> ){
								$("#"+game+"_team_"+letter+"_"+player+"_name").html("");//Player Pos, Name, Team
								$("#"+game+"_team_"+letter+"_"+player+"_status").html("");//status
								
								$("."+game+"_team_"+letter+"_"+player+"_score").html("");//player score
								player++;
							}
							
							
							//bench
							$("#"+game+"_team_"+letter+"_bench").empty();
							$.each(val["bench_"+letter], function( fffl_player_id, player_data ){
								//if the player game is final, just score, otherwise time and score
								if (~player_data["status"].indexOf("Final")){
									var status = "";
								}
								else {
									var status = player_data["status"];
									
									if(jQuery.inArray(player_data["team"], data.redzone)>-1){//in the redzone
										var background = "bg-danger";
									}
									else if(jQuery.inArray(player_data["team"], data.possession)>-1){//in the redzone
										var background = "bg-info";
									}
									else if(player_data["score"] != null){//if the player's score is not null 
										var background = "bg-warning";
									}
									
								}
								
								if(player_data["score"] != null){
									var score = player_data["score"];
								}
								else {
									var score = "";
								}
								
								var bench_player_content = "<tr class='row "+background+"'><td><div class='row'><div class='text-left  col-xs-13 col-sm-13 ellipses '><small><span >"+player_data['name']+"</span></small></div><div class='text-right  col-xs-11 col-sm-11'><small><small><span >"+status+"</span></small></small> <strong><small>&nbsp;<a role='button' href='#' tabindex='0' class='stats_button' data-player='"+player_data["fffl_player_id"]+"'><span >"+score+"</span></a></small></strong></div></td></tr>";
			                                        
			                                        $("#"+game+"_team_"+letter+"_bench").append(bench_player_content);

								player++;
							});
							
						});
						game++;
					});
					
					var tb_count=1;
					$.each(data.toilet_bowl_standings, function(index, val) {
						if(tb_count<7){
							$("#tb_"+tb_count).html(val.score+" "+val.team);
						}
						tb_count++;
					});
					$(".tb span").removeClass("link_color");
					
					
				}).complete(function(){
					
					setTimeout(function(){load_league_games();}, 30000);
				})
			};
		
			load_league_games();	
		
		
		$(document).ready(function(){
    		$('[data-toggle="popover"]').popover(); 
		});
		
		
		//stats popover
		$('.stats_button').popover({
			html: true,
			trigger: 'click',
			placement: 'top',
          	title: '' + '',
			container: '#stats_container',
			
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
         	
        });
      
		$(document).on("click",function() {	
			$("body").on("click", function (e) {
				
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
    <div id="stats_container">
    <div><h2><span id="playoffs"></span></h2></div>
    <div id="test_area"></div>
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
		$game_priority =0;
		
		while($game_priority < $number_games){  
			$game_priority++;
			
			if(($week==17 && $game_priority==1) || ($game_priority==5 || ($number_games == 3 && $game_priority==3)) && $is_playoffs){ ?>
				<div class="panel panel-primary red_panel-primary " >
                    <div class="panel-heading red_panel-heading">
                        <h4 class="panel-title red_panel-title">
                            <div class="row">
                            	<? if ($week==17 && $game_priority==1){ ?>
                                	<div class="col-xs-24 text-center">Pro Bowl</div>
                                <? } else { ?>
                                	<div class="col-xs-24 text-center">Toilet Bowl</div>
                                <? } ?>
                            </div>
                        </h4>
                    </div>
                    <? if(($game_priority==5 && $is_playoffs) || $week==17){ ?>
                    <div class="row">
                    	
                        	
                        	<div class="tb col-xs-24 col-sm-12" id="tb_1"></div>
                            <div class="tb col-xs-24 col-sm-12" id="tb_2"></div>
                            <div class="tb col-xs-24 col-sm-12" id="tb_3"></div>
                            <div class="tb col-xs-24 col-sm-12" id="tb_4"></div>
                            <div class="tb col-xs-24 col-sm-12" id="tb_5"></div>
                            <div class="tb col-xs-24 col-sm-12" id="tb_6"></div>
                    </div>
              
                   <? } ?>
            	</div>
			<? }
			
			if($game_priority==1 && !$is_playoffs && !$week==17) { $panel_color='red'; } else { $panel_color='blue'; } ?>
        	<div class="panel panel-primary <? echo $panel_color; ?>_panel-primary " >
            	<div class="panel-heading <? echo $panel_color; ?>_panel-heading">
                    <h4 class="panel-title <? echo $panel_color; ?>_panel-title">
						<div class="row">
                            <div class="col-xs-11 hidden-sm hidden-md hidden-lg text-right">
                            	<strong><? echo '<small><span class="link_color '.$game_priority.'_team_a_record">0-0</span></small><small>&nbsp;&nbsp;<span class="'.$game_priority.'_team_a_small"></span>'; ?></small></strong>
                            </div>
                            <div class="col-sm-11 hidden-xs text-right">
                            	<strong><? echo '<small><span class="link_color '.$game_priority.'_team_a_record">0-0</span></small>&nbsp;&nbsp;<span class="'.$game_priority.'_team_a"></span>'; ?></strong>
                            </div>
                            <div class="col-xs-2">|</div>
                            <div class="col-xs-11 hidden-sm hidden-md hidden-lg  text-left">
                            	<strong><small><span class="<? echo $game_priority; ?>_team_b_small"></span>&nbsp;&nbsp;</small><small><span class="link_color <? echo $game_priority; ?>_team_b_record"></span></small></strong>
                            </div>
                            <div class="col-sm-11 hidden-xs  text-left">
                            	<strong><? echo '<span class="'.$game_priority.'_team_b"></span>&nbsp;&nbsp;<small><span class="link_color '.$game_priority.'_team_b_record"></span></small>'; ?></strong>
                            </div>
                    	</div>
					</h4>
                </div>
                
                <!-- a new row, each tema will ahve a column -->
               
                <div class="row ">
                	
                        <div class="visible-xs row">
                            <span class="pointer" onClick=" $('.roster_<? echo $game_priority; ?>').toggleClass('hidden-xs'); $(this).toggle(); $(this).next().toggle(); "><small>Show Rosters</small></span>
                            <span class="pointer" onClick="$('.roster_<? echo $game_priority; ?>').toggleClass('hidden-xs'); $(this).toggle();  $(this).prev().toggle(); " style="display:none"><small>Hide Rosters</small></span>
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
                                    if($letter=='b'){ echo ' <span id="'.$game_priority.'_team_b_score">0</span> '; }
                                        
                                        $image_properties = array(
                                            'src' => 'http://fantasy.thefffl.com/assets/img/logos/fffl_logo.gif',
                                            'class' =>  $game_priority.'_team_'.$letter.'_logo img-responsive',
                                            
                                            'style' => 'display:inline;height:30px'
                                        );
                                    echo img($image_properties).'';
                                    
                                    if($letter=='a'){ echo ' <span id="'.$game_priority.'_team_a_score">0</span>'; }
                                    ?>
                                    </strong></h2> 
                                </div>
                                
                                <table class="table table-hover table-condensed col-xs-24 hidden-xs roster_<? echo $game_priority; ?>" id="">
                                    <tbody id = "<? echo $game_priority; ?>_team_<? echo $letter; ?>_roster"> 
                                        <?
										
                                        //for each player in the starting lineup
										$player_count = 1;
										
                                        while($player_count<=$number_starters){
											
											
											?>
                                        
                                            <tr id="<? echo $game_priority.'_team_'.$letter.'_'.$player_count.'_background'; ?>" class="row ">
                                                <td>
                                                    <div class="row">
                                                        <div class="text-left  col-xs-13 col-sm-13 ellipses "><small><span id="<? echo $game_priority.'_team_'.$letter.'_'.$player_count.'_name'; ?>">Pos Name team</span></small></div>
                                                        <div class="text-right  col-xs-11 col-sm-11"><small><small><span id="<? echo $game_priority.'_team_'.$letter.'_'.$player_count.'_status'; ?>"></span></small></small> <strong>
                                                        <small>&nbsp;
														<a role="button" 
                                                            href="#" 
                                                            tabindex="0" 
                                                            class="stats_button" 
                                                           	id="<? echo $game_priority.'_team_'.$letter.'_'.$player_count.'_stats'; ?>" 
                                                            
                                                            data-player="0" 
                                                         >
														<span class="<? echo $game_priority.'_team_'.$letter.'_'.$player_count.'_score'; ?>">0</span></a></small></strong>
                                                    </div>
                                                </td>
                                            </tr>
                                        <? $player_count++;
										 }//end starters ?>
											<tr class="row">
												<td class="text-right"><small>Tiebreaker: <strong><span id="<? echo $game_priority.'_team_'.$letter.'_tiebreaker'; ?>">0.0</span></strong></small></td>
											</tr>
										</tbody>
									</table>
                                        <div class="row">
                                            <div class="text-center col-xs-24">
                                                <span class="pointer <? echo $game_priority; ?>_bench_toggle <? if($letter=="a"){ echo "hidden-xs"; } ?>" onClick=" $('.<? echo $game_priority; ?>_benches').toggle(); $('.<? echo $game_priority; ?>_bench_toggle').toggle();  "><small>View Bench</small></span>
                                                <span class="pointer <? echo $game_priority; ?>_bench_toggle" onClick="$('.<? echo $game_priority; ?>_benches').toggle(); $('.<? echo $game_priority; ?>_bench_toggle').toggle(); " style="display:none"><small>Hide Bench</small></span>
                                            </div>
                                        </div>
                                            
                                        <? //bench players 
										 ?>
								<table class="table table-hover table-condensed col-xs-24 <? echo $game_priority; ?>_benches" style="display:none">
                                    <tbody id = "<? echo $game_priority; ?>_team_<? echo $letter; ?>_bench"> 
                                        
                                    </tbody>
                                </table> 
                        	
                        <!--each team's column-->
                    </div>
                  	<? } //end the table for each team 
					?>
               </div><!--end teams row-->
               
            </div>
            
        <? } ?>
	
        </div>
       

       


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/