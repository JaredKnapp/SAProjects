<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SAView extends MY_Controller {

    var $columnOrder    = array('industry', 'sa', 'priority', 'workload', 'platform', 'effort_target', 'efforttype', 'effort_output', 'effort_justification', 'notes', 'estimated_completion_date', 'status', null);
    var $searchColumns  = array('industries.name', 'users.firstname', 'users.lastname', 'projects.priority', 'workloads.name', 'platforms.name', 'projects.effort_target', 'efforttypes.name', 'vflatprojecttasks.effortoutput', 'projects.effort_justification', 'projects.status');
    var $where          = array();
    var $order          = array('industries.name'=>'ASC', 'platforms.sortorder'=>'ASC', 'priority_index'=>'ASC');

    public function __construct()
    {
        parent::__construct(array('+manager', '+architect'));

        $this->load->model('AuditHistory_model', 'audithistory');
        $this->load->model('EffortType_model', 'efforttype');
        $this->load->model('EffortOutput_model', 'effortoutput');
        $this->load->model('Industry_model', 'industry');
        $this->load->model('Platform_model', 'platform');
        $this->load->model('Project_model', 'project');
        $this->load->model('ProjectFollower_model', 'projectfollower');
        $this->load->model('ProjectNote_model', 'projectnote');
        $this->load->model('ProjectTask_model', 'projecttask');
        $this->load->model('User_model', 'user');
        $this->load->model('Workload_model', 'workload');

        $this->load->library('notification');
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
        $data['priorityList']               = unserialize(SAP_PRIORITYLIST);
        $data['statusList']                 = unserialize(SAP_STATUSLIST);
        $data['efforttypes']                = $this->efforttype->get_list();
        $data['platforms']                  = $this->platform->get_list();
        $data['sausers']                    = $this->user->get_salist();

        $data['body_content']               = 'architect/saview/index';
        $this->load->view('templates/default', $data);
    }

    public function load_project(){
        $data = array();

        $data['industries']                 = $this->industry->get_list();
        $data['platforms']                  = $this->platform->get_list();
        $data['sausers']                    = $this->user->get_salist();
        $data['efforttypes']                = $this->efforttype->get_list();

        $id = $this->input->get('id');
        $status = $this->input->get('newstatus');

        $project                    = (!null_or_empty($id) ? $this->project->get_by_id($id) : null);
        $workload                   = (!null_or_empty($project) ? $this->workload->get_by_id($project->workloads_id) : null);

        $data['id']                 = (!null_or_empty($project) ? $project->id : '');
        $data['sapid']              = (!null_or_empty($project) ? $project->sapid : '');
        $data['priority']           = (!null_or_empty($project) ? $project->priority : '');
        $data['priority_index']     = (!null_or_empty($project) ? $project->priority_index : '');
        $data['status']             = (!null_or_empty($status) ? $status : (!null_or_empty($project) ? $project->status : ''));
        $data['notification_list']  = (!null_or_empty($project) ? $project->notification_list : '');
        $data['effort_target']      = (!null_or_empty($project) ? $project->effort_target : '');
        $data['effort_justification']       = (!null_or_empty($project) ? $project->effort_justification : '');
        $data['desired_completion_date']    = $this->_toMDY((!null_or_empty($project) ? $project->desired_completion_date : ''));
        $data['projected_start_date']       = $this->_toMDY((!null_or_empty($project) ? $project->projected_start_date : ''));
        $data['estimated_completion_date']  = $this->_toMDY((!null_or_empty($project) ? $project->estimated_completion_date : ''));
        $data['estimated_work_days']        = (!null_or_empty($project) ? $project->estimated_work_days : '');
        $data['completion_date']            = $this->_toMDY((!null_or_empty($project) ? $project->completion_date : ''));
        $data['author_email']       = (!null_or_empty($project) ? $project->author_email : '');
        $data['workloads_id']       = (!null_or_empty($project) ? $project->workloads_id : '');
        $data['platforms_id']       = (!null_or_empty($project) ? $project->platforms_id : '');
        $data['sa_users_id']        = (!null_or_empty($project) ? $project->sa_users_id : '');
        $data['efforttypes_id']     = (!null_or_empty($project) ? $project->efforttypes_id : '');
        $data['created']            = (!null_or_empty($project) ? $project->created : '');
        $data['modified']           = (!null_or_empty($project) ? $project->modified : '');
        $data['industries_id']      = (!null_or_empty($workload) ? $workload->industries_id : '');
        $data['showallefforts_checked'] = (!null_or_empty($project) ? '' : 'checked');

        $this->load->view('architect/saview/edit_project', $data);
    }

    public function load_project_task(){
        $data = array();

        $data['rowIndex'] = $this->input->get('rowIndex', TRUE);
        $data['is_task'] = $this->input->get('is_task', TRUE);
        $data['is_selected'] = $this->input->get('is_selected', TRUE);
        $data['projected_start_date'] = $this->input->get('projected_start_date', TRUE);
        $data['estimated_completion_date'] = $this->input->get('estimated_completion_date', TRUE);
        $data['duration'] = $this->input->get('duration', TRUE);
        $data['completion_date'] = $this->input->get('completion_date', TRUE);
        $data['collateral_url'] = $this->input->get('collateral_url', TRUE);

        $this->load->view('architect/saview/edit_project_task', $data);
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
            $index++;

            $projectedStartDate = preg_match('/^0000-00-00/', $project->projected_start_date) ? '' : $this->_toMDY($project->projected_start_date);
            $projectEstimatedCompletionDate = preg_match('/^0000-00-00/', $project->estimated_completion_date) ? '' : $this->_toMDY($project->estimated_completion_date);
            $taskEstimatedCompletionDate = preg_match('/^0000-00-00/', $project->task_estimated_completion_date) ? '' : $this->_toMDY($project->task_estimated_completion_date);
            $estimatedCompletionDate = !null_or_empty($projectEstimatedCompletionDate) ? "$projectEstimatedCompletionDate!$taskEstimatedCompletionDate" : $taskEstimatedCompletionDate;
            $duration = ($project->estimated_work_days > 0) ? "$project->estimated_work_days!$project->task_duration" : $project->task_duration;

            $row = array(
                'id'                        =>$project->id,
                'sapid'                     =>$project->sapid,
                'industry'                  =>$project->industry,
                'sa'                        =>$project->sa,
                'priority'                  =>array_key_exists($project->priority, $priorityList) ? $priorityList[$project->priority] : '',
                'priority_index'            =>$project->priority_index,
                'workload'                  =>$project->workload,
                'platform'                  =>$project->platform,
                'effort_target'             =>html_escape($project->effort_target),
                'effort_type'               =>$project->effort_type,
                'effort_justification'      =>html_escape($project->effort_justification),
                'notes'                     =>html_escape($project->notes),
                'projected_start_date'      =>$projectedStartDate,
                'estimated_completion_date' =>$estimatedCompletionDate,
                'duration'                  =>$duration,
                'status'                    =>array_key_exists($project->status, $statusList) ? $statusList[$project->status] : "",
                'notification_list'         =>$project->notification_list
            );

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

    public function ajax_save()
    {
        if($this->_validate())
        {
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
                'status'                    => $this->input->post('status') == '' ? NULL : $this->input->post('status')
            );

            $isnew = null_or_empty($this->input->post('id'));
            if($isnew || !array_key_exists($this->input->post('status'), unserialize(SAP_ACTIVESTATUSLIST)))
            {
                $project['priority'] = SAP_DEFAULTPRIORITY;
                $project['priority_index'] = SAP_DEFAULTPRIORITYINDEX;
            }

            $original = $this->project->get_by_id($this->input->post('id'));
            $project_id = $this->project->set_project($project);
            if($project_id)
            {
                if($isnew)
                {
                    $cookie = array(
                        'name'=>        'author_email',
                        'value'=>       $this->input->post('author_email'),
                        'expire'=>      time()+86500
                    );
                    $this->input->set_cookie($cookie);
                }

                //Update Project Email Notification Table
                $notification_list_current = array();
                $notification_list_new = array_map('trim', explode(',', $this->input->post('notification_list')));

                //Delete removed followers
                if(null_or_empty($notification_list_new))
                {
                    $this->projectfollower->delete_by_project_id($project_id);
                }
                else
                {
                    $follower_data_list = $this->projectfollower->get_by_project_id($project_id);
                    foreach($follower_data_list as $follower_data){
                        if(!in_array($follower_data['email'], $notification_list_new))
                        {
                            $this->projectfollower->delete($follower_data['id']);
                        }
                        else 
                        {
                            array_push($notification_list_current, $follower_data['email']);
                        }
                    }
                }

                //Add new followers
                $notification_list_add = array_diff($notification_list_new, $notification_list_current);
                foreach($notification_list_add as $follower)
                {
                    if(!null_or_empty($follower))
                    {
                        $data = array('email'=>$follower, 'projects_id'=>$project_id);
                        $this->projectfollower->set($data);
                    }
                }

                //Process Tasks
                $task_data = $this->input->post('task_data');
                $task_ids = array();

                //First, Add missing tasks
                foreach($task_data as $task_string)
                {
                    $task = $this->_parseTask($task_string);
                    $task_ids[] = $this->projecttask->set_projecttask($task['id'], $project_id, $task['effort_id'], $task['projected_start_date'], $task['estimated_completion_date'], $task['duration'], $task['completion_date'], $task['collateral_url']);
                }

                //Next, Delete removed tasks
                $projecttasks = $this->projecttask->get(array('projects_id'=>$project_id));
                foreach($projecttasks as $projecttask)
                {
                    if(!in_array($projecttask['id'], $task_ids))
                    {
                        $this->projecttask->delete_by_id($projecttask['id']);
                    }
                }

                //Create Notes
                $notes = $this->input->post('notes');
                if(!null_or_empty($notes))
                {
                    $userId = ($this->authorization->is_logged_in() ? $this->session->userdata("id") : NULL);
                    $this->projectnote->set(NULL, $project_id, NULL, $userId, $notes);
                }

                //Cleanup priorities of original record if they are leaving to new industry or platform
                if($original->industries_id != $this->input->post('industries_id') || $original->platforms_id != $this->input->post('platforms_id'))
                {
                    $this->industry->cleanup_priority_index($original->industries_id, $original->platforms_id);
                }

                $this->industry->cleanup_priority_index($this->input->post('industries_id'), $this->input->post('platforms_id'));

                //Send notifications
                $this->notification->projectupdate($this->input->post('author_email'), NULL, $project_id, $project);
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

            //Send notification
            $project_array = get_object_vars($project);
            $this->notification->projectupdate($project_array['author_email'], NULL, $projectId, $project_array);
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

    public function ajax_gettasktable(){
        $output = array();

        $projectsId = $this->input->post('projects_id');
        $isNew = null_or_empty($projectsId) ? true : false;
        $project = null_or_empty($projectsId) ? null : $this->project->get_by_id($projectsId);
        $projecttasks = null_or_empty($projectsId) ? array() : $this->projecttask->get(array('projects_id'=>$projectsId), array(array('name', 'ASC')));

        $efforttypesId = null_or_empty($this->input->post('efforttypes_id')) ? (null_or_empty($project) ? '' : $project->efforttypes_id) : $this->input->post('efforttypes_id');
        $effortdata = $this->effortoutput->get_ordered(array('efforttypes_id'=>$efforttypesId));

        foreach($effortdata as $effort){
            $effortRow = array();
            $taskRow = array();
            $isDefault = $effort['isdefault'] == 1 ? true : false;

            $effortRow['id'] = $effort['id'];
            $effortRow['name'] = $effort['name'];
            $effortRow['sortorder'] = $effort['sortorder'];
            $effortRow['affinity'] = $effort['affinity'];
            $effortRow['isselectable'] = $effort['isselectable'];
            $effortRow['isdefault'] = $effort['isdefault'];
            $effortRow['produce'] = is_null($effort['produce'])? '' : $effort['produce'];
            $effortRow['duration'] = $effort['duration'];
            $effortRow['exampleurl'] = $effort['exampleurl'];
            $effortRow['helptext'] = $effort['helptext'];
            $effortRow['kit_reference_id'] = $effort['kit_reference_id'];
            $effortRow['efforttypes_id'] = $effort['efforttypes_id'];

            foreach($projecttasks as $task){
                if($task['effortoutputs_id']==$effort['id']){

                    $taskRow['id'] = $task['id'];
                    $taskRow['name'] = $task['name'];
                    $taskRow['duration'] = is_null($task['duration']) ? '' : $task['duration'];
                    $taskRow['desired_completion_date'] =  $this->_toMDY($task['desired_completion_date']);
                    $taskRow['projected_start_date'] = $this->_toMDY($task['projected_start_date']);
                    $taskRow['estimated_completion_date'] = $this->_toMDY($task['estimated_completion_date']);
                    $taskRow['completion_date'] = $this->_toMDY($task['completion_date']);
                    $taskRow['collateralurl'] = is_null($task['collateralurl']) ? '' : $task['collateralurl'];

                    break;
                }
            }
            $output[] = array(
                'is_task'       => (array_key_exists('id', $taskRow) ? '1' : '0'),
                'is_selected'   => ( ( ( $isNew  && $isDefault ) || array_key_exists('id', $taskRow)) ? '1' : '0'),  //Everything that is currently is a task should be selected
                'effort'        => $effortRow,
                'task'          => $taskRow);
        }

        echo json_encode($output);
    }

    public function ajax_getprojectnotes(){
        $projectsId = $this->input->post('projects_id');
        $projecttasks_id = $this->input->post('projecttasks_id');
        $searchText = $_POST['search']['value'];

        $where = array('projects_id'=>$projectsId);
        if(!null_or_empty($projecttasks_id)){
            $where['projecttasks_id'] = $projecttasks_id;
        }
        if(!null_or_empty($searchText)){
            $where["concat(users.firstname, ' ', users.lastname, '|', notes) like"] = "%$searchText%";
        }

        $data = $this->projectnote->get_notes($where);

        $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->project->count_all(),
                "recordsFiltered" => count($data),
                "data" => $data
        );

        echo json_encode($output);
    }

    public function ajax_getprojecthistory(){
        $projectsId = $this->input->post('projects_id');

        $where = array("(table='projects' AND row_id={$projectsId}) OR (table='projectnotes' AND parent_row_id={$projectsId}) OR (table='projecttasks' AND parent_row_id={$projectsId}) ");

        $data = $this->audithistory->get($where, array(array('audit_history.created', 'DESC')));

        $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->project->count_all(),
                "recordsFiltered" => count($data),
                "data" => $data
        );

        echo json_encode($output);
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
        if($this->input->post('industries_id') == ''){
            $data['inputerror'][] = 'industries_id';
            $data['error_string'][] = 'Industry is required';
            $data['status'] = FALSE;
        }
        if($this->input->post('workloads_id') == ''){
            $data['inputerror'][] = 'workloads_id';
            $data['error_string'][] = 'Workload is required';
            $data['status'] = FALSE;
        }
        if($this->input->post('platforms_id') == ''){
            $data['inputerror'][] = 'platforms_id';
            $data['error_string'][] = 'Product is required';
            $data['status'] = FALSE;
        }
        if($this->input->post('effort_target') == ''){
            $data['inputerror'][] = 'effort_target';
            $data['error_string'][] = 'Effort Target is required';
            $data['status'] = FALSE;
        }
        if($this->input->post('effort_justification') == ''){
            $data['inputerror'][] = 'effort_justification';
            $data['error_string'][] = 'Effort Justification is required';
            $data['status'] = FALSE;
        }
        if($this->input->post('desired_completion_date') == ''){
            $data['inputerror'][] = 'desired_completion_date';
            $data['error_string'][] = 'Desired Completion Date is required';
            $data['status'] = FALSE;
        }
        if($this->input->post('efforttypes_id') == ''){
            $data['inputerror'][] = 'efforttypes_id';
            $data['error_string'][] = 'Effort Type is required';
            $data['status'] = FALSE;
        }
        if($this->input->post('efforttypes_id') == ''){
            $data['inputerror'][] = 'efforttypes_id';
            $data['error_string'][] = 'Effort Type is required';
            $data['status'] = FALSE;
        }



        if($data['status'] === FALSE)
        {
            echo json_encode($data);
        }
        return $data['status'];
    }

    private function _parseTask($taskString){
        $task = array();
        $taskfields = explode('~~', $taskString);
        foreach($taskfields as $fieldString){
            $taskFieldArray = explode('|', $fieldString);
            $task[$taskFieldArray[0]] = $taskFieldArray[1];
        }

        return $task;
    }

    private function _toMDY($date, $interval = 0){
        if($date && ! preg_match("/^0000-00-00/", $date)){
            $datetimearray = explode(' ', $date);
            $datearray = explode('-', $datetimearray[0]);
            if(count($datearray) == 3){
                return $datearray[1] . '/' . $datearray[2] . '/' . $datearray[0];
            }
        } else {
            return '';
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