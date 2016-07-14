<?php

class SAView extends CI_Controller {

    var $columnOrder    = array('industry','priority','workload','sa','effort_target', 'effort_type', 'effort_output', 'notes', 'estimated_completion_date', 'status',null);
    var $searchColumns  = array('industries.name', 'projects.priority', 'workloads.name', 'users.firstname', 'users.lastname', 'projects.effort_target', 'vflatprojecttasks.effortoutput', 'efforttypes.name', 'projects.notes');
    var $sort           = array('industry'=>'asc');

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Project_model', 'project');
    }

    public function index(){
        $this->load->helper('url');

        $data['title'] = 'SA Project View';

        $this->load->view('templates/header', $data);
        $this->load->view('project/saview');
        $this->load->view('templates/footer');
    }

    public function ajax_list(){
        $list = $this->project->get_datatables($this->sort, $this->columnOrder, $this->searchColumns);

        $priorityList = unserialize(SAP_PRIORITYLIST);
        $statusList = unserialize(SAP_STATUSLIST);

        $data = array();
        $index = $this->input->post('start');
        foreach($list as $project){
            $index++;
            $row = array();
            $row[] = $project->industry;
            $row[] = array_key_exists($project->priority, $priorityList) ? $priorityList[$project->priority] : '';
            $row[] = $project->workload;
            $row[] = $project->sa;
            $row[] = $project->effort_target;
            $row[] = $project->effort_type;
            $row[] = $project->effort_output;
            $row[] = $project->notes;
            $row[] = $project->estimated_completion_date;
            $row[] = array_key_exists($project->status, $statusList) ? $statusList[$project->status] : '';

            //add html for action
            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_person('."'".$project->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
                  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_person('."'".$project->id."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';

            $data[] = $row;
        }

        $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->project->count_all(),
                "recordsFiltered" => $this->project->count_filtered($this->sort, $this->columnOrder, $this->searchColumns),
                "data" => $data
        );

        echo json_encode($output);
    }
}