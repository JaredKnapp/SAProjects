<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Project_model extends CI_Model{

    protected $table = 'projects';
    var $column_order = array();
    var $column_search = array();
    var $order = array( 'projects.id' => 'desc' );

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

    public function set_project($data)
    {
        $this->load->helper('url');
        $id = null;

        $data['created'] = date("Y-m-d H:i:s");
        $data['modified'] = date("Y-m-d H:i:s");

        if($this->db->insert($this->table, $data)){
            $id = $this->db->insert_id();
        }

        return $id;
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if(array_key_exists('length', $_POST) && $_POST['length'] != -1) $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    private function _get_datatables_query()
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

        foreach ($this->column_search as $item) // loop column
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

                if(count($this->column_search) - 1 == $index) //last loop
                    $this->db->group_end(); //close bracket
            }
            $index++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
}