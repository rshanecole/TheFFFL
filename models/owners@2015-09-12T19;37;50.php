<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Owners Model.
	 *
	 * inserts registration, validates logins, gets session data
	 *		updates profiles
	 *		sends login informatin by email upon request from 
	 * 			account controller
	 */
	
Class Owners extends CI_Model 
{
	public function __construct() 
    {
		parent::__construct();
		//$ci = get_instance();
		$this->load->library('encrypt');
		$this->load->library('email');
	}
	
	public function get_owner_first_name($user_id)
	{
		$this->db->select('first_name');
		$this->db->from('Owners');
		$this->db->where('user_id',$user_id);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() === 1) 
		{
			return $query->row('first_name');
		}
		else 
		{
			return NULL;
		}
	}
	
	public function get_owner_last_name($user_id)
	{
		$this->db->select('last_name');
		$this->db->from('Owners');
		$this->db->where('user_id',$user_id);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() === 1) 
		{
			return $query->row('last_name');
		}
		else 
		{
			return NULL;
		}
	}
	
	public function get_owner_email($user_id)
	{
		$this->db->select('email');
		$this->db->from('Owners');
		$this->db->where('user_id',$user_id);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() === 1) 
		{
			return $query->row('email');
		}
		else 
		{
			return NULL;
		}
	}
  
	public function get_owner_security_level($user_id)
	{
		$this->db->select('security_level');
		$this->db->from('Owners');
		$this->db->where('user_id',$user_id);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() === 1) 
		{
			return $query->row('security_level');
		}
		else 
		{
			return NULL;
		}
	}
  
	public function get_owner_username($user_id)
	{
		$this->db->select('username');
		$this->db->from('Owners');
		$this->db->where('user_id',$user_id);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() === 1) 
		{
			return $query->row('username');
		}
		else 
		{
			return NULL;
		}
	}	

	public function get_owner_city($user_id)
	{
		$this->db->select('city');
		$this->db->from('Owners');
		$this->db->where('user_id',$user_id);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() === 1) 
		{
			return $query->row('city');
		}
		else 
		{
			return NULL;
		}
	}

	public function get_owner_state($user_id)
	{
		$this->db->select('state');
		$this->db->from('Owners');
		$this->db->where('user_id',$user_id);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() === 1) 
		{
			return $query->row('state');
		}
		else 
		{
			return NULL;
		}
	}

	public function get_owner_occupation($user_id)
	{
		$this->db->select('occupation');
		$this->db->from('Owners');
		$this->db->where('user_id',$user_id);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() === 1) 
		{
			return $query->row('occupation');
		}
		else 
		{
			return NULL;
		}
	}
	
	public function get_owner_date_of_birth($user_id)
	{
		$this->db->select('date_of_birth');
		$this->db->from('Owners');
		$this->db->where('user_id',$user_id);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() === 1) 
		{
			return $query->row('date_of_birth');
		}
		else 
		{
			return NULL;
		}
	}
	
	// Insert registration data in database
	public function insert_registration($data) 
	{

		// Query to check whether username already exist or not
		$condition = "username =" . "'" . $data['username'] . "'";
		$this->db->select('*');
		$this->db->from('Owners');
		$this->db->where($condition);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() == 0) 
		{
		// Query to insert data in database
		$this->db->insert('Owners', $data);
			if ($this->db->affected_rows() > 0) 
			{
				return true;
			} 
			else 
			{
				return false;
			}
		}
	}

	// Read data using username and password
	public function login($data) {

		$condition = "username =" . "'" . $data['username'] . "' AND " . "password =" . "'" . $data['password'] . "'";
		$this->db->select('*');
		$this->db->from('Owners');
		$this->db->where($condition);
		$this->db->limit(1);
		$query = $this->db->get();

		if ($query->num_rows() == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	// Send password to email upon request. data array must include email and username
	public function reset_password($data) {
		//make sure the email and username match
		$condition = "email =" . "'" . $data['email'] . "' AND " . "username =" . "'" . $data['username'] . "'";
		$this->db->select('user_id');
		$this->db->from('Owners');
		$this->db->where($condition);
		$this->db->limit(1);
		$query = $this->db->get();

		if ($query->num_rows() == 1) {
			//it matches, get the sha1 hash of fffl
			$new_pass = random_string('alnum', 5);
			$update_data = array(
									'password'=>sha1($new_pass)
								);
			//update the account with the reset password
			$this->db->where('username',$data['username']);
			$this->db->update('Owners', $update_data);
			//send email
			$this->email->from('admin@thefffl.com', 'TheFFFL');
			$this->email->to($data['email']); //
			$this->email->subject('Your FFFL login information');
			$this->email->set_mailtype("html");
			$first_name = $this->get_owner_first_name($query->row('user_id'));
			$message = 'Hi '.$first_name.',<br><br>You, or someone impersonating you, requested your password be reset.  Your password has been reset to '.$new_pass.'. Please login using the new password.  Then change your password in your profile.<br><br>Thanks,<br>The FFFL';
			$this->email->message($message);	
			$this->email->send();
			//echo $this->email->print_debugger();
			return true;
		} else {
			//username and email did not match
			return false;
		}
	}
	
	// Send username to email. 
	public function retrieve_username($data) {
		
		$condition = "email = '" . $data['email'] ."'";
		$this->db->select('username, user_id');
		$this->db->from('Owners');
		$this->db->where($condition);
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			
			//send email
			$this->email->from('admin@thefffl.com', 'TheFFFL');
			$this->email->to('coles91@gmail.com'); //$data['email']
			$this->email->subject('Your FFFL login information');
			$this->email->set_mailtype("html");
			$first_name = $this->get_owner_first_name($query->row('user_id'));
			$message = 'Hi '.$first_name.',<br><br>You, or someone impersonating you, requested your username(s). The username(s) associated with this email address are:<br>';
			//list each username from the query
			foreach ($query->result() as $row)
			{
			  $message .= $row->username.'<br>';
			}
			//continue message
			$message .='<br>Thanks,<br>The FFFL';
			$this->email->message($message);	
			$this->email->send();
			//echo $this->email->print_debugger();
			return true;
		} else {
			return false;
		}
	}

	// Read data from database to place data in session cookie
	public function get_session_data($username) 
	{

		$condition = "username =" . "'" . $username . "'";
		$this->db->select('*');
		$this->db->from('Owners');
		$this->db->where($condition);
		$this->db->limit(1);
		$query = $this->db->get();

		if ($query->num_rows() == 1) 
		{
			return $query->result();
		} 
		else 
		{
			return false;
		}
	}
	
	//gather data for populating profile update page
	public function get_profile_information($user_id) {
		$date_of_birth = getdate($this->get_owner_date_of_birth($user_id));
		$birth_month = $date_of_birth['mon'];//date('n',$date_of_birth);
		$birth_day = $date_of_birth['mday'];//date('j',$date_of_birth);
		$birth_year = $date_of_birth['year'];//date('Y',$date_of_birth);
		
		$data = array( 
			'first_name' => $this->get_owner_first_name($user_id),
			'last_name' => $this->get_owner_last_name($user_id),
			'email' => $this->get_owner_email($user_id),
			'username' => $this->get_owner_username($user_id),
			'city' => $this->get_owner_city($user_id),
			'state' => $this->get_owner_state($user_id),
			'occupation' => $this->get_owner_occupation($user_id),
			'birth_month' => $birth_month,
			'birth_day' => $birth_day,
			'birth_year' => $birth_year,
			'security_level' => $this->get_owner_security_level($user_id)
		);
			
		return $data;
	}

		// update registration data in database
	public function update_profile($data) 
	{
		//update timestamp to track last time profile updated
		$data['profile_update'] = now();
		// Query to check whether username already exist or not
		//echo $data['user_id'];
		$condition = "user_id =" . "'" . $data['user_id'] . "'";
		$this->db->where($condition);
		$this->db->limit(1);
		$this->db->update('Owners', $data);
		
		if ($this->db->affected_rows() > 0) 
		{
			return true;
		} 
		else 
		{
			return false;
		}
	}


	
	
}


/*End of file owners.php*/
/*Location: ./application/models/owners.php*/