<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setting_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct('settings');

        $this->load->helper('string');
    }

    public function get_value($key)
    {
        $this->db->select( $this->table.'.value', FALSE );
        $this->db->from($this->table);
        $this->db->where(array('key'=>$key));

        $query = $this->db->get();
        $result = $query->row_array();

        return null_or_empty($result) ? NULL : $result['value'];
    }

    public function set_value($key, $value)
    {
        $sql = "INSERT INTO {$this->table} (`key`,`value`) VALUES('{$key}','{$value}') ON DUPLICATE KEY UPDATE `value`='{$value}';";
        $this->db->query($sql);
    }
}
