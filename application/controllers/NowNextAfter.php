<?php

class NowNextAfter extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Project_model', 'project');
    }

    public function index(){
        $this->load->helper('url');

        $data['title'] = 'Now Next After Report';

        $this->load->view('templates/header', $data);
        $this->load->view('project/nownextafter');
        $this->load->view('templates/footer');
    }

    public function ajax_list(){
        $list = $this->project->get_datatables();
        $data = array();
        $index = $this->input->post('start');
        foreach($list as $project){
            $index++;
            $row = array();
            $row[] = $project->industry;
            $row[] = $project->priority;
            $row[] = $project->workload;
            $row[] = $project->sa;
            $row[] = $project->effort_target;
            $row[] = $project->effort_type;
            $row[] = $project->effort_output;
            $row[] = $project->notes;
            $row[] = $project->estimated_completion_date;
            $row[] = $project->status;

            //add html for action
            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_person('."'".$project->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
                  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_person('."'".$project->id."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';

            $data[] = $row;
        }

        $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->project->count_all(),
                "recordsFiltered" => $this->project->count_filtered(),
                "data" => $data
        );

        echo json_encode($output);
    }
}