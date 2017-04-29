<?PHP
	/**
	 * players search view.
	 *
	 * includes list of all players
	 */
		//d($this->_ci_cached_vars);
?>

	<script type="text/javascript">

		
		
			function hide_position($position,$button) {
				$($position).toggle();
				$button.toggleClass("btn-primary");
				$button.toggleClass("btn-danger");
			}
			

			
	</script>

        
        <div id="content_area" class="container-fluid">
			<div id="filters_row" class="row text-center" style="margin-bottom:5px;">		
                
                <button type="button" class="btn btn-primary btn-sm" id="#QB_filter" onClick="hide_position('.QB',$(this));">
                  QB
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="#RB_filter" onClick="hide_position('.RB',$(this));">
                  RB
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="#WR_filter" onClick="hide_position('.WR',$(this));">
                  WR
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="#TE_filter" onClick="hide_position('.TE',$(this));">
                  TE
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="#K_filter" onClick="hide_position('.K',$(this));">
                  K
                </button>
                <!--<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#filter_modal">
                  More
                </button>-->
			</div>
					
			<?php 
            
            //players_array indexes : first_name last_name current_team position is_rookie is_injured injury_text nfl_injury_game_status nfl_status nfl_esbid
            foreach($players_array as $fffl_player_id => $player)
            {
            ?>
					<div class="striped <?php echo $player['position'];?>">
            	<div class="row  " style="border-top:solid #CCC 1px;">
            		<div class="col-sm-1 hidden-xs  ">
            		<?php
            			$image_properties = array(
							'src' => 'http://static.nfl.com/static/content/public/static/img/fantasy/transparent/200x200/'.$player['nfl_esbid'].'.png',
							'width' => '50%',
							
            			);
            			echo img($image_properties);
						if($player['current_team'] != 'FA' && $player['current_team'] != 'RET') 
						{
							$image_properties = array(
								'src' => base_url().'assets/img/nfl_team_logos/'.$player['current_team'].'.svg',
								'width' => '50%',
							);
							echo img($image_properties);
            			}
            		?>
            		</div>
            		<div class="col-xs-6 col-sm-3 " >
            			<strong><? echo player_name_link($fffl_player_id,FALSE,FALSE) ?></strong>
						<br>
            			<small>
            				<span class="visible-xs-inline">
								<?php
								if($player['current_team'] !== 'FA' && $player['current_team'] !== 'RET') 
								{
									$image_properties = array(
										'src' => base_url().'assets/img/nfl_team_logos/'.$player['current_team'].'.svg',
										'width' => '15%',
									);
									echo img($image_properties).nbs(1);
                            	}
								?>
                            </span>
                            <?php echo $player['position'].' | '.$player['current_team'];
							if($player['is_rookie'])
							{
								echo ' | R';	
							}
							echo ' | Bye: '.$player['bye_week']; ?>
                        </small>
            		</div>
            		<div class="col-xs-6 col-sm-3">
						<?php
                        $br=0;
                        $fa_addon = "";
                        foreach($player['salaries'] as $key => $salary)
                        {
                        
                        	if($key<>'fa_salary')
                        	{
                              echo '<small>'.$key.' | '.$salary.'</small>';
                        		if($br===0)
                        		{
                        			echo '<br>';
                        			$br++;
                        		}
                        	}
                        	else 
                        	{
                        		$image_properties = array(
									'src' => base_url().'assets/img/add_fa.gif',
									'width' => '30px',
                        		);
                              $fa_addon .= img($image_properties).' | <small>'.$salary.'</small>';
								
                        	}//if else $key<>'fa_salary'
                        }//foreach players['salaries']
                        echo $fa_addon;
                        ?>
					</div>
                    
                   
            		<div class="col-xs-12 col-sm-5 ">
            			<div class="row"> 
                            <div class="col-xs-2 visible-xs">
                              <small>Scores: </small>
                            </div>
                            <div class="col-xs-10 col-sm-12">
                                <div class="player_scores_div">
                                	<table class="player_scores" width="100%">
                                        <tr>
                                          <td>
                                            <small>Avg.</small>
                                          </td>
																					<?php 
																						$week_number=1;
																						while($week_number<17)
																						{
																							echo '<td>
																										<small>'.$week_number.'
																									</td>';
																						
																							$week_number++;
																						}
																					?>
                                          
                                        </tr>
                                        <tr >
																					<?php 
																					echo '<td>'.$player['scores']['average'].'</td>';
																					$week_number=$player['scores']['start_week'];
																					while($week_number<($player['scores']['end_week']+1))
																					{
																						if(is_array($player['scores']) && array_key_exists($week_number,$player['scores']) && $player['scores'][$week_number]['player_opponent']<>'Bye')
																						{
																							echo '<td>'.$player['scores'][$week_number]['points'].'</td>';
																						}
																						else if(is_array($player['scores']) && array_key_exists($week_number,$player['scores']) && $player['scores'][$week_number]['player_opponent']==='Bye')
																						{
																							echo '<td></td>';
																						}
																						else
																						{
																							echo '<td>0</td>';
																						}
																						$week_number++;
																					}
                                          ?>  
                                        </tr>
                                        
											
                                 	</table>
                                </div>
                            </div>
            			</div>
            		</div>
                   
                    	<?php if($player['is_injured'])
											{?>
                                            	<div class="row reset"><div class="col-xs-12">
														<span class="glyphicon glyphicon-plus" aria-hidden="true" style="color:#b01f24; font-size:small;"></span>
                                                        <? echo $player['nfl_injury_game_status'].' | '.$player['injury_text']; ?>
																								</div></div>
											<? } else {?>
														<div class="row reset"><div class="col-xs-12">
														<br>
                                                        
																								</div></div>
													<?}?>
                    </div><!-- end player_row --> 
								</div><!--end position wrapper for fileter -->
            <?php
            } //end foreach
            ?>  
            
        </div> <!-- end content_area div -->
	</div>



<?PHP 
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/