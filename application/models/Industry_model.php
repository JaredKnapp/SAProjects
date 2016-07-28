<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Industry_model extends CI_Model{

    protected $table = 'industries';

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function get_list(){
        $data = array();

        $this->db->order_by('name', 'ASC');
        $query = $this->db->get($this->table);
        foreach($query->result_array() as $row){
            $data[$row['id']] = $row['name'];
        }

        return $data;
    }

    public function cleanup_priority_index($industryId, $platformId){
        $this->load->model('Project_model', 'project');

        $where = array();
        $where['industries.id = '] = $industryId;
        $where['platforms.id = '] = $platformId;
        $where['projects.status IN'] = array_keys(unserialize(SAP_ACTIVESTATUSLIST));

        $order = array('industries.name'=>'ASC', 'platforms.sortorder'=>'ASC', 'priority_index'=>'ASC');

        $projects = $this->project->get_projects(NULL, $where, $order);

        $index = 0;
        $saList = array();
        foreach($projects as $project){
            $index++;
            $isChanged = false;
            if($project['priority_index'] != $index){
                $project['priority_index'] = $index;
                $isChanged = true;
            }

            $userId = strval($project['sa_users_id']);
            if(array_key_exists($userId, $saList)){
                if ($saList[$userId] === 'now') $saList[$userId] = 'next';
                else if ($saList[$userId] === 'next') $saList[$userId] = 'after';
                else if ($saList[$userId] === 'after') $saList[$userId] = 'beyond';
            } else {
                $saList[$userId] = 'now';
            }

            if($project['priority'] != $saList[$userId]){
                $project['priority'] = $saList[$userId];
                $isChanged = true;
            }

            if($isChanged){
                $this->project->set_project($project);
            }
        }
    }
}