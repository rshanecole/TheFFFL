<?PHP
	/**
	 * supplemental selection view.
	 *
	 * through ajax will reorder supplemental selections
	 */
	d($this->_ci_cached_vars);
	//$this->output->enable_profiler(TRUE);
?>
	<script type="text/javascript" src="<? echo base_url(); ?>assets/js/jquery-ui.min.js"></script>
    <script src="<? echo base_url(); ?>assets/js/jquery.ui.touch-punch.min.js"></script>
    <link rel="stylesheet" href="<? echo base_url(); ?>assets/css/jquery-ui.min.css" />
	<script type="text/javascript">
		
			$('.gow_radio').on('click', function(){

					var gow_a_team = $(this).attr("data-gow_a");
					var gow_b_team = $(this).attr("data-gow_b");
					
					//change order in the database using Ajax
					$.ajax({
						url: "<?php  echo base_url(); ?>Admin/set_gow/",
						type: 'POST',
						data: {gow_a:gow_a_team,gow_b:gow_b_team},
						success: function(data) {
							
						}
					})
			});
				
			

	</script>
		<div id="test_area"></div>
        <div id="selections" class="col-xs-24 " >
        	<div class="panel panel-primary blue_panel-primary " >
            	<div class="panel-heading blue_panel-heading">
                    <h3 class="panel-title blue_panel-title">
						<strong>Week <? echo $games['0']['week']; ?> Games</strong>
                   	</h3>
                </div>
                <div class="panel-body">
                        <form>
                        	
                        	  
							<?php
							
							$priority = 0;
                            foreach ($games as $data) {
								$priority++;
								if($priority==1){ $selected = 'checked'; } else { $selected = ''; }
                            ?> 
                               
                                	
                             <div class="row">     
                                <input type="radio" class="col-xs-2 gow_radio" data-gow_a="<?  echo $data['opponent_a']; ?>" data-gow_b="<?  echo $data['opponent_b']; ?>" <? echo $selected; ?> name="gow" >
                               	</input>      
                                        <label style="font-family:Arial, Helvetica, sans-serif;display:inline"><small><strong>
                                        <?  
										echo team_name_no_link($data['opponent_a']).' vs '.team_name_no_link($data['opponent_b']); ?>
                                        </strong></small>
                                        </label>
                                
                               
      						</div>
                            <?php
								
                            }
							
                            ?>
                            
                        </form>

               </div>
            </div>
		</div>
    	
        
        
     </div> 


       


<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/