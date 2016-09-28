<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Email_model extends MY_Model
{
    public function __construct(){
        parent::__construct('emails');
    }

    public function get($where){
        $this->db->select( $this->table.'.*', FALSE );
        $this->db->from($this->table);
        $this->db->where($where);
        $this->db->order_by('created', 'DESC');

        $sql = $this->db->get_compiled_select(null, FALSE);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function set($from, $to = array(), $cc = array(), $subject = NULL, $message = NULL){
        $id = null;
        $data = array(
            'from'              => $from,
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