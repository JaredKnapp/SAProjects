<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model{

    protected $table = 'users';

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function get_salist(){
        $data = array();

        $this->db->where(array('is_sa'=>'1'));
        $this->db->order_by('firstname', 'ASC');
        $this->db->order_by('lastname', 'ASC');
        $query = $this->db->get($this->table);
        foreach($query->result_array() as $row){
            $data[$row['id']]=$row['firstname']. ' ' . $row['lastname'];
        }

        return $data;
    }
}
