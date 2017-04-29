<?PHP
	/**
	 * player container view.
	 *
	 * through ajax will display a player's data
	 */
	//d($this->_ci_cached_vars);
	
?>

	<script type="text/javascript">
		
		
		$(document).ready(function() {
			var width = $("#news").width() - 10 + 'px';
			$(".ellipses").css("width",width) ;
			
			
		});
		
		
		//launch the rss modal
		var modal = "<!--rss item modal -->\
					<!--data loaded into elements by the button that launches this on the actual trade list page-->\
					<div class='modal fade' id='rss_modal' tabindex='-1' role='dialog' aria-labelledby='rss_modal_Label'>\
						<div class='modal-dialog' role='document'>\
							<div class='modal-content'>\
								<div class='modal-header'>\
								<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>\
									<h4 class='modal-title' id='rss_modal_Label'></h4>\
								</div>\
								<div class='modal-body'>\
									<div id='story'>\
									</div>\
								</div>\
								<div class='modal-footer'>\
								</div>\
							</div>\
						</div>\
					</div>";
			jQuery(function() {
				$('#modal_area').html(modal);

				$(".news_item").on("click", function() {
						var description = $(this).attr("description");
						var path = $(this).attr("link");
						var source = $(this).attr("source");
						var title = $(this).attr("title");
						var date = $(this).attr("date");
						$('#rss_modal_Label').html(title);
						$('#story').html(date + ' (' + source + ')<br>' + description + '... <a href="' + path + '" target="_new">Continue Reading</a>' );
				});	
				
				$(".show_more_btn").on("click",function() {
						$(".show_more").toggle();
						$(".show_more_btn").toggle();
					
				});
			});
			
			
		//add fa to requests
			function add_fa(player){
				
				$("#fa_"+player).attr('src','<? echo base_url().'assets/img/loading.gif'; ?>').attr('height','11').attr('width','11');
				$.ajax({
					url: '<? echo base_url(); ?>Free_Agent/add_fa_request/' + player,
					type: "POST",
					success: function() {
						$("#fa_"+player).addClass('fade').attr('src','<? echo base_url().'assets/img/add_fa.gif'; ?>').removeAttr('onClick').attr('width','40px').attr('height','18px');
							
					}
				});
			}
			
			//add open fa 
			function add_open_fa(player){
				
				$("#open_fa_"+player).attr('src','<? echo base_url().'assets/img/loading.gif'; ?>').attr('height','11').attr('width','11').off("click");
				$.ajax({
					url: '<? echo base_url(); ?>Free_Agent/add_open_fa/' + player,
					type: "POST",
					success: function() {
						$("#fa_"+player).addClass('fade').attr('src','<? echo base_url().'assets/img/add_fa.gif'; ?>').removeAttr('onClick').attr('width','40px').attr('height','18px');
						$("#open_fa_"+player).hide();
							
					}
				});
			}

	</script>

        <div id="content_area" class="">
        	<div class="row">
            	<!--player pickture-->
            	<div class="col-xs-6 col-sm-4">
            	<?php
            			$image_properties = array(
							'src' => 'http://static.nfl.com/static/content/public/static/img/fantasy/transparent/200x200/'.$player_data['nfl_esbid'].'.png',
							'class' => 'img-responsive img-thumbnail',
							
            			);
            			echo img($image_properties); ?>
				</div>
                <!--player team pick, and palyer data-->
				<div class="col-xs-18 col-sm-10 "  >
                    <div class="row row-eq-height">
                        <div class="col-xs-6" >
                            
                            <? if($player_data['current_team'] != 'FA' && $player_data['current_team'] != 'RET') 
                            {
                                $image_properties = array(
                                    'src' => base_url().'assets/img/nfl_team_logos/'.$player_data['current_team'].'.svg',
                                    'class' => 'img-responsive',
                                );
                                echo img($image_properties);
                            ?>
                        </div>
                        <div class="col-xs-18 vertical_center" >
                            <strong>
                                <? echo '#'.$player_data['nfl_jersey_number'];
                                if($player_data['depth_chart_order']!=0){
                                    echo ' | '.$player_data['current_team'].' Depth: #'.$player_data['depth_chart_order'].' '.$player_data['position'];
                                }
                                else {
                                    echo ' | '.$player_data['current_team'].' Depth: None';
                                }
                                if($player_data['is_rookie']==1){
                                    echo ' | R';	
                                    
                                }
                            }
                        ?>
                            </strong>
                        </div>
                        
                    </div> 
                    
                    <div>   
                    	<!--teams and salaries-->
                        <div class="col-xs-24">
                        	<small>
                            	<? 
									$top = 0; $top5=0; $top10=0;
									
								foreach($ranks as $year=>$rank){
									
									if($rank==1) { $top++; }
									if($rank>0 && $rank<6) { $top5++; }
									if($rank>0 && $rank<11) { $top10++; }	
									
									
								}?>
                                Finishes Top <? echo $player_data['position']; ?>: <? echo $top; ?><br>
                                Finishes Top 5 <? echo $player_data['position']; ?>: <? echo $top5; ?><br>
                                Finishes Top 10 <? echo $player_data['position']; ?>: <? echo $top10; ?><br>
                                FFFL All Pro: <? echo $all_pro; ?>
                            </small>
                        </div>	
					</div>
                    <div>
                        	<?php if($player_data['is_injured'])
						{?>
							<div class=" reset">
                            	<div class="col-xs-24 text-left" style="padding-top:10px;" >
									<small><span class="glyphicon glyphicon-plus" aria-hidden="true" style="color:#b01f24; font-size:small;"></span>
									<? echo $player_data['nfl_injury_game_status'].' | '.$player_data['injury_text']; ?></small>
							
                            	</div>
                            </div>
						<? } else {?>
                            <div class="row reset">
                                <div class="col-xs-24">
                                <br>
                                
                                </div>
                            </div>
                        <? }?>
                        
                   </div>
				</div>
                <!--teams and salaries-->

                <div class="col-xs-24 col-sm-10" >
                     <div class="panel panel-primary blue_panel-primary" >
                        <div class="panel-heading blue_panel-heading">
                            <h4 class="panel-title blue_panel-title text-center"><small>Current Teams</small></h4>
                        </div>
                        <table class="table table-condensed " style="border-radius:10px; overflow:hidden;">
                            <thead class="" >
                                <th class="text-center" colspan="2">Team</th><th class="text-center" >Roster</th><th class="text-center" >Salary</th>
                            </thead>
                            <tbody>
                            <? 
                            if(isset($owners)){
                                foreach($owners as $team_id => $data){ ?>
                                <tr>
                                    <td class="text-center col-xs-4 col-md-2"><?
                                        $image_properties = array(
                                            'src' => $data['image_path'],
                                            'class' => 'img-responsive',
                                            
                                        );
                                        echo img($image_properties);
                                    ?></td>
                                    <td class="col-xs-12"><? echo team_name_link($team_id); ?></td>
                                    <td class="text-center col-xs-4"><? echo $data['area']; ?></td>
                                    <td class="text-center col-xs-4"><? echo $data['salary']; ?></td>
                                </tr>
                            <? } 
                            }
                            if(isset($add_fa)){  ?>
                                <tr>
                                    <td class="text-center col-xs-1" colspan=4><?
                                        if($open_fa){
                                            $image_properties = array(
                                                'src' => base_url().'assets/img/open_fa.gif',
                                                'width' => '40px',
												'onClick'=>"add_open_fa(".$fffl_player_id.");",
												'id'=>"open_fa_".$fffl_player_id
                                            );
                                            echo img($image_properties).' | ';
                                        }
                                        if($add_fa){
											
                                            $image_properties = array(
                                                'src' => base_url().'assets/img/add_fa.gif',
                                                'width' => '40px',
												 'id'=>"fa_".$fffl_player_id
                                            );
											if($fa_requested){
												$image_properties['class']='fade';	
											}
											else{
												$image_properties['onClick']="add_fa(".$fffl_player_id.");";
											}
                                            echo img($image_properties).' | '.$player_data['fa_salary'];
                                        }
                                        
                                    ?>
                                    </td>
                                </tr>
                            <? } ?>
                            </tbody>
                        
                        </table>
                	</div>
                
                <!--news-->

                  
                      
                     <div class="panel panel-primary blue_panel-primary " id="news">
                        <div class="panel-heading blue_panel-heading">
                            <h4 class="panel-title blue_panel-title text-center"><small>News</small></h4>
                        </div>
                        <table class="table table-condensed "  style="border-radius:10px; overflow:hidden;">
                                
                                <tbody >
                                <?
								if(isset($headlines)){ 
									$more=0;
									foreach($headlines as $story){ 
										$more++;
										$date=  date('M j',$story['date']);?>
										<tr <? if($more>2){ echo 'class="show_more" style="display:none"';  }?>>
											<td class="text-left "   >
												<div class=" ">
													<a href="#" class="news_item" date="<? echo $date; ?>" title="<? echo $story['title']; ?>" source="<? echo $story['source']; ?>" link="<? echo $story['link']; ?>" description="<? echo str_replace('"','&quot;',$story['description']); ?>" data-toggle="modal" data-target="#rss_modal"><small><? echo $date; ?> (<? echo $story['source']; ?>) </small><? echo $story['title']; ?></a>
                                                </div>
											 
											</td>
										</tr>
                                <? 	}
									echo "<tr><td class='text-center'><small>";
										echo "<a href='#' class='show_more_btn'>Show More</a>";
										echo "<a href='#' class='show_more_btn' style='display:none'>Show Less</a>";
									echo "</small></td></tr>";

								}?>
                                </tbody>
                            
                            </table>
                         
                   		</div>
               	 	</div>
                </div>
            </div>
            
            
            <div class="row">
                
                <div id="ajax_display_area" class=" text-center" style="margin-top:0px;">
                    
					<? echo img(base_url().'assets/img/loading.gif'); ?>

                </div>
            </div>
			
        </div> <!-- end content_area div -->
	


<?PHP 
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/