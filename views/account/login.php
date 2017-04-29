<?PHP
	/**
	 * login view.
	 *
	 * includes form for login. Accepts username and password
	 *		and sends data to Account controller for validation
	 *		Forgot username asks for username and email
	 */

?>
	<script type="text/javascript">

		// Ajax posts
		$(document).ready(function() {
			
			//On click of login button, this sends password and usernmae to Account.php login 
			//function. Validation occurs and returns json data placing an error message in
			//div error_message if failed, or redirects to base_url if success. A please wait
			//message is shown first in case the user's device lags
			$("#button").click(function() {
				//show please wait message in blue box
				$("#error_message").removeClass("alert-danger");
				$("#error_message").addClass("alert-info");
				$("#error_message").html("Please Wait...");
				//get the values from user and password
				var username = $("#username").val();
				var password = $("#password").val();
				$.ajax({
					type:"POST",
                  	dataType: "json",
					url: "<?php echo base_url(); ?>Account/login",
					data: {password:password, username:username},
					success: function(result){
						//if its validated, it redirects to base_url
                      if(result.login_result) {
                       	window.location.replace("<?php base_url(); ?>");
                      } 
					  //it fails, so post error message returned by Account.php and turn message to red
					  else {
						 $("#error_message").toggleClass("alert-info alert-danger");
                      	 $("#error_message").html(result.error_message);
                      }
            
					}//succss function
				})//ajax							 
			})//button.click

        });//document.ready
			
	</script>
        
        <div id="content_area">
            <div class="row" id="login"> <!--login box--> 
                <div class="col-xs-24" >
                    <?php 
                        //begins the login form.
                        //echo form_open();
                        //display error messages
                        echo "<div class='alert text-center' role='alert' id='error_message'>"; 
                        if (isset($message_display)) 
                        {
							echo "<div class ='alert alert-info text-center' role='alert' id='message'>";
                            echo $message_display;
							
                        }
                        if (isset($error_message)) 
                        {
							echo "<div class ='alert alert-info text-center' role='alert' id='message'>";	
                            echo $error_message;
							echo "</div>";
                        }
                        echo validation_errors();
                        echo "</div>"; //display error_msg
                    
                    //login form itself
                    ?>
                    <label>Username :</label>
                    <p>
                        <input type="text" name="username" id="username" <?php if(isset($username)) { echo "value='".$username."'"; } else { echo 'placeholder="username"'; } ?> />
                    </p>
                    <label>Password :</label>
                    <p>
                        <input type="password" name="password" id="password" placeholder="**********" />
                    </p>
                    <!-- End of the form, begin submit button -->
									
                    <button class="btn btn-primary btn-center" name="button" id="button">Login</button>
                    <!--Link to register -->
                    <div style="padding-top:10px">
                        <p>
                          <?php echo anchor('Account/register','Register to Join the FFFL Waiting List') ?>
                        </p>
                        <!--Link to retrieve password -->
                        <p>
                          <?php echo anchor('Account/password','Forgot Password?') ?>
                        </p>
                        <!--Link to retrieve username -->
                        <p>
                          <?php echo anchor('Account/username','Forgot Username?') ?>
                        </p>
                    </div>

                    <?php //echo form_close(); ?>
                </div> <!-- end login div -->
        	</div><!--end row containing login box-->
        </div> <!-- end content_area div -->
	</div>


<?PHP 
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/