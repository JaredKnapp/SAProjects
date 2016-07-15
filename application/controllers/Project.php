<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Project extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Project_model');
        $this->load->model('ProjectTask_model');

        $this->load->helper('cookie');
        $this->load->helper('url_helper');
    }

    public function index()
    {
        $data['projects'] = $this->project_model->get_projects();
        $data['title'] = 'Project Request archive';

        $this->load->view('templates/header', $data);
        $this->load->view('project/index', $data);
        $this->load->view('templates/footer');
    }

    public function view($id = NULL)
    {
        $data['project_item'] = $this->Project_model->get_projects($id);

        if (empty($data['project_item']))
        {
            show_404();
        }

        $data['title'] = $data['project_item']['id'];

        $this->load->view('templates/header', $data);
        $this->load->view('project/view', $data);
        $this->load->view('templates/footer');
    }

    public function create()
    {
        $this->load->model('EffortType_model');
        $this->load->model('Industry_model');
        $this->load->model('Platform_model');
        $this->load->helper('form');
        $this->load->library('form_validation');

        $data['title'] = 'Submit a project request';

        $this->form_validation->set_rules('author_email', 'Email address', 'required|valid_email');
        $this->form_validation->set_rules('industries_id', 'Industry', 'required');
        $this->form_validation->set_rules('workloads_id', 'Workload', 'required');
        $this->form_validation->set_rules('platforms_id', 'Product', 'required');
        $this->form_validation->set_rules('effort_target', 'Effort Target', 'required');
        $this->form_validation->set_rules('efforttypes_id', 'Effort Type', 'required');
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

            $this->load->view('templates/header', $data);
            $this->load->view('project/create');
            $this->load->view('templates/footer');

        }
        else
        {
            $project = array(
                'author_email'              => $this->input->post('author_email'),
                'workloads_id'              => $this->input->post('workloads_id'),
                'platforms_id'              => $this->input->post('platforms_id'),
                'effort_target'             => $this->input->post('effort_target'),
                'efforttypes_id'            => $this->input->post('efforttypes_id'),
                'desired_completion_date'   => $this->tosqldate($this->input->post('desired_completion_date')),
                'effort_justification'      => $this->input->post('effort_justification'),
                'notes'                     => $this->input->post('notes'),
                'status'                    => 'draft',
                'priority'                  => 'after'
            );
            $projectId = $this->Project_model->set_project($project);
            if($projectId){
                $cookie = array(
                    'name'=>        'author_email',
                    'value'=>       $this->input->post('author_email'),
                    'expire'=>      time()+86500
                );
                $this->input->set_cookie($cookie);

                $efforttypes_id = $this->input->post('efforttypes_id');
                $desiredDate = $this->input->post('desired_completion_date');
                $effortoutputs = $this->input->post('effortoutputs_id');

                foreach($effortoutputs as $key=>$value){
                    $childResult = $this->ProjectTask_model->set_projecttask(NULL, $projectId, $value, 'necessary??', $desiredDate);
                }
            }


            $data['title'] = 'Thank You';

            $this->load->view('templates/header', $data);
            $this->load->view('project/create_success');
            $this->load->view('templates/footer');
        }
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

    public function tosqldate($date){
        if($date){
            $datearray = explode('/', $date);
            if(count($datearray)==3){
                return $datearray[2].'-'.$datearray[0].'-'.$datearray[1];
            }
        }
        return $date;
    }
}
