<?PHP
	/**
	 * username view.
	 *
	 * includes form for username and email to send password to user.
	 *
	 */
?>


        
        <div id="content_area">
			<div class="row" id="login"> <!--login box--> 
                <div class="col-xs-24" >
                    <?php 
                        //begins the login form.
                        echo form_open(base_url().'Account/username');
                        //display error messages
                        
                        if (isset($message_display)) 
                        {
                            echo $message_display;
                        }
                        if (isset($error_message)) 
                        {
                            echo "<div class='alert alert-danger text-center' role='alert'>";
							echo $error_message; 
							echo validation_errors();
							echo "</div>"; //display error_msg
                        }
                        
                       
				
				//login form itself
				echo '<h5 class="text-center">Provide your email address.</h5>';
				?>
				<label>Email :</label>
				<p>
					<input type="email" name="email" id="email" placeholder="email@email.com"/>
				</p>
				<!-- End of the form, begin submit button -->
                <div class='submit_button_container text-center'>
                    <button type="submit" class="btn btn-primary btn-center" name="submit"/>Get Username</button>			
                    <!--Link to retrieve username -->
                    <div style="padding-top:10px">
                      <?php echo anchor('Account/password','Forgot Password?') ?>
                    </div>
                </div><!-- end submit button container -->
				<?php echo form_close(); ?>
								

			</div> <!-- end login div -->
		</div> <!-- end content_area div -->
	</div></div>


<?PHP 
/*End of file login.php*/
/*Location: ./application/veiws/Account/username.php*/