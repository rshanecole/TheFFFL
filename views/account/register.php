<?PHP  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Register View.
	 *
	 * accepts registration data in form and sends it to Account controller
	 *		for validation. Form rules set in Account controller.
	 * 
	 */
	
?>


		<div id="content_area">
          	<div class="row" id='register'><!--contains register box, just one column with nested rows and columns-->
				<div class="col-xs-24">
  
            	<?php
					//display errors if returned from Account controller
					if (isset($message_display)) {
						echo "<div class='alert alert-warning' role='alert'>";
								echo $message_display;
						echo "</div>";//end error_msg
					}
					echo '<div id="form" >';
					//begin the registration form. Sections divided by <fieldset>
					//Each field is preceded by an error returned by failed validation
					echo form_open(base_url().'Account/register');
					
						//first section, Contact information: name and email
						echo '<div class="form-group">'; //Group: Contact
							echo '<h4>Contact</h4>';
							//first and last names side by side
							echo '<div class="row" id="names">';
								echo '<div class="col-xs-24 ">';
									if(form_error('first_name') || form_error('last_name') || form_error('email')){
										echo "<div class=' alert alert-danger' role='alert'>".
											form_error('first_name').
											form_error('last_name').
											form_error('email').
										'</div>';
									}
								echo '</div>';
								echo '<div class="col-xs-12 col-sm-8" id="first_name" >';
									echo form_label('First Name');
									echo form_input('first_name',set_value('first_name'),"class='form-control'");
								echo '</div>';//end first_name
								echo '<div class="col-xs-12 col-sm-8" id="last_name" >';
									echo form_label('Last Name');
									echo form_input('last_name',set_value('last_name'),"class='form-control'");
								echo '</div>';//end last_name
							//email address
								echo '<div class="col-xs-24 col-sm-8" id="email">';
									echo form_label('Email');
									$data = array(
													'name'=>'email',
													'id'=>'email',
													'type'=>'email',
													'value'=>set_value('email'),
													'class'=>'form-control',
												);
									echo form_input($data);
								echo '</div>';//end email
							echo '</div>'; //end email row
						echo '</div>';//end Contact
						
						//Login information: username and password
						echo '<div class="form-group">'; //Group: Login
							echo '<h4>Login</h4>';
							//first and last names side by side
							echo '<div class="row" id="username">';
								echo '<div class="col-xs-24">';
									if(form_error('username') || form_error('password') ){
										echo "<div class=' alert alert-danger' role='alert'>".
												form_error('username').
												form_error('password').
											'</div>';
									}
								echo '</div>';
								echo '<div class="col-xs-24 col-sm-8" id="username" >';
									echo form_label('Username');
									echo form_input('username',set_value('username'),"class='form-control'");
								echo '</div>';//end username
							//password
								echo '<div class="col-xs-12 col-sm-8" id="password">';
									echo form_label('Password');
									$data = array(
        								'name'  => 'password',
       									'value' => '',
        								'class' => 'form-control',
										'type'	=> 'password'
									);
									echo form_input($data);
								echo '</div>';//end password
								echo '<div class="col-xs-12 col-sm-8" id="confirm">';
									echo form_label('Confirm Password');
									$data = array(
        								'name'  => 'password_confirmation',
       									'value' => '',
        								'class' => 'form-control',
										'type'	=> 'password'
									);
									echo form_input($data);
								echo '</div>';//end password
							echo '</div>'; //end password row
						echo '</div>';//end Login
						
						//Third section owner details: location: city state, occupation, birthday: month day year
						echo '<div class="form-group">'; //Group: owner details
							echo '<h4>Owner Details</h4>';
							//city and state side by side xs, city state occupation sm and up
							echo '<div class="row" id="location">';
								echo '<div class="col-xs-24">';
								if(form_error('city') || form_error('occupation') || form_error('birth_month') || form_error('birth_day') || form_error('birth_year') || form_error('reference')){
									echo "<div class='alert alert-danger' role='alert'>".
											form_error('city').
											form_error('occupation').
											form_error('birth_month').
											form_error('birth_day').
											form_error('birth_year').
											form_error('reference').
										'</div>';
								}
								echo '</div>';
								echo '<div class="col-xs-12 col-sm-8" id="city">';
									echo form_label('City');
									echo form_input('city',set_value('city'),"class='form-control'");
								echo '</div>'; //end city column
								echo '<div class="col-xs-12 col-sm-6" id="state" >';
									echo form_label('State');
									$states_arr = array('AL'=>"Alabama",'AK'=>"Alaska",'AZ'=>"Arizona",'AR'=>"Arkansas",'CA'=>"California",'CO'=>"Colorado",'CT'=>"Connecticut",'DE'=>"Delaware",'DC'=>"District Of Columbia",'FL'=>"Florida",'GA'=>"Georgia",'HI'=>"Hawaii",'ID'=>"Idaho",'IL'=>"Illinois", 'IN'=>"Indiana", 'IA'=>"Iowa",  'KS'=>"Kansas",'KY'=>"Kentucky",'LA'=>"Louisiana",'ME'=>"Maine",'MD'=>"Maryland", 'MA'=>"Massachusetts",'MI'=>"Michigan",'MN'=>"Minnesota",'MS'=>"Mississippi",'MO'=>"Missouri",'MT'=>"Montana",'NE'=>"Nebraska",'NV'=>"Nevada",'NH'=>"New Hampshire",'NJ'=>"New Jersey",'NM'=>"New Mexico",'NY'=>"New York",'NC'=>"North Carolina",'ND'=>"North Dakota",'OH'=>"Ohio",'OK'=>"Oklahoma", 'OR'=>"Oregon",'PA'=>"Pennsylvania",'RI'=>"Rhode Island",'SC'=>"South Carolina",'SD'=>"South Dakota",'TN'=>"Tennessee",'TX'=>"Texas",'UT'=>"Utah",'VT'=>"Vermont",'VA'=>"Virginia",'WA'=>"Washington",'WV'=>"West Virginia",'WI'=>"Wisconsin",'WY'=>"Wyoming");
									echo form_dropdown('state',$states_arr,set_value('state'),"class='form-control'");
								echo '</div>';//end state
								echo '<div class="col-xs-24 col-sm-10" id="occupation">';
									echo form_label('Occupation');
									echo form_input('occupation',set_value('occupation'),"class='form-control'");
								echo '</div>'; //end occupation
								
								echo '<div class="col-xs-24">'.form_label('Birthday:').'</div>';
								echo '<div class="col-sm-16 col-xs-24" id="birthday">';
									echo '<div class="row">';
										echo '<div class="col-xs-8" id="month">';
											$months_arr = array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
											echo form_label('Month');
											echo form_dropdown('birth_month',$months_arr,set_value('birth_month'),"class='form-control'");
										echo '</div>';//end month
										echo '<div class="col-xs-6" id="day" >';
											echo form_label('Day');
											$data = array(
													'name' => 'birth_day',
													'id' => 'birth_day',
													'maxlength' => '2',
													'size' => '2',
													'value' => set_value('birth_day'),
													'class' => 'form-control'
													);
											echo form_input($data);
										echo '</div>';//end day
										echo '<div class="col-xs-10" id="year" >';
											echo form_label('Year');
											$data = array(
													'name' => 'birth_year',
													'id' => 'birth_year',
													'maxlength' => '4',
													'size' => '4',
													'value' => set_value('birth_year'),
													'class' => 'form-control'
													);
											echo form_input($data);
										echo '</div>';//end year
									echo '</div>';//end birthday group	
								echo '</div>';//end birthday
						
								echo '<div class="col-sm-8 col-xs-24" id="reference">';
									echo form_label('Reference');
										echo form_input('reference',set_value('reference'),"class='form-control'");
								echo '</div>';//end reference
							echo '</div>';//end details
						echo '</div>';//end formgroup details
							

						//Keep out the robots
						echo '<div class="row" >';
							echo '<div class="col-xs-24">';
								if(form_error('security')){
									echo "<div class=' alert alert-danger' role='alert'>".
											form_error('security').
										'</div>';
								}
							echo '</div>';
							echo '<div class="col-sm-16 col-xs-24" id="security" >';
								echo form_label('Are you human?').' What is the nickname of the University of Oklahoma athletic teams?';
							echo '</div>'; //label column
							echo '<div class="col-sm-8 col-xs-24">';	
								echo form_input('security',set_value('security'),"class='form-control'");
							echo '</div>';
							echo form_hidden('answer', 'sooners');
						echo '</div>';//end security row
						
						// End of the form, begin submit button
                    	echo '<br><button type="submit" class="btn btn-primary btn-center" name="submit"/>Sign Up</button></p>';	

					echo '</div>';//end form
					echo form_close();
				echo '</div>';//end col for content
			echo '</div>';//end register
				?>
		</div><!--end content_area-->
	</div>

<?PHP
/* End of file register.php */
/* Location: ./application/views/Account/register.php */