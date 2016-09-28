<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Project_model extends MY_Model{

    public function __construct(){
        parent::__construct('projects');

        $this->load->model('ProjectTask_model', 'projecttask');
        $this->load->model('ProjectNote_model', 'projectnote');

        $this->audit->add_to_ignore('priority_index');
    }

    public function get_projects($id = NULL, $where = array(), $order = array()){
        if($id === NULL){
            $this->db->select( $this->table.'.*', FALSE );
            $this->db->select( "lpad(convert($this->table.id, char), 5, '0') AS sapid", FALSE);

            $this->db->from( $this->table );

            $this->db->join( 'workloads', 'workloads.id = '.$this->table.'.workloads_id' , 'left' );
            $this->db->join( 'industries', 'industries.id = workloads.industries_id' , 'left' );
            $this->db->join( 'platforms', 'platforms.id = '.$this->table.'.platforms_id' , 'left' );
            $this->db->join( 'users', 'users.id = '.$this->table.'.sa_users_id' , 'left' );
            $this->db->join( 'efforttypes', 'efforttypes.id = '.$this->table.'.efforttypes_id' , 'left' );
            $this->db->join( 'vflatprojecttasks', 'vflatprojecttasks.projects_id = '.$this->table.'.id', 'left');

            $isFirst = TRUE;
            $whereString = "";
            if($where && count($where>0)){
                foreach($where as $key=>$value){
                    if(is_array($value)){
                        $whereString .= ($isFirst ? '' : ' AND ') . $key . ' ("' . implode('","', $value) . '")';
                    } else {
                        $whereString .= ($isFirst ? '' : ' AND ') . $key . ' "' . $value . '"';
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

            $query = $this->db->get();
            return $query->result_array();
        }

        $query = $this->db->get_where($this->table, array('id' => $id));
        return $query->row_array();
    }

    public function get_by_id($id)
    {
        $this->db->select( $this->table.'.*', FALSE );
        $this->db->select( "lpad(convert($this->table.id, char), 5, '0') AS sapid", FALSE);
        $this->db->select( 'vflatprojecttasks.effortoutput_id AS effortoutput_id' );
        $this->db->select( 'workloads.industries_id AS industries_id', FALSE );
        $this->db->select( '(select sum(`projecttasks`.duration) from `projecttasks` where `projecttasks`.projects_id = projects.id) as `task_duration`', FALSE);
        $this->db->select( '(select max(`projecttasks`.estimated_completion_date) from `projecttasks` where `projecttasks`.projects_id = projects.id) as `task_estimated_completion_date`', FALSE);
        $this->db->join( 'workloads', 'workloads.id = ' . $this->table . '.workloads_id', 'left' );
        $this->db->join( 'vflatprojecttasks', 'vflatprojecttasks.projects_id = ' . $this->table . '.id', 'left');
        $query = $this->db->get_where($this->table, array($this->table . '.id' => $id));

        return $query->row();
    }

    public function set_project($data)
    {
        $id = null;

        if(array_key_exists('sapid', $data)){ unset($data['sapid']); }
        $data['modified'] = date("Y-m-d H:i:s");

        if(!array_key_exists('id', $data) || empty($data['id'])){
            $data['created'] = date("Y-m-d H:i:s");

            $this->db->insert($this->table, $data);
            $id = $this->db->insert_id();
            $this->_audit(Audit::DBINSERT, $id, NULL, array($data['effort_target']));
        } else {
            $id = $data['id'];
            $old = $this->get_by_id($id);
            $this->db->update($this->table, $data, array('id'=>$id));
            $this->_audit(Audit::DBUPDATE, $id, NULL, $data, $old);
        }

        return $id;
    }

    public function delete_by_id($id)
    {
        $projectnotes = $this->projectnote->get(array('projects_id'=>$id));
        foreach($projectnotes as $projectnote){
            $this->projectnote->delete_by_id($projectnote['id']);
        }

        $projecttasks = $this->projecttask->get_list(array('projects_id'=>$id));
        foreach($projecttasks as $projecttask){
            $this->projecttask->delete_by_id($projecttask['id']);
        }

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

    protected function _get_datatables_query($columnOrder, $searchColumns, $searchText, $where, $order)
    {
        $this->db->select( $this->table.'.*', FALSE );
        $this->db->select( "lpad(convert($this->table.id, char), 5, '0') AS sapid", FALSE);
        $this->db->select( 'workloads.name AS workload', FALSE );
        $this->db->select( 'industries.name AS industry', FALSE );
        $this->db->select( 'platforms.name AS platform', FALSE );
        $this->db->select( 'CONCAT(users.firstname, " ", users.lastname ) AS sa', FALSE );
        $this->db->select( 'efforttypes.name AS effort_type', FALSE );
        $this->db->select( 'vflatprojecttasks.effortoutput AS effort_output', FALSE );
        $this->db->select( 'vflatprojecttasks.produce AS effort_output_produce', FALSE );
        $this->db->select( 'vflatprojecttasks.duration AS effort_output_duration', FALSE );
        $this->db->select( 'vflatprojectnotesbyproject.notes AS notes', TRUE );
        $this->db->select( '(select sum(`projecttasks`.duration) from `projecttasks` where `projecttasks`.projects_id = projects.id) as `task_duration`', FALSE);
        $this->db->select( '(select max(`projecttasks`.estimated_completion_date) from `projecttasks` where `projecttasks`.projects_id = projects.id) as `task_estimated_completion_date`', FALSE);

        $this->db->from( $this->table );
        $this->db->join( 'workloads', 'workloads.id = '.$this->table.'.workloads_id' , 'left' );
        $this->db->join( 'industries', 'industries.id = workloads.industries_id' , 'left' );
        $this->db->join( 'platforms', 'platforms.id = '.$this->table.'.platforms_id' , 'left' );
        $this->db->join( 'users', 'users.id = '.$this->table.'.sa_users_id' , 'left' );
        $this->db->join( 'efforttypes', 'efforttypes.id = '.$this->table.'.efforttypes_id' , 'left' );
        $this->db->join( 'vflatprojecttasks', 'vflatprojecttasks.projects_id = '.$this->table.'.id', 'left');
        $this->db->join( 'vflatprojectnotesbyproject', 'vflatprojectnotesbyproject.projects_id = '.$this->table.'.id', 'left');

        $isFirst = TRUE;
        if( $searchText && preg_match("/^\d{5}$/", $searchText)) {
            //Searching ONLY by SAPID
            $this->db->having("sapid", $searchText, 1);
        } else {
            if($searchText){
                foreach ( $searchColumns as $item ){

                    if( $isFirst ){
                        $this->db->group_start();
                        $this->db->like( $item, $searchText );
                        $isFirst = FALSE;
                    } else {
                        $this->db->or_like( $item, $searchText );
                    }
                }
                if(! $isFirst) $this->db->group_end();
            }

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