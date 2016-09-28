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
        $this->CI->load->model('GroupMember_model', 'groupmember');
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
        //  if Admin, then just allow anythihng
        $membership = $this->CI->groupmember->get_membership($this->CI->session->userdata('id'), 'users');
        if(in_array(SAP_ADMINISTRATORGROUP, $membership)){
            return true;
        }

        //  Otherwise, check for specific membership
        if(is_array($groups)){
            foreach($groups as $group){
                if(in_array($group, $membership)){
                    return true;
                }
            }
        } else {
            if(in_array($groups, $membership)){
                return true;
            }
        }

        if($doLogin){
            //Store the return page here so we can come back to it
            $this->CI->session->set_flashdata('current_page', base_url(uri_string()));
            redirect('login');
        }
        return false;
    }

    public function get_id(){
        if($this->is_logged_in()){
            return $this->CI->session->userdata['id'];
        }
        return null;
    }

}