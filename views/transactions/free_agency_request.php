<?PHP
	/**
	 * transactions year view.
	 *
	 * through ajax will display the given year's transactions
	 */
	//d($this->_ci_cached_vars);
?>
	<script type="text/javascript" src="<? echo base_url(); ?>assets/js/jquery-ui.min.js"></script>
    <script src="<? echo base_url(); ?>assets/js/jquery.ui.touch-punch.min.js"></script>
    <link rel="stylesheet" href="<? echo base_url(); ?>assets/css/jquery-ui.min.css" />
    
	<script type="text/javascript">
		$(document).ready(function() {

			//sortable player priority lists
				$('.player_list').sortable({
					axis: 'y',
					opacity: 0.7,
					handle: 'span',
					update: function(event, ui) {
						
						var list_sortable = $(this).sortable('toArray').toString();
						
						// change order in the database using Ajax
						$.ajax({
							url: "<? echo base_url(); ?>Free_Agent/list_player_priority/<? echo $team_id; ?>/"+$(this).attr("data-list-id"),
							type: 'POST',
							data: {list_order:list_sortable},
							success: function(data) {
								
							}
						});
					}
				}); // fin sortable
				
			//sortable release priority lists
				$('.release_list').sortable({
					axis: 'y',
					opacity: 0.7,
					handle: 'span',
					update: function(event, ui) {
						
						var list_sortable = $(this).sortable('toArray').toString();
						
						// change order in the database using Ajax
						$.ajax({
							url: "<? echo base_url(); ?>Free_Agent/list_release_priority/<? echo $team_id; ?>/"+$(this).attr("data-list-id"),
							type: 'POST',
							data: {list_order:list_sortable},
							success: function(data) {
								
							}
						});
					}
				}); // fin sortable
			
			//activate a list
				$(document).on("click",".list_submit_button",function() {
					var number_desired = $(this).parent().next().children().children().next('input').val();
					var number_players = $('#list_'+$(this).attr('data-list-id') +' li').length;
					var enough_players = number_players - number_desired;
					var number_release = $('#release_'+$(this).attr('data-list-id') +' li').length;
					var enough_release = (number_release + <? echo $empty_spots; ?>) - number_desired; 
					if(number_desired>0 && enough_players > -1 && enough_release>-1){
						$(this).parent().parent().parent().prev().removeClass('fade'); //unfade the list header
						$(this).text('Unsubmit');  // change the button text
						$(this).removeClass('list_submit_button');
						$(this).addClass('list_unsubmit_button');
						$.ajax({
								url: "<? echo base_url(); ?>Free_Agent/list_activate/" + $(this).attr('data-list-id') + "/<? echo $team_id; ?>",
								success: function(data) {
									
								}
							});
					}
					
					
				});
			
			//deactivate a list
				$(document).on("click",".list_unsubmit_button",function() {
					$(this).parent().parent().parent().prev().addClass('fade'); //fade the list ehader
					$(this).text('Submit');  //set button text
					$(this).removeClass('list_unsubmit_button');
					$(this).addClass('list_submit_button');
					
					
					
					$.ajax({
							url: "<? echo base_url(); ?>Free_Agent/list_deactivate/" + $(this).attr('data-list-id') + "/<? echo $team_id; ?>",
							success: function(data) {
								
							}
						});
					
				});
				
			//add a new list
				$("#new_button").on("click",function() {
					$.ajax({
							url: "<? echo base_url(); ?>Free_Agent/list_add/<? echo $team_id; ?>",
							success: function(data) {
								change_content('<?  echo base_url(); ?>Free_Agent/manage_request','');
							}
						});
					
				});
			
			//delete a list
				$(document).on("click",".delete_button",function() {
					$.ajax({
							url: "<? echo base_url(); ?>Free_Agent/list_delete/<? echo $team_id; ?>/" + $(this).attr('data-list-id'),
							success: function(data) {
								change_content('<?  echo base_url(); ?>Free_Agent/manage_request','');
							}
						});
					
				});
				
			//update number desired
				$(document).on("keyup",".number_desired",function() {
					
					$.ajax({
							url: "<? echo base_url(); ?>Free_Agent/update_number_desired/<? echo $team_id; ?>/" + $(this).attr('data-list-id') + "/" + $(this).val(),
							success: function(data) {
								
							}
						});
					
					//activate restrictions
					var number_desired = $(this).val();
					var number_players = $('#list_'+$(this).attr('data-list-id') +' li').length;
					var enough_players = number_players - number_desired;
					var number_release = $('#release_'+$(this).attr('data-list-id') +' li').length;
					var enough_release = (number_release + <? echo $empty_spots; ?>) - number_desired; 	
						
					if($(this).val()==0 || enough_players<0 || enough_release<0){
						$(this).parent().parent().prev().children().click();
					}
					
				});
			
			//add player to list
				$(".add_player_to_list").on("click", function() {
					
					//update data base, on success refresh
					$.ajax({
							url: "<? echo base_url(); ?>Free_Agent/add_player_list/<? echo $team_id; ?>/" + $(this).attr('data-list-id') + "/" + $(this).attr('data-fffl_player_id'),
							success: function(data) {
								change_content('<?  echo base_url(); ?>Free_Agent/manage_request','');
							}
						});
					
				});
				
			//delete a player from list
				$(document).on("click",".remove_player_list_button",function() {
					var list_id = $(this).attr('data-list-id');
					$.ajax({
							url: "<? echo base_url(); ?>Free_Agent/list_player_delete/<? echo $team_id; ?>/" + $(this).attr('data-list-id') + "/" + $(this).attr('data-fffl_player_id'),
							success: function(data) {
								
								//activate restrictions
								var number_desired = $("#number_desired_"+list_id).val();
								var number_players = $('#list_'+list_id +' li').length - 1;
								var enough_players = number_players - number_desired;
								var number_release = $('#release_'+list_id +' li').length;
								var enough_release = (number_release + <? echo $empty_spots; ?>) - number_desired; 	
									
								if(number_desired==0 || enough_players<0 || enough_release<0){
									
									$("#submit_button_"+list_id+" .list_unsubmit_button").click();
								}
								
								change_content('<?  echo base_url(); ?>Free_Agent/manage_request','');
							}
						});
					
				});
				
				//delete a player from watchlist
				$(document).on("click",".remove_watch_button",function() {
					$.ajax({
							url: "<? echo base_url(); ?>Free_Agent/delete_fa_request/" + $(this).attr('data-fffl_player_id'),
							success: function(data) {
								change_content('<?  echo base_url(); ?>Free_Agent/manage_request','');
							}
						});
					
				});
				
				//add player to release to list
				$(".add_release_player").on("click", function() {
					
					//update data base, on success refresh
					$.ajax({
							url: "<? echo base_url(); ?>Free_Agent/add_release_player/<? echo $team_id; ?>/" + $(this).attr('data-list-id') + "/" + $(this).attr('data-fffl_player_id'),
							success: function(data) {
								change_content('<?  echo base_url(); ?>Free_Agent/manage_request','');
							}
						});
					
				});
				
				//delete a player from release list
				$(document).on("click",".remove_player_release_button",function() {
					var list_id = $(this).attr('data-list-id');
					$.ajax({
							url: "<? echo base_url(); ?>Free_Agent/list_release_player_delete/<? echo $team_id; ?>/" + $(this).attr('data-list-id') + "/" + $(this).attr('data-fffl_player_id'),
							success: function(data) {
								//activate restrictions
								var number_desired = $("#number_desired_"+list_id).val();
								var number_players = $('#list_'+list_id +' li').length;
								var enough_players = number_players - number_desired;
								var number_release = $('#release_'+list_id +' li').length;
								var enough_release = (number_release - 1 + <? echo $empty_spots; ?>) - number_desired; 	
									
								if(number_desired==0 || enough_players<0 || enough_release<0){
									
									$("#submit_button_"+list_id+" .list_unsubmit_button").click();
								}
								
								change_content('<? echo base_url(); ?>Free_Agent/manage_request','');
							}
						});
					
				});
				
				//move list up
				$(document).on("click",".list_up",function() {
					var list_id = $(this).attr('data-list-id');
					$.ajax({
							url: "<? echo base_url(); ?>Free_Agent/list_order/<? echo $team_id; ?>/" + list_id + "/-1",
							success: function(data) {
								//change_content('<? //echo base_url(); ?>Free_Agent/manage_request','');
									
								 
							}
						});
						var current = $("#"+list_id);
						var previous = current.prev('li');
						if(previous.length !== 0){
							current.insertBefore(previous);
						}
		
				});
				//move list down
				$(document).on("click",".list_down",function() {
					var list_id = $(this).attr('data-list-id');
					
					$.ajax({
							url: "<? echo base_url(); ?>Free_Agent/list_order/<? echo $team_id; ?>/" + list_id + "/1",
							success: function(data) {
								//change_content('<? //echo base_url(); ?>Free_Agent/manage_request','');
							}
						});
						var current = $("#"+list_id);
						var next = current.next('li');
						if(next.length !== 0){
							next.insertBefore(current);
						}
					
				});
				
		});
	</script> 
 	
    <div id="test_area"></div>

    <div class="row">
    	<div class="col-xs-24 col-sm-8 ">
            <div class="panel panel-primary blue_panel-primary" style="margin-top:5px;" >
                <div class="panel-heading blue_panel-heading">
                    <h4 class="panel-title blue_panel-title text-center">
                       <strong>Watch List</strong>
                    </h4>
                </div> 
                <div class="panel-body ">
                    <table class="table table-condensed table-responsive">
                        <tbody>
							<?
                            foreach($requests as $fffl_player_id){
        
                                    echo '<tr class="" style="">';
                                        echo '<td class="text-left col-xs-24"><small><span class="glyphicon glyphicon-remove-sign red_font remove_watch_button pointer" data-fffl_player_id="'.$fffl_player_id.'" style="display:inline;padding-top:4px;" aria-hidden="true" ></span>&nbsp;<span>'.player_name_link($fffl_player_id).'</span></small></td>';
                                    echo '</tr>';
                                    
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
		</div>
        <!--Lists -->
        <div class="text-right col-xs-24 col-sm-16 pointer " id="new_button">
        	<small><span class="glyphicon glyphicon-plus blue_font" aria-hidden="true"></span><strong> New List</strong></small>
        </div>
        <ul class="col-xs-24 col-sm-16 " style="list-style:none" id="lists">
        	<? $list_number = 0; 
			foreach($lists as $list_data){ 
				$list_number++;
				
				if($list_data['is_submitted']==1) { $active = ''; $button="Unsubmit"; } else { $active = 'fade'; $button="Submit";}
			?>
            	<!--start of individual list-->
                <li id="<? echo $list_data['list_id']; ?>">
                    <div class="col-xs-24">
                        <div class="panel panel-primary blue_panel-primary " style="margin-top:5px;" >
                            <div class="panel-heading blue_panel-heading  <? echo $active; ?>">
                                <h4 class="panel-title blue_panel-title text-right pointer" style="float:right" data-list-id="<? echo $list_data['list_id']; ?>"><span data-list-id="<? echo $list_data['list_id']; ?>" class="glyphicon glyphicon-triangle-bottom pointer list_down" aria-hidden="true"></span>&nbsp;&nbsp;<span data-list-id="<? echo $list_data['list_id']; ?>" class="glyphicon glyphicon-triangle-top pointer list_up" aria-hidden="true"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="delete_button " data-list-id="<? echo $list_data['list_id']; ?>">X</h4>
                                 <h4 class="panel-title blue_panel-title text-center" >
                                   <strong>List <? echo $list_number; ?></strong>
                                    
                                </h4>
                                  
                            </div> 
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-4 list text-left form-inline" data-list_number="1" id="submit_button_<? echo $list_data['list_id']; ?>">
                                        <button type="button" class="btn btn-primary btn-sm list_<? echo strtolower($button); ?>_button" data-list-id="<? echo $list_data['list_id']; ?>" >
                                            <? echo $button; ?>
                                        </button>
                                     </div>
                                     <div "col-xs-20  ">
                                        <div class="form-group form-inline  pull-right" style="padding:0">
                                            <label ><small>Number to Draft: </small></label>
                                            <input style="width: 50px" data-list-id="<? echo $list_data['list_id']; ?>" size="1" type="text" class="form-control number_desired" value="<? echo $list_data['number_desired']; ?>" id="number_desired_<? echo $list_data['list_id']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!--player list container-->
                                    <div class="col-xs-24 col-sm-12">
                                        <div><strong><small>Draft Priority</small></strong></div>
                                        <!--start of player list-->
                                        <ul style="list-style:none; padding:0;" class="text-left player_list" data-list-id="<? echo $list_data['list_id']; ?>" id="list_<? echo $list_data['list_id']; ?>">
                                            <?
                                            foreach($list_data['list_players'] as $priority => $fffl_player_id){ ?>
                                                <li class="pointer" id="<? echo $fffl_player_id; ?>">
                                                    <span>
                                                        <small><span class="glyphicon glyphicon-remove-sign red_font remove_player_list_button" data-fffl_player_id="<? echo $fffl_player_id; ?>" data-list-id="<? echo $list_data['list_id']; ?>" style="display:inline;padding-top:4px;" aria-hidden="true" ></span></small>&nbsp;
                                                        <small><small><div class="glyphicon glyphicon-menu-hamburger" aria-hidden="true" style="display:inline; color:#aaa;"></div></small></small>
                                                        <div style="font-family:Arial, Helvetica, sans-serif;display:inline"><small><strong>
                                                    <? echo player_name_no_link($fffl_player_id); ?>	
                                                        </strong></small>
                                                        </div>
                                                    </span>
                                                </li>
                                                
                                            <? }
                                            ?>
                                        </ul>
                                        <br>
                                        <div class="dropdown">   
                                            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                <span >Add Player</span>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu add_player_dropdown"  aria-labelledby="dropdownMenu1" >
                                                <?php
                                                
                                                foreach($requests as $fffl_player_id_add)
                                                {
                                                    
                                                    if(!in_array($fffl_player_id_add,$list_data['list_players'])){
                                                    ?><li ><a href="#"  class="add_player_to_list" data-list-id="<? echo $list_data['list_id']; ?>" data-fffl_player_id="<? echo $fffl_player_id_add; ?>" ><? echo player_name_no_link($fffl_player_id_add); ?></a></li>
                                                
                                                <?	 }
                                                } ?>
                                            </ul>
                                             
                                        </div>
                                     </div>
                                     <!--release list container-->
                                    <div class="col-xs-24 col-sm-12">
                                        
                                        <div><strong><small>Release Priority <? if($empty_spots>0) { echo '('.$empty_spots.' empty)'; } ?></small></strong></div>
                                        <!--start of release list-->
                                        <ul style="list-style:none; padding:0;" class="text-left release_list" data-list-id="<? echo $list_data['list_id']; ?>" id="release_<? echo $list_data['list_id']; ?>">
                                            <?
                                            foreach($list_data['release_players'] as $priority => $fffl_player_id){ ?>
                                                <li class="pointer" id="<? echo $fffl_player_id; ?>">
                                                    <span>
                                                        <small><span class="glyphicon glyphicon-remove-sign red_font remove_player_release_button" data-fffl_player_id="<? echo $fffl_player_id; ?>" data-list-id="<? echo $list_data['list_id']; ?>" style="display:inline;padding-top:4px;" aria-hidden="true" ></span></small>&nbsp;
                                                        <small><small><div class="glyphicon glyphicon-menu-hamburger" aria-hidden="true" style="display:inline; color:#aaa;"></div></small></small>
                                                        <div style="font-family:Arial, Helvetica, sans-serif;display:inline"><small><strong>
                                                    <? echo player_name_no_link($fffl_player_id); ?>	
                                                        </strong></small>
                                                        </div>
                                                    </span>
                                                </li>
                                                
                                            <? }
                                            ?>
                                        </ul>
                                        <br>
                                        <div class="dropdown">   
                                            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                <span >Add Player</span>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu add_release_dropdown"  aria-labelledby="dropdownMenu1" >
                                                <?php
                                                
                                                foreach($roster as $fffl_player_id_release)
                                                {
                                                    
                                                    if(!in_array($fffl_player_id_release,$list_data['release_players'])){
                                                    ?><li ><a href="#"  class="add_release_player" data-list-id="<? echo $list_data['list_id']; ?>" data-fffl_player_id="<? echo $fffl_player_id_release; ?>" ><? echo player_name_no_link($fffl_player_id_release); ?></a></li>
                                                
                                                <?	 }
                                                } ?>
                                            </ul>
                                             
                                        </div>
                                     </div>    
                                </div>
                            </div>
                        </div>
                    </div>
                   
                </li>
            <? } ?>
            
		</ul>
    </div>
    
    </div>
 

       
 


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/