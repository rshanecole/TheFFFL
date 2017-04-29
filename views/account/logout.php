<?PHP   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * logout view.
	 *
	 * asks user if he wants to logout
	 * provides back button and logout button. redirects home after 4 seconds
	 *		when logged out.
	 */
if(isset($message_display)) {
	header('Refresh: 3; URL='.base_url());
}
?>
	
    
		<div id="content_area">
			<div id="logout">
				<?php 
					
					//successful logout message
					if (isset($message_display)) 
					{			
						echo "<div class='alert alert-info text-center' style='margin-bottom:0px' role='alert'>";
							echo $message_display;
						echo "</div>";
					}
					//display the warning to logout first
					else 
					{ 							
						echo form_open(base_url().'Account/logout/TRUE');
						?>
						<div class='alert alert-warning text-center' role='alert'>
                        	Are you sure you want to logout?
                        </div>
                        <div class="text-center" style="padding-top:10px">	
						<button type="submit" class="btn btn-primary btn-center" name="submit"/>Logout</button>
                        <br>
							<!-- escape without logging out. takes back to referring page-->
						
							  
                              <a href="javascript:history.go(-1)">
								  Go Back
							  </a>
						</div>	
						<?php echo form_close(); 
					}
					echo "</div>";//end error_msg div
				?>
			</div><!-- end logout -->
		</div><!-- end content_area -->

<?PHP
/*End of file logout.php*/
/*Location: ./application/views/Account/logout.php*/