<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProjectTask_model extends MY_Model{

    public function __construct(){
        parent::__construct('projecttasks');
        $this->load->database();
    }

    public function get_projecttask($where){
        $query = $this->db->get_where($this->table, $where);
        return $query->row();
    }

    public function get_list($where){
        $query = $this->db->get_where($this->table, $where);
        return $query->result_array();
    }

    public function get($where){
        $this->db->select( $this->table.'.*', FALSE );

        $this->db->from($this->table);
        $this->db->where($where);

        $this->db->order_by('name', 'ASC');

        $sql = $this->db->get_compiled_select(null, FALSE);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function set_projecttask($id = NULL, $projectsID = null, $effortOutputsID = NULL, $projectedStartDate = NULL, $estimatedCompletionDate = NULL, $duration = NULL, $completionDate = NULL, $collateralURL = NULL){

        $data = array(
            'id'                        => $id,
            'effortoutputs_id'          => $effortOutputsID,
            'projects_id'               => $projectsID,
            'name'                      => '-see effort-',
            'projected_start_date'      => empty($projectedStartDate) ? NULL : date('Y-m-d', strtotime($projectedStartDate)),
            'estimated_completion_date' => empty($estimatedCompletionDate) ? NULL : (date('Y-m-d', strtotime($estimatedCompletionDate))),
            'duration'                  => empty($duration) ? 0 : $duration,
            'completion_date'           => empty($completionDate) ? NULL : date('Y-m-d', strtotime($completionDate)),
            'collateralurl'             => $collateralURL,
            'modified'                  => date("Y-m-d H:i:s")
        );

        if(!array_key_exists('id', $data) || empty($data['id'])){
            $data['created'] = date("Y-m-d H:i:s");
            if($this->db->insert($this->table, $data)){
                $id = $this->db->insert_id();
            }
        } else {
            $this->db->update($this->table, $data, array('id'=>$data['id']));
            $id = $data['id'];
        }

        return $id;
    }

    public function delete_by_id($id){
        $this->db->where('id', $id);
        $this->db->delete($this->table);
    }
}