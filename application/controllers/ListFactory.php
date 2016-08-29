<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ListFactory extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Project_model', 'project');
        $this->load->model('EffortType_model', 'efforttype');

        $this->load->helper('url_helper');
    }

    public function GetEffortOutputsCheckbox(){
        $this->load->model('EffortOutput_model', 'effortoutput');

        $efforttype_id = $this->input->post('id', TRUE);
        $data = $this->effortoutput->get(array('efforttypes_id'=>$efforttype_id));

        $output = '';
        foreach ($data as $value)
        {

            $description = ($value['name'] . ': ' . $value['duration'] . ' days' . (empty($value['produce']) ? '' : (' ('.$value['produce'].')')));
            $checked = $value['isdefault']=='1' ? 'checked' : '';
            $duration = $value['duration'];
            $id = $value['id'];

            $checkbox = "<input type='checkbox' name='effortoutputs_id[]' $checked value='$id' effort-duration='$duration'>";
            $output .= "<div class='checkbox'><label>$checkbox &nbsp" . $description . "</label></div>";
        }

        if(empty($output)){
            $output = 'Select an Effort Type...<br />';
        }

        echo $output;
    }

    public function GetEffortOutputsJSON(){
        $this->load->model('EffortOutput_model');

        $efforttype_id = $this->input->post('id', TRUE);
        $data = $this->effortoutput->get(array('efforttypes_id'=>$efforttype_id));
        echo json_encode($data);
    }


    public function GetWorkloadDropdown(){
        $this->load->model('Workload_model', 'workload');

        $id = $this->input->post('id', TRUE);
        $data = $this->workload->get_list($id);

        $output = "<option value=''>Select One...</option>";
        foreach ($data as $key=>$value)
        {
            $output .= "<option value='".$key."'>".$value."</option>";
        }

        echo $output;
    }

}