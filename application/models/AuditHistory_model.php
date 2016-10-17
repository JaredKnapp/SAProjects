<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AuditHistory_model extends MY_Model{

    public function __construct(){
        parent::__construct('audit_history');
    }


    public function insert($session_key, $author_id, $action, $table, $row_id, $parent_row_id, $field_name = null, $old_value = null, $new_value = null)
    {
        $id = null;
        $created = date("Y-m-d H:i:s");
        $data = compact('session_key', 'author_id', 'action', 'table', 'row_id', 'parent_row_id', 'field_name', 'old_value', 'new_value', 'created');

        if($this->db->insert($this->table, $data))
        {
            $id = $this->db->insert_id();
        }

        return $id;
    }

    public function get(array $where = array(), array $order = array())
    {
        $this->db->select("projects.*", FALSE);
        $this->db->select("users.*", FALSE);
        $this->db->select('effortoutputs.name AS projecttask', FALSE);

        $this->db->join( 'projects', "(`{$this->table}`.`table`='projects' AND `projects`.`id` = `{$this->table}`.`row_id`) OR ((`{$this->table}`.`table`='projectnotes' OR `{$this->table}`.`table`='projecttasks') AND `projects`.`id` = `{$this->table}`.`parent_row_id`)" , 'left' );
        $this->db->join( 'effortoutputs', "`{$this->table}`.`table`='projecttasks' AND `{$this->table}`.`action`='insert' AND `effortoutputs`.`id` = `{$this->table}`.`new_value`" , 'left' );
        $this->db->join( 'users', "`users`.`id` = `{$this->table}`.`author_id`" , 'left' );

        return parent::get($where, $order);
    }

    public function get_notificationlist(array $where = array(), array $order = array())
    {
        $this->db->select("projectfollowers.email as watchaddress", FALSE);
        $this->db->select("projects.id as projects_id", FALSE);
        $this->db->select("projects.effort_target as effort_target", FALSE);
        $this->db->select("audit_history.created as audit_date", FALSE);
        $this->db->select("audit_history.id as audithistory_id", FALSE);
        //$this->db->select("projects.*", FALSE);
        //$this->db->select("users.*", FALSE);
        $this->db->select('effortoutputs.name AS projecttask', FALSE);

        $this->db->from('projectfollowers');

        $this->db->join('projects', "(`{$this->table}`.`table`='projects' AND `projects`.`id` = `{$this->table}`.`row_id`) OR ((`{$this->table}`.`table`='projectnotes' OR `{$this->table}`.`table`='projecttasks') AND `projects`.`id` = `{$this->table}`.`parent_row_id`)" , 'left' );
        $this->db->join('effortoutputs', "`{$this->table}`.`table`='projecttasks' AND `{$this->table}`.`action`='insert' AND `effortoutputs`.`id` = `{$this->table}`.`new_value`" , 'left' );
        $this->db->join('users', "`users`.`id` = `{$this->table}`.`author_id`" , 'left' );

        $this->db->where('`projects`.`id` = `projectfollowers`.`projects_id`');

        return parent::get($where, $order);
    }



}