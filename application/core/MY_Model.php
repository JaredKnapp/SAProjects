<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    protected $table = NULL;

    public function __construct($table){
        parent::__construct();
        $this->table = $table;

        $this->load->database();

        $this->load->library('audit', array('ignore'=>array('created', 'modified')));
    }

    public function get_by_id($id)
    {
        $this->db->select( "{$this->table}.*", FALSE );

        $query = $this->db->get_where($this->table, array("{$this->table}.id" => $id));
        return $query->row();
    }

    public function get(array $whereList = array(), array $orderList = array())
    {
        $this->db->select( "{$this->table}.*", FALSE );
        $this->db->from($this->table);

        foreach($whereList as $fieldname=>$value)
        {
            if(is_numeric($fieldname))
            {
                $this->db->where($value);
            }
            else
            {
                $this->db->where(array($fieldname=>$value));
            }
        }

        foreach($orderList as $order){
            if(is_array($order))
            {
                $field = $order[0];
                $direction = sizeof($order) > 1 ? $order[1] : 'ASC';
                $this->db->order_by($field, $direction);
            }
            else
            {
                $this->db->order_by($order);
            }
        }

        $sql = $this->db->get_compiled_select(null, FALSE);
        //echo $sql."\n\r";

        $query = $this->db->get();
        return $query->result_array();
    }

    public function delete_by_id($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
    }

    public function get_datatables($columnOrder = array(), $searchColumns = array(), $searchText = null, $where = array(), $order = array())
    {
        $this->_get_datatables_query($columnOrder, $searchColumns, $searchText, $where, $order);

        if(array_key_exists('length', $_POST) && $_POST['length'] != -1) $this->db->limit($_POST['length'], $_POST['start']);

        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($columnOrder = array(), $searchColumns = array(), $searchText = null, $where = array(), $order = array())
    {
        $this->_get_datatables_query($columnOrder, $searchColumns, $searchText, $where, $order);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    protected function _get_datatables_query($columnOrder, $searchColumns, $searchText, $where, $order){
        die("_get_datatables_query: Method must be overridden by a child class.");
    }

    protected function _audit($action, $id, $parent_id, $data = array(), $oldData = array()){
        $author_id = $this->authorization->get_id();

        if($action===Audit::DBINSERT){
            $this->audit->log_insert($author_id, $this->table, $id, $parent_id, implode("\n", $data));
        } elseif($action===Audit::DBDELETE){
            $this->audit->log_delete($author_id, $this->table, $id, $parent_id);
        } elseif($action===Audit::DBUPDATE){
            $this->audit->log_update($author_id, $this->table, $id, $parent_id, $data, $oldData);
        }
    }

}
