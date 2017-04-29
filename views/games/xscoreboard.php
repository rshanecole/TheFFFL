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
				//league games are loaded into the div id games on an interval
					//$(".popover").hide();
					// Obviously this is quite expensive but in a situation where there *can* be escapees
					// then you have to check all elements to see if they have a popover.
					/*$("*").each(function () {
						// Bootstrap sets a data field with key `bs.popover` on elements that have a popover.
						// Note that there is no corresponding **HTML attribute** on the elements so we cannot
						// perform a search by attribute.
						var popover = $.data(this, "bs.popover");
						if (popover)
							$(this).popover('destroy');
					});*/
					//var open_player = $("#open_player").attr("data-player");
					//var open_team = $("#open_team").attr("data-team");
					//var bench_open_team = $("#bench_open_team").attr("data-team");
					
				$.ajax({
					type: "POST",
					url: "<? echo base_url(); ?>Game/league_games/<? echo $league_id; ?>/<? echo $year; ?>/<? echo $week; ?>",
					async: false
				}).success(function(data){
					$('#games').html(data);
				}).complete(function(){
					/*$.each( $('#open_scores'), function(i, open_scores) {
					   $('div', open_scores).each(function() {
							var box = $(this).attr("data-team");
							$("#" + box).click();
					   });
					})
					$.each( $('#bench_open_scores'), function(i, open_scores) {
					   $('div', open_scores).each(function() {
							var box = $(this).attr("data-team");
							$("#" + box).click();
					   });
					})
					$("#"+open_player+"_"+open_team).click();*/
					setTimeout(function(){load_league_games();}, 30000);
				})
			};
		
			load_league_games();	
			
			function load_nfl_games(){
				//nfl games are loaded into the div id nfl_games on an interval
					 
				$.ajax({
					type: "POST",
					url: "<? echo base_url(); ?>Game/nfl_games/<? echo $year; ?>/<? echo $week; ?>",
					async: false
				}).success(function(data){
					$('#nfl_games').html(data);
				}).complete(function(){
					/*$.each( $('#open_stats'), function(i, open_stats) {
					   $('div', open_stats).each(function() {
							var box = $(this).attr("data-team");
							$("#" + box).click();
					   });
					})*/
					setTimeout(function(){load_nfl_games();}, 47000);
				})
			};
		
			load_nfl_games()
			
			
		
			
	</script>
    <style>
		.link_color{
			color:white;
		}
	</style>
    <div style="" id="open_stats"></div>
    <div style="" id="open_scores"></div>
    <div style="" id="bench_open_scores"></div>
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