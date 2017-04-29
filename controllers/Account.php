<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Account extends MY_Controller
{
	/**
	 * Account controller.
	 *
	 * accepts requests for login, logout, register and profile views
	 * preps login, register and profile update data then requests 
	 *		from owners model to insert the registration or profile 
	 *		update or to validate the login.
	 * requests login info be sent to user by email
	 */
	
	//Load the needed libraries.  
	public function __construct() 
    {
		parent::__construct();

		$this->load->helper('form');
		$this->load->helper('date');
		$this->load->helper('cookie');
		
		$this->load->library('form_validation');
		$this->load->library('upload');

		$this->load->model('Owners');
		$this->load->model('Teams');
		
		$league_id=1; //***NI*** ELIMINATE THIS WHEN A MEANS TO ADD MORE LEAGUES IS CREATED
		
	}
  
  

	// Loads the view, whether it's the login, register or profile page
	// requests views from MY_Controller load_view function
	public function index($page='login', $content_data=array()) 
    {
		//Just in case already logged in we don't want to
		//go back to login.  Go to Home instead.
		if ($this->session->userdata('logged_in') && ($page === 'login' || $page === 'register' || $page === 'password' || $page==='username'))
		{
			redirect();
		}
		
		//titles of the pages will be upper cased either Register Login or Update Profile
		$title = str_replace('_',' ',$page);
		$content_data['title']= ucwords($title);
		$path ='account/'.$page;
		$this->load_view($path, $content_data, true);
	}
  
  
  
	//request to update profile
	public function update($user_id=NULL)
	{
		//RESTRICTED TO LOGGED IN MEMBERS ONLY
		//Just in case not logged in we direct to login page
		if (!$this->session->userdata('logged_in') )
		{
			redirect("/Restricted");
		} 
		if(is_NULL($user_id))
		{
			$user_id = $this->session->userdata('user_id');
		}
		//security check 
      	//if security level isnt 3 set user_id back to session
      	if($this->session->userdata('security_level')<3)
        {
        	$user_id= $this->session->userdata('user_id');
        }
		$this->register(TRUE, $user_id);
	}
  
  
  
	// Validate and store registration data in database
	public function register($update=FALSE, $user_id=NULL) 
    {
		if($this->session->userdata('logged_in')==TRUE)
		{
			$update=TRUE;
			if(is_NULL($user_id))
			{
				$user_id=$this->session->userdata('user_id');
			}
		}

		// Check validation of registration data from register.php view form
		$this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
		$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_rules('occupation', 'Occupation', 'trim|required');
		$this->form_validation->set_rules('birth_month', 'Month', 'trim|required');
		$this->form_validation->set_rules('birth_day', 'Day', 'trim|required');
		$this->form_validation->set_rules('birth_year', 'Year', 'trim|required');
		$this->form_validation->set_rules('city', 'City', 'trim|required');
		$this->form_validation->set_rules('state', 'State', 'trim|required');
		if($update === FALSE)
		{
			$this->form_validation->set_rules('password', 'Password', 'trim|required|matches[password_confirmation]|sha1');
			$this->form_validation->set_rules('password_confirmation', 'Confirm Password', 'trim|required');
			$this->form_validation->set_rules('reference', 'Reference', 'trim|required');
			$this->form_validation->set_rules('answer', 'Answer', 'trim|required');
			$this->form_validation->set_rules('security', 'Security', 'trim|required|strtolower|matches[answer]');
			$this->form_validation->set_rules('username', 'Username', 'trim|required|is_unique[Owners.username]');
		}
		else
		{
			$this->form_validation->set_rules('password', 'Password', 'trim|matches[password_confirmation]|sha1');
			$this->form_validation->set_rules('password_confirmation', 'Confirm Password', 'trim');
			$this->form_validation->set_rules('username', 'Username', 'trim|required');
			if($this->session->userdata('security_level')==3){
				$this->form_validation->set_rules('security_level', 'Security', 'trim|required');
			}
			
		}
		
		//If the form validation hasn't been submitted, then we render the view
		if ($this->form_validation->run() == FALSE) 
        {
			if($update===FALSE)
			{
				$page='register';
				$data=array();
        		$this->index($page,$data);
			}
			else
			{
				$page='update_profile';
				$data= $this->Owners->get_profile_information($user_id);
				$data['user_security_level'] = $this->session->userdata('security_level');
				$data['user_id'] = $user_id;
        		$this->index($page,$data);
			}
			
		} 
		//data submitted is submitted and good, let's insert it in the database
      	else 
        {
			//prep birthdate to string
			$date_of_birth = $this->input->post('birth_year').'-'.$this->input->post('birth_month').'-'.$this->input->post('birth_day').' 00:00:00';
			$date_of_birth = human_to_unix($date_of_birth);
			
			//all the data needed for registration in array to send to owners model
			$data = array(
					
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'username' => $this->input->post('username'),
					'email' => $this->input->post('email'),
					'password' => $this->input->post('password'),
					'date_of_birth' => $date_of_birth,
					'city' => $this->input->post('city'),
					'state' => $this->input->post('state'),
					'occupation' => $this->input->post('occupation'),
				);

			//inserting a new registration
			if($update===FALSE)
			{
				$data['user_id'] = $this->session->userdata('user_id');
				$data['reference'] = $this->input->post('reference');
				//send the data to the model for validation and/or insertion
				$result = $this->Owners->insert_registration($data) ;
				//successful registration, send user to login view
				if ($result == TRUE) 
				{
					$data['message_display'] = 'Registration Successful';
					$page='login';
				} 
				//failed registration, back to register view
				else 
				{
					$data['message_display'] = 'Error:';
					$page='register';
				}
			}
			//Update an existing profile
			else 
			{
				$data['user_id'] = $user_id;
				if( $this->session->userdata('security_level')==3){
					//add security level to the update
					$data['security_level']=$this->input->post('security_level');
				}
				
				
				//send the data to the model for validation and/or update
				$result = $this->Owners->update_profile($data) ;
				//successful update, send admin user back to populated owner profile
				if ($result == TRUE) 
				{
					$data= $this->Owners->get_profile_information($user_id);
					$data['user_security_level'] = $this->session->userdata('security_level');
					$data['user_id'] = $user_id;
					$data['message_display'] = 'Profile Updated';
					$page='update_profile';
					
				} 
				//failed update, back to update view
				else 
				{
					$data['message_display'] = 'Error:';
					$page='update_profile';
				}
			}
			
          	//request the appropriate view
      		$this->index($page, $data);
		}
		
	}
  
  

	// Validate a user's login
	public function login() 
    {

		$this->form_validation->set_rules('username', 'Username', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|sha1');
		//if user hasn't logged in this displays the view
		if ($this->form_validation->run() == FALSE) 
        {
			$this->index('login');
		} 
		//validation running, send to model to validate username/password
      	else 
        {
			//array to send to owners model
			$data = array(
				'username' => $this->input->post('username'),
				'password' => $this->input->post('password')
			);
          $result = $this->Owners->login($data);//result of login validation
			
			//login info matches
			if($result == TRUE)
            {
				//gather needed information from database about user for cookie
				$result = $this->Owners->get_session_data($this->input->post('username'));
				
				if($result != FALSE)
                {
					//add data to session cookie
                  $league_id=1; //when new league feature added, replace this
					$sess_array = array(
						'username' => $this->input->post('username'),
						'user_id' => $result[0]->user_id,
						'security_level' => $result[0]->security_level,
						'team_id' => $this->Teams->get_team_id($result[0]->user_id,$league_id),
						'league_id' => $league_id, //league_id declared in construct, but when new league feature is added, update this
						'logged_in' => TRUE
					);
					$this->session->set_userdata($sess_array);
					
					
					//send back to Home page
                  //redirect();
                  $data = array(
                    'login_result' => true
					);
                  echo json_encode($data);
				}
			}
			//login failed, back to login page
          	else
            {
				$data = array(
					'error_message' => 'Invalid Username or Password',
					'username' => $this->input->post('username'),
                  	'login_result' => false
				);
				//$this->index('login', $data);
				echo json_encode($data);
			}
		}
	}
  
  

	// Logout by removing session data and going back to logout page
	public function logout($confirm_logout=FALSE) 
    {
		//RESTRICTED TO LOGGED IN MEMBERS ONLY
		//Just in case not logged in we direct to login page
		if (!$this->session->userdata('logged_in') )
		{
			redirect("/Restricted");
		} 
		if($confirm_logout===FALSE) 
		{
			$this->index('logout');
		}
		 
		else
		{
			// Removing session data
			$sess_array = array(
				'username' ,
				'user_id' ,
				'logged_in' ,
				'security_level',
				'team_id',
				'league_id'
			);
			//unset($_SESSION[$sess_array]);
			$this->session->unset_userdata($sess_array);
			$data['message_display'] = 'Logged out.';
			$this->index('logout', $data);
		}
		
	}
  
  
	
	// Send a user's password to him
	public function password() 
    {

		$this->form_validation->set_rules('username', 'Username', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

		if ($this->form_validation->run() == FALSE) 
        {
			$this->index('password');
		} 
		//validation running, send to model to validate username/email combination
      	else 
        {
			//array to send to owners model
			$data = array(
				'username' => $this->input->post('username'),
				'email' => $this->input->post('email')
			);
			$result = $this->Owners->reset_password($data);
			
			//retrieveal info matches
			if($result == TRUE)
            {
				$data = array(
					'error_message' => 'Check your email for your password. Allow a few minutes for delivery.'
				);
				//send back to login
				$this->index('login',$data);
			}
			//login failed, back to login page
          	else
            {
				$data = array(
					'error_message' => 'Username and Email did not match.'
				);
				$this->index('password', $data);
			}
		}
	}
	
  
  
	// Send a user's username(s) to him
	public function username() 
    {
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

		if ($this->form_validation->run() == FALSE) 
        {
			$this->index('username');
		} 
		//validation running, send to model to validate username/email combination
      	else 
        {
			//array to send to owners model
			$data = array(
				'email' => $this->input->post('email')
			);
			$result = $this->Owners->retrieve_username($data);
			
			//retrieveal info matches
			if($result == TRUE)
            {
				$data = array(
					'error_message' => 'Check your email for your username. Allow a few minutes for delivery.'
				);
				//send back to login
				$this->index('login',$data);
			}
			//login failed, back to login page
          	else
            {
				$data = array(
					'error_message' => 'Email address not found.'
				);
				$this->index('username', $data);
			}
		}
	}
  
  
  
	//upload an owner's personal image
	function upload_owner_image()
	{
		//the file name of the image uploaded
		$file_name = $this->input->post('file_name');

		//sets the image to be placed in owners folder
		$config['upload_path'] = './assets/img/owners/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['file_name'] = $file_name;
		
		//run the upload
		$this->load->library('upload');
		$this->upload->initialize($config);
		
		//if the upload doesn't run, return the errors from codeigniter
		if ( ! $this->upload->do_upload('userfile'))
		{
			$status='error';
			$msg = $this->upload->display_errors('','');

		}
		//the upload is running fine, process it
		else
		{
			//get the image location including file name and extension
			$data = $this->upload->data();
			$image_path = $data['full_path'];
			//if it's there, decrease size to limit download size
			//and remove extension so don't have to store name 
			//of each owner's image in database
			if(file_exists($image_path))
			{
				$config['image_library'] = 'gd2';
				$config['source_image'] = $image_path;
				$config['maintain_ratio'] = TRUE;
				$config['width']         = 250;			
				$this->load->library('image_lib', $config);
				$this->image_lib->resize();
				//if the no_extension was set in post then strip
				//the extension
				if($this->input->post('no_extension')) {
					
					$temp = explode('.', $image_path);
					$ext  = array_pop($temp);
					$without_extension = implode('.', $temp);
					rename($image_path,$without_extension);
				}
				$status = "success";
				$msg = "File successfully uploaded";
			}
			//the file wasn't there, so return error
			else
			{
			  $status = "error";
			  $msg = "There was an error. Please try again. If it persists, please contact the administrator.";
			}
			@unlink($_FILES[$file_name]);
 		}
		//send the success or error status and the message
		echo json_encode(array('status' => $status, 'msg' => $msg));
	}//end function upload
	
	
	
}//end Class Account extends MY_Controller

/*End of file account.php*/
/*Location: ./application/controllers/account.php*/

