<?PHP
	/**
	 * player scoring view.
	 *
	 * through ajax will display the player's historical scoring
	 */
//d($this->_ci_cached_vars);
?>

	<script type="text/javascript">


			
	</script> 


    <div class="col-xs-24 col-md-14 col-md-offset-5" >
     <div class="panel panel-primary blue_panel-primary " >
        <div class="panel-heading blue_panel-heading">
            <h4 class="panel-title blue_panel-title text-center"><small>Transaction History</small></h4>
        </div>
        <div class="table-responsive">
        <table class="table table-condensed table-striped" style="border-radius:10px;">
            <thead class="" >
                <th class="visible-xs text-center" style="position: absolute; background-color:white; left:11px;
    display: inline-block;border-right:solid #ccc 1px; width:45px;" ><small>Year</small></th>
    			<th class="hidden-xs text-center" ><small>Year</small></th>
                <th class="visible-xs text-center" style="padding-left:56px;" ><small>Team</small></th>
    			<th class="hidden-xs text-center" ><small>Team</small></th>
                <th class=" text-center" ><small>Transaction</small></th>
                <th class="text-center" ><small>Time</small></th>
            </thead>
            <tbody>
				<? 
                //d($scores);
                foreach($transactions_array as $data){
					if($data['transaction_type']=='Release'){
						$transaction = 'Released';
					}
					elseif($data['transaction_type']=='FA'){
						$transaction = 'Signed Free Agent';
					}
					elseif($data['transaction_type']=='Add PS'){
						$transaction = 'Signed to PS';
					}
					elseif($data['transaction_type']=='Add PUP'){
						$transaction = 'Signed to PUP';
					}
					elseif($data['transaction_type']=='Activate PS'){
						$transaction = 'Activated from PS';
					}
					elseif($data['transaction_type']=='Activate PUP'){
						$transaction = 'Activated from PUP';
					}
					elseif($data['transaction_type']=='Trade'){
						$transaction=$data['text'];	
					}
                    echo '<tr>';
						echo '<td class="visible-xs" style="position: absolute; background-color:white; left:11px;
    display: inline-block;border-right:solid #ccc 1px;width:45px;"><small>'.date('Y',$data['time']).'</small></td>';
						echo '<td class="hidden-xs"><small>'.date('Y',$data['time']).'</small></td>';
						if(isset($data['team_id'])){
							echo '<td class="visible-xs text-center" style="padding-left:56px;"><small>'.team_name_link($data['team_id']).'</small></td>';
							echo '<td  class="hidden-xs text-center"><small>'.team_name_link($data['team_id']).'</small></td>';
							echo '<td class="text-center"><small>'.$transaction.'</small></td>';
						}
						else {
							echo '<td colspan=2 class="text-center"><small>'.$transaction.'</small></td>';
						}
						
						
						echo '<td nowrap ><small>'.date('M j, g:ia',$data['time']).'</small></td>';

					echo '</tr>';
				
                } ?>
              </tbody>
		</table>
        </div>
	  </div>
    </div>


       
 


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/