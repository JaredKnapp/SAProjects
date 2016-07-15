<?php

class SAView extends CI_Controller {

    var $columnOrder    = array('industry', 'sa', 'priority', 'workload', 'platform', 'effort_target', 'effort_type', 'effort_output', 'effort_justification', 'notes', 'estimated_completion_date', 'status', null);
    var $searchColumns  = array('industries.name', 'users.firstname', 'users.lastname', 'projects.priority', 'workloads.name', 'platforms.name', 'projects.effort_target', 'efforttypes.name', 'vflatprojecttasks.effortoutput', 'projects.effort_justification', 'projects.notes');
    var $initialSort    = array('industry'=>'asc');
    var $where          = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Project_model', 'project');
        $this->load->model('EffortType_model', 'effort_type');
        $this->load->model('Industry_model', 'industry');
        $this->load->model('Platform_model', 'platform');
        $this->load->model('User_model', 'user');
    }

    public function index(){
        $this->load->helper('url');

        $data['title'] = 'SA Project View';

        $data['choicesEffortType']          = array('' => 'Select One...') + $this->effort_type->get_list();
        $data['choicesIndustry']            = array('' => 'Select One...') + $this->industry->get_list();
        $data['choicesWorkload']            = array('' => 'Select One...');
        $data['choicesPlatform']            = array('' => 'Select One...') + $this->platform->get_list();
        $data['choicesSAUser']              = array('' => 'Select One...') + $this->user->get_salist();

        $this->load->view('templates/header', $data);
        $this->load->view('project/saview');
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
            $row[] = $project->effort_output;
            $row[] = $project->effort_justification;
            $row[] = $project->notes;
            $row[] = $project->estimated_completion_date;
            $row[] = array_key_exists($project->status, $statusList) ? $statusList[$project->status] : '';

            //add html for action
            $row[] =    '<a class="btn btn-sm btn-primary" title="Move Up"><i class="glyphicon glyphicon-chevron-up"></i></a>
                        <a class="btn btn-sm btn-primary" title="Move Down"><i class="glyphicon glyphicon-chevron-down"></i></a>
                        <a class="btn btn-sm btn-success" href="javascript:void(0)" title="Edit" onclick="edit_project('."'".$project->id."'".')"><i class="glyphicon glyphicon-pencil"></i></a>
                        <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_project('."'".$project->id."'".')"><i class="glyphicon glyphicon-trash"></i></a>';

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