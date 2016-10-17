<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Email_model extends MY_Model
{
    public function __construct(){
        parent::__construct('emails');
    }

    public function set($to = array(), $cc = array(), $subject = NULL, $message = NULL){
        $id = null;
        $data = array(
            'from'              => SAP_RETURNEMAIL,
            'to'                => implode(',', $to),
            'cc'                => implode(',', is_null($cc) ? array() : $cc),
            'subject'           => $subject,
            'body'              => $message,
            'created'           => date("Y-m-d H:i:s")
        );

        //Messages can't be Updated, only Created
        if($this->db->insert($this->table, $data)){
            $id = $this->db->insert_id();
        }

        return $id;
    }

    public function markSent($id){
        $data['date_sent'] = date("Y-m-d H:i:s");
        $this->db->update($this->table, $data, array('id'=>$id));
    }
}