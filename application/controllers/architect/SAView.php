<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SAView extends MY_Controller {

    var $columnOrder    = array('industry', 'sa', 'priority', 'workload', 'platform', 'effort_target', 'effort_type', 'effort_output', 'effort_justification', 'notes', 'estimated_completion_date', 'status', null);
    var $searchColumns  = array('industries.name', 'users.firstname', 'users.lastname', 'projects.priority', 'workloads.name', 'platforms.name', 'projects.effort_target', 'efforttypes.name', 'vflatprojecttasks.effortoutput', 'projects.effort_justification', 'projects.notes', 'projects.status');
    var $where          = array();
    var $order          = array('industries.name'=>'ASC', 'platforms.sortorder'=>'ASC', 'priority_index'=>'ASC');

    public function __construct()
    {
        parent::__construct('admin');
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
        $data['topmenu'] = 'architects';

        $data['industries']                 = $this->industry->get_list();
        $data['efforttypes']                = $this->efforttype->get_list();
        $data['platforms']                  = $this->platform->get_list();
        $data['sausers']                    = $this->user->get_salist();

        $data['body_content'] = 'architect/saview/index';
        $this->load->view('templates/default', $data);
    }

    public function ajax_list(){

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
            $this->order = array($this->columnOrder[$_POST['order']['0']['column']] => $_POST['order']['0']['dir']);
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
            $row[] = $project->id;
            $row[] = $project->id;
            $row[] = $project->industry;
            $row[] = $project->sa;
            $row[] = array_key_exists($project->priority, $priorityList) ? $priorityList[$project->priority] : '';
            $row[] = $project->priority_index;
            $row[] = $project->workload;
            $row[] = $project->platform;
            $row[] = html_escape($project->effort_target);
            $row[] = $project->effort_type;
            $row[] = '<ul style="margin-left: 5px;"><li>'.implode('</li><li>', $taskArr).'</li></ul>';
            $row[] = html_escape($project->effort_justification);
            $row[] = html_escape($project->notes);
            $row[] = preg_match('/^0000-00-00/', $project->projected_start_date) ? '' : $this->_toMDY($project->projected_start_date);
            $row[] = preg_match('/^0000-00-00/', $project->estimated_completion_date) ? '' : $this->_toMDY($project->estimated_completion_date);
            $row[] = $project->estimated_work_days;
            $row[] = array_key_exists($project->status, $statusList) ? $statusList[$project->status] : "";
            $row[] = '';

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
        $data->projected_start_date = preg_match('/^0000-00-00/', $data->projected_start_date) ? '' : $this->_toMDY($data->projected_start_date);
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
                'projected_start_date'      => $this->_tosqldate($this->input->post('projected_start_date')),
                'estimated_completion_date' => $this->_tosqldate($this->input->post('estimated_completion_date')),
                'estimated_work_days'       => $this->input->post('estimated_work_days'),
                'completion_date'           => $this->_tosqldate($this->input->post('completion_date')),
                'effort_justification'      => $this->input->post('effort_justification'),
                'notes'                     => $this->input->post('notes'),
                'status'                    => $this->input->post('status'),
                'priority'                  => SAP_DEFAULTPRIORITY,
                'priority_index'            => SAP_DEFAULTPRIORITYINDEX
            );

            $projectId = $this->project->set_project($project);
            if($projectId){

                $this->industry->cleanup_priority_index($this->input->post('industries_id'), $this->input->post('platforms_id'));

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
                'projected_start_date'      => $this->_tosqldate($this->input->post('projected_start_date') == '' ? NULL : $this->input->post('projected_start_date')),
                'estimated_completion_date' => $this->_tosqldate($this->input->post('estimated_completion_date') == '' ? NULL : $this->input->post('estimated_completion_date')),
                'estimated_work_days'       => $this->input->post('estimated_work_days') == '' ? 0 : $this->input->post('estimated_work_days'),
                'completion_date'           => $this->_tosqldate($this->input->post('completion_date') == '' ? NULL : $this->input->post('completion_date')),
                'effort_justification'      => $this->input->post('effort_justification') == '' ? NULL : $this->input->post('effort_justification'),
                'notes'                     => $this->input->post('notes') == '' ? NULL : $this->input->post('notes'),
                'status'                    => $this->input->post('status') == '' ? NULL : $this->input->post('status')
            );

            if(!array_key_exists($this->input->post('status'), unserialize(SAP_ACTIVESTATUSLIST))){
                $project['priority'] = SAP_DEFAULTPRIORITY;
                $project['priority_index'] = SAP_DEFAULTPRIORITYINDEX;
            }

            $original = $this->project->get_by_id($this->input->post('id'));

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

                //Cleanup priorities of original record if they are leaving to new industry or platform
                if($original->industries_id != $this->input->post('industries_id') || $original->platforms_id != $this->input->post('platforms_id')) {
                    $this->industry->cleanup_priority_index($original->industries_id, $original->platforms_id);
                }

                $this->industry->cleanup_priority_index($this->input->post('industries_id'), $this->input->post('platforms_id'));
            }

            echo json_encode(array("status" => TRUE));
        }
    }

    public function ajax_defer($id)
    {
        $project = array(
            'id'                        => $id,
            'status'                    => SAP_DEFERREDSTATUS
        );

        $projectId = $this->project->set_project($project);
        if($projectId){
            $project = $this->project->get_by_id($id);
            $this->industry->cleanup_priority_index($project->industries_id, $project->platforms_id);
        }

        echo json_encode(array("status" => TRUE));
    }

    public function ajax_delete($id)
    {
        $this->project->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_reorder(){
        $diff = $_POST;

        $errorText = NULL;
        $activeStatusList = unserialize(SAP_ACTIVESTATUSLIST);
        $statusList = unserialize(SAP_STATUSLIST);

        if(count($diff) > 1){
            $movedRecord = $this->project->get_by_id($diff['key']);
            if(array_key_exists($movedRecord->status, $activeStatusList)){

                //1 - verify that they are all in the same group, and that all status are active
                foreach($diff as $targetId=>$sourceId){
                    if($targetId != 'key'){
                        $target = $this->project->get_by_id($targetId);
                        $source = $this->project->get_by_id($sourceId);

                        if($source->platforms_id != $movedRecord->platforms_id || $source->industries_id != $movedRecord->industries_id) {
                            $errorText = "You may only reorder projects within their own Industry / Product. ";
                            break;
                        }

                        if(!array_key_exists($target->status, $activeStatusList) || !array_key_exists($source->status, $activeStatusList)){
                            $errorText = "You may only reorder within active projects.";
                            break;
                        }
                    }
                }

                //2 - Make sure priority index is unique and in the appropriate order
                if(!$errorText){
                    //Tighten up Priority_Index
                    $this->industry->cleanup_priority_index($movedRecord->industries_id, $movedRecord->platforms_id);
                    $newPriorities = array();

                    //3 - Move Priority_Indexes
                    foreach($diff as $targetId=>$sourceId){
                        if($targetId != 'key'){

                            $target = $this->project->get_projects($targetId);
                            $source = $this->project->get_projects($sourceId);

                            //Save the original Priority Index
                            if(!array_key_exists($targetId, $newPriorities)) $newPriorities[$targetId] = $target['priority_index'];
                            if(!array_key_exists($sourceId, $newPriorities)) $newPriorities[$sourceId] = $source['priority_index'];

                            //Use the original Priority Index
                            $target['priority_index'] = $newPriorities[$sourceId];
                            $this->project->set_project($target);
                        }
                    }

                    //4 - Make sure priority (now, next, after, beyond) is set
                    if(!$errorText){
                        //Tighten up Priority_Index
                        $this->industry->cleanup_priority_index($movedRecord->industries_id, $movedRecord->platforms_id);
                    }
                }
            } else {
                $errorText = "You may only reorder projects with an active status. (". (array_key_exists($movedRecord->status, $statusList) ? $statusList[$movedRecord->status] : $movedRecord->status) . ")";
            }
        }

        if($errorText){
            echo json_encode(array("errorText" => $errorText));
        } else {
            echo json_encode(array("status" => TRUE, 'count'=>count($diff)));
        }
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