<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    protected $table = NULL;

    public function __construct($table){
        parent::__construct();
        $this->table = $table;

        $this->load->database();
    }

    public function get_by_id($id)
    {
        $this->db->select( $this->table.'.*', FALSE );
        $query = $this->db->get_where($this->table, array($this->table . '.id' => $id));

        return $query->row();
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
}
