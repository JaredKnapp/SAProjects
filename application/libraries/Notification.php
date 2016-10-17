<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification
{
    private $CI;
    private $from;

    public function __construct()
    {
        $this->from = SAP_RETURNEMAIL;

        $this->CI = &get_instance();
        $this->CI->load->model('Email_model', 'emailmodel');
    }

    public function newproject($author_email, $cc_list, $project_id, $project)
    {
        $to = array($author_email);
        $cc = is_null($cc_list) ? array() : $cc_list;
        $subject = "New SA Project SAPID# $project_id";
        $message = "A new project has been created for you:\n".
            $project['effort_target']."\n\n".
            'You may access this project record using one of the following links:'."\n".
            'Read-Only link: http://sap.solarch.lab.emc.com'.site_url('project').'?search='.str_pad($project_id, 5, '0', STR_PAD_LEFT)."\n".
            'Editable link:  http://sap.solarch.lab.emc.com'.site_url('architect/SAView').'?search='.str_pad($project_id, 5, '0', STR_PAD_LEFT)."\n";

        $this->_create($to, $cc, $subject, $message);
    }

    public function projectupdate($author_email, $cc_list, $project_id, $project)
    {
        $to = array($author_email);
        $cc = is_null($cc_list) ? array() : $cc_list;
        $subject = "Update to SA Project SAPID# $project_id";
        $message = "The following project has recently been updated:\n".
            "Project SAPID# $project_id: ".$project['effort_target']."\n\n".
            'You may access this project record using one of the following links:'."\n".
            'Read-Only link: http://sap.solarch.lab.emc.com'.site_url('project').'?search='.str_pad($project_id, 5, '0', STR_PAD_LEFT)."\n".
            'Editable link:  http://sap.solarch.lab.emc.com'.site_url('architect/SAView').'?search='.str_pad($project_id, 5, '0', STR_PAD_LEFT)."\n";

        $this->_create($to, $cc, $subject, $message);
    }


    private function _create($to, $cc, $subject, $message){
        $this->CI->emailmodel->set($to, $cc, $subject, $message);
    }

}