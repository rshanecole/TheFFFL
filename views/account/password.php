<?PHP
	/**
	 * password view.
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
                        echo form_open(base_url().'Account/password');
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
				echo '<h5 class="text-center">Provide your username and email address.</h5>';
				?>
				<label>Username :</label>
				<p>
					<input type="text" name="username" id="name" placeholder="username"/>
				</p>
				<label>Email :</label>
				<p>
					<input type="email" name="email" id="email" placeholder="email@email.com"/>
				</p>
				<!-- End of the form, begin submit button -->
                <div class='submit_button_container text-center'>
                    <button type="submit" class="btn btn-primary btn-center" name="submit"/>Get Password</button>			
                    <!--Link to retrieve username -->
                    <div style="padding-top:10px">
                      <?php echo anchor('Account/username','Forgot Username?') ?>
                    </div>
                </div><!-- end submit button container -->
				<?php echo form_close(); ?>
								

			</div> <!-- end login div -->
		</div> 
	</div><!-- end content_area div -->
    </div>

<?PHP 
/*End of file login.php*/
/*Location: ./application/veiws/Account/password.php*/