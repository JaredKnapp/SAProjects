<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authorization
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->helper('url');
        $this->CI->load->library('session');
    }

    public function is_logged_in(){
        return $this->CI->session->userdata("logged_in");
    }

    public function is_member($groups, $doLogin = FALSE){
        if(!$this->is_logged_in()){
            if($doLogin){
                //Store the return page here so we can come back to it
                $this->CI->session->set_flashdata('current_page', base_url(uri_string()));
                redirect('login');
            }
            return false;
        }

        //Check Group Membership Database
        if(! $this->CI->session->userdata('is_sa') == '1'){
            return false;
        }

        return true;
    }
}