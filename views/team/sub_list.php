<?PHP
	/**
	 * sub_list view.
	 *
	 * loads the sub list into a modal located in the footer
	 * also handels the updating of the sub priority
	 */
	//d($this->_ci_cached_vars);

?>
	<script type="text/javascript" src="<? echo base_url(); ?>assets/js/jquery-ui.min.js"></script>
    <script src="<? echo base_url(); ?>assets/js/jquery.ui.touch-punch.min.js"></script>
    <link rel="stylesheet" href="<? echo base_url(); ?>assets/css/jquery-ui.min.css" />
    
	<script type="text/javascript">
		$(document).ready(function() {
			//sortable release priority lists
				$('.sub_list').sortable({
					axis: 'y',
					opacity: 0.7,
					handle: 'span',
					update: function(event, ui) {
						
						var list_sortable = $(this).sortable('toArray').toString();
						
						// change order in the database using Ajax
						$.ajax({
							url: "<? echo base_url(); ?>Team/update_subs/<? echo $team_id; ?>",
							type: 'POST',
							data: {list_order:list_sortable},
							success: function(data) {
								
							}
						});
					}
				}); // fin sortable
			
		});
	</script>
    
    
	 <div class="col-xs-24 col-sm-14 col-sm-offset-5 ">
        <div class="panel panel-primary blue_panel-primary" >
            <div class="panel-heading blue_panel-heading">
                <h4 class="panel-title blue_panel-title text-center"><small>Current Substitute Priority</small></h4>
            </div>
             <ul style="list-style:none;" class="text-left sub_list" >
                
					<? $count=1;
                    foreach($active_roster as $fffl_player_id){ 
						 ?>
                        <li class="pointer " id="<? echo $fffl_player_id; ?>">
                             <span>
                             	<small>
                                	<? echo $count.'. <small><small><div class="glyphicon glyphicon-menu-hamburger" aria-hidden="true" style="display:inline; color:#aaa;"></div></small></small>&nbsp;'.player_name_no_link($fffl_player_id); $count++;?>
                                </small>
                             </span>
                        </li>
                   <? }
                    ?>
            </ul>
        </div>
	</div>
    
    



<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/