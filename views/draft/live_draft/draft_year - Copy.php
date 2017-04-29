<?PHP
	/**
	 * draft year view.
	 *
	 * through ajax will display the results of the given year's draft
	 */
	//d($this->_ci_cached_vars);
?>
	<script type="text/javascript">
		$(document).ready(function() {
			$(".click_name").on("click",function() {
				$('tr').removeClass('alert-danger');
				var id = $(this).attr('id');
				$('.'+id).addClass("alert");
				$('.'+id).addClass("alert-danger");
				$('#dropdown_teams').text($(this).text());
				
			});
			
			$(".draft_button").on("click",function() {
				$('#test_area').html('asdf');
				$('#supplemental_drafts').toggle();
				$('#supplemental_button').toggle();
				$('#common_drafts').toggle();
				$('#common_button').toggle();
				
	
			});
		});
	</script>

<? 
	function create_draft_table($selections,$viewer_team_id) {

		//create the outer container of the table first
		?>
        
		
        
            <table class="table table-hover table-condensed" id="">
                <tbody > 
                    <tr >
                    	<td class="text-center  col-xs-1"><small>Rd</small></td>
                        <td class="text-center  col-xs-1"><small>Pick</small></td>
                        <td class="text-center  col-xs-10"><small>Team</small></td>
                        <td class="text-center  col-xs-10"><small>Player</small></td>
                    </tr>
		<?
        
		foreach($selections as $pick_number => $data_array){
			
			if($data_array['original_owner']!=$data_array['team_id']){
				$traded = '<sup><small><span class="glyphicon glyphicon-transfer" aria-hidden="true"></span></small></sup>';
			}
			else {
				$traded = '';
			}
			//create the display table
			$alert="";
			if($data_array['team_id']==$viewer_team_id){
				$alert="alert alert-info";
			}
			
			echo '<tr class="'.$data_array['team_id'].' '.$alert.'" >';
				echo '<td class="text-center col-xs-1">';
					echo '<small>'.$data_array['round'].'</small>';
				echo '</td>';
				echo '<td class="text-center col-xs-1">';
				  	echo '<small>'.$pick_number.'</small>';
				echo '</td>';
				echo '<td class="text-left col-xs-10 ellipses">';
				  	echo '<small><a href="'.base_url().'Team/id/'.$data_array['team_id'].'">'.$data_array['team_name'].$traded.'</a></small>';
				echo '</td>';
				echo '<td class="text-left  col-xs-10">';
					if($data_array['fffl_player_id']){
						echo '<small>'.player_name_link($data_array['fffl_player_id'],TRUE,FALSE).'</small>';
					}
				echo '</td>';

				
			echo '</tr>';
			
			
		}//end foreach of each individual player's row 
		
		//close table container
		?>
                </tbody>
            </table>
        
		<?
	}
?>

<div id="drafts">
    <div class="row">
        <div class="text-center col-xs-24">
            <h3><strong><? echo $year; ?> Drafts</strong></h3>
        </div>
        <div class="text-center col-xs-24 draft_button pointer"  id="supplemental_button">
            <div ><strong>League Draft</strong></div>
            <div>Go to: Supplemental Draft</div>
        </div>
        <div class="text-center col-xs-24 draft_button pointer" style="display:none" id="common_button">
             <div ><strong>Supplemental Draft</strong></div>
            <div>Go to: League Draft</div>
        </div>
    </div>
    	
         <div id="" class="dropdown" style="margin:5px;" >
         	<div class="btn-group">
                <button class="btn btn-default dropdown-toggle" type="button" id="team_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <span id="dropdown_teams">Select Team</span>
                    <span class="caret"></span>
                </button>
                
                <ul class="dropdown-menu"  aria-labelledby="dropdownMenu1" >
                   <? 
                       foreach($all_teams_id_name as $team_id_ => $name){//team_id is already used for the viewer; added _ to differentiate
                            echo '<li><a href="#" id="'.$team_id_.'" class="click_name"><small>'.$name.'</small></a></li>';   
                           
                       }
                    ?>
                </ul>
        	</div>      
		</div>
       
        <div id="common_drafts">
        <? foreach($draft_picks_array['Common'] as $selections){ ?>
            <div id="" class="col-xs-24 col-sm-24 col-md-12" >
                <div class="panel panel-primary blue_panel-primary" >
                    <div class="panel-heading blue_panel-heading">
                        <h3 class="panel-title blue_panel-title">
                            <strong><? echo date('D M j, g A', $selections['start_time']);?></strong>
                        </h3>
                    </div>
                    <?
                        create_draft_table($selections['picks'],$team_id);
                    ?>
                </div>
            </div>
            <div class="clearfix visible-sm-block"></div>
        <? } ?>
        </div>
        <div id="supplemental_drafts" style="display:none">
        <? foreach($draft_picks_array['Supplemental'] as $selections){ ?>
            <div id="" class="col-xs-24 col-sm-24 col-md-12" >
                <div class="panel panel-primary blue_panel-primary" >
                    <div class="panel-heading blue_panel-heading">
                        <h3 class="panel-title blue_panel-title">
                            <strong><? echo date('D M j, g A', $selections['start_time']);?></strong>
                        </h3>
                    </div>
                    <?
                        create_draft_table($selections['picks'],$team_id);
                    ?>
                </div>
            </div>
            <div class="clearfix visible-sm-block"></div>
        <? } ?>
        </div>
       
   </div> <!--drafts-->    


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/