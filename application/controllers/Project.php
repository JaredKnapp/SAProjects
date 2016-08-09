<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Project extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Project_model', 'project');
        $this->load->model('ProjectTask_model', 'projecttask');

        $this->load->helper('cookie');
        $this->load->helper('url_helper');
    }

    public function index()
    {
        $this->load->helper('url');
        $this->load->model('Industry_model', 'industry');
        $this->load->model('EffortType_model', 'efforttype');
        $this->load->model('Platform_model', 'platform');
        $this->load->model('User_model', 'user');

        $data['title'] = 'Project Request List';
        $data['topmenu'] = 'project';

        $data['industries']     = $this->industry->get_list();
        $data['efforttypes']    = $this->efforttype->get_list();
        $data['platforms']      = $this->platform->get_list();
        $data['sausers']        = $this->user->get_salist();

        $data['body_content'] = 'project/index';
        $this->load->view('templates/default', $data);
    }

    public function view($id = NULL)
    {
        $data['project_item'] = $this->project->get_projects($id);

        if (empty($data['project_item']))
        {
            show_404();
        }

        $data['title'] = $data['project_item']['id'];
        $data['topmenu'] = 'project';

        $data['body_content'] = 'project/view';
        $this->load->view('templates/default', $data);
    }

    public function create()
    {
        $this->load->model('EffortType_model');
        $this->load->model('Industry_model');
        $this->load->model('Platform_model');
        $this->load->helper('form');
        $this->load->library('form_validation');

        $data['title'] = 'Submit a project request';
        $data['topmenu'] = 'project';

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
        $this->form_validation->set_rules('author_email', 'Email address', 'required|valid_email');
        $this->form_validation->set_rules('industries_id', 'Industry', 'required');
        $this->form_validation->set_rules('workloads_id', 'Workload', 'required');
        $this->form_validation->set_rules('platforms_id', 'Product', 'required');
        $this->form_validation->set_rules('effort_target', 'Effort Target', 'required');
        $this->form_validation->set_rules('efforttypes_id', 'Effort Type', 'required');
        $this->form_validation->set_rules('effortoutputs_id[]', 'Effort Output', 'required');
        $this->form_validation->set_rules('desired_completion_date', 'Desired Completion Date', 'callback_checkDateFormat');
        $this->form_validation->set_rules('effort_justification', 'Effort Justification', 'required');

        $today = new DateTime();

        if ($this->form_validation->run() === FALSE)
        {
            $author_email = $this->input->cookie('author_email');

            $data['author_email']               = (isset($_POST['author_email']) ? $this->input->post('author_email') : $author_email);
            $data['industries_id']              = $this->input->post('industries_id');
            $data['workloads_id']               = $this->input->post('workloads_id');
            $data['platforms_id']               = $this->input->post('platforms_id');
            $data['effort_target']              = $this->input->post('effort_target');
            $data['efforttypes_id']             = $this->input->post('efforttypes_id');
            $data['effortoutputs_id']           = $this->input->post('effortoutputs_id');
            $data['desired_completion_date']    = (isset($_POST['desired_completion_date']) ? $this->input->post('desired_completion_date') : $today->add(new DateInterval('P3M'))->format('m/d/Y'));
            $data['effort_justification']       = $this->input->post('effort_justification');
            $data['notes']                      = $this->input->post('notes');

            $data['choicesEffortType']          = array('' => 'Select One...') + $this->EffortType_model->get_list();
            $data['choicesIndustry']            = array('' => 'Select One...') + $this->Industry_model->get_list();
            $data['choicesWorkload']            = array('' => 'Select One...');
            $data['choicesPlatform']            = array('' => 'Select One...') + $this->Platform_model->get_list();

            $data['body_content'] = 'project/create';
            $this->load->view('templates/default', $data);

        }
        else
        {
            $project = array(
                'author_email'              => $this->input->post('author_email'),
                'workloads_id'              => $this->input->post('workloads_id'),
                'platforms_id'              => $this->input->post('platforms_id'),
                'effort_target'             => $this->input->post('effort_target'),
                'efforttypes_id'            => $this->input->post('efforttypes_id'),
                'desired_completion_date'   => $this->_tosqldate($this->input->post('desired_completion_date')),
                'effort_justification'      => $this->input->post('effort_justification'),
                'notes'                     => $this->input->post('notes'),
                'status'                    => SAP_DEFAULTSTATUS,
                'priority'                  => SAP_DEFAULTPRIORITY,
                'priority_index'            => SAP_DEFAULTPRIORITYINDEX
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
                    $this->projecttask->set_projecttask(NULL, $projectId, $value, 'necessary??', $desiredDate);
                }
            }


            $data['title'] = '';
            $data['author_email'] = $this->input->post('author_email');

            $data['body_content'] = 'project/create_success';
            $this->load->view('templates/default', $data);
        }
    }

    public function ajax_list(){
        $columnOrder    = array('industry', 'sa', 'priority', 'workload', 'platform', 'effort_target', 'effort_type', 'effort_output', 'effort_justification', 'notes', 'estimated_completion_date', 'status');
        $searchColumns  = array('industries.name', 'users.firstname', 'users.lastname', 'projects.priority', 'workloads.name', 'platforms.name', 'projects.effort_target', 'efforttypes.name', 'vflatprojecttasks.effortoutput', 'projects.effort_justification', 'projects.notes', 'projects.status');
        $where          = array();
        $order          = array('industries.name'=>'ASC', 'platforms.sortorder'=>'ASC', 'priority_index'=>'ASC');

        $searchText = $_POST['search']['value'];

        foreach($_POST['columns'] as $column){
            if($column['search']['value']){
                $where[$column['name']. ' REGEXP'] = $column['search']['value'];
            }
        }

        if(isset($_POST['order']))
        {
            $order = array($columnOrder[$_POST['order']['0']['column']] => $_POST['order']['0']['dir']);
        }


        $list = $this->project->get_datatables($columnOrder, $searchColumns, $searchText, $where, $order);

        $priorityList = unserialize(SAP_PRIORITYLIST);
        $statusList = unserialize(SAP_STATUSLIST);

        $data = array();
        $index = $this->input->post('start');
        foreach($list as $project){

            $nameArr = explode('||', $project->effort_output);
            $produceArr = explode('||', $project->effort_output_produce);
            $durationArr = explode('||', $project->effort_output_duration);

            $taskArr = array();
            $arrayIndex = 0;
            for($arrayIndex = 0; $arrayIndex < count($nameArr); $arrayIndex++){
                $taskArr[] = $nameArr[$arrayIndex] . ': ' . $durationArr[$arrayIndex] . ' days' . (empty($produceArr[$arrayIndex])?'':'. (' . $produceArr[$arrayIndex] . ')');
            }

            $index++;
            $row = array();
            $row[] = $project->industry;
            $row[] = $project->sa;
            $row[] = array_key_exists($project->priority, $priorityList) ? $priorityList[$project->priority] : '';
            $row[] = $project->workload;
            $row[] = $project->platform;
            $row[] = $project->effort_target;
            $row[] = $project->effort_type;
            $row[] = '<ul style="margin-left: 5px;"><li>'.implode('</li><li>', $taskArr).'</li></ul>';
            $row[] = $project->effort_justification;
            $row[] = $project->notes;
            $row[] = preg_match('/^0000-00-00/', $project->estimated_completion_date) ? '' : $this->_toMDY($project->estimated_completion_date);
            $row[] = array_key_exists($project->status, $statusList) ? $statusList[$project->status] : '';

            $data[] = $row;
        }

        $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->project->count_all(),
                "recordsFiltered" => $this->project->count_filtered($columnOrder, $searchColumns, $searchText, $where, $order),
                "data" => $data
        );

        echo json_encode($output);
    }

    /*********************************************************************************
     * Utilities and Callbacks
     *********************************************************************************/
    public function checkDateFormat($date) {
        $dateArray = explode('/', $date);
        if(sizeof($dateArray)==3){
            if(checkdate($dateArray[0], $dateArray[1], $dateArray[2])){
                return true;
            }
        }
        return false;
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

}
