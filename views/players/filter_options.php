<?PHP
	/**
	 * filter_options view.
	 *
	 * will be loaded as a modal to add filter functions then pass alla the filter choices back to Player/filter method
	 */
	//d($this->_ci_cached_vars);
?>
	<style>
		.margined { margin-top:10px; }
	</style>
	<script type="text/javascript">
     
       	function send_filter(type,team){
     	     var filters_array = {};
      		//set the initial global array to be used by all filtersfilters_array
      		<? foreach($filters_array as $key => $value){ ?>
      			filters_array['<? echo $key; ?>']='<? echo $value ?>';
      
      		<? } ?>
            path='<?  
				//reset page to 1 
				$pagination['page']=1; 
				echo base_url().'Player/filter/'.implode('/',$pagination).'/'; 
				?>';
          
			//nfl retired and fa
			if($('#inactives').is(':checked')){
				filters_array['NFL_FA']=1;
            }
			else {
              	filters_array['NFL_FA']=0;
              }
			  
			//rookies
			if($('#is_rookie').is(':checked')){
				filters_array['is_rookie']=1;
            }
			else {
              	filters_array['is_rookie']=0;
            }
			
			//free agents
			if($('#free_agents').is(':checked')){
				filters_array['free_agents']=1;
            }
			else {
              	filters_array['free_agents']=0;
            }
			  
          //name
		  	if($("#name").val()==''){ 
				filters_array['name_like']=0;
			}	
			else {
              filters_array['name_like']=$("#name").val();
			}
             	
           //current team
		   	if(type=='nfl'){
				filters_array['current_team']=team;
				if(team=='All'){
					$("#current_team").text('All');
				}
				else {
					$("#current_team").text(team);
				}
			}
			else {
				filters_array['current_team']=$("#current_team").text();
			}
			
			//fffl team
		   	if(type=='fffl'){
				
				filters_array['team']=team;
				if(team=='All'){
					$("#selected_team_name").text('All');
					$("#selected_team_name").data('team_id','All');
				}
				else {
					$("#selected_team_name").text($("#"+team).data('team_name'));
					$("#selected_team_name").data('team_id',team);
				}
			}
			else {
				filters_array['team']=$("#selected_team_name").data('team_id');
			}
			
			//salary low
		  	filters_array['salary_low']=$("#salary_low").val();
			
			//salary high
		  	filters_array['salary_high']=$("#salary_high").val();
			
			//injuries
		   	if(type=='inj'){
				
				filters_array['injured_players']=team;
				if(team=='Include'){
					$("#injury_filter_text").text('Include All');
					$("#injury_filter_text").data('injured_players','Include');
				}
				else if(team=='Remove') {
					$("#injury_filter_text").text('Remove Injuries');
					$("#injury_filter_text").data('injured_players','Remove');
				}
				else if(team=='Only') {
					$("#injury_filter_text").text('Injuries Only');
					$("#injury_filter_text").data('injured_players','Only');
				}
			}
			else {
				filters_array['injured_players']=$("#injury_filter_text").data('injured_players');
			}
			

			$.each(filters_array, function(index,value){
              	path = path + value + '/';
              });
			path = path + '<? echo implode('/',$sort_array); ?>';
           
			change_content(path,'');
          
		};
		
      $('.filters').on("click", function(){ send_filter(0,0); });
      $('.filters').keyup( function (){ send_filter(0,0); });
			
	</script> 

	<div class="row">
    
    

		<!--inactives-->
			<div class="col-xs-12 col-md-10 margined">
				<? if($filters_array['NFL_FA']==1){ $checked='checked'; } else { $checked=''; } ?>
				<label><input type="checkbox" id="inactives" value='1' class="filters" <? echo $checked; ?>><small> Include Inactive Players</small></label>
            </div>
            <!--rookies-->
			<div class="col-xs-12 col-md-7 margined">
				<? if($filters_array['is_rookie']==1){ $checked='checked'; } else { $checked=''; } ?>
                <label><input type="checkbox" id="is_rookie" value='1' class="filters" <? echo $checked; ?>><small> Rookies Only</small></label>
            </div>
			<div class="clearfix visible-xs-block visible-sm-block"></div>
            <!--free agents-->
			<div class="col-xs-12 col-md-7 margined">
				<? if($filters_array['free_agents']==1){ $checked='checked'; } else { $checked=''; } ?>
                <label><input type="checkbox" id="free_agents" value='1' class="filters" <? echo $checked; ?>><small> Free Agents Only</small></label>
            </div>
			<div class="clearfix visible-md-block visible-lg-block " ></div>


            <!--name-->
            <div class="col-xs-12 col-md-8 margined">
            	<small><strong>Name:</strong></small>
                <br>
           
                <? 
				$value= $filters_array['name_like'];
				if($value=='0'){$value='';}
				$data = array(
						'id' => 'name',
						'value' => $value,
						'class' => 'filters form-control',
						'style' => 'width:100%'
						
				);
                        
                        
                echo form_input($data);
                 ?>
            </div>
            <div class="clearfix visible-xs-block visible-sm-block" ></div>
            
            <!--current team-->
            <div class="col-xs-6 col-md-4 margined">
            	<small><strong>NFL Team:</strong></small> 
            <br>
                <div class="dropdown btn-group" id="dropdown_container_nfl">
                	
                    <button class="btn btn-default dropdown-toggle" type="button" id="nfl_team_menu" team="0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><span id='current_team'><? 
						if( $filters_array['current_team']=='All') { echo '<small><strong>All</strong></small>'; }
						else { echo '<small><strong>'.$filters_array['current_team'].'</strong></small>'; } ?></span> <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1" style="max-height:200px;overflow-y:scroll;">
                    	<li style="font-size:small; font-weight:bolder;"><a href="#" class="" onClick="send_filter('nfl','All');" id="All">All</a></li>
                    	<? foreach($all_nfl_teams as $nfl_team){ ?>
                        	<li style="font-size:small; font-weight:bolder;"><a href="#" class="" onClick="send_filter('nfl',$(this).attr('id'));" id="<? echo $nfl_team; ?>"><? echo $nfl_team; ?></a></li>
                        <? } ?>
                    </ul>
                </div>
            </div>
            <!--fffl team-->
            <div class="col-xs-18 col-md-12 margined">
            	<small><strong>FFFL Team:</strong></small> 
           		<br>
                <div class="dropdown btn-group" id="dropdown_container_fffl">
                	
                    <button class="btn btn-default dropdown-toggle" type="button" id="fffl_team_menu" team="0" data-toggle="dropdown" aria-haspopup="true"  aria-expanded="true"><div class="ellipses" style="display:inline;" id='selected_team_name' data-team_id='<? echo $filters_array['team']; ?>'><small><strong><? 
						if( $filters_array['team']=='All') { echo 'All'; }
						else { echo ''.team_name_no_link($filters_array['team']).''; } ?></div> <span class="caret"></span></strong></small>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1" style="max-height:200px;overflow-y:scroll;">
                    	<li style="font-size:small; font-weight:bolder;"><a href="#" class="" data-team_name='All' onClick="send_filter('fffl','All');" id="All">All</a></li>
                    	<? foreach($all_teams as $team_id){ ?>
                        	<li style="font-size:small; font-weight:bolder;"><a href="#" class="" data-team_name="<? echo team_name_no_link($team_id); ?>" onClick="send_filter('fffl',$(this).attr('id'));" id="<? echo $team_id; ?>"><? echo team_name_no_link($team_id); ?></a></li>
                        <? } ?>
                    </ul>
                </div>
            </div>
            <div class="clearfix visible-xs-block visible-sm-block visible-md-block visible-lg-block " ></div>
           
            <!--salary-->
            <div class="col-xs-24 col-md-12 margined">
            	<small><strong>Salary:</strong></small> 
           		<br>
           
           		<div class="" style="display:inline">
                    <label for="salary_low">$</label>
                    <? 
                    $data_low = array(
                            'id' => 'salary_low',
                            'value' => $filters_array['salary_low'],
                            'class' => 'filters form-control',
							'type' => 'text' ,
							'style'=>'width:60px; display:inline'
                    ); 
					echo form_input($data_low); ?>
				to
                </div>
                <div class="" style="display:inline">
                	 <label for="salary_high">$</label>
                <?
                    $data_high = array(
                            'id' => 'salary_high',
                            'value' => $filters_array['salary_high'],
                            'class' => 'filters form-control',
							'type' => 'text' ,
							'style'=>'width:60px; display:inline' 
                    );        

                    echo form_input($data_high);
                     ?>
                </div>
        	
			</div>
            <div class="clearfix visible-xs-block visible-sm-block " ></div>
            <!--injuries-->
            <div class="col-xs-12 col-md-8 margined">
            	<small><strong>Injured Players:</strong></small> 
           		<br>
                <div class="dropdown btn-group" id="dropdown_container_fffl">
                	
                    <button class="btn btn-default dropdown-toggle" type="button" id="injured_players_menu" data-toggle="dropdown" aria-haspopup="true"  aria-expanded="true"><small><strong><div style="display:inline;" id='injury_filter_text' data-injured_players='<? echo $filters_array['injured_players']; ?>'><? 
						if( $filters_array['injured_players']=='Include') { echo 'Include All'; }
						elseif($filters_array['injured_players']=='Remove') { echo 'Remove Injuries'; }
                        elseif($filters_array['injured_players']=='Only') { echo 'Injuries Only'; } ?></div></strong></small> <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1" style="max-height:200px;overflow-y:scroll;">
                    	<li style="font-size:small; font-weight:bolder;"><a href="#" class="filters" data-injured_players='Include' id="Include" onClick="send_filter('inj',$(this).attr('id'));">Include All</a></li>
                        <li style="font-size:small; font-weight:bolder;"><a href="#" class="filters" data-injured_players='Remove' id="Remove" onClick="send_filter('inj',$(this).attr('id'));">Remove Injuries</a></li>
                        <li style="font-size:small; font-weight:bolder;"><a href="#" class="filters" data-injured_players='Only' id="Only" onClick="send_filter('inj',$(this).attr('id'));">Injuries Only</a></li>
                    	
                    </ul>
                </div>
            </div>
            <div class="clearfix visible-xs-block visible-sm-block visible-md-block visible-lg-block " ></div>
    </div>


       
 


<?PHP
/*End of file filter_options.php*/
/*Location: ./application/veiws/players/filter_options.php*/