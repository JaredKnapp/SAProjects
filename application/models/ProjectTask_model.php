<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProjectTask_model extends CI_Model{

    protected $table = 'projecttasks';

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function get_by_id($id = null){
        $this->db->select( $this->table.'.*', FALSE );
        return $this->get_projecttask(array('id'=>$id));
    }

    public function get_projecttask($where){
        $query = $this->db->get_where($this->table, $where);
        return $query->row();
    }

    public function get_list($where){
        $query = $this->db->get_where($this->table, $where);
        return $query->result_array();
    }

    public function set_projecttask($id = NULL, $projectsID = null, $effortOutputsID = NULL, $name = NULL, $desiredCompletionDate = NULL, $estimatedDueDate = NULL, $completionDate = NULL){
        $data = array(
            'projects_id' => $projectsID,
            'effortoutputs_id' => $effortOutputsID,
            'name' => $name,
            'desired_completion_date' => is_null($desiredCompletionDate)?NULL:date('Y-m-d', strtotime($desiredCompletionDate)),
            'estimated_due_date' => is_null($estimatedDueDate)?NULL:(date('Y-m-d', strtotime($estimatedDueDate))),
            'completion_date' => is_null($completionDate)?NULL:date('Y-m-d', strtotime($completionDate)),
            'created' => date("Y-m-d H:i:s"),
            'modified' => date("Y-m-d H:i:s")
        );

        return $this->db->insert($this->table, $data);
    }

    public function delete_by_id($id){
        $this->db->where('id', $id);
        $this->db->delete($this->table);
    }
}