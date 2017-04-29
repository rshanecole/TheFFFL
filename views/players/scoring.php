<?PHP
	/**
	 * player scoring view.
	 *
	 * through ajax will display the player's historical scoring
	 */
	//d($this->_ci_cached_vars);
?>

	<script type="text/javascript">
		//stats popover
			$('.stats_button').popover({
				html: true,
				trigger: 'click',
				placement: 'top',
				title: '' + '',
				container: 'body',
				
				content: function() {
					var id = <? echo $fffl_player_id; ?>;
					var week = $(this).attr('data-week');
					var year = $(this).attr('data-year');
				  return $.ajax({url: 'http://fantasy.thefffl.com/Player/stats_info/'+id+'/'+year+'/'+week,
								 dataType: 'html',
								 async: false}).responseText;
				}
			  }).click(function(e) {
				
				$(this).popover('toggle');
				
			});
		  
			$(document).on("click",function() {	
				$('body').on('click', function (e) {
					
					$('[data-original-title]').each(function () { 
						//the 'is' for buttons that trigger popups
						//the 'has' for icons within a button that triggers a popup
						if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
							$(this).popover('hide');
							
						}
					});
				});
			});
			
		//emd stats popover

			
	</script> 
     
        <div class="col-xs-24 text-center" >
            
    
        </div>
        <div class="col-xs-24 " >
           
         <div class="panel panel-primary blue_panel-primary " >
            <div class="panel-heading blue_panel-heading">
                <h4 class="panel-title blue_panel-title text-center"><small><? 
                    if($single_year){ echo 'Scoring'; } else { echo 'Career Scoring'; } ?></small></h4>
            </div>
            <div class="table-responsive">
            <table class="table table-condensed table-striped" style="border-radius:10px;">
                <thead class="" >
                    <th class="visible-xs text-center" style="position: absolute; background-color:white; left:11px;
        display: inline-block;border-right:solid #ccc 1px; width:45px;" >Year</th>
                    <th class="hidden-xs text-center" >Year</th>
                    <th class="visible-xs text-center" style="padding-left:53px;">Team</th>
                    <th class="hidden-xs text-center" >Team</th>
                    <th class=" text-center"  >Avg</th>
                   
                    <th class="text-center" >Rk</th>
                    <th class="text-center" >1</th>
                    <th class="text-center" >2</th>
                    <th class="text-center" >3</th>
                    <th class="text-center" >4</th>
                    <th class="text-center" >5</th>
                    <th class="text-center" >6</th>
                    <th class="text-center" >7</th>
                    <th class="text-center" >8</th>
                    <th class="text-center" >9</th>
                    <th class="text-center" >10</th>
                    <th class="text-center" >11</th>
                    <th class="text-center" >12</th>
                    <th class="text-center" >13</th>
                    <th class="text-center" >14</th>
                    <th class="text-center" >15</th>
                    <th class="text-center" >16</th>
                </thead>
                <tbody>
                    <? 
                    //d($scores);
                    foreach($scores as $year=>$data){
                        if($data){
                        if(isset($data['rank'])){
                            $rank = $data['rank'];	
                        }
                        else{
                            $rank = $this->session->userdata['ranks'][$year];
                        }
                        echo '<tr>';
                            echo '<td class="visible-xs" style="position: absolute; background-color:white; left:11px;
        display: inline-block;border-right:solid #ccc 1px;width:45px;">'.$year.'</td>';
                            echo '<td class="hidden-xs">'.$year.'</td>';
							echo '<td class="text-center visible-xs" style="padding-left:53px;">'.$data['team'].'</td>';
							echo '<td class="text-center hidden-xs">'.$data['team'].'</td>';
                            echo '<td class="">'.$data['average'].'</td>';
                            echo '<td class="text-center"><a href="'.base_url().'Player/rankings/'.$year.'">'.$rank.'</a></td>';					
                            
                                foreach($data['weeks'] as $week=>$points_opponent){
                                   
                                    echo '<td class="text-center" >';
                                    if($points_opponent['player_opponent']=='Bye'){
                                        echo 'Bye';
                                    } 
                                    else {
                                        echo '<span class="pointer stats_button" data-week="'.$week.'" data-year="'.$year.'">'.$points_opponent['points'].'</span>';
                                    }
                                    echo '</td>';	
                                }
                            
                        echo '</tr>';
                        }
                    } ?>
                  </tbody>
            </table>
            </div>
          </div>
       </div>
    


       
 


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/