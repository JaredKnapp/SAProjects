<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Group_model extends MY_Model{

    public function __construct(){
        parent::__construct('groups');
    }

    public function set_group($data)
    {
        $id = null;

        if(!array_key_exists('id', $data) || empty($data['id'])){
            if($this->db->insert($this->table, $data)){
                $id = $this->db->insert_id();
            }
        } else {
            $this->db->update($this->table, $data, array('id'=>$data['id']));
            $id = $data['id'];
        }

        return $id;
    }

    public function get_list(){
        $data = array();

        $this->db->order_by('name', 'ASC');
        $query = $this->db->get($this->table);
        foreach($query->result_array() as $row){
            $data[$row['id']]=$row['name'];
        }

        return $data;
    }

    protected function _get_datatables_query($columnOrder, $searchColumns, $searchText, $where, $order)
    {
        $this->db->select( $this->table.'.*', FALSE );
        $this->db->from( $this->table );

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
