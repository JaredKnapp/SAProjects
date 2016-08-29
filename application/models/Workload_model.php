<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Workload_model extends MY_Model{

    public function __construct(){
        parent::__construct('workloads');
        $this->load->database();
    }

    public function get_list($industries_id = NULL){
        $data = array();

        $this->db->order_by('name', 'ASC');
        $query = $this->db->get_where($this->table, array('industries_id' => $industries_id));
        foreach($query->result_array() as $row){
            $data[$row['id']]=$row['name'];
        }

        return $data;
    }
}