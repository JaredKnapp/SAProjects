<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BuildNotifications extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

		$this->load->model('Email_model', 'emailmodel');
        $this->load->model('Setting_model', 'setting');
    }

    public function run(){
        $this->load->model('AuditHistory_model', 'audithistory');

        $to = array();
        $cc = array();
        $subject = "SA Project Update Summary";
        $message = "";

        $currentTime = date("Y-m-d H:i:s");
        $lastUpdateSaved = $this->setting->get_value(SAP_SETTING_LASTWATCHERUPDATE);
        $lastUpdate = $lastUpdateSaved ? $lastUpdateSaved : $currentTime;

        $where = array(
            "(`audit_history`.`created` BETWEEN '{$lastUpdate}' AND '{$currentTime}')"
            );
        $order = array(
            array('`watchaddress`', 'ASC'),
            array('`projects`.`id`', 'ASC'),
            array('`audit_history`.`created`', 'ASC')
            );

        $currentAddress = NULL;
        $projectId = NULL;
        $auditResults = $this->audithistory->get_notificationlist($where, $order);
        foreach($auditResults as $row){

            if(is_null($currentAddress) || $currentAddress != $row['watchaddress'])
            {
                //Save current email first...
                if(!is_null($currentAddress)){
                    $this->_save($to, $cc, $subject, $message);
                }

                //Reset for new email
                $currentAddress = $row['watchaddress'];
                $projectId = NULL;

                $to = array($currentAddress);
                $message = "The following projects have recently been updated:\n\n";

            }

            if(is_null($projectId) || $projectId != $row['projects_id'])
            {
                $projectId = $row['projects_id'];
                $message .= "\n================================\n" .
                "Project SAPID# {$row['projects_id']}: {$row['effort_target']}\n" .
                'link: http://sap.solarch.lab.emc.com'.site_url('architect/SAView').'?search='.str_pad($row['projects_id'], 5, '0', STR_PAD_LEFT) . "\n" .
                "--------------------------------\n";
            }


            $message = $message . $this->_build_message($row) . "\n\n";
        }

        if(!is_null($currentAddress))
        {
            // Save email if we have one sitting in the Queue
            $this->_save($to, $cc, $subject, $message);
        }

        $this->setting->set_value(SAP_SETTING_LASTWATCHERUPDATE, $currentTime);
    }

    private function _save($to, $cc, $subject, $message)
    {
        //echo 'to: ';
        //foreach($to as $value){
        //    echo " $value";
        //}
        //echo "\n";

        //echo "subject: $subject \n";
        //echo "message: $message \n";
        //echo "\n";

        $this->emailmodel->set($to, $cc, $subject, $message);
    }

    private function _build_message(array $row = array())
    {
        $message = "";
        switch ($row['table']) {
            case 'projects':
                $message = $message . 'Project';
                switch ($row['action']) {
                    case 'insert':
                        $message = $message . " created: '{$row['new_value']}'.";
                        break;
                    case 'update':
                        $message = $message . " updated: Field {$row['field_name']} = '{$row['new_value']}'.";
                        break;
                    default:
                        $message = $message . " {$row['action']}.";
                        break;
                }
                break;
            case 'projecttasks':
                $message = $message . 'Task';
                switch ($row['action']) {
                    case 'insert':
                        $message = $message . " created: '{$row['projecttask']}'.";
                        break;
                    case 'update':
                        $message = $message . " updated: Field {$row['field_name']} = '{$row['new_value']}'.";
                        break;
                    default:
                        $message = $message . " {$row['action']}.";
                        break;
                }
                break;
            case 'projectnotes':
                $message = $message . 'Note';
                switch ($row['action']) {
                    case 'insert':
                        $message = $message . " created: '{$row['new_value']}'.";
                        break;
                    case 'update':
                        $message = $message . " updated: Field {$row['field_name']} = '{$row['new_value']}'.";
                        break;
                    default:
                        $message = $message . " {$row['action']}.";
                        break;
                }
                break;
            default:
                $message = $message . $row['table'];
                switch ($row['action']) {
                    case 'insert':
                        $message = $message . " created: '{$row['new_value']}'.";
                        break;
                    case 'update':
                        $message = $message . " updated: Field {$row['field_name']} = '{$row['new_value']}'.";
                        break;
                    default:
                        $message = $message . " {$row['action']}.";
                        break;
                }
                break;
        }

        return $message;

    }

}