<?php

class NowNextAfter extends CI_Controller {

    var $columnOrder    = array('industry', 'sa', 'priority', 'workload', 'platform', 'effort_target', 'effort_type', 'effort_output', 'effort_justification', 'notes', 'estimated_completion_date', 'status');
    var $searchColumns  = array('industries.name', 'users.firstname', 'users.lastname', 'projects.priority', 'workloads.name', 'platforms.name', 'projects.effort_target', 'efforttypes.name', 'vflatprojecttasks.effortoutput', 'projects.effort_justification', 'projects.notes');
    var $initialSort    = array('industry'=>'asc');
    var $where          = array('projects.status <>'=>'draft');

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Project_model', 'project');
    }

    public function index(){
        $this->load->helper('url');

        $data['title'] = 'SA Project Report: Now Next After';

        $this->load->view('templates/header', $data);
        $this->load->view('project/nownextafter');
        $this->load->view('templates/footer');
    }

    public function ajax_list(){
        $list = $this->project->get_datatables($this->initialSort, $this->columnOrder, $this->searchColumns, $this->where);

        $priorityList = unserialize(SAP_PRIORITYLIST);
        $statusList = unserialize(SAP_STATUSLIST);

        $data = array();
        $index = $this->input->post('start');
        foreach($list as $project){
            $index++;
            $row = array();
            $row[] = $project->industry;
            $row[] = $project->sa;
            $row[] = array_key_exists($project->priority, $priorityList) ? $priorityList[$project->priority] : '';
            $row[] = $project->workload;
            $row[] = $project->platform;
            $row[] = $project->effort_target;
            $row[] = $project->effort_type;
            $row[] = implode('<br>', explode('||', $project->effort_output));
            $row[] = $project->effort_justification;
            $row[] = $project->notes;
            $row[] = $project->estimated_completion_date;
            $row[] = array_key_exists($project->status, $statusList) ? $statusList[$project->status] : '';

            $data[] = $row;
        }

        $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->project->count_all(),
                "recordsFiltered" => $this->project->count_filtered($this->initialSort, $this->columnOrder, $this->searchColumns, $this->where),
                "data" => $data
        );

        echo json_encode($output);
    }
}