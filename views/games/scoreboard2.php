<?PHP
	/**
	 * scoreboard view.
	 *
	 * through ajax will display scores by week	 */
	 
	//d($this->_ci_cached_vars);
	//$this->output->enable_profiler(TRUE);
?>
	<script type="text/javascript">
		
		function init_league_games(){
				$('#games').html("here");
				$.ajax({
					type: "POST",
					url: "<? echo base_url(); ?>Game/league_games_load/<? echo $league_id; ?>/<? echo $year; ?>/<? echo $week; ?>",
					async: false
				}).success(function(data){
					$('#games').html(data);
					
				}).complete(function(){
					
					
				})
			};
		
			init_league_games();	
		
			
			function init_nfl_games(){
				//nfl games are loaded into the div id nfl_games on an interval
					 
				$.ajax({
					type: "POST",
					url: "<? echo base_url(); ?>Game/nfl_games_load/<? echo $year; ?>/<? echo $week; ?>",
					async: false
				}).success(function(data){
					$('#nfl_games').html(data);
				}).complete(function(){
					
					
				})
			};
		
			init_nfl_games()
		
		$(document).ready(function(){
			$("#switch_scores").on("click",function(){
				var current = $("#switch_scores").html();
				if( current === "NFL Games"){
					$("#switch_scores").html("League Games");
				}
				else {
					$("#switch_scores").html("NFL Games");
				}
					$("#games").toggleClass("col-xs-24 col-sm-24");
					$("#games").toggleClass("hidden-xs hidden-sm");
					$("#nfl_games").toggleClass("col-xs-24 col-sm-24");
					$("#nfl_games").toggleClass("hidden-xs hidden-sm");
				
			});
		});
		
			
	</script>
    <style>
		.link_color{
			color:white;
		}
	</style>
   
    <div style="" id="open_player" data-player="0" data-team="0"></div>
		<div id="week_selector" class="col-xs-24 page_title text-center " style="padding-bottom:5px;">
        	<div class="btn-group">
            <button class="btn btn-default dropdown-toggle" type="button" id="group_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span id="dropdown_group">Week <? echo $week; ?></span>
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu"  aria-labelledby="dropdownMenu1" >
                <?
                $w=1;
                while($w<=17){ ?>
                    <li><a href="#" onClick="change_content('<? echo base_url()."Game/week/".$league_id."/".$year."/".$w; ?>',$('#dropdown_title').html())" ><? echo $w; ?></a></li>
                    
                <? $w++; 
                } ?>
            </ul>
            </div>
   	 	
        <button class="btn btn-default hidden-md hidden-lg" type="button" id="switch_scores" aria-haspopup="true" aria-expanded="true">NFL Games</button>
        </div><!--content_selector-->
        <div class="" id="stats_container">
            <div id="games" class="col-xs-24 col-sm-24 col-md-17" >
            
            </div>
            <div id="nfl_games" class="hidden-xs hidden-sm col-md-7" >
            
            </div>
		</div>        


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/