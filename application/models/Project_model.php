<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Project short summary.
 *
 * Project description.
 *
 * @version 1.0
 * @author knappj1
 */
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

    public function set_project()
    {
        $this->load->helper('url');
        $this->load->model('ProjectTask_model');

        $data = array(
            'author_email' => $this->input->post('author_email'),
            'workloads_id' => $this->input->post('workloads_id'),
            'platforms_id' => $this->input->post('platforms_id'),
            'effort_target' => $this->input->post('effort_target'),
            'efforttypes_id' => $this->input->post('efforttypes_id'),
            'effort_justification' => $this->input->post('effort_justification'),
            'notes' => $this->input->post('notes'),
            'status' => 'draft',
            'priority' => 'after',
            'created' => date("Y-m-d H:i:s"),
            'modified' => date("Y-m-d H:i:s")
        );

        $result = $this->db->insert($this->table, $data);
        $id = $this->db->insert_id();
        $efforttypes_id = $this->input->post('efforttypes_id');

        $effortoutputs = $this->input->post('effortoutputs_id');
        foreach($effortoutputs as $key=>$value){
            $desiredDate = $this->input->post($value.'_date');
            $childResult = $this->projecttask_model->set_projecttask(NULL, $id, $efforttypes_id, 'necessary??', $desiredDate);
        }

        return $result;
    }

}