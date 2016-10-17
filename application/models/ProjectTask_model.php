<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProjectTask_model extends MY_Model{

    public function __construct(){
        parent::__construct('projecttasks');

        $this->load->database();
        $this->load->helper('string');
    }

    public function get_projecttask($where){
        $query = $this->db->get_where($this->table, $where);
        return $query->row();
    }

    public function set_projecttask($id = NULL, $projectsId = null, $effortOutputsId = NULL, $projectedStartDate = NULL, $estimatedCompletionDate = NULL, $duration = NULL, $completionDate = NULL, $collateralURL = NULL){

        $data = array(
            'id'                        => $id,
            'effortoutputs_id'          => $effortOutputsId,
            'projects_id'               => $projectsId,
            'name'                      => '-see effort-',
            'projected_start_date'      => null_or_empty($projectedStartDate) ? NULL : date('Y-m-d', strtotime($projectedStartDate)),
            'estimated_completion_date' => null_or_empty($estimatedCompletionDate) ? NULL : (date('Y-m-d', strtotime($estimatedCompletionDate))),
            'duration'                  => null_or_empty($duration) ? 0 : $duration,
            'completion_date'           => null_or_empty($completionDate) ? NULL : date('Y-m-d', strtotime($completionDate)),
            'collateralurl'             => $collateralURL,
            'modified'                  => date("Y-m-d H:i:s")
        );

        if(null_or_empty($id)){
            $data['created'] = date("Y-m-d H:i:s");
            if($this->db->insert($this->table, $data)){
                $id = $this->db->insert_id();
                $this->_audit(Audit::DBINSERT, $id, $projectsId, array($data['effortoutputs_id']));
            }
        } else {
            $old = $this->get_by_id($id);
            $this->db->update($this->table, $data, array('id'=>$id));
            $this->_audit(Audit::DBUPDATE, $id, $projectsId, $data, $old);
        }

        return $id;
    }

}