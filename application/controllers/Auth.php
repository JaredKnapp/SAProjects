<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller{

    private $_userData = array();

    public function __construct(){
        parent::__construct();
        $this->load->model('User_model', 'user');
        $this->load->model('Group_model', 'group');
        $this->load->model('GroupMember_model', 'groupmember');
    }

	public function logged_in_check()
	{
		return ($this->session->userdata("logged_in"));
	}

	public function index()
	{
        $currentPage = $this->session->flashdata('current_page') ? $this->session->flashdata('current_page') : $this->input->post('current_page');

		if(!$this->logged_in_check()){

            $this->load->library('form_validation');
            $this->form_validation->set_rules("email", "Email Address", "trim|valid_email|required");
            $this->form_validation->set_rules("password", "Password", "trim|required");

            if ($this->form_validation->run() == true)
            {
                $email = $this->input->post('email');
                $password = $this->input->post('password');

                $status = $this->login($email, $password);
                if ($status == ERR_INVALID_USERNAME) {
                    $this->session->set_flashdata("error", "Username is invalid");
                }
                elseif ($status == ERR_INVALID_PASSWORD) {
                    $this->session->set_flashdata("error", "Password is invalid");
                }
                else
                {
                    // success
                    // store the user data to session
                    $this->session->set_userdata($this->_userData);
                    $this->session->set_userdata("logged_in", true);

                    // redirect to origin
                    if($currentPage){
                        redirect($currentPage);
                    }
                    redirect("/");
                }
            }

            $data['title'] = 'Login';
            $data['topmenu'] = 'project';
            $data['current_page'] = $currentPage;

            $data['body_content'] = 'login';
            $this->load->view('templates/default', $data);
        } else {
            redirect("/");
        }

	}

    public function login($email, $password)
    {
        $this->db->where("email", $email);
        $query = $this->db->get('users');

        if ($query->num_rows())
        {
            // found row by username
            $row = $query->row_array();

            // now check for the password
            $check = Auth::_crypt($password, $row['password']);
            if ($row['password'] === $check )
            {
                // we not need password to store in session
                unset($row['password']);

                //Store the results in the session
                $this->_userData = $row;
                return ERR_NONE;
            }

            // password not match
            return ERR_INVALID_PASSWORD;
        }
        else {
            // not found
            return ERR_INVALID_USERNAME;
        }
    }

	public function logout()
	{
		$this->session->unset_userdata("logged_in");
		$this->session->sess_destroy();
		redirect("login");
	}

    /* blowfish encryption*/
    private static function _crypt($password, $salt = false) {
		if ($salt === false) {
			$salt = static::_salt(22);
			$salt = vsprintf('$2a$%02d$%s', array(static::$hashCost, $salt));
		}

		$invalidCipher = (
			strpos($salt, '$2y$') !== 0 &&
			strpos($salt, '$2x$') !== 0 &&
			strpos($salt, '$2a$') !== 0
		);
		if ($salt === true || $invalidCipher || strlen($salt) < 29) {
		    //'Invalid salt: %s for %s Please visit http://www.php.net/crypt and read the appropriate section for building %s salts.'
			return '';
		}

        $check = crypt($password, $salt);
		return $check;
	}

}
