<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProjectTask_model extends CI_Model{

    protected $table = 'projecttasks';

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function set_projecttask($id = NULL, $projectsID = null, $effortTypesID = NULL, $name = NULL, $desiredCompletionDate = NULL, $estimatedDueDate = NULL, $completionDate = NULL){
        $data = array(
            'projects_id' => $projectsID,
            'efforttypes_id' => $effortTypesID,
            'name' => $name,
            'desired_completion_date' => is_null($desiredCompletionDate)?NULL:date('Y-m-d', strtotime($desiredCompletionDate)),
            'estimated_due_date' => is_null($estimatedDueDate)?NULL:(date('Y-m-d', strtotime($estimatedDueDate))),
            'completion_date' => is_null($completionDate)?NULL:date('Y-m-d', strtotime($completionDate)),
            'created' => date("Y-m-d H:i:s"),
            'modified' => date("Y-m-d H:i:s")
        );

        return $this->db->insert($this->table, $data);
    }
}