<?PHP
	/**
	 * players search view.
	 *
	 * includes list of all players
	 */
	//d($this->_ci_cached_vars);

        ?>
        <script type="text/javascript">
			$(document).ready(function(){
				$('[data-toggle="popover"]').popover(); 
				
			});
			
			//add fa to requests
			function add_fa(player){
				
				$("#fa_"+player).attr('src','<? echo base_url().'assets/img/loading.gif'; ?>').attr('height','11').attr('width','11');
				$.ajax({
					url: '<? echo base_url(); ?>Free_Agent/add_fa_request/' + player,
					type: "POST",
					success: function() {
						change_content('<? 
							$filters = $filters_array;
							echo base_url().'Player/filter/'.implode('/',$pagination).'/'.implode('/',$filters).'/'.implode('/',$sort_array); ?>','');
							
					}
				});
			}
			
			
			$("#filter_modal_button").unbind('click');
			$("#filter_modal_button").on("click",function(){
				
				//load the filter_list
				var path = '<? echo base_url().'Player/load_filter_list/'.implode('/',$pagination).'/'.implode('/',$filters_array).'/'.implode('/',$sort_array); ?>';
				$('#filter_list').load(path);
				
			});
			
			
	
			//opens the selection to sort by week
			function reveal_week_selection(){
				$('#week_selection_menu').show();
				$('#sort_text').text('Week');
			}
			
			function hide_week_selection(){
				$('#week_selection_menu').hide();
			}
			
			//stats popover
			$('.stats_button').popover({
				html: true,
				trigger: 'click',
				placement: 'top',
				title: '' + '',
				container: 'body',
				
				content: function() {
					var id = $(this).attr('data-player');
					var week = $(this).attr('data-week');
					var year = <? echo $year; ?>;
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
        <div id="test_area"></div>
        <div class="row">

        <div id="filters_row" class="row text-center" style="margin-bottom:5px;">	
        
        	
                <? $positions = array('QB','RB','WR','TE','K');
				foreach($positions as $position){ ?>
                    <button type="button" class="btn btn-primary btn-sm <? if($filters_array[$position]==0) {echo 'fade'; } ?>" id="<? echo $position; ?>_filter" onClick="change_content('<? 
                        $filters = $filters_array;
                        if($filters_array[$position]==1){ $filters[$position]=0; } else {  $filters[$position]=1; }
                        echo base_url().'Player/filter/'.implode('/',$pagination).'/'.implode('/',$filters).'/'.implode('/',$sort_array); ?>','')">
                      <? echo $position; ?>
                    </button>
                <? } ?>
                <button type="button" class="btn btn-primary btn-sm" id="filter_modal_button" data-target="#filter_modal" data-toggle="modal">
                  More
                </button>
                
                
		</div>
        <? if ($after_franchise==1 && $draft_upcoming==1){ ?>
        <div id="draft_filter_row" class= "row text-center" style="margin-bottom:5px;">
        	<button type="button" class="btn btn-primary btn-sm <? if($filters_array['draftable']==0) {echo 'fade'; } ?>" id="draftable_filter" onClick="change_content('<? 
				$filters = $filters_array;
				if($filters_array['draftable']==1){ $filters['draftable']=0; } else {  $filters['draftable']=1; }
				echo base_url().'Player/filter/'.implode('/',$pagination).'/'.implode('/',$filters).'/'.implode('/',$sort_array); ?>','')">
			  Draftable
			</button>
            <button type="button" class="btn btn-primary btn-sm <? if($filters_array['supplemental_eligible']==0) {echo 'fade'; } ?>" id="supplemental_eligible_filter" onClick="change_content('<? 
				$filters = $filters_array;
				if($filters_array['supplemental_eligible']==1){ $filters['supplemental_eligible']=0; } else {  $filters['supplemental_eligible']=1; }
				echo base_url().'Player/filter/'.implode('/',$pagination).'/'.implode('/',$filters).'/'.implode('/',$sort_array); ?>','')">
			  Supplemental
			</button>
            <button type="button" class="btn btn-primary btn-sm <? if($filters_array['undraftable']==0) {echo 'fade'; } ?>" id="undraftable_filter" onClick="change_content('<? 
				$filters = $filters_array;
				if($filters_array['undraftable']==1){ $filters['undraftable']=0; } else {  $filters['undraftable']=1; }
				echo base_url().'Player/filter/'.implode('/',$pagination).'/'.implode('/',$filters).'/'.implode('/',$sort_array); ?>','')">
			  Undraftable
			</button>
          <? } ?>
        
        </div>
        <nav aria-label="Page navigation" class="col-xs-24 text-center">
            	<small><strong>Sort By: </strong></small>
                <div class="dropdown btn-group" id="dropdown_container">
                	
                    <button class="btn btn-default dropdown-toggle" type="button" id="sort_by_menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><span id="sort_text">
                    <? echo $current_sort; ?></span>
                    	<span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1" >
                        <li><a href="#" onClick="change_content('<? echo base_url().'Player/filter/'.implode('/',$pagination).'/'.implode('/',$filters_array).'/Players.last_name/ASC'; ?>','')">Last Name</a></li>
                        <li><a href="#" onClick="change_content('<? echo base_url().'Player/filter/'.implode('/',$pagination).'/'.implode('/',$filters_array).'/average/DESC'; ?>','')">Average: High to Low</a></li>
                        <li><a href="#" onClick="change_content('<? echo base_url().'Player/filter/'.implode('/',$pagination).'/'.implode('/',$filters_array).'/Rosters.salary/DESC'; ?>','')">Salary: High to Low</a></li>
                        <li><a href="#" onClick="change_content('<? echo base_url().'Player/filter/'.implode('/',$pagination).'/'.implode('/',$filters_array).'/Rosters.salary/ASC'; ?>','')">Salary: Low to High</a></li>
                        <li><a href="#" onClick="reveal_week_selection();">Week</a></li>
                    </ul>
                  </div>
                  <div class="dropdown btn-group" id="dropdown_container"> 
                     <button class="btn btn-default dropdown-toggle" type="button" id="week_selection_menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="display:none">
                    <? echo $sort_array['sort_week']; ?>
                    	<span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu2" >
                        <? $select_week =1; 
							while($select_week<=16){ ?>
                        		<li><a href="#" onClick="change_content('<? echo base_url().'Player/filter/'.implode('/',$pagination).'/'.implode('/',$filters_array).'/week/DESC/'.$select_week; ?>','')"><? echo $select_week; ?></a></li>
                        <? 		$select_week++;
							} ?>
                    </ul>
                </div>
				<div>
                    <ul class="pagination pagination-sm">
                    
                    <? $page=1;
                        $current_page = $pagination['page'];
                            while($page<=$pages){ 
                                if($current_page==$page){ ?>
                                    <li class="active"><a href="#"><? echo $page; ?> <span class="sr-only">(current)</span></a></li>	
                                    
                                <? }
                                else{ 
                                    $pagination['page']=$page; ?>
                                    <li><a href="#" onClick="change_content('<? echo base_url().'Player/filter/'.implode('/',$pagination).'/'.implode('/',$filters_array).'/'.implode('/',$sort_array); ?>','')"><? echo $page; ?></a></li>
                                <? } 
                                $page++;
                            } ?>
                    
                    </ul>
                </div>
            
			
		</nav>  
        </div>
        <div id="excel_button" class="pointer text-left" onClick="window.location.href='<? 
                        $filters = $filters_array;
                        echo base_url().'Player/worksheet_load/'.implode('/',$pagination).'/'.implode('/',$filters).'/'.implode('/',$sort_array); ?>'"><span class=" glyphicon glyphicon-save-file" aria-hidden="true" style="color:green"></span> CSV</div>
        <? 
            //players_array indexes : first_name last_name current_team position is_rookie is_injured injury_text nfl_injury_game_status nfl_status nfl_esbid
            foreach($players_array as $player)
            {
				//get the headline data
				if(count($player['headlines'])>0) {
					//there's a story
					$headlines = ' <a href="#" 
									tabindex="0" 
									class="" 
									role="button" 
									data-toggle="popover" 
									data-container="#popover_container" 
									data-html="true" 
									data-trigger="focus" 
									title="'.$player['headlines']['0']['title'].' '.date('M d',$player['headlines']['0']['date']).'" 
									data-content="'.str_replace('"','&quot;',$player['headlines']['0']['description']).'...<a href=&quot;'.$player['headlines']['0']['link'].'&quot; target=&quot;_new&quot;>Read More</a>" 
									data-placement="top">
										<small><sup><span class="glyphicon glyphicon-file blue_font" aria-hidden="true"></span></sup></small>
									</a> ';
				} 
				else{
					$headlines='';	
				}
				
				//injury alerts
				//out, suspended, bye week turns red
				if($player['bye_week']==$week || in_array($player['nfl_injury_game_status'],array('OUT','SUSPENDED','IR','PUP'))){
					$alert = 'danger';
				}
				//prob, quest, or doubt turns yellow
				elseif(in_array($player['nfl_injury_game_status'],array('PROBABLE','QUESTIONABLE','DOUBTFUL','NFI')) ){
					$alert = 'warning';
					
				} 
				else {
					$alert='';
				}
            ?>
				<div class=" <?php echo $player['position'];  ?> <? echo 'bg-'.$alert; ?>" style="margin-left:0px; margin-right:0px; padding-left:10px; padding-right:10px;" id="popover_container">
            		<div class="row " style="border-top:solid #CCC 1px;">
            		<div class="col-sm-2 hidden-xs  ">
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
            		<div class="col-xs-12 col-sm-4 " >
            			<small><span class="visible-xs-inline">
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
                            </span><strong><? echo player_name_link($player['fffl_player_id'],FALSE,FALSE).' '.$headlines; ?></strong>
						<br>
            			
            				
                            <?php echo $player['position'].' | '.$player['current_team'];
							if($player['is_rookie'])
							{
								echo ' | R';	
							}
							echo ' | Bye: '.$player['bye_week']; ?>
                        </small>
            		</div>
            		<div class="col-xs-12 col-sm-7">
						<?php
                        $br=0;
                        $fa_addon = "";
						
                        foreach($player['salaries'] as $key => $salary)
                        {
                        
                        	if($key<>'fa_salary')
                        	{
                              echo '<small>'.team_name_link($key).' | '.$salary.'</small>';
                        		if($br===0)
                        		{
                        			echo '<br>';
                        			$br++;
                        		}
                        	}
							
                        	elseif(!in_array($player['fffl_player_id'],$team_fa_requests) && !in_array($player['fffl_player_id'],$team_roster))
                        	{ 
                        		$image_properties = array(
									'src' => base_url().'assets/img/add_fa.gif',
									'width' => '30px',
									'id' => 'fa_'.$player['fffl_player_id']
                        		);
								
                              $fa_addon .= '<span onClick="add_fa('.$player['fffl_player_id'].')" data-player="'.$player['fffl_player_id'].'">'.img($image_properties).'</span> | <small>'.$salary.'</small>';
								
                        	}//if else $key<>'fa_salary'
							else{//fade the butotn becasue he is a fa but this team can't select him
								$image_properties = array(
									'src' => base_url().'assets/img/add_fa.gif',
									'width' => '30px',
									'class' => 'fade'
                        		);
								
                              $fa_addon .= '<span >'.img($image_properties).'</span> | <small>'.$salary.'</small>';
							}
                        }//foreach players['salaries']
                        echo $fa_addon;
                        ?>
					</div>
                    
                   
            		<div class="col-xs-24 col-sm-11 ">
            			<div class="row"> 
                            <div class="col-xs-4 visible-xs">
                              <small>Scores: </small>
                            </div>
                            <div class="col-xs-20 col-sm-24">
                                <div class="player_scores_div table-responsive">
                                	<table class="player_scores " style="">
                                        <tr >
                                          <td class="text-center" width="70px" style="padding-right:4px;padding-left:4px;">
                                            <small>Avg.</small>
                                          </td>
											<?php 
                                                $week_number=1;
                                                while($week_number<17)
                                                {
                                                    echo '<td class="text-center" width="65px" style="padding-right:4px;padding-left:4px;">
                                                                <small>'.$week_number.'
                                                            </td>';
                                                
                                                    $week_number++;
                                                }
                                            ?>
  
                                        </tr>
                                        <tr >
                                            <?php 
                                            echo '<td class="text-center" width="70px" style="padding-right:4px;padding-left:4px;"><small>'.$player['scores']['average'].'</small></td>';
                                            
											$week_number=$player['scores']['start_week'];
                                            while($week_number<($player['scores']['end_week']+1))
                                            {
                                                if(is_array($player['scores']['weeks']) && array_key_exists($week_number,$player['scores']['weeks']) && $player['scores']['weeks'][$week_number]['player_opponent']<>'Bye')
                                                {
                                                    echo '<td class="text-center stats_button pointer" data-player="'.$player['fffl_player_id'].'" data-week="'.$week_number.'" data-year="'.$year.'" width="65px"  style="padding-right:4px;padding-left:4px;"><small>'.$player['scores']['weeks'][$week_number]['points'].'</small></td>';
                                                }
                                                else if(is_array($player['scores']['weeks']) && array_key_exists($week_number,$player['scores']['weeks']) && $player['scores']['weeks'][$week_number]['player_opponent']==='Bye')
                                                {
                                                    echo '<td width="65px" style="padding-right:4px;padding-left:4px;"></td>';
                                                }
                                                else
                                                {
                                                    echo '<td class="text-center" width="65px"  style="padding-right:4px;padding-left:4px;"><small>0</small></td>';
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
                   
                    	<?php if($player['nfl_injury_game_status']!='')
						{?>
							<div class=" reset">
                            	<div class="col-xs-24 text-left" >
									<small><span class="glyphicon glyphicon-plus" aria-hidden="true" style="color:#b01f24; font-size:small;"></span>
									<? echo $player['nfl_injury_game_status'].' | '.$player['injury_text']; ?></small>
							
                            	</div>
                            </div>
						<? } else {?>
                            <div class="row reset">
                                <div class="col-xs-24">
                                <br>
                                
                                </div>
                            </div>
                        <? }?>
                    </div><!-- end player_row --> 
				</div><!--end position wrapper for fileter -->
            <?php
            } //end foreach
			?>
			<div>
                <ul class="pagination pagination-sm">
                
                <? $page=1;
                    
					while($page<=$pages){ 
						if($current_page==$page){ ?>
							<li class="active"><a href="#"><? echo $page; ?> <span class="sr-only">(current)</span></a></li>	
							
						<? }
						else{ 
							$pagination['page']=$page; ?>
							<li><a href="#" onClick="change_content('<? echo base_url().'Player/filter/'.implode('/',$pagination).'/'.implode('/',$filters_array).'/'.implode('/',$sort_array); ?>','')"><? echo $page; ?></a></li>
						<? } 
						$page++;
					} ?>
                
                </ul>
            </div>


<script>
	//restore position filters
	$.each(['QB','RB','WR','TE','K'], function(index, value){
		if($('#'+value+'_filter').hasClass('fade')){
			hide_position('.'+value,$(this));
		}

	});


</script>

<?
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/