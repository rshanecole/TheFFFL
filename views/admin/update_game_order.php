<?PHP
	/**
	 * supplemental selection view.
	 *
	 * through ajax will reorder supplemental selections
	 */
	//d($this->_ci_cached_vars);
	//$this->output->enable_profiler(TRUE);
?>
	<script type="text/javascript" src="<? echo base_url(); ?>assets/js/jquery-ui.min.js"></script>
    <script src="<? echo base_url(); ?>assets/js/jquery.ui.touch-punch.min.js"></script>
    <link rel="stylesheet" href="<? echo base_url(); ?>assets/css/jquery-ui.min.css" />
	<script type="text/javascript">
		
      
		
		$(function() {

			//sortable selections
			$('#sortable').sortable({
				axis: 'y',
				opacity: 0.7,
				handle: 'span',
				update: function(event, ui) {
					$(this).find(".priority").html('<? $image_properties = array('src' => base_url().'assets/img/loading.gif', 'height'=>'11'); echo img($image_properties); ?>');
					var list_sortable = $(this).sortable('toArray').toString();
					// change order in the database using Ajax
					$.ajax({
						url: "<?php echo base_url(); ?>Admin/adjust_fa_draft_order/",
						type: 'POST',
						data: {list_order:list_sortable},
						success: function(data) {
							
						}
					});
				}
			}); // fin sortable
			
			
		});
	</script>
		
        <div id="selections" class="col-xs-24 " >
        	<div class="panel panel-primary blue_panel-primary " >
            	<div class="panel-heading blue_panel-heading">
                    <h3 class="panel-title blue_panel-title">
						<strong>Week <? echo $week; ?> Games</strong></h3><h5></h5>
                </div>
                
                        <ul id="sortable" class="table table-striped" style="padding-left:0px;">
                        	
							<?php
							
						
                            foreach ($games as $data) {
								
                            ?> 
                               
                                	
                                    
                                    <div class="col-xs-17" >
                               
                                    		
                                            <div style="font-family:Arial, Helvetica, sans-serif;display:inline"><small><strong>
                                            <? echo team_name_no_link($team_a).' ('.$record_a.') vs '.team_name_no_link($team_b); ?>
                                            </strong></small>
                                            </div>
                                   
                                    </div>
      
                            <?php
								
                            }
							
                            ?>
                        </ul>

               
            </div>
		</div>
    	
        
        
     </div> 


       


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/