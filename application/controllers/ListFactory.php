<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ListFactory extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Project_model');
        $this->load->model('EffortType_model');
        $this->load->helper('url_helper');
    }

    public function GetEffortOutputsCheckbox(){
        $this->load->model('EffortOutput_model');

        $id = $this->input->post('id', TRUE);
        $data = $this->EffortOutput_model->get_list($id);

        $output = '';
        foreach ($data as $key=>$value)
        {
            //$completionDate = "<label>Desired Completion Date: </label>&nbsp;<input type='text' name='".$key."_date' id='".$key."_date' value=''/>";
            //$script = '<script>$(function(){$("#'.$key.'_date").datepicker();});</script>';
            $checkbox = "<input type='checkbox' name='effortoutputs_id[]' value='$key'>";
            $output .= "$checkbox &nbsp".$value. "<br /><br />";
        }

        if(empty($output)){
            $output = 'Select an Effort Type...<br />';
        }

        echo $output;
    }

    public function GetWorkloadDropdown(){
        $this->load->model('Workload_model');

        $id = $this->input->post('id', TRUE);
        $data = $this->Workload_model->get_list($id);

        $output = "<option value='0'>Select One...</option>";
        foreach ($data as $key=>$value)
        {
            $output .= "<option value='".$key."'>".$value."</option>";
        }

        echo $output;
    }

}