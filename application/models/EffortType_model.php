<?php
defined('BASEPATH') or exit('No direct script access allowed');

class EffortType_model extends MY_Model{

    public function __construct(){
        parent::__construct('efforttypes');

        $this->load->model('ProjectTask_model', 'projecttask');
    }

    public function get_list(){
        $data = array();

        $this->db->select( 'id', FALSE );
        $this->db->select( 'name', FALSE );
        $this->db->from($this->table);
        $this->db->order_by('name', 'ASC');

        $query = $this->db->get();
        foreach($query->result_array() as $row){
            $data[$row['id']]=$row['name'];
        }

        return $data;
    }
}