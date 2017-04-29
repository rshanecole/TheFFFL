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
				
				$button.toggleClass("fade");
			}
			

			
			
			var modal = "<!--filter modal -->\
						<div class='modal fade' id='filter_modal' tabindex='-1' role='dialog' aria-labelledby='filter_modal_Label'>\
						  <div class='modal-dialog' role='document'>\
							<div class='modal-content'>\
							  <div class='modal-header'>\
								<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>\
								<h4 class='modal-title' id='filter_modal_Label'>Filter Options</h4>\
							  </div>\
							  <div class='modal-body'><div id='test_area'></div>\
							  	<div id='filter_list'>\
								</div>\
							  </div>\
							  <div class='modal-footer'>\
							  </div>\
							</div>\
						  </div>\
						</div>";
			jQuery(function() {
				$('#modal_area').html(modal);
			
			});
		
		
		
			
		
		

	</script>

        <div id="content_area" class="container-fluid">
			
            
            
            
            <div class="row">
                
                <div id="ajax_display_area" class=" text-center" style="margin-top:0px;">
                    
					<? echo img(base_url().'assets/img/loading.gif'); ?>

                </div>
            </div>
			

        </div> <!-- end content_area div -->
	</div>



<?PHP 
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/