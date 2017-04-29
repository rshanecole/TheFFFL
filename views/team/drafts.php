<?PHP
	/**
	 * drafts view.
	 *
	 * through ajax will display past draft selections 
	 */
	//d($this->_ci_cached_vars);
?>
	<script type="text/javascript">
		/*example from roster page
			//add a player to lineup
			$("#Bench_table .glyphicon-plus-sign").on("click",function(){
				$(this).parent().html('<? //$image_properties = array('src' => base_url().'assets/img/loading.gif', 'height'=>'11'); echo img($image_properties); ?>');
				path = 	'<? //echo base_url().'Team/add_player_starting_lineup/'.$team_id.'/'.$year.'/'.$week.'/'; ?>' + this.id;
				change_content(path,'Roster');
			});
		*/
	</script>
	<? 
	function max_with_key($array, $key) {
        if (!is_array($array) || count($array) == 0) return false;
        $max = $array[0][$key];
        foreach($array as $a) {
			if($a['type']=='Common'){
				if($a[$key] > $max) {
					$max = $a[$key];
				}
			}
        }
        return $max;
    }
	
	function create_draft_table($selections,$year,$current_year,$week,$team_id) {
      

		//create the outer container of the table first
		if(($week==0 && $year==$current_year) || $year==($current_year+1) ){
			$panel='red';
		}
		else {
			$panel='blue';
		}
		$max_draft_id = max_with_key($selections,'draft_id');
		?>
        <div class="panel panel-primary <? echo $panel; ?>_panel-primary" >
        	<div class="panel-heading <? echo $panel; ?>_panel-heading">
            	<h3 class="panel-title <? echo $panel; ?>_panel-title"><? echo $year; ?></h3>
            </div>
            <table class="table table-hover table-condensed" id="">
                <tbody > 
                    <tr >
                    	<td class="text-center col-xs-1"><small>Rd</small></td>
                        <td class="text-center col-xs-4" colspan="2"><small>Pick</small></td>
                        <td class="text-center col-xs-18"><small>Player</small></td>
                        
                        
                    </tr>
				
				<?
        
		//add rows for each individual player 
		
             
		foreach($selections as $data_array){
			if($data_array['type']=='Common'){
				
				if($data_array['original_owner']!=$team_id){
					$traded = '<sup><small><span class="glyphicon glyphicon-transfer" aria-hidden="true"></span></small></sup>';
				}
				else {
					$traded = '';
				}
				//create the display table
				echo '<tr >';
					echo '<td class="text-center col-xs-1">';
						echo '<small>'.$data_array['round'].'</small>';
					echo '</td>';
					echo '<td class="text-center col-xs-1">';
					  if($data_array['draft_id']!=$max_draft_id){
						echo '<small>'.$data_array['pick_number'].$traded.'</small>';
					  }
					echo '</td>';
					echo '<td class="text-center col-xs-1">';
					  if($data_array['draft_id']==$max_draft_id){
						echo '<small>'.$data_array['pick_number'].$traded.'</small>';
					  }
					echo '</td>';
					echo '<td class="text-left col-xs-18">';
						if($data_array['fffl_player_id'] && $data_array['fffl_player_id']>-1){
							echo '<small><strong>'.player_name_link($data_array['fffl_player_id'],TRUE,FALSE).'<strong></small> ';
						}
					echo '</td>';
	
					
				echo '</tr>';
			}
			
		}//end foreach of each individual player's row 
		$initial=1;
		foreach($selections as $data_array){
			
			if($data_array['type']=='Supplemental'){
				if($initial ==1){ echo '<tr><td colspan=4 class="text-center">Supplemental Draft</td></tr>'; $initial++; }
				//create the display table
				echo '<tr >';
					echo '<td class="text-center col-xs-1">';
						echo '<small>'.$data_array['round'].'</small>';
					echo '</td>';
					echo '<td class="text-center col-xs-1">';
						echo '<small>'.$data_array['pick_number'].'</small>';
					echo '</td>';
					echo '<td class="text-center col-xs-1">';
						 echo '';
					echo '</td>';
					echo '<td class="text-left  col-xs-16">';
					if($data_array['fffl_player_id']){
						echo '<small>'.player_name_link($data_array['fffl_player_id'],TRUE,FALSE).'</strong></small> ';
					}
					echo '</td>';
	
					
				echo '</tr>';
			}
			
		}//end foreach of each individual player's row 


		
		
		//close table container
		?>
                </tbody>
            </table>
        </div>
		<?
	}//end create table function
	
	if(empty($draft_history)){
		echo '<div><strong>No Draft History</strong></div>';
	}
	//because bootstrap grid doesn't clear left correctly clearfix has to
	//be used. since these are created dynamically, I'll need to create
	//the visible clearfixes programatically
	$clearfix_count=0;
	foreach($draft_history as $draft_year => $selections){ 
		
        $clearfix_count++;?>
    	<div class="col-xs-24 col-sm-12 col-md-8">
        	
                <? 
                    create_draft_table($selections,$draft_year,$year,$week,$team_id);
                ?>

		</div>
        <?
		if($clearfix_count %2 == 0){  echo '<div class="clearfix visible-sm-block"></div>'; }
		if($clearfix_count %3 == 0){  echo '<div class="clearfix visible-md-block visible-lg-block"></div>'; }
        
    } ?>


<?PHP
/*End of file franchise.php*/
/*Location: ./application/veiws/Team/franchise.php*/