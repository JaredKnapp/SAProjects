<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AuditHistory_model extends CI_Model{

    protected $table = 'audit_history';

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }


    public function insert($session_key, $author_id, $action, $table, $row_id, $parent_row_id, $field_name = null, $old_value = null, $new_value = null){
        $id = null;
        $created = date("Y-m-d H:i:s");
        $data = compact('session_key', 'author_id', 'action', 'table', 'row_id', 'parent_row_id', 'field_name', 'old_value', 'new_value', 'created');

        if($this->db->insert($this->table, $data)){
            $id = $this->db->insert_id();
        }

        return $id;
    }


}