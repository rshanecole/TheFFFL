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


    <div class="col-xs-24 " >
     <div class="panel panel-primary blue_panel-primary " >
        <div class="panel-heading blue_panel-heading">
            <h4 class="panel-title blue_panel-title text-center"><small>Career Stats</small></h4>
        </div>
        <div class="table-responsive">
        <table class="table table-condensed table-striped" style="border-radius:10px;">
            <thead class="" >
                <th class="visible-xs text-center" style="position: absolute; background-color:white; left:11px;
    display: inline-block;border-right:solid #ccc 1px; width:45px;" ><br>Year</th>
    			<th class="hidden-xs text-center" ><br>Year</th>
                <th class="visible-xs text-center" style="padding-left:53px;" >Comp</th>
    			<th class="hidden-xs text-center" >Comp/Att</th>
                <th class="text-center" >Pass<br>Yds</th>
                <th class="text-center" >Pass<br>TD</th>
                <th class="text-center" >Int</th>
                <th class="text-center" >Rush<br>Att/Yds</th>
                <th class="text-center" >Rush<br>TD</th>
                <th class="text-center" >Rec/Yds</th>
                <th class="text-center" >Tgts</th>
                <th class="text-center" >Rec<br>TD</th>
                <th class="text-center" >2-pt</th>
                <th class="text-center" >PR<br>TD</th>
                <th class="text-center" >KR<br>TD</th>
                <th class="text-center" >Fum/Lst</th>
                <th class="text-center" >Fgs<br>Made/Att</th>
                <th class="text-center" >XP<br>Made/Att</th>
            </thead>
            <tbody>
				<? 
                //d($scores);
                foreach($stats as $year=>$data){
                    echo '<tr>';
						echo '<td class="visible-xs" style="position: absolute; background-color:white; left:11px;
    display: inline-block;border-right:solid #ccc 1px;width:45px;">'.$year.'</td>';
						echo '<td class="hidden-xs">'.$year.'</td>';
						echo '<td class="visible-xs text-center" style="padding-left:56px;">'.$data['completions'].'/'.($data['completions']+$data['incompletions']).'</td>';
						echo '<td class="hidden-xs text-center">'.$data['completions'].'/'.($data['completions']+$data['incompletions']).'</td>';
						echo '<td >'.$data['pass_yards'].'</td>';
						echo '<td >'.$data['pass_tds'].'</td>';
						echo '<td >'.$data['interceptions'].'</td>';
						echo '<td >'.$data['rushes'].'/'.$data['rush_yards'].'</td>';
						echo '<td >'.$data['rush_tds'].'</td>';
						echo '<td >'.$data['receptions'].'/'.$data['receiving_yards'].'</td>';
						echo '<td >'.$data['targets'].'</td>';
						echo '<td >'.$data['receiving_tds'].'</td>';
						echo '<td >'.$data['two_point_made'].'</td>';
						echo '<td >'.$data['punt_return_tds'].'</td>';
						echo '<td >'.$data['kick_return_tds'].'</td>';
						echo '<td >'.$data['fumbles'].'/'.$data['fumbles_lost'].'</td>';
						echo '<td >'.$data['fgs_made'].'/'.($data['fgs_made']+$data['fgs_missed']).'</td>';
						echo '<td >'.$data['xps_made'].'/'.($data['xps_made']+$data['xps_missed']).'</td>';
						
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