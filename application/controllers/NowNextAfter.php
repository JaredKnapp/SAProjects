<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NowNextAfter extends MY_Controller {

    var $columnOrder    = array('industry', 'sa', 'priority', 'workload', 'platform', 'effort_target', 'effort_type', 'effort_output', 'effort_justification', 'notes', 'estimated_completion_date', 'status');
    var $searchColumns  = array('industries.name', 'users.firstname', 'users.lastname', 'projects.priority', 'workloads.name', 'platforms.name', 'projects.effort_target', 'efforttypes.name', 'vflatprojecttasks.effortoutput', 'projects.effort_justification', 'projects.notes', 'projects.status');
    var $where          = array();
    var $order          = array('industries.name'=>'ASC', 'platforms.sortorder'=>'ASC', 'priority_index'=>'ASC');

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Project_model', 'project');
    }

    public function index(){
        $this->load->helper('url');
        $this->load->model('Industry_model', 'industry');
        $this->load->model('EffortType_model', 'efforttype');
        $this->load->model('Platform_model', 'platform');
        $this->load->model('User_model', 'user');

        $data['title']          = 'SA Project Report: Now Next After';
        $data['topmenu']        = 'report';

        $data['industries']     = $this->industry->get_list();
        $data['efforttypes']    = $this->efforttype->get_list();
        $data['platforms']      = $this->platform->get_list();
        $data['sausers']        = $this->user->get_salist();

        $data['body_content'] = 'project/nownextafter';
        $this->load->view('templates/default', $data);
    }

    public function ajax_list(){
        //$activeStatusList = unserialize(SAP_ACTIVESTATUSLIST);
        //$this->where['projects.status IN'] = array_keys($activeStatusList);

        $this->where['industries.id IN'] = array_key_exists('searchIndustries', $_POST) ? $_POST['searchIndustries'] : array();
        $this->where['projects.priority IN'] = array_key_exists('searchPriorities', $_POST) ? $_POST['searchPriorities'] : array();
        $this->where['projects.status IN'] = array_key_exists('searchStatuses', $_POST) ? $_POST['searchStatuses'] : array();
        $this->where['projects.platforms_id IN'] = array_key_exists('searchPlatforms', $_POST) ? $_POST['searchPlatforms'] : array();

        $searchText = $_POST['search']['value'];
        foreach($_POST['columns'] as $column){
            if($column['search']['value']){
                $this->where[$column['name']. ' REGEXP'] = $column['search']['value'];
            }
        }


        if(isset($_POST['order']))
        {
            $this->order = array_merge(array($this->columnOrder[$_POST['order']['0']['column']] => $_POST['order']['0']['dir']),
                array('industries.name'=>'ASC', 'platforms.sortorder'=>'ASC', 'priority_index'=>'ASC'));
        }


        $list = $this->project->get_datatables($this->columnOrder, $this->searchColumns, $searchText, $this->where, $this->order);

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
                "recordsFiltered" => $this->project->count_filtered($this->columnOrder, $this->searchColumns, $searchText, $this->where, $this->order),
                "data" => $data
        );

        echo json_encode($output);
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