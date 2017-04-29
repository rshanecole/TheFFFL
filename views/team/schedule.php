<?PHP
	/**
	 * schedule view.
	 *
	 * through ajax will display schedule by year
	 */
	//d($this->_ci_cached_vars);
	//$this->output->enable_profiler(TRUE);
?>
	<script type="text/javascript">
		
	</script>
		<div id="year_selector" class="col-xs-24 page_title hidden-sm hidden-md hidden-lg">
			<small>Select a Year:</small><br>			
            <button class="btn btn-default dropdown-toggle" type="button" id="year_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span id="dropdown_year"><? echo $year; ?></span>
                <span class="caret"></span>
            </button>
            
            <ul class="dropdown-menu"  aria-labelledby="dropdownMenu1" >
                <?php
				$select_year = $current_year;
				
                while($select_year>=$first_year)
                {
                    ?><li><a href="#" onClick="change_content('<? echo base_url()."Team/schedule/".$team_id."/".$select_year; ?>',$('#dropdown_title').html())" ><? echo $select_year; ?></a></li>
                
				<?	$select_year--; 
				} ?>
            </ul>
       
    </div><!--content_selector-->
    <div class="row">
    	<div id="schedule" class="col-xs-24 col-sm-16 col-md-12 col-sm-offset-1 col-md-offset-2" >
        	<div class="panel panel-primary blue_panel-primary " >
            	<div class="panel-heading blue_panel-heading">
                    <h3 class="panel-title blue_panel-title">
						<strong><?php echo $year; ?> Schedule</strong></h3><h5>(<? echo $wins_losses['wins'].'-'.$wins_losses['losses'].', '.$points.')'; ?></h5>
                </div>
                <table class="table table-hover table-condensed" id="">
                <tbody > 
                    <tr class="row">
                        <td class="text-center  col-xs-1"><small>Wk</small></td>
                        <td class="text-center  col-xs-9"><small>Opponent</small></td>
                        <td class="text-center  col-xs-4"><small>VS</small></td>
                        <td class="text-center  col-xs-10"><small>Result (Rec)</small></td>
                    </tr>
                    
                <?	$wins=0; $losses=0;
					foreach($team_schedule as $week => $data){
						if($data['wl']=='W') { $wins++; } else { $losses++; }
						
                        	if($week == 14){ ?>
                            	<tr><td colspan="5" class="text-center col-xs-24"><strong>Playoffs</strong></td></tr>
                            <? } ?>
                        <tr class="row">
                        	<td	class="text-center  col-xs-1"><small><? echo $week; ?></small></td>
                            <td	class="text-left  col-xs-9 ellipses" style="max-width:125px;"><strong><small><? echo team_name_link($data['opponent']); ?></small></strong></td>
                            <td	class="text-center  col-xs-4 "><small><? echo $data['vs']['wins'].'-'.$data['vs']['losses']; ?></small></td>
                            <td	class="text-center  col-xs-10 "><small> 
								<? if ($data['wl']!=''){
									echo '
									<div class="hidden-xs col-sm-4">'.$data['wl'].'</div>
									<div class="hidden-xs col-sm-12">'.$data['score'].'</div>
									<div class="col-xs-24 hidden-sm hidden-md hidden-lg" style="min-width:40px;">'.$data['wl'].' '.$data['score'].'</div>
									<div class="col-sm-8 hidden-xs" style="float:right">('.$wins.'-'.$losses.')</div>'; 
								}	?>
                               </small></td>
                        </tr>
					<? } ?>
                </tbody>
               </table> 
               
            </div>
		</div>
        <div id="year_selector" class="col-sm-6 pull-right page_title hidden-xs">
			<small>Select a Year:</small><br>			
            <button class="btn btn-default dropdown-toggle" type="button" id="year_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span id="dropdown_year"><? echo $year; ?></span>
                <span class="caret"></span>
            </button>
            
            <ul class="dropdown-menu"  aria-labelledby="dropdownMenu1" >
                <?php
				$select_year = $current_year;
				
                while($select_year>=$first_year)
                {
                    ?><li><a href="#" onClick="change_content('<? echo base_url()."Team/schedule/".$team_id."/".$select_year; ?>')" ><? echo $select_year; ?></a></li>
                
				<?	$select_year--; 
				} ?>
            </ul>
       </div>
    </div><!--content_selector-->

       


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/