
<button class="btn btn-success" onclick="add_project_request()">
    <i class="glyphicon glyphicon-plus"></i>Add Project Request
</button>
<button class="btn btn-default" onclick="reload_table()">
    <i class="glyphicon glyphicon-refresh"></i>Reload
</button>
<br />
<br />
<table id="table" class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>id</th>
            <th class="search-industry">Industry</th>
            <th class="search-sauser">SA</th>
            <th class="search-priority">Priority</th>
            <th>Priority Index</th>
            <th class="search-input">Workload</th>
            <th class="search-platform">Product</th>
            <th class="search-input">Effort Target</th>
            <th class="search-efforttype">Effort Type</th>
            <th class="search-input">Effort Output</th>
            <th class="search-input">Effort Justification</th>
            <th class="search-input">Notes</th>
            <th>Estimated Complete Date</th>
            <th class="search-status">Status</th>
            <th style="width:125px;">&nbsp;</th>
        </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
        <tr>
            <th>id</th>
            <th>Industry</th>
            <th>SA</th>
            <th>Priority</th>
            <th>
                <!--Priority Index-->
            </th>
            <th>Workload</th>
            <th>Product</th>
            <th>Effort Target</th>
            <th>Effort Type</th>
            <th>Effort Output</th>
            <th>Effort Justification</th>
            <th>Notes</th>
            <th>
                <!--Estimated Complete Date-->
            </th>
            <th>Status</th>
            <th>
                <!--Action-->
            </th>
        </tr>
    </tfoot>
</table>
<script type="text/javascript">

    var save_method; //for save method string
    var table;
    var editor;

    $(document).ready(function () {

        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": false,
            "searching": true,
            "order": [],
            "rowReorder": true,
            "ajax": {
                url: "<?php echo site_url('SAView/ajax_list')?>",
                type: "POST"
            },
            "columnDefs": [
                { "name": "id", "targets": 0, "visible": false, "searchable": false },
                { "name": "industries.name", "targets": 1, "orderable": false },
                { "name": "sa_users_id", "targets": 2, "orderable": false },
                { "name": "priority", "targets": 3, "orderable": false },
                { "name": "priority_index", "visible": false, "targets": 4, "orderable": false },
                { "name": "workloads.name", "targets": 5, "orderable": false },
                { "name": "platforms.name", "targets": 6, "orderable": false },
                { "name": "effort_target", "targets": 7, "orderable": false },
                { "name": "efforttypes.name", "targets": 8, "orderable": false },
                { "name": "vflatprojecttasks.effortoutput", "targets": 9, "orderable": false },
                { "name": "effort_justification", "targets": 10, "orderable": false },
                { "name": "notes", "targets": 11, "orderable": false },
                { "name": "estimated_complete_date", "targets": 12, "orderable": false },
                { "name": "status", "targets": 13, "orderable": false },
                { "name": "actions", "targets": 14, "orderable": false }
            ],
            "initComplete": function () {
                this.api().columns('.search-select').every(function () {
                    var column = this;
                    var select = $('<select><option value="">Search...</option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
                    column.data().unique().sort().each(function (d, j) {
                        select.append('<option value="' + d + '">' + d + '</option>')
                    });
                });
                this.api().columns('.search-input').every(function () {
                    var column = this;
                    $('<input type="text" value="" placeholder="Search...">')
                        .appendTo($(column.footer()).empty())
                        .on('keyup change', function () {
                            if (window.event && event.type == "propertychange" && event.propertyName != "value") return;

                            var searchText = $.fn.dataTable.util.escapeRegex($(this).val());

                            window.clearTimeout($(this).data("timeout"));
                            $(this).data("timeout", setTimeout(function () {
                                if (column.search() !== this.value) {
                                    column.search(searchText).draw();
                                }
                            }, 1000));
                        });
                });
                this.api().columns('.search-industry').every(function () {
                    var column = this;
                    var select = $('<select><option value="">Search...</option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
                    var options = [];
<?php
foreach($industries as $key=>$value){
    echo "select.append('<option value=\"$value\">$value</option>');";
}
?>
                });
                this.api().columns('.search-sauser').every(function () {
                    var column = this;
                    var select = $('<select><option value="">Search...</option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
                    var options = [];
<?php
foreach($sausers as $key=>$value){
    echo "select.append('<option value=\"$key\">$value</option>');";
}
?>
                });
                this.api().columns('.search-efforttype').every(function () {
                    var column = this;
                    var select = $('<select><option value="">Search...</option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
                    var options = [];
<?php
foreach($efforttypes as $key=>$value){
    echo "select.append('<option value=\"$value\">$value</option>');";
}
?>
                });
                this.api().columns('.search-status').every(function () {
                    var column = this;
                    var select = $('<select><option value="">Search...</option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
                    var options = [];
<?php
$statusList = unserialize(SAP_STATUSLIST);
foreach($statusList as $key=>$value){
    echo "select.append('<option value=\"$key\">$value</option>');";
}
?>
                });
                this.api().columns('.search-priority').every(function () {
                    var column = this;
                    var select = $('<select><option value="">Search...</option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
                    var options = [];
<?php
$statusList = unserialize(SAP_PRIORITYLIST);
foreach($statusList as $key=>$value){
    echo "select.append('<option value=\"$key\">$value</option>');";
}
?>
                });
                this.api().columns('.search-platform').every(function () {
                    var column = this;
                    var select = $('<select><option value="">Search...</option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
                    var options = [];
<?php
foreach($platforms as $key=>$value){
    echo "select.append('<option value=\"$value\">$value</option>');";
}
?>
                });
            }
        });

        table.on('row-reorder', function (e, diff, edit) {

            if (diff.length > 0) {
                var result = 'Reorder started on row: ' + edit.triggerRow.data()[0] + ' Status: ' + edit.triggerRow.data()[13] + '\n';

                var triggerId = edit.triggerRow.data()[0];
                var status = edit.triggerRow.data()[13];

                var data = 'key=' + edit.triggerRow.data()[0];

                for (var i = 0, ien = diff.length ; i < ien ; i++) {
                    var rowData = table.row(diff[i].node).data();

                    result += rowData[0] + ' updated to be in position ' + diff[i].newData + ' (was ' + diff[i].oldData + ')\n';
                    data += ('&' + rowData[0] + '=' + diff[i].newData);
                }

                if (confirm('Reorder this Project Request?\n' + result)) {
                    $.ajax({
                        url: "<?php echo site_url('SAView/ajax_reorder')?>",
                        type: "POST",
                        data: data,
                        dataType: "JSON",
                        success: function (data) {
                            if (data.status) reload_table();
                            else alert(data.errorText);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            alert('Error reordering project. ' + textStatus);
                        }
                    });
                }
            }
        });

        /************************************************
        //Disable select until i can get a cleaner interface.
        *************************************************/
        //$('#table tbody').on('click', 'tr', function () {
        //    if ($(this).hasClass('selected')) {
        //        $(this).removeClass('selected');
        //    }
        //    else {
        //        table.$('tr.selected').removeClass('selected');
        //        $(this).addClass('selected');
        //    }
        //});

        $('#button').click(function () {
            alert(table.rows('.selected').data().length + ' row(s) selected');
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

        $('[name="status"]').val('<?php echo SAP_DEFAULTSTATUS; ?>');
        $("#effortoutput").html('Select an effort type...');
        $('#modal_form').modal('show');
        $('.modal-title').text('New Project Request');
    }

    function edit_project(id, newStatus) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('SAView/ajax_edit/')?>/" + id,
            cache: false,
            type: "GET",
            dataType: "JSON",
            success: function (data) {

                $("#effortoutput").html('Select an effort type...');

                $('[name="id"]').val(data.id);
                $('[name="status"]').val(newStatus ? newStatus : data.status);
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

    function save() {
        var url;

        $('#btnSave').text('saving...');
        $('#btnSave').attr('disabled', true);

        if (save_method == 'add') {
            url = "<?php echo site_url('SAView/ajax_add')?>";
        } else {
            url = "<?php echo site_url('SAView/ajax_update')?>";
        }

        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function (data) {

                if (data.status) {
                    $('#modal_form').modal('hide');
                    reload_table();
                }
                else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                    }
                }
                $('#btnSave').text('save');
                $('#btnSave').attr('disabled', false);


            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error adding / update data');
                $('#btnSave').text('save');
                $('#btnSave').attr('disabled', false);

            }
        });
    }

    function defer_project(id) {
        if (confirm('Are you sure you wish to defer this Project Request for later?')) {
            $.ajax({
                url: "<?php echo site_url('SAView/ajax_defer')?>/" + id,
                type: "POST",
                dataType: "JSON",
                success: function (data) {
                    //if success reload ajax table
                    $('#modal_form').modal('hide');
                    reload_table();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Error deferring Project Request');
                }
            });

        }
    }

    function delete_project(id) {
        if (confirm('Are you sure you wish to delete this Project Request?')) {
            $.ajax({
                url: "<?php echo site_url('SAView/ajax_delete')?>/" + id,
                type: "POST",
                dataType: "JSON",
                success: function (data) {
                    //if success reload ajax table
                    $('#modal_form').modal('hide');
                    reload_table();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Error deleting Project Request');
                }
            });

        }
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
                        <?php echo form_label('Email Address *', 'author_email', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_input(array('name'=>'author_email', 'placeholder'=>'Enter your email address...', 'class'=>'form-control required'), '', array('id'=>'author_email')); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Industry *', 'industries_id', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_dropdown('industries_id', array('' => 'Select One...') + $industries, '', 'class="form-control required" id="industry"'); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Workload *', 'workloads_id', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_dropdown('workloads_id', array('' => 'Select One...'), '', 'class="form-control required" id="workload"'); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Product *', 'platforms_id', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_dropdown('platforms_id', array('' => 'Select One...') + $platforms, '', 'class="form-control required" id="platform"'); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('SA *', 'sa_users_id', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_dropdown('sa_users_id', array('' => 'Select One...') + $sausers, '', 'class="form-control required" id="platform"'); ?>
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
                            <?php echo form_dropdown('efforttypes_id', array('' => 'Select One...') + $efforttypes, '', 'class="form-control required" id="efforttype"'); ?>
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
