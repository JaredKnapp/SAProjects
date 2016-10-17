<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProjectNote_model extends MY_Model
{
    public function __construct(){
        parent::__construct('projectnotes');
        $this->load->database();
        $this->load->helper('string');
    }

    public function get_notes($where = array(), $searchText = NULL){
        $this->db->select( $this->table.'.*', FALSE );
        $this->db->select( 'CONCAT(users.firstname, " ", users.lastname ) AS user', FALSE );

        $this->db->from($this->table);
        $this->db->join( 'users', 'users.id = '.$this->table.'.users_id' , 'left' );
        $this->db->where($where);

        if($searchText){
            $this->db->group_start()
            ->where("concat(users.firstname, ' ', users.lastname) like", "%$searchText%")
            ->or_where("notes like", "%$searchText%");
            $this->db->group_end();
        }

        $this->db->order_by('created', 'DESC');
        $sql = $this->db->get_compiled_select(null, FALSE);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function set($id = NULL, $projectId = NULL, $projectTaskId = NULL, $userId = NULL, $notes){
        $data = array(
            'notes'             => $notes,
            'projects_id'       => $projectId,
            'projecttasks_id'    => $projectTaskId,
            'users_id'          => $userId,
            'modified'           => date("Y-m-d H:i:s")
        );

        if(null_or_empty($id)){
            $data['created'] = date("Y-m-d H:i:s");
            if($this->db->insert($this->table, $data)){
                $id = $this->db->insert_id();
                $this->_audit(Audit::DBINSERT, $id, $projectId, array($data['notes']));
            }
        } else {
            $old = $this->get_by_id($id);
            $this->db->update($this->table, $data, array('id'=>$id));
            $this->_audit(Audit::DBUPDATE, $id, $projectId, $data, $old);
        }

        return $id;
    }
}