<?PHP
	/**
	 * franchise view.
	 *
	 * through ajax will display past franchise selections and add a link to 
	 * select new franchise if that is sent from controller
	 */
	//d($this->_ci_cached_vars);
?>
	<script type="text/javascript">
		
	</script>
	<? 
	function create_franchise_table($selections,$year,$current_year,$week) {

		//create the outer container of the table first
		if($week==0 && $year==$current_year ){
			$panel='red';
		}
		else {
			$panel='blue';
		}
		?>
        <div class="panel panel-primary <? echo $panel; ?>_panel-primary" >
        	<div class="panel-heading <? echo $panel; ?>_panel-heading">
            	<h3 class="panel-title <? echo $panel; ?>_panel-title"><? echo $year; ?></h3>
            </div>
            <table class="table table-hover table-condensed" id="">
                <tbody > 
                    <tr >
                        <td class="text-center col-xs-16"><small>Player</small></td>
                        <td class="text-center col-xs-4"><small>Salary</small></td>
                        
                    </tr>
				
				<?
        
		//add rows for each individual player 
		$position_array = array('QB','RB','WR','TE','K');
		foreach($position_array as $position){
			if(isset($selections['Roster'][$position])){              
				foreach($selections['Roster'][$position] as $fffl_player_id => $data_array){
					
					//create the display table
					echo '<tr >';
						
						echo '<td class="text-left  col-xs-16">';
							echo '<small>'.$position.' <strong>'.player_name_link($fffl_player_id,FALSE,FALSE).'</strong></small> ';
							
						echo '</td>';
						echo '<td class="text-center col-xs-4">';
							echo '<small>'.$data_array['salary'].'</small>';
						echo '</td>';
						echo '<td class="text-center  col-xs-4">';
							echo '<small></small>';
						echo '</td>';
						
					echo '</tr>';
					
				}//end foreach of each individual player's row in roster area
			}//end if isset position
		}//foreach position
		if(isset($selections['PUP'])){
			foreach($selections['PUP'] as $position => $data_array){
				foreach($data_array as $fffl_player_id => $data){
					//create the display table
					echo '<tr >';
						
						echo '<td class="text-left  col-xs-16">';
							echo '<small>'.$position.' <strong>'.player_name_link($fffl_player_id,FALSE,FALSE).'</strong></small> ';
							
						echo '</td>';
						echo '<td class="text-center col-xs-4">';
							echo '<small>'.$data['salary'].'</small>';
						echo '</td>';
						echo '<td class="text-center  col-xs-4">';
							echo '<small>PUP</small>';
						echo '</td>';
						
					echo '</tr>';
				}
					
			}//end foreach of each individual player's row in pup area
		}//if isset PUP
		if(isset($selections['PS'])){
			foreach($selections['PS'] as $position => $data_array){
				foreach($data_array as $fffl_player_id => $data){
					//create the display table
					echo '<tr >';
						
						echo '<td class="text-left  col-xs-16">';
							echo '<small>'.$position.' <strong>'.player_name_link($fffl_player_id,FALSE,FALSE).'</strong></small> ';
							
						echo '</td>';
						echo '<td class="text-center col-xs-4">';
							echo '<small>'.$data['salary'].'</small>';
						echo '</td>';
						echo '<td class="text-center  col-xs-4">';
							echo '<small>PS</small>';
						echo '</td>';
						
					echo '</tr>';
				}
					
			}//end foreach of each individual player's row in ps area
		}//isset PS
		
		
		//close table container
		?>
                </tbody>
            </table>
        </div>
		<?
	}//end create table function
	
	//create link to launch modal
	if($franchise_open==1 && ($team_id==$_SESSION['team_id'] || $_SESSION['security_level']==3)){
	
		
		?> 
        <script type="text/javascript">
			var modal = "<!--franchise modal -->\
						 <!--data loaded into elements by the button that launches this on the actual franchise page-->\
						<div class='modal fade' id='franchise_modal' tabindex='-1' role='dialog' aria-labelledby='franchise_modal_Label'>\
						  <div class='modal-dialog' role='document'>\
							<div class='modal-content'>\
							  <div class='modal-header'>\
								<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>\
								<h4 class='modal-title' id='franchise_modal_Label'>Select Franchise Players</h4>\
							  </div>\
							  <div class='modal-body'><div id='test_area'></div>\
								Select your franchise players. Total salary must be <span id='salary_cap'></span> or less.\
							  </div>\
							  <div class='col-xs-24' id='franchise_list'>\
							  </div>\
							  <div class='modal-footer'>\
							  </div>\
							</div>\
						  </div>\
						</div>";
			jQuery(function() { $('#modal_area').html(modal);});
		
		
		
			$("#franchise_modal_button").on("click",function(){
				//display the league's salary cap in the instructions
				$("#salary_cap").html('<? echo $league_salary_cap; ?>');
				//load the franchise_list
				var path = '<? echo base_url().'Team/load_franchise_list/'.$team_id; ?>';
				$('#franchise_list').load(path);
				
			});

		</script>
        <!-- Button trigger modal -->
       
            <div style="margin-bottom:5px;">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#franchise_modal" id="franchise_modal_button">
                  Select Franchise Players
                </button>
            </div>
     
       
	<?
    }
	
	if(empty($franchise_history)){
		echo '<div><strong>No Franchise History</strong></div>';
	}
	//because bootstrap grid doesn't clear left correctly clearfix has to
	//be used. since these are created dynamically, I'll need to create
	//the visible clearfixes programatically
	$clearfix_count=0;
	foreach($franchise_history as $franchise_year => $selections){ 
		if(($week==0 && $franchise_year==$year ) && ($team_id!=$_SESSION['team_id'])){
			continue;
		}
        $clearfix_count++;?>
    	<div class="col-xs-24 col-sm-12 col-md-8">
        	
                <? 
                    create_franchise_table($selections,$franchise_year,$year,$week);
                ?>

		</div>
        <?
		if($clearfix_count %2 == 0){  echo '<div class="clearfix visible-sm-block"></div>'; }
		if($clearfix_count %3 == 0){  echo '<div class="clearfix visible-md-block visible-lg-block"></div>'; }
        
    } ?>


<?PHP
/*End of file franchise.php*/
/*Location: ./application/veiws/Team/franchise.php*/