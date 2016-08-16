<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ListFactory extends MY_Controller {

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
            $cbValue = $value['name'];
            $cbChecked = $value['isdefault']=='1' ? 'checked' : '';
            $checkbox = "<input type='checkbox' name='effortoutputs_id[]' $cbChecked value='$key'>";
            $output .= "<div class='checkbox'><label>$checkbox &nbsp" . $cbValue . "</label></div>";
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

        $output = "<option value=''>Select One...</option>";
        foreach ($data as $key=>$value)
        {
            $output .= "<option value='".$key."'>".$value."</option>";
        }

        echo $output;
    }

}