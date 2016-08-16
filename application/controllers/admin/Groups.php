<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Groups extends MY_Controller{

    public function __construct()
    {
        //Allow only 'admin' group members
        parent::__construct(array(SAP_ADMINISTRATORGROUP));

        $this->load->helper('url');

        $this->load->model('Group_model', 'group');
        $this->load->model('GroupMember_model', 'groupmember');
    }

    public function index(){

        $data['topmenu'] = 'admin';
        $data['title'] = 'Groups';

        $data['body_content'] = 'admin/groups/index';
        $this->load->view('templates/default', $data); //, $data);
    }

    public function ajax_index(){
        $columnOrder    = array('id', 'name', 'code');
        $searchColumns  = array();
        $where          = array();
        $order          = array('name'=>'ASC');

        if(isset($_POST['order']))
        {
            $order = array_merge($order,  array($columnOrder[$_POST['order']['0']['column']] => $_POST['order']['0']['dir']));
        }

        $list = $this->group->get_datatables($columnOrder, $searchColumns, NULL, $where, $order);

        $data = array();
        $index = $this->input->post('start');
        foreach($list as $group){
            $index++;
            $row = array();
            $row[] = $group->id;
            $row[] = $group->name;
            $row[] = $group->code;
            $row[] = ''; //

            $data[] = $row;
        }

        $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->group->count_all(),
                "recordsFiltered" => $this->group->count_filtered($columnOrder, $searchColumns, NULL, $where, $order),
                "data" => $data
        );

        echo json_encode($output);
    }

    public function ajax_add()
    {
        if($this->_validate()){

            $record = array(
                'name'              => $this->input->post('name'),
                'code'              => $this->input->post('code')
            );

            if($this->group->set_group($record)){
                echo json_encode(array("status" => TRUE));
            } else {
                $data = array();
                $data['error_string'] = array('author_email');
                $data['inputerror'] = array('Email Address is required');
                $data['status'] = FALSE;
                echo json_encode($data);
            }
        }
    }

    public function ajax_edit($id)
    {
        $data = $this->group->get_by_id($id);
        echo json_encode($data);
    }

    public function ajax_update()
    {
        if($this->_validate()){
            $record = array(
                'id'                        => $this->input->post('id'),
                'name'              => $this->input->post('name') == '' ? NULL: $this->input->post('name'),
                'code'              => $this->input->post('code') == '' ? NULL: $this->input->post('code')
            );

            $this->group->set_group($record);

            echo json_encode(array("status" => TRUE));
        }
    }

    public function ajax_delete($id)
    {
        $this->group->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_memberindex(){

        $id = array_key_exists('groupId', $_POST) ? $_POST['groupId'] : array();

        $columnOrder    = array('id', 'name', 'member_table');
        $searchColumns  = array();
        $where          = array('groupmembers.groups_id = '=>$id);
        $order          = array('name'=>'ASC');

        $list = $this->groupmember->get_datatables($columnOrder, $searchColumns, NULL, $where, $order);

        $data = array();
        $index = $this->input->post('start');
        foreach($list as $group){
            $index++;
            $row = array();
            $row[] = $group->id;
            $row[] = empty($group->users_name) ? $group->groups_name : $group->users_name;
            $row[] = $group->member_table;
            $row[] = ''; //

            $data[] = $row;
        }

        $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->groupmember->count_all(),
                "recordsFiltered" => $this->groupmember->count_filtered($columnOrder, $searchColumns, NULL, $where, $order),
                "data" => $data
        );

        echo json_encode($output);
    }

    private function _validate()
    {
        $this->load->library('form_validation');

        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        $this->form_validation->set_rules('name', 'Group Name', 'required');
        $this->form_validation->set_rules('code', 'Code', 'required');
        if(!$this->form_validation->run()){
            $errors = $this->form_validation->error_array();
            foreach($errors as $fieldname=>$error){
                $data['inputerror'][] = $fieldname;
                $data['error_string'][] = $error;
                $data['status'] = FALSE;
            }
        }

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
        }
        return $data['status'];
    }

}