<?PHP
	/**
	 * transactions year view.
	 *
	 * through ajax will display the given year's transactions
	 */
	//d($this->_ci_cached_vars);
?>

	<script type="text/javascript">

		
			
	</script> 
	
    
    	<div id="draft_selector" class="col-xs-24 page_title ">
			<div class="btn-group">	
            <button class="btn btn-default dropdown-toggle" type="button" id="draft_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span id="dropdown_draft"><? if($week==0){ $display_week='Preseason'; } else { echo 'Week '; $display_week=$week; } echo $display_week; ?> <? echo $day; ?></span>
                <span class="caret"></span>
            </button>
            
            <ul class="dropdown-menu"  aria-labelledby="dropdownMenu1" >
				<? foreach($drafts_array as $draft){ 
					if($draft['week']==0){ $display_week='Preseason'; } else { $display_week=$draft['week']; }?>
                    <li><a href="#" onClick="change_content('<? echo base_url()."Free_Agent/fa_draft/".$league_id."/".$year."/".$draft['week']."/".$draft['day']; ?>',$('#dropdown_title').html())" >
					<? if($display_week!='Preseason'){ ?>Week <? } ?><? echo $display_week; ?> <? echo $draft['day']; ?></a></li>
                
				<? } ?>
            </ul>
       		</div>
   		</div><!--content_selector-->
    <div class="row">
        <div class="col-xs-24 col-md-16 col-md-offset-4">
            <div class="panel panel-primary blue_panel-primary" style="margin-top:5px;" id="transactions_panel">
                <div class="panel-heading blue_panel-heading">
                    <h3 class="panel-title blue_panel-title text-center">
                       <strong>Free Agency Draft Results</strong>
                    </h3>
                </div> 
                <div class="panel-body ">
                    <table class="table table-condensed table-striped table-responsive table-hover">
                        <thead>
                            <th class="text-center col-xs-3 " style="border-bottom:solid #ccc 1px; "><small>Pick</small></th>
                            
                            <th class="text-center col-xs-7" style="border-bottom:solid #ccc 1px; "><small>Team</small></th>
                            <th class="text-center col-xs-7 " style="border-bottom:solid #ccc 1px; " ><small>Player</small></th>
                            <th class="text-center col-xs-7 " style="border-bottom:solid #ccc 1px; " ><small>Released</small></th>
                        </thead>
                        <tbody>
    
                    
                    
                    <?
                
                    foreach($draft_results_array as $data){
    
                        
                           
                            
                            echo '<tr class="" style="border-bottom:solid #ccc 1px; padding-left:0px; padding-right:0px;">';
    							echo '<td class="text-center col-xs-3"><small>'.$data['pick'].'</small></td>';
                                echo '<td class="text-left col-xs-7 "><small>'.team_name_link($data['team_id']).'</small></td>';
                               
                                echo '<td class="text-center col-xs-7 "  ><small>'.player_name_link($data['fffl_player_id'],TRUE,FALSE).'</small></td>';
								
								if($data['released_fffl_player_id']!=0){
									echo '<td class="text-center col-xs-7 "  ><small>'.player_name_link($data['released_fffl_player_id'],TRUE,FALSE).'</small></td>';
								}
    
                            echo '</tr>';
                            
                        
                    } ?>
                        </tbody>
                    </table>
                </div>
            </div>
		</div>
    </div>
    
    </div>
 

       
 


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/