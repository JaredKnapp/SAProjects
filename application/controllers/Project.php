<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Project extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Project_model');
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
        $this->form_validation->set_rules('effort_justification', 'Effort Justification', 'required');


        if ($this->form_validation->run() === FALSE)
        {
            $data['author_email'] = (isset($_POST['author_email']) ? $_POST['author_email'] : '');
            $data['industries_id'] = (isset($_POST['industries_id']) ? $_POST['industries_id'] : '');
            $data['workloads_id'] = (isset($_POST['workloads_id']) ? $_POST['workloads_id'] : '');
            $data['platforms_id'] = (isset($_POST['platforms_id']) ? $_POST['platforms_id'] : '');
            $data['effort_target'] = (isset($_POST['effort_target']) ? $_POST['effort_target'] : '');
            $data['efforttypes_id'] = (isset($_POST['efforttypes_id']) ? $_POST['efforttypes_id'] : '');
            $data['effortoutputs_id'] = (isset($_POST['effortoutputs_id']) ? $_POST['effortoutputs_id'] : '');
            $data['effort_justification'] = (isset($_POST['effort_justification']) ? $_POST['effort_justification'] : '');
            $data['notes'] = (isset($_POST['notes']) ? $_POST['notes'] : '');

            $data['choicesEffortType'] = array(NULL => 'Select One...') + $this->EffortType_model->get_list();
            $data['choicesIndustry'] = array(NULL => 'Select One...') + $this->Industry_model->get_list();
            $data['choicesWorkload'] = array(NULL => 'Select One...');
            $data['choicesPlatform'] = array(NULL => 'Select One...') + $this->Platform_model->get_list();

            $this->load->view('templates/header', $data);
            $this->load->view('project/create');
            $this->load->view('templates/footer');

        }
        else
        {
            $this->Project_model->set_project();

            $data['title'] = 'Thank You';

            $this->load->view('templates/header', $data);
            $this->load->view('project/create_success');
            $this->load->view('templates/footer');
        }
    }
}