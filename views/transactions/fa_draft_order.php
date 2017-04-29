<?PHP
	/**
	 * transactions year view.
	 *
	 * through ajax will display the given year's transactions
	 */
	d($this->_ci_cached_vars);
?>

	<script type="text/javascript">

		
			
	</script> 

    <div class="row">
    	<div class="col-xs-24">
            <div class="panel panel-primary blue_panel-primary" style="margin-top:5px;" id="transactions_panel">
                <div class="panel-heading blue_panel-heading">
                    <h3 class="panel-title blue_panel-title">
                       <strong>Free Agency Draft Order</strong>
                    </h3>
                </div> 
                <div class="panel-body ">
                    <table class="table table-condensed table-striped table-responsive table-hover">
                        <thead>
                            <th class="text-center col-xs-8 col-sm-6" style="border-bottom:solid #ccc 1px; "><small>Pick</small></th>
                            
                            <th class="text-center col-xs-2 col-sm-2" style="border-bottom:solid #ccc 1px; "><small>Team</small></th>
                            <th class="text-center col-xs-11 col-sm-12" style="border-bottom:solid #ccc 1px; " ><small>Last</small></th>
                        </thead>
                        <tbody>
    
                    
                    
                    <?
                
                    foreach($fa_draft_order as $pick =>$data){
    
                        
                           
                            
                            echo '<tr class="" style="border-bottom:solid #ccc 1px; padding-left:0px; padding-right:0px;">';
    							echo '<td class="text-center col-xs-2"><small>'.$pick.'</small></td>';
                                echo '<td class="text-left col-xs-10 col-sm-6"><small>'.team_name_link($data['team_id']).'</small></td>';
                               
                                echo '<td class="text-center col-xs-3 col-sm-4"  ><small>'.$data['last'].'</small></td>';
    
                            echo '</tr>';
                            
                        
                    } ?>
                    </tbody>
                </table>
            </div>
		</div>
    </div>
    </div>

       
 


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/