<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Project_model extends CI_Model{

    protected $table = 'projects';

    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('ProjectTask_model', 'projecttask');
    }

    public function get_projects($id = NULL){
        if($id === NULL){
            $query = $this->db->get($this->table);
            return $query->result_array();
        }
        $query = $this->db->get_where($this->table, array('id' => $id));
        return $query->row_array();
    }

    public function get_by_id($id)
    {
        $this->db->select( $this->table.'.*', FALSE );
        $this->db->select( 'vflatprojecttasks.effortoutput_id AS effortoutput_id' );
        $this->db->select( 'workloads.industries_id AS industries_id', FALSE );
        $this->db->join( 'workloads', 'workloads.id = ' . $this->table . '.workloads_id', 'left' );
        $this->db->join( 'vflatprojecttasks', 'vflatprojecttasks.projects_id = ' . $this->table . '.id', 'left');
        $query = $this->db->get_where($this->table, array($this->table . '.id' => $id));

        return $query->row();
    }

    public function set_project($data)
    {
        $this->load->helper('url');
        $id = null;

        $data['modified'] = date("Y-m-d H:i:s");

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

    public function delete_by_id($id)
    {
        $projecttasks = $this->projecttask->get_list(array('projects_id'=>$id));
        foreach($projecttasks as $projecttask){
            $this->projecttask->delete_by_id($projecttask['id']);
        }

        $this->db->where('id', $id);
        $this->db->delete($this->table);
    }

    public function get_datatables($sort = array(), $columnOrder = array(), $searchColumns = array(), $searchText = null, $where = array(), $order = array())
    {
        $this->_get_datatables_query($sort, $columnOrder, $searchColumns, $searchText, $where, $order);
        if(array_key_exists('length', $_POST) && $_POST['length'] != -1) $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($sort = array(), $columnOrder = array(), $searchColumns = array(), $searchText = null, $where = array(), $order = array())
    {
        $this->_get_datatables_query($sort, $columnOrder, $searchColumns, $searchText, $where, $order);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    private function _get_datatables_query($sort, $columnOrder, $searchColumns, $searchText, $where, $order)
    {
        $this->db->select( $this->table.'.*', FALSE );
        $this->db->select( 'workloads.name AS workload', FALSE );
        $this->db->select( 'industries.name AS industry', FALSE );
        $this->db->select( 'platforms.name AS platform', FALSE );
        $this->db->select( 'CONCAT(users.firstname, " ", users.lastname ) AS sa', FALSE );
        $this->db->select( 'efforttypes.name AS effort_type', FALSE );
        $this->db->select( 'vflatprojecttasks.effortoutput AS effort_output', FALSE );
        $this->db->from( $this->table );
        $this->db->join( 'workloads', 'workloads.id = '.$this->table.'.workloads_id' , 'left' );
        $this->db->join( 'industries', 'industries.id = workloads.industries_id' , 'left' );
        $this->db->join( 'platforms', 'platforms.id = '.$this->table.'.platforms_id' , 'left' );
        $this->db->join( 'users', 'users.id = '.$this->table.'.sa_users_id' , 'left' );
        $this->db->join( 'efforttypes', 'efforttypes.id = '.$this->table.'.efforttypes_id' , 'left' );
        $this->db->join( 'vflatprojecttasks', 'vflatprojecttasks.projects_id = '.$this->table.'.id', 'left');

        $isFirst = TRUE;
        if( $searchText )
        {
            foreach ( $this->searchColumns as $item )
            {

                if( $isFirst )
                {
                    $this->db->group_start();
                    $this->db->like( $item, $searchText );
                    $isFirst = FALSE;
                }
                else
                {
                    $this->db->or_like( $item, $searchText );
                }
            }
            if(! $isFirst) $this->db->group_end();
        }

        $isFirst = TRUE;
        $whereString = "";
        if($where && count($where>0)){
            foreach($where as $key=>$value){
                $whereString .= ($isFirst?'':' AND ').$key. ' "' . $value . '"';
                $isFirst = FALSE;
            }
            $this->db->where($whereString);
        }

        if( $order && count( $order ) > 0 ){
            foreach( $order as $key=>$value ){
                $this->db->order_by( $key, $value );
            }
        }

        $sql = $this->db->get_compiled_select(null, FALSE);
        return $sql;
    }
}