<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Project_model extends CI_Model{

    protected $table = 'projects';

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function get_projects($id = NULL){
        if($id === NULL){
            $query = $this->db->get($this->table);
            return $query->result_array();
        }
        $query = $this->db->get_where($this->table, array('id' => $id));
        return $query->row_array();
    }

    public function set_project($data)
    {
        $this->load->helper('url');
        $this->load->model('ProjectTask_model');

        $data['created'] = date("Y-m-d H:i:s");
        $data['modified'] = date("Y-m-d H:i:s");

        $result = $this->db->insert($this->table, $data);
        $id = $this->db->insert_id();
        $efforttypes_id = $this->input->post('efforttypes_id');
        $desiredDate = $this->input->post('desired_completion_date');

        $effortoutputs = $this->input->post('effortoutputs_id');
        foreach($effortoutputs as $key=>$value){
            $childResult = $this->ProjectTask_model->set_projecttask(NULL, $id, $efforttypes_id, 'necessary??', $desiredDate);
        }

        return $result;
    }

}