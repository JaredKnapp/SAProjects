<?php
defined('BASEPATH') or exit('No direct script access allowed');

class EffortOutput_model extends CI_Model{

    protected $table = 'effortoutputs';

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function get_list($efforttypes_id = NULL){
        $data = array();

        $this->db->order_by('sortorder', 'ASC');
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get_where($this->table, array('efforttypes_id' => $efforttypes_id));
        foreach($query->result_array() as $row){
            $data[$row['id']]=$row['name'] . ': ' . $row['duration'] . ' days' . (empty($row['produce'])?'':(' ('.$row['produce'].')'));
        }

        return $data;
    }
}