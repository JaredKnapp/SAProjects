
<button class="btn btn-success" onclick="add_project_request()">
    <i class="glyphicon glyphicon-plus"></i>Add Project Request
</button>
<button class="btn btn-default" onclick="reload_table()">
    <i class="glyphicon glyphicon-refresh"></i>Reload
</button>
<br />
<br />
<table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Industry</th>
            <th>SA</th>
            <th>Priority</th>
            <th>Workload</th>
            <th>Product</th>
            <th>Effort Target</th>
            <th>Effort Type</th>
            <th>Effort Output</th>
            <th>Effort Justification</th>
            <th>Notes</th>
            <th>Estimated Complete Date</th>
            <th>Status</th>
            <th style="width:125px;">Action</th>
        </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
        <tr>
            <th>Industry</th>
            <th>SA</th>
            <th>Priority</th>
            <th>Workload</th>
            <th>Product</th>
            <th>Effort Target</th>
            <th>Effort Type</th>
            <th>Effort Output</th>
            <th>Effort Justification</th>
            <th>Notes</th>
            <th>Estimated Complete Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </tfoot>
</table>
<script type="text/javascript">

    var save_method; //for save method string
    var table;

    $(document).ready(function () {
        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "searching": true,
            "order": [],
            "ajax": {
                url: "<?php echo site_url('SAView/ajax_list')?>",
                type: "POST"
            },
            "columnDefs": [
            {
                "targets": [-1],
                "orderable": false
            },
            ],

        });

        $("input").change(function () {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("textarea").change(function () {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("select").change(function () {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });

    });

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function add_project_request() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $('[name="status"]').val('draft');
        $('[name="priority"]').val('beyond');
        $("#effortoutput").html('Select an effort type...');
        $('#modal_form').modal('show');
        $('.modal-title').text('New Project Request');
    }

    function edit_project(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('SAView/ajax_edit/')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data) {

                $('[name="id"]').val(data.id);
                $('[name="status"]').val(data.status);
                $('[name="priority"]').val(data.priority);
                $('[name="author_email"]').val(data.author_email);
                $('[name="industries_id"]').val(data.industries_id);
                getWorkload(data.workloads_id);
                $('[name="platforms_id"]').val(data.platforms_id);
                $('[name="sa_users_id"]').val(data.sa_users_id);
                $('[name="effort_target"]').val(data.effort_target);
                $('[name="efforttypes_id"]').val(data.efforttypes_id);
                getEffortOutput(data.effortoutput_id.split('||'));
                $('[name="desired_completion_date"]').val(data.desired_completion_date);
                $('[name="estimated_completion_date"]').val(data.estimated_completion_date);
                $('[name="completion_date"]').val(data.completion_date);
                $('[name="effort_justification"]').val(data.effort_justification);
                $('[name="notes"]').val(data.notes);

                $('#modal_form').modal('show');
                $('.modal-title').text('Edit Project Request');

            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

</script>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Project Request Form</h3>
            </div>
            <div class="modal-body form">

                <!--<form action="#" id="form" class="form-horizontal">-->
                <?php echo form_open('#', array('id'=>'form', 'class'=>'form-horizontal')); ?>
                <input type="hidden" value="" name="id" />
                <div class="form-body">
                    <div class="form-group">
                        <?php echo form_label('Status *', 'status', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_dropdown('status', unserialize(SAP_STATUSLIST), '', 'class="form-control required" id="status"'); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Priority *', 'priority', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_dropdown('priority', unserialize(SAP_PRIORITYLIST), '', 'class="form-control required" id="priority"'); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Email Address *', 'author_email', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_input(array('name'=>'author_email', 'placeholder'=>'Enter your email address...', 'class'=>'form-control required'), '', array('id'=>'author_email')); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Industry *', 'industries_id', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_dropdown('industries_id', $choicesIndustry, '', 'class="form-control required" id="industry"'); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Workload *', 'workloads_id', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_dropdown('workloads_id', $choicesWorkload, '', 'class="form-control required" id="workload"'); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Product *', 'platforms_id', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_dropdown('platforms_id', $choicesPlatform, '', 'class="form-control required" id="platform"'); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('SA *', 'sa_user_id', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_dropdown('sa_user_id', $choicesSAUser, '', 'class="form-control required" id="platform"'); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Effort Target *', 'effort_target', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_textarea(array('name'=>'effort_target', 'id'=>'effort_target', 'placeholder'=>'Enter a short description of this project...', 'class'=>'form-control form-control'), '');?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Effort Type *', 'efforttypes_id', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_dropdown('efforttypes_id', $choicesEffortType, '', 'class="form-control required" id="efforttype"'); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Effort Output *', 'effortoutputs_id', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <div id="effortoutput">Select an Effort Type...</div>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Desired Completion Date *', 'desired_completion_date', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_input(array('name'=>'desired_completion_date', 'placeholder'=>'mm/dd/yyyy', 'class'=>'form-control required'), '', array('id'=>'desired_completion_date')); ?>
                            <script>$(function () { $('#desired_completion_date').datepicker(); });</script>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Estimated Completion Date', 'estimated_completion_date', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_input(array('name'=>'estimated_completion_date', 'placeholder'=>'mm/dd/yyyy', 'class'=>'form-control'), '', array('id'=>'estimated_completion_date')); ?>
                            <script>$(function () { $('#estimated_completion_date').datepicker(); });</script>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Completion Date', 'completion_date', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_input(array('name'=>'completion_date', 'placeholder'=>'mm/dd/yyyy', 'class'=>'form-control'), '', array('id'=>'completion_date')); ?>
                            <script>$(function () { $('#completion_date').datepicker(); });</script>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Effort Justification *', 'effort_justification', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_textarea(array('name'=>'effort_justification', 'id'=>'effort_justification', 'placeholder'=>'Explain why this project should be considered...', 'class'=>'form-control'), '');?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Notes', 'notes', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_textarea(array('name'=>'notes', 'id'=>'notes', 'placeholder'=>'Provide any additional information that may be relevant...', 'class'=>'form-control'), '');?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->



<script type="text/javascript">

    function getEffortOutput(val) {
        $.ajax({
            url: "<?php echo base_url();?>index.php/ListFactory/GetEffortOutputsCheckbox",
            data: { id: $('#efforttype').val() },
            type: "POST",
            success: function (data) {
                $("#effortoutput").html(data);
                if (val) {
                    $.each($("input[name='effortoutputs_id[]']"), function () {
                        $(this).prop("checked", ($.inArray($(this).val(), val) != -1));
                    });
                }
            }
        }).fail(function () {
            alert("ERROR: problem populating Effort Output checkboxes.");
        });
    }

    function getWorkload(val) {
        $.ajax({
            url: "<?php echo base_url();?>index.php/ListFactory/GetWorkloadDropdown",
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

    $(document).ready(function () {
        $("#efforttype").on('change', function () {
            getEffortOutput('');
        });
        $("#industry").on('change', function () {
            getWorkload('');
        });
    });
</script>
