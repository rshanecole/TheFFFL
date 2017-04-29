<?PHP
	/**
	 * player draft view.
	 *
	 * through ajax will display the player's historical draft
	 */
//d($this->_ci_cached_vars);
?>

	<script type="text/javascript">


			
	</script> 


    <div class="col-xs-24 " >
     <div class="panel panel-primary blue_panel-primary " >
        <div class="panel-heading blue_panel-heading">
            <h4 class="panel-title blue_panel-title text-center"><small>Draft History</small></h4>
        </div>
        <div class="table-responsive">
        <table class="table table-condensed table-striped" style="border-radius:10px;">
            <thead class="" >
                <th class="visible-xs text-center" style="position: absolute; background-color:white; left:11px;
    display: inline-block;border-right:solid #ccc 1px; width:45px;" >Year</th>
    			<th class="hidden-xs text-center" >Year</th>
                <th class="visible-xs text-center" style="padding-left:56px;" >Team</th>
    			<th class="hidden-xs text-center" >Team</th>
                <th class=" text-center" colspan="2" >Pick</th>
              	<th class=" text-center" >Team</th>
              	<th class=" text-center" colspan="2" >Pick</th>
            </thead>
            <tbody>
				<? 
                //d($scores);
                foreach($draft as $year => $teams){
                    echo '<tr>';
						echo '<td class="visible-xs" style="position: absolute; background-color:white; left:11px;display: inline-block;border-right:solid #ccc 1px;width:45px;"><a href="'.base_url().'Draft/results/'.$year.'">'.$year.'</a></td>';
						echo '<td class="hidden-xs"><a href="'.base_url().'Draft/results/'.$year.'">'.$year.'</a></td>';
                  	foreach($teams as $team_id => $data){
                   
						echo '<td class="visible-xs text-left" style="padding-left:56px;" >'.team_name_link($team_id).'</td>';
                      
						echo '<td class="hidden-xs text-left" >'.team_name_link($team_id).'</td>';
						echo '<td class="text-center"><a href="'.base_url().'Draft/results/'.$year.'">'.$data['pick_number'].'</a></td><td class="text-center">';
                      	if($data['draft_type']=='Supplemental' ){ echo 'Supp'; }
                      echo '</td>';
					
                    }

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