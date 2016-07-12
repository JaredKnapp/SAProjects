<?php
defined('BASEPATH') or exit('No direct script access allowed');

class EffortType_model extends CI_Model{

    protected $table = 'efforttypes';

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function get_list(){
        $data = array();

        $this->db->order_by('name', 'ASC');
        $query = $this->db->get($this->table);
        foreach($query->result_array() as $row){
            $data[$row['id']]=$row['name'];
        }

        return $data;
    }
}