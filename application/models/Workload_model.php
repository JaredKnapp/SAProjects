<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Workload_model extends CI_Model{

    protected $table = 'workloads';

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function get_list($industries_id = NULL){
        $data = array();

        $query = $this->db->get_where($this->table, array('industries_id' => $industries_id));
        foreach($query->result_array() as $row){
            $data[$row['id']]=$row['name'];
        }

        return $data;
    }
}