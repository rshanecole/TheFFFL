<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Restricted extends MY_Controller
{
	/**
	 * Restricted controller.
	 *
	 * Simply redirects to page for non-logged in users
	 *			for now redirects to login. Exists so no need 
	 *			to write this code on every restricted page
	 */
	
	//Load the needed libraries.  
	public function __construct() 
    {
		parent::__construct();
		
		}


	public function index() 
    {
			redirect("/Account/login");
		}
  
  
  
	
}//end Class Restricted extends MY_Controller

/*End of file restricted.php*/
/*Location: ./application/controllers/restricted.php*/

