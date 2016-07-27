<?php

class SAView extends CI_Controller {

    var $columnOrder    = array('industry', 'sa', 'priority', 'workload', 'platform', 'effort_target', 'effort_type', 'effort_output', 'effort_justification', 'notes', 'estimated_completion_date', 'status', null);
    var $searchColumns  = array('industries.name', 'users.firstname', 'users.lastname', 'projects.priority', 'workloads.name', 'platforms.name', 'projects.effort_target', 'efforttypes.name', 'vflatprojecttasks.effortoutput', 'projects.effort_justification', 'projects.notes', 'projects.status');
    var $where          = array();
    var $order          = array('industries.name'=>'ASC', 'platforms.name'=>'ASC');

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Project_model', 'project');
        $this->load->model('ProjectTask_model', 'projecttask');
        $this->load->model('EffortType_model', 'effort_type');
        $this->load->model('Industry_model', 'industry');
        $this->load->model('Platform_model', 'platform');
        $this->load->model('User_model', 'user');
    }

    public function index(){
        $this->load->helper('url');
        $this->load->model('Industry_model', 'industry');
        $this->load->model('EffortType_model', 'efforttype');
        $this->load->model('Platform_model', 'platform');
        $this->load->model('User_model', 'user');

        $data['title'] = 'SA Project View';
        $data['topmenu'] = 'admin';

        $data['industries']                 = $this->industry->get_list();
        $data['efforttypes']                = $this->efforttype->get_list();
        $data['platforms']                  = $this->platform->get_list();
        $data['sausers']                    = $this->user->get_salist();

        $this->load->view('templates/header', $data);
        $this->load->view('project/saview');
        $this->load->view('templates/footer');
    }

    public function ajax_list(){

        $searchText = $_POST['search']['value'];

        foreach($_POST['columns'] as $column){
            if($column['search']['value']){
                $this->where[$column['name']. ' REGEXP'] = $column['search']['value'];
            }
        }

        if(isset($_POST['order']))
        {
            $this->order = array($this->columnOrder[$_POST['order']['0']['column']] => $_POST['order']['0']['dir']);
        }


        $list = $this->project->get_datatables($this->columnOrder, $this->searchColumns, $searchText, $this->where, $this->order);

        $priorityList = unserialize(SAP_PRIORITYLIST);
        $statusList = unserialize(SAP_STATUSLIST);

        $data = array();
        $index = $this->input->post('start');
        foreach($list as $project){
            $index++;
            $row = array();
            $row[] = $project->id;
            $row[] = $project->industry;
            $row[] = $project->sa;
            $row[] = array_key_exists($project->priority, $priorityList) ? $priorityList[$project->priority] : '';
            $row[] = $project->priority_index;
            $row[] = $project->workload;
            $row[] = $project->platform;
            $row[] = $project->effort_target;
            $row[] = $project->effort_type;
            $row[] = implode('<br>', explode('||', $project->effort_output));
            $row[] = $project->effort_justification;
            $row[] = $project->notes;
            $row[] = preg_match('/^0000-00-00/', $project->estimated_completion_date) ? '' : $this->_toMDY($project->estimated_completion_date);
            $row[] = array_key_exists($project->status, $statusList) ? $statusList[$project->status] : "";

            $row[] =    '<a class="btn btn-sm btn-success" href="javascript:void(0)" title="Edit" onclick="edit_project('."'".$project->id."'".')"><i class="glyphicon glyphicon-pencil"></i></a>
                        <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_project('."'".$project->id."'".')"><i class="glyphicon glyphicon-trash"></i></a>';

            $data[] = $row;
        }

        $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->project->count_all(),
                "recordsFiltered" => $this->project->count_filtered($this->columnOrder, $this->searchColumns, $searchText, $this->where, $this->order),
                "data" => $data
        );

        echo json_encode($output);
    }

    public function ajax_edit($id)
    {
        $data = $this->project->get_by_id($id);

        $data->desired_completion_date = preg_match('/^0000-00-00/', $data->desired_completion_date) ? '' : $this->_toMDY($data->desired_completion_date);
        $data->estimated_completion_date = preg_match('/^0000-00-00/', $data->estimated_completion_date) ? '' : $this->_toMDY($data->estimated_completion_date);
        $data->completion_date = preg_match('/^0000-00-00/', $data->completion_date) ? '' : $this->_toMDY($data->completion_date);

        echo json_encode($data);
    }

    public function ajax_add()
    {
        if($this->_validate()){

            $project = array(
                'author_email'              => $this->input->post('author_email'),
                'workloads_id'              => $this->input->post('workloads_id'),
                'platforms_id'              => $this->input->post('platforms_id'),
                'sa_users_id'               => $this->input->post('sa_users_id'),
                'effort_target'             => $this->input->post('effort_target'),
                'efforttypes_id'            => $this->input->post('efforttypes_id'),
                'desired_completion_date'   => $this->_tosqldate($this->input->post('desired_completion_date')),
                'estimated_completion_date' => $this->_tosqldate($this->input->post('estimated_completion_date')),
                'completion_date'           => $this->_tosqldate($this->input->post('completion_date')),
                'effort_justification'      => $this->input->post('effort_justification'),
                'notes'                     => $this->input->post('notes'),
                'status'                    => $this->input->post('status'),
                'priority'                  => $this->input->post('priority')
            );

            $projectId = $this->project->set_project($project);
            if($projectId){
                $cookie = array(
                    'name'=>        'author_email',
                    'value'=>       $this->input->post('author_email'),
                    'expire'=>      time()+86500
                );
                $this->input->set_cookie($cookie);

                $desiredDate = $this->input->post('desired_completion_date');
                $effortoutputs = $this->input->post('effortoutputs_id');

                foreach($effortoutputs as $key=>$value){
                    $childResult = $this->projecttask->set_projecttask(NULL, $projectId, $value, 'necessary??', $desiredDate);
                }
            }

            echo json_encode(array("status" => TRUE));
        }
    }

    public function ajax_update()
    {
        if($this->_validate()){
            $project = array(
                'id'                        => $this->input->post('id'),
                'author_email'              => $this->input->post('author_email') == '' ? NULL: $this->input->post('author_email'),
                'workloads_id'              => $this->input->post('workloads_id') == '' ? NULL : $this->input->post('workloads_id'),
                'platforms_id'              => $this->input->post('platforms_id') == '' ? NULL : $this->input->post('platforms_id'),
                'sa_users_id'               => $this->input->post('sa_users_id') == '' ? NULL : $this->input->post('sa_users_id'),
                'effort_target'             => $this->input->post('effort_target') == '' ? NULL : $this->input->post('effort_target'),
                'efforttypes_id'            => $this->input->post('efforttypes_id') == '' ? NULL : $this->input->post('efforttypes_id'),
                'desired_completion_date'   => $this->_tosqldate($this->input->post('desired_completion_date') == '' ? NULL : $this->input->post('desired_completion_date')),
                'estimated_completion_date' => $this->_tosqldate($this->input->post('estimated_completion_date') == '' ? NULL : $this->input->post('estimated_completion_date')),
                'completion_date'           => $this->_tosqldate($this->input->post('completion_date') == '' ? NULL : $this->input->post('completion_date')),
                'effort_justification'      => $this->input->post('effort_justification') == '' ? NULL : $this->input->post('effort_justification'),
                'notes'                     => $this->input->post('notes') == '' ? NULL : $this->input->post('notes'),
                'status'                    => $this->input->post('status') == '' ? NULL : $this->input->post('status'),
                'priority'                  => $this->input->post('priority') == '' ? NULL : $this->input->post('priority')
            );

            $projectId = $this->project->set_project($project);
            if($projectId){
                $desiredDate = $this->input->post('desired_completion_date');
                $effortoutputs = $this->input->post('effortoutputs_id');

                //Add missing tasks
                foreach($effortoutputs as $key=>$value){
                    $current = $this->projecttask->get_projecttask(array('projects_id'=>$projectId, 'effortoutputs_id'=>$value));
                    if(!$current){
                        $this->projecttask->set_projecttask(NULL, $projectId, $value, 'necessary??', $desiredDate);
                    }
                }

                //Delete removed tasks
                $projecttasks = $this->projecttask->get_list(array('projects_id'=>$projectId));
                foreach($projecttasks as $projecttask){
                    if(!in_array($projecttask['effortoutputs_id'], $effortoutputs)){
                        $this->projecttask->delete_by_id($projecttask['id']);
                    }
                }
            }

            echo json_encode(array("status" => TRUE));
        }
    }

    public function ajax_delete($id)
    {
        $this->project->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

    private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if($this->input->post('author_email') == ''){
            $data['inputerror'][] = 'author_email';
            $data['error_string'][] = 'Email Address is required';
            $data['status'] = FALSE;
        }

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
        }
        return $data['status'];
    }

    private function _toMDY($date){
        if($date){
            $datetimearray = explode(' ', $date);
            $datearray = explode('-', $datetimearray[0]);
            if(count($datearray) == 3){
                return $datearray[1] . '/' . $datearray[2] . '/' . $datearray[0];
            }
        }
        return $date;
    }

    private function _tosqldate($date){
        if($date){
            $datearray = explode('/', $date);
            if(count($datearray)==3){
                return $datearray[2].'-'.$datearray[0].'-'.$datearray[1];
            }
        }
        return $date;
    }
}