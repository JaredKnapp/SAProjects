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

    public function get_datatables($sort = array(), $columnOrder = array(), $searchColumns = array(), $where = array())
    {
        $this->_get_datatables_query($sort, $columnOrder, $searchColumns, $where);
        if(array_key_exists('length', $_POST) && $_POST['length'] != -1) $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($sort = array(), $columnOrder = array(), $searchColumns = array(), $where = array())
    {
        $this->_get_datatables_query($sort, $columnOrder, $searchColumns, $where);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    private function _get_datatables_query($sort, $columnOrder, $searchColumns, $where)
    {
        $this->db->select($this->table.'.*', FALSE);
        $this->db->select('workloads.name AS workload', FALSE);
        $this->db->select('industries.name AS industry', FALSE);
        $this->db->select('platforms.name AS platform', FALSE);
        $this->db->select('CONCAT(users.firstname, " ", users.lastname) AS sa', FALSE);
        $this->db->select('efforttypes.name AS effort_type', FALSE);
        $this->db->select('vflatprojecttasks.effortoutput AS effort_output', FALSE);
        $this->db->from( $this->table );
        $this->db->join( 'workloads', 'workloads.id = '.$this->table.'.workloads_id' , 'left' );
        $this->db->join( 'industries', 'industries.id = workloads.industries_id' , 'left' );
        $this->db->join( 'platforms', 'platforms.id = '.$this->table.'.platforms_id' , 'left' );
        $this->db->join( 'users', 'users.id = '.$this->table.'.sa_users_id' , 'left' );
        $this->db->join( 'efforttypes', 'efforttypes.id = '.$this->table.'.efforttypes_id' , 'left' );
        $this->db->join( 'vflatprojecttasks', 'vflatprojecttasks.projects_id = '.$this->table.'.id', 'left');
        $index = 0;

        foreach ($this->searchColumns as $item) // loop column
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {

                if($index===0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if(count($this->searchColumns) - 1 == $index) //last loop
                    $this->db->group_end(); //close bracket
            }
            $index++;
        }

        if($where){
            foreach($where as $key=>$name){
                $this->db->where($key, $name);
            }
        }

        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->columnOrder[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
}