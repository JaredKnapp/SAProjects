
<script src="<?php echo $this->config->base_url('assets/js/sap-getprojecttaskdata.js'); ?>" type="text/javascript"></script>

<script type="text/javascript">
    var task_table = null;
    var task_editor = null;
    var notechars_max = 10000;

    var edit_project_task_page = "<?php echo site_url('/architect/SAView/load_project_task'); ?>";

    var ajax_workload_url = "<?php echo site_url('ListFactory/GetWorkloadDropdown');?>";
</script>

<style>
    .project-task-dialog .modal-dialog {
        width: 700px;
    }
</style>

<?php echo form_open('#', array('id'=>'form')); ?>
<input type="hidden" value="" name="id" />
<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?php echo form_label('Email Address *', 'author_email', array('class'=>'control-label')); ?>
                <?php echo form_input(array('name'=>'author_email', 'placeholder'=>'Enter your email address...', 'class'=>'form-control required'), '', array('id'=>'author_email')); ?>
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <?php echo form_label('Industry *', 'industries_id', array('class'=>'control-label')); ?>
                <?php echo form_dropdown('industries_id', array('' => 'Select One...') + $industries, '', 'class="form-control required" id="industry"'); ?>
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <?php echo form_label('Workload *', 'workloads_id', array('class'=>'control-label')); ?>
                <?php echo form_dropdown('workloads_id', array('' => 'Select One...'), '', 'class="form-control required" id="workload"'); ?>
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <?php echo form_label('Product *', 'platforms_id', array('class'=>'control-label')); ?>
                <?php echo form_dropdown('platforms_id', array('' => 'Select One...') + $platforms, '', 'class="form-control required" id="platform"'); ?>
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <?php echo form_label('Effort Target *', 'effort_target', array('class'=>'control-label')); ?>
                <?php echo form_textarea(array('name'=>'effort_target', 'id'=>'effort_target', 'placeholder'=>'Enter a short description of this project...', 'class'=>'form-control form-control'), '');?>
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <?php echo form_label('Effort Justification *', 'effort_justification', array('class'=>'control-label')); ?>
                <?php echo form_textarea(array('name'=>'effort_justification', 'id'=>'effort_justification', 'placeholder'=>'Explain why this project should be considered...', 'class'=>'form-control'), '');?>
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <?php echo form_label('Desired Completion Date *', 'desired_completion_date', array('class'=>'control-label')); ?>
                <?php echo form_input(array('name'=>'desired_completion_date', 'placeholder'=>'mm/dd/yyyy', 'class'=>'form-control required'), '', array('id'=>'desired_completion_date')); ?>
                <script>$(function () { $('#desired_completion_date').datepicker(); });</script>
                <span class="help-block"></span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <?php echo form_label('Status *', 'status', array('class'=>'control-label')); ?>
                <?php echo form_dropdown('status', unserialize(SAP_STATUSLIST), '', 'class="form-control required" id="status"'); ?>
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <?php echo form_label('SA *', 'sa_users_id', array('class'=>'control-label')); ?>
                <?php echo form_dropdown('sa_users_id', array('' => 'Select One...') + $sausers, '', 'class="form-control required" id="platform"'); ?>
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <?php echo form_label('Projected Start Date', 'projected_start_date', array('class'=>'control-label')); ?>
                <?php echo form_input(array('name'=>'projected_start_date', 'placeholder'=>'mm/dd/yyyy', 'class'=>'form-control'), '', array('id'=>'projected_start_date')); ?>
                <script>$(function () { $('#projected_start_date').datepicker(); });</script>
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <?php echo form_label('Estimated Completion Date', 'estimated_completion_date', array('class'=>'control-label')); ?>
                <?php echo form_input(array('name'=>'estimated_completion_date', 'placeholder'=>'mm/dd/yyyy', 'class'=>'form-control'), '', array('id'=>'estimated_completion_date')); ?>
                <script>$(function () { $('#estimated_completion_date').datepicker(); });</script>
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <?php echo form_label('Estimated Work Days', 'estimated_work_days', array('class'=>'control-label')); ?>
                <?php echo form_input(array('name'=>'estimated_work_days', 'class'=>'form-control'), '', array('id'=>'estimated_work_days')); ?>
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <?php echo form_label('Completion Date', 'completion_date', array('class'=>'control-label')); ?>
                <?php echo form_input(array('name'=>'completion_date', 'placeholder'=>'mm/dd/yyyy', 'class'=>'form-control'), '', array('id'=>'completion_date')); ?>
                <script>$(function () { $('#completion_date').datepicker(); });</script>
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <?php echo form_label('Notes', 'notes', array('class'=>'control-label')); ?>
                <?php echo form_textarea(array('name'=>'notes', 'id'=>'notes', 'placeholder'=>'Provide any additional information that may be relevant...', 'class'=>'form-control'), '');?>
                <span class="help-block"></span>
                <h6 class="pull-right" id="note_count_message"></h6>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 ">
            <div class="form-group">
                <?php echo form_label('Effort Type *', 'efforttypes_id', array('class'=>'control-label')); ?>
                <?php echo form_dropdown('efforttypes_id', array('' => 'Select One...') + $efforttypes, '', 'class="form-control required" id="efforttype"'); ?>
                <span class="help-block"></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php echo form_label('Project Tasks:', 'projecttask_id'); ?>
                    <label class="pull-right">
                        <input type="checkbox" id="showallefforts" name="showallefforts" value="yes" checked />&nbsp;Show All Effort Choices
                    </label>
                </div>
                <div class="panel-body">
                    <table id="task_table" class="table table-bordered table-condensed table-hover table-striped" cellpadding="0" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 40px; align-content:center;"></th>
                                <th style="">Task</th>
                                <th style="width: 200px;">Projected Start Date</th>
                                <th style="width: 50px;">Work Days</th>
                                <th style="width: 200px;">Estimated Completion Date</th>
                                <th style="width: 200px;">Date Completed</th>
                                <th style="width: 50px">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

    $(document).ready(function () {

        task_table = $('#task_table').DataTable({
            rowId: 'effort_id',
            autowidth: false,
            info: false,
            ordering: false,
            paging: false,
            processing: true,
            searching: false,
            columns: [
                {
                    data: null, defaultContent: '', orderable: false, render: function (data, type, row, meta) {
                        var isNew = ("id" in row['task']) ? false : true;
                        var isDefault = row['effort']['isdefault'] == 1;
                        var checkedProp = (((isNew && isDefault) || row['is_selected'] == 1) ? 'checked' : '');

                        return "<input type='checkbox' id='effortoutput_" + row['effort']['id'] + "' name='effortoutput_" + row['effort']['id'] + "' value='" + row['effort']['id'] + "' " + checkedProp + " onchange='toggleTaskState(this, \"" + meta['row'] + "\")'/>";
                    }
                },
                { data: "description", width: "400px" },
                {
                    data: null, defaultContent: '', width: "200px", render: function (data, type, row, meta) {
                        return row['task']['projected_start_date'];
                    }
                },
                {
                    data: null, defaultContent: '', width: "50px", render: function (data, type, row, meta) {
                        return row['task']['duration'];
                    }
                },
                {
                    data: null, defaultContent: '', width: "200px", render: function (data, type, row, meta) {
                        return row['task']['estimated_completion_date'];
                    }
                },
                {
                    data: null, defaultContent: '', width: "200px", render: function (data, type, row, meta) {
                        return row['task']['completion_date'];
                    }
                },
                {
                    data: "actions", width: "200px", render: function (data, type, row, meta) {
                        editButton = '<a class="btn btn-sm btn-success" href="javascript:void(0)" title="Edit" onclick="editTask(\'' + meta['row'] + '\')"><i class="glyphicon glyphicon-pencil"></i></a>';
                        return editButton;
                    }
                }
            ],
            language: {
                infoEmpty: "Select an effort type...",
            },
            createdRow: function (row, data, index) {
                //Check for task id to 'paint' row color. Don't use is_task, because that changes with checkboxes.
                if (data['task_id']) {
                    $(row).addClass('info');
                }
            }
        });

        $("#industry").on('change', function () {
            getWorkload();
        });

        $("#efforttype").on('change', function () {
            buildDetailTaskTable();
        });

        $("#showallefforts").on('change', function () {
            buildDetailTaskTable();
        });

    });

    function toggleTaskState(cb, rowIndex) {
        var tableData = task_table.data();
        var rowData = tableData[rowIndex];

        rowData['is_selected'] = rowData['is_selected'] == 1 ? 0 : 1;
    }

    function getWorkload(val) {
        $.ajax({
            url: ajax_workload_url,
            data: { id: $('#industry').val() },
            type: "POST",
            success: function (data) {
                $("#workload").html(data);
                if (val) {
                    $("#workload").val(val);
                }
            }
        }).fail(function () {
            alert("ERROR: problem populating Workload dropdown.");
        });
    }

    function buildDetailTaskTable() {
        var projects_id = $('[name="id"]').val();
        var efforttypes_id = $('[name="efforttypes_id"]').val();

        task_table.clear().draw();
        getProjectTaskData(ajax_tasktable_url, null, projects_id, efforttypes_id, formatDetailTaskTable);
    }

    function formatDetailTaskTable(control, data) {

        var showAll = $('input[name="showallefforts"]').prop('checked');

        $.each(data, function (index, value) {
            var task = value['task'];
            var effort = value['effort'];

            if (showAll || task['id']) {
                task_table.row.add({
                    effort_id: effort['id'],
                    task_id: (task['id'] ? task['id'] : null),
                    is_task: value['is_task'],
                    is_selected: value['is_selected'],
                    description: effort['name'] + (effort['duration'] ? (': ' + effort['duration'] + ' days') : '') + (effort['produce'] ? (' (' + effort['produce'] + ')') : ''),
                    projected_start_date: (task['projected_start_date'] ? task['projected_start_date'] : null),
                    work_days: (task['duration'] ? task['duration'] : null),
                    estimated_completion_date: (task['estimated_completion_date'] ? task['estimated_completion_date'] : null),
                    date_completed: (task['completion_date'] ? task['completion_date'] : ''),
                    task: task,
                    effort: effort
                });
            }
        });

        task_table.draw('full-reset');
    }

    function editTask(rowIndex) {

        var tableData = task_table.data();
        var rowData = tableData[rowIndex];

        BootstrapDialog.show({
            cssClass: 'project-task-dialog',
            draggable: true,
            title: 'Edit Project Task',
            message: function (dialog) {

                var rowIndex = dialog.getData('rowIndex');
                var isSelected = dialog.getData('is_selected');
                var effort = dialog.getData('effort');
                var task = dialog.getData('task');

                var isSelected = $('input[name=effortoutput_' + effort['id'] + ']').prop('checked');

                var message = $('<div><center><i class="icon-spinner icon-spin icon-large"></i>Loading...</center></div>');
                var pageToLoad = dialog.getData('pageToLoad');
                var queryString =
                    'rowIndex=' + rowIndex +
                    '&is_selected=' + isSelected +
                    (("projected_start_date" in task) ? ('&projected_start_date=' + encodeURIComponent(task['projected_start_date'])) : '') +
                    (("estimated_completion_date" in task) ? ('&estimated_completion_date=' + encodeURIComponent(task['estimated_completion_date'])) : '') +
                    (("duration" in task) ? ('&duration=' + encodeURIComponent(task['duration'])) : '') +
                    (("completion_date" in task) ? ('&completion_date=' + encodeURIComponent(task['completion_date'])) : '') +
                    (("collateral_url" in task) ? ('&collateral_url=' + encodeURIComponent(task['collateral_url'])) : '');

                alert(pageToLoad + "?" + queryString);

                message.load(pageToLoad + "?" + queryString);

                return message;
            },
            buttons: [{
                label: 'Save',
                cssClass: 'btn-primary',
                action: function (dialogItself) {
                    var rowIndex = $('input[name=t_rowindex]').val();
                    var isSelected = $('input[name=t_selected]').prop('checked');
                    var projectedStartDate = $('input[name=t_projected_start_date]').val();
                    var estimated_completion_date = $('input[name=t_estimated_completion_date]').val();
                    var duration = $('input[name=t_duration]').val();
                    var completion_date = $('input[name=t_completion_date]').val();
                    var collateral_url = $('input[name=t_collateral_url]').val();

                    alert('hello');

                    var row = task_table.row(rowIndex);
                    var rowData = row.data();

                    rowData['is_selected'] = isSelected;
                    rowData['task']['projected_start_date'] = projectedStartDate;
                    rowData['task']['estimated_completion_date'] = estimated_completion_date;
                    rowData['task']['duration'] = duration;
                    rowData['task']['completion_date'] = completion_date;
                    rowData['task']['collateral_url'] = collateral_url;

                    task_table.row(row).data(rowData).draw();

                    var test = task_table.data();

                    dialogItself.close();

                }
            }, {
                label: 'Close',
                action: function (dialogItself) {
                    dialogItself.close();
                }
            }],
            data: {
                'pageToLoad': edit_project_task_page,
                'rowIndex': rowIndex,
                'is_selected': rowData['is_selected'],
                'effort': rowData['effort'],
                'task': rowData['task']
            }
        });
    }

</script>