<?php
defined('BASEPATH') or exit('No direct script access allowed');

class GroupMember_model extends MY_Model{

    protected $table = 'groups';

    public function __construct(){
        parent::__construct('groupmembers');
    }

    public function get_list(){
        $data = array();

        $this->db->select($this->table . '.id');
        $this->db->select($this->table . '.member_id');
        $this->db->select($this->table . '.member_table');
        $this->db->select('CONCAT(users.firstname, \' \', users.lastname) AS users_name');
        $this->db->select('groups.name AS groups_name');
        $this->db->from($this->table);
        $this->db->join('users', "$this->table.member_table = \'users\' AND users.id = $this->table.member_id", 'left');
        $this->db->join('groups', "$this->table.member_table = \'groups\' AND groups.id = $this->table.member_id", 'left');
        $query = $this->db->get();

        foreach($query->result_array() as $row){
            $nameField = $row['member_table'] . '_name';
            $data[$row['id']]= $row[$nameField];
        }
        return $data;
    }

    public function get_membership($id, $type){
        $data = array();

        $this->db->select($this->table . '.id');
        $this->db->select('groups.code AS groups_code');
        $this->db->from($this->table);
        $this->db->join('groups', "$this->table.groups_id = groups.id", 'left');

        $this->db->where(array('member_id'=>$id, 'member_table'=>$type));

        $sql = $this->db->get_compiled_select(null, FALSE);
        $query = $this->db->get();

        foreach($query->result_array() as $row){
            $data[]= $row['groups_code'];
        }
        return $data;
    }

    protected function _get_datatables_query($columnOrder, $searchColumns, $searchText, $where, $order)
    {
        $this->db->select( $this->table . '.*', FALSE );
        $this->db->select('CONCAT(users.firstname, \' \', users.lastname) AS users_name');
        $this->db->select('groups.name AS groups_name');
        $this->db->from($this->table);
        $this->db->join('users', "$this->table.member_table = 'users' AND users.id = $this->table.member_id", 'left');
        $this->db->join('groups', "$this->table.member_table = 'users' AND groups.id = $this->table.member_id", 'left');

        $isFirst = TRUE;
        $whereString = "";
        if($where && count($where>0)){
            foreach($where as $key=>$value){
                if(is_array($value)){
                    $whereString .= ($isFirst?'':' AND ').$key. ' ("' . implode('","', $value) . '")';
                } else {
                    $whereString .= ($isFirst?'':' AND ').$key. ' "' . $value . '"';
                }
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