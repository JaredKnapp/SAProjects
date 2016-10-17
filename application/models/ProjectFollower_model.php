<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProjectFollower_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct('projectfollowers');
    }

    public function get_by_project_id($project_id)
    {
        $where = array($this->table . '.projects_id' => $project_id);
        return $this->get($where);
    }

    public function set($data, $id = NULL){
        if(null_or_empty($id)){
            if($this->db->insert($this->table, $data)){
                $id = $this->db->insert_id();
            }
        } else {
            $this->db->update($this->table, $data, array('id'=>$id));
        }

        return $id;
    }

    public function delete($id){
        $this->db->where(array('id'=>$id));
        $this->db->delete($this->table);
    }

    public function delete_by_project_id($project_id)
    {
        $this->db->where(array('projects_id'=>$project_id));
        $this->db->delete($this->table);
    }


}