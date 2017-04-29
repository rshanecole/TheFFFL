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
					
            <button class="btn btn-default dropdown-toggle" type="button" id="year_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span id="dropdown_year"><? echo $grouping; ?></span>
                <span class="caret"></span>
            </button>
            
            <ul class="dropdown-menu"  aria-labelledby="dropdownMenu1" >

                    <li><a href="#" onClick="change_content('<? echo base_url()."Standing/regular/".$year; ?>',$('#dropdown_title').html())" >FFFL</a></li>
                    <li><a href="#" onClick="change_content('<? echo base_url()."Standing/regular/".$year."/AFC"; ?>',$('#dropdown_title').html())" >AFC</a></li>
                    <li><a href="#" onClick="change_content('<? echo base_url()."Standing/regular/".$year."/AFC/East"; ?>',$('#dropdown_title').html())" >AFC East</a></li>
                    <li><a href="#" onClick="change_content('<? echo base_url()."Standing/regular/".$year."/AFC/West"; ?>',$('#dropdown_title').html())" >AFC West</a></li>
                    <li><a href="#" onClick="change_content('<? echo base_url()."Standing/regular/".$year."/NFC"; ?>',$('#dropdown_title').html())" >NFC</a></li>
                    <li><a href="#" onClick="change_content('<? echo base_url()."Standing/regular/".$year."/NFC/East"; ?>',$('#dropdown_title').html())" >NFC East</a></li>
                    <li><a href="#" onClick="change_content('<? echo base_url()."Standing/regular/".$year."/NFC/West"; ?>',$('#dropdown_title').html())" >NFC West</a></li>
                
				
            </ul>
       
    </div><!--content_selector-->
    	<div id="standings" class="col-xs-24 col-sm-10 col-md-10" >
        	<div class="panel panel-primary blue_panel-primary " >
            	<div class="panel-heading blue_panel-heading">
                    <h3 class="panel-title blue_panel-title">
						<strong><?php echo $year.' '.$grouping; ?> Standings</strong></h3><h5></h5>
                </div>
                <table class="table table-hover table-condensed" id="">
                <tbody > 
                    <tr class="row">
                        <td class="text-center  col-xs-1"><small></small></td>
                        <td class="text-center  col-xs-12"><small>Team</small></td>
                        <td class="text-center  col-xs-1"><small>W</small></td>
                        <td class="text-center  col-xs-1"><small>L</small></td>
                        <td class="text-center  col-xs-4"><small>Pts</small></td>
                       <? if($current_year ==$year){ ?>
                        <td class="text-center  col-xs-1"><small>Str</small></td>
                        <? } ?>
                    </tr>
                    <?
					foreach($standings as $data){
						?>
                    <tr class="row <? if ($data['playoffs']==TRUE) { echo 'bg-info'; } ?> ">
                    	<td class="text-center vertical_center col-xs-2"><small>
                        <?
							$image_properties = array(
								'src'   => $data['team_logo_path'],
								'class' => 'img-responsive',
							);
							echo img($image_properties);
						?>
                        </small></td>
                        <td class="text-left  col-xs-12 ellipses vertical_center" style="max-width:115px"><small><? echo team_name_link($data['team_id']); ?> </small></td>
                        <td class="text-center  col-xs-1 vertical_center"><small><? echo $data['wins']; ?></small></td>
                        <td class="text-center  col-xs-1 vertical_center"><small><? echo $data['losses']; ?></small></td>
                        <td class="text-center  col-xs-4 vertical_center"><small><? echo $data['points']; ?></small></td>
                         <? if($current_year ==$year){ ?>
                        <td class="text-center  col-xs-1 vertical_center"><small><? 
							if($data['streak']<0){
								echo 'L'.abs($data['streak']);	
							}
							else{
								echo 'W'.$data['streak'];
								
							}?></small></td>
                        <? } ?>
                    
                    </tr>	
						
					<? }
					
					
					?>
                
                </tbody>
               </table> 
               
            </div>
		</div>
        <div id="points_standings" class="col-xs-24 col-sm-10 col-md-10" >
        	<div class="panel panel-primary blue_panel-primary " >
            	<div class="panel-heading blue_panel-heading">
                    <h3 class="panel-title blue_panel-title">
						<strong><?php echo $year.' '.$grouping; ?> Scoring</strong></h3><h5></h5>
                </div>
                <table class="table table-hover table-condensed" id="">
                <tbody > 
                    <tr class="row">
                        <td class="text-center  col-xs-1"><small></small></td>
                        <td class="text-center  col-xs-12"><small>Team</small></td>
                        <td class="text-center  col-xs-1"><small>W</small></td>
                        <td class="text-center  col-xs-1"><small>L</small></td>
                        <td class="text-center  col-xs-4"><small>Pts</small></td>
                       <? if($current_year ==$year){ ?>
                        <td class="text-center  col-xs-1"><small>Str</small></td>
                        <? } ?>
                    </tr>
                    <?
					foreach($points_standings as $data){
						?>
                    <tr class="row <? if ($data['playoffs']==TRUE) { echo 'bg-info'; } ?> ">
                    	<td class="text-center vertical_center col-xs-2"><small>
                        <?
							$image_properties = array(
								'src'   => $data['team_logo_path'],
								'class' => 'img-responsive',
							);
							echo img($image_properties);
						?>
                        </small></td>
                        <td class="text-left  col-xs-12 ellipses vertical_center" style="max-width:115px"><small><? echo team_name_link($data['team_id']); ?> </small></td>
                        <td class="text-center  col-xs-1 vertical_center"><small><? echo $data['wins']; ?></small></td>
                        <td class="text-center  col-xs-1 vertical_center"><small><? echo $data['losses']; ?></small></td>
                        <td class="text-center  col-xs-4 vertical_center"><small><? echo $data['points']; ?></small></td>
                         <? if($current_year ==$year){ ?>
                        <td class="text-center  col-xs-1 vertical_center"><small><? 
							if($data['streak']<0){
								echo 'L'.abs($data['streak']);	
							}
							else{
								echo 'W'.$data['streak'];
								
							}?></small></td>
                        <? } ?>
                    
                    </tr>	
						
					<? }
					
					
					?>
                
                </tbody>
               </table> 
               
            </div>
		</div>
        <div id="year_selector" class="col-sm-4 pull-right page_title hidden-xs">
			<small></small><br>			
            <button class="btn btn-default dropdown-toggle" type="button" id="group_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span id="dropdown_group"><? echo $grouping; ?></span>
                <span class="caret"></span>
            </button>
            
            <ul class="dropdown-menu"  aria-labelledby="dropdownMenu1" >

                    <li><a href="#" onClick="change_content('<? echo base_url()."Standing/regular/".$year; ?>')" >FFFL</a></li>
                    <li><a href="#" onClick="change_content('<? echo base_url()."Standing/regular/".$year."/AFC"; ?>')" >AFC</a></li>
                    <li><a href="#" onClick="change_content('<? echo base_url()."Standing/regular/".$year."/AFC/East"; ?>')" >AFC East</a></li>
                    <li><a href="#" onClick="change_content('<? echo base_url()."Standing/regular/".$year."/AFC/West"; ?>')" >AFC West</a></li>
                    <li><a href="#" onClick="change_content('<? echo base_url()."Standing/regular/".$year."/NFC"; ?>')" >NFC</a></li>
                    <li><a href="#" onClick="change_content('<? echo base_url()."Standing/regular/".$year."/NFC/East"; ?>')" >NFC East</a></li>
                    <li><a href="#" onClick="change_content('<? echo base_url()."Standing/regular/".$year."/NFC/West"; ?>')" >NFC West</a></li>
                
				
            </ul>
       
    </div><!--content_selector-->

       


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/