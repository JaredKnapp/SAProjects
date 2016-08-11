<?php
$statusList = unserialize(SAP_STATUSLIST);
$priorityList = unserialize(SAP_PRIORITYLIST);
?>

<button class="btn btn-success" onclick="add_project_request()">
    <i class="glyphicon glyphicon-plus"></i>Add Project Request
</button>
<button class="btn btn-default" onclick="reload_table()">
    <i class="glyphicon glyphicon-refresh"></i>Reload
</button>
<br />
<br />
<div id="filters">
    <div id="accordion">
        <span>
            <span>
                <strong>Additional&nbsp;Filters</strong>
            </span>
        </span>
        <div style="background-color: #f1f1f1; ">
            <form class="form-horizontal">
                <table>
                    <tbody>
                        <tr>
                            <td width="200" valign="top">
                                <strong>Industries:</strong>
                                <?php
                                foreach($industries as $key=>$value){
                                    echo "<div class='checkbox center-vertical'><label for='industries_$key' class='selected'><input type='checkbox' name='industries[]' id='industries_$key' onchange='search_changed()' value=\"$key\" checked>$value</label></div>";
                                }
                                ?>
                            </td>
                            <td width="200" valign="top">
                                <strong>Priorities:</strong>
                                <?php
                                foreach($priorityList as $key=>$value){
                                    echo "<div class='checkbox'><label for='priorities_$key' class='selected'><input type='checkbox' name='priorities[]' id='priorities_$key' onchange='search_changed()' value=\"$key\" checked>$value</label></div>";
                                }
                                ?>
                                <br />
                                <strong>Statuses:</strong>
                                <?php
                                foreach($statusList as $key=>$value){
                                    echo "<div class='checkbox'><label for='statuses_$key' class='selected'><input type='checkbox' name='statuses[]' id='statuses_$key' onchange='search_changed()' value=\"$key\" checked>$value</label></div>";
                                }
                                ?>

                            </td>
                            <td width="200" valign="top">
                                <strong>Platforms</strong>
                                <?php
                                foreach($platforms as $key=>$value){
                                    echo "<div class='checkbox'><label for='platforms_$key' class='selected'><input type='checkbox' name='platforms[]' id='platforms_$key' onchange='search_changed()' value=\"$key\" checked>$value</label></div>";
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>

<br /><!--table hover table-striped table-bordered-->
<table id="table" class="display table table-bordered table-hover table-striped table-condensed" border="0" cellpadding="0" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th></th>
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
            <th>Notes</th>
            <th>Projected Start</th>
            <th>Estimated Completion</th>
            <th>Duration (Work Days)</th>
            <th class="search-status">Status</th>
            <th></th>
        </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
        <tr>
            <td></td>
            <th>id</th>
            <th>Industry</th>
            <th>SA</th>
            <th>Priority</th>
            <th></th>
            <th>Workload</th>
            <th>Product</th>
            <th>Effort Target</th>
            <th>Effort Type</th>
            <th>Effort Output</th>
            <th>Effort Justification</th>
            <th>Notes</th>
            <th></th>
            <th></th>
            <th></th>
            <th>Status</th>
            <th></th>
        </tr>
    </tfoot>
</table>
<script type="text/javascript">

    var save_method; //for save method string
    var accordion;
    var table;
    var editor;
    var timeoutHandle;

    $(document).ready(function () {
        accordion = $("#accordion").accordion({
            active: false,
            activate: function (event, ui) {
                $('#FilterCollapsed').val(ui.newHeader.text() ? false : true);
            },
            heightStyle: "content",
            collapsible: true,
            create: function (event, ui) { $("#accordion").show(); }
        });

        editor = new $.fn.dataTable.Editor({
            ajax: "../php/staff.php",
            table: "#table",
            fields: [{
                label: "Projected start:",
                name: "projected_start_date"
            }, {
                label: "Estimated completion date:",
                name: "estimated_completion_date"
            }, {
                label: "Duration:",
                name: "estimated_work_days"
            }
            ]
        });

        //$('#table').on('click', 'tbody td:not(:first-child)', function (e) {
        //    editor.inline(this);
        //});

        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": false,
            "searching": true,
            "rowReorder": {
                selector: 'td:nth-child(2)'
            },
            "ajax": {
                url: "<?php echo site_url('architect/SAView/ajax_list')?>",
                data: function (data) {
                    data.searchIndustries = $("input[name='industries[]']:checked:enabled").map(function () { value = $(this).val(); return value; }).get();
                    data.searchPriorities = $("input[name='priorities[]']:checked:enabled").map(function () { value = $(this).val(); return value; }).get();
                    data.searchStatuses = $("input[name='statuses[]']:checked:enabled").map(function () { value = $(this).val(); return value; }).get();
                    data.searchPlatforms = $("input[name='platforms[]']:checked:enabled").map(function () { value = $(this).val(); return value; }).get();
                },
                type: "POST"
            },
            //"columns": [
            //        { data: null },
            //        { data: "id" },
            //        { data: "industries" },
            //        { data: "sa_users_id" },
            //        { data: "priority" },
            //        { data: "priority_index" },
            //        { data: "workloads" },
            //        { data: "platforms" },
            //        { data: "effort_target" },
            //        { data: "efforttypes" },
            //        { data: "effortoutput" },
            //        { data: "effort_justification" },
            //        { data: "notes" },
            //        { data: "projected_start_date" },
            //        { data: "estimated_complete_date" },
            //        { data: "estimated_work_days" },
            //        { data: "status" },
            //        { data: null }
            //    ],
            "columnDefs": [
                {
                    "name": "details", "targets": 0, "orderable": false, "className": 'details-control center-vertical center-horizontal', "width": '20px',
                    "render": function (data, type, row) {
                        return '<i id="details-twisty" class="details-control-icon glyphicon glyphicon-triangle-right" data-toggle="tooltip" title="Show Project Tasks" placement="bottom"></i>';
                    }
                },
                { "name": "id", "targets": 1, "visible": false, "searchable": false },
                { "name": "industries.name", "targets": 2, "orderable": true, "className": "reorder dragable" },
                { "name": "sa_users_id", "targets": 3, "orderable": false },
                { "name": "priority", "targets": 4, "orderable": false },
                { "name": "priority_index", "visible": false, "targets": 5, "orderable": false },
                { "name": "workloads.name", "targets": 6, "orderable": false },
                { "name": "platforms.name", "targets": 7, "orderable": false },
                { "name": "effort_target", "targets": 8, "orderable": false },
                { "name": "efforttypes.name", "targets": 9, "orderable": false },
                { "name": "vflatprojecttasks.effortoutput", "targets": 10, "visible": false, "orderable": false },
                { "name": "effort_justification", "targets": 11, "orderable": false },
                { "name": "notes", "targets": 12, "visible": false, "orderable": false },
                { "name": "projected_start_date", "targets": 13, "orderable": true },
                { "name": "estimated_complete_date", "targets": 14, "orderable": false },
                {
                    "name": "estimated_work_days", "targets": 15, "orderable": false,
                    "render": function (data, type, row) {
                        if (data) {
                            dataArray = String(data).split("!");
                            if (dataArray.length == 1) {
                                return data;
                            } else {
                                return '<div>' + dataArray[0] + '&nbsp;<i class="glyphicon glyphicon-comment" aria-hidden="true"  data-toggle="popover" data-html="true" data-trigger="focus" title="Overridden Value" data-content="Value has been overriden in the project. Acual sum of project tasks: ' + dataArray[1] + ' days." style="cursor: pointer;"></i></div>';
                            }
                        }
                        else {
                            return 'empty';
                        }
                    }
                },
                {
                    "name": "status", "targets": 16, "orderable": false, "className": "center-vertical center-horizontal",
                    "render": function (data, type, row) {
                        labelStyle = 'label-default';

                        if (data === '<?php echo $statusList['draft']; ?>') labelStyle = 'label-default';
                        if (data === '<?php echo $statusList['approved']; ?>') labelStyle = 'label-info';
                        if (data === '<?php echo $statusList['deferred']; ?>') labelStyle = 'label-danger';
                        if (data === '<?php echo $statusList['inprocess']; ?>') labelStyle = 'label-success';
                        if (data === '<?php echo $statusList['scheduled']; ?>') labelStyle = 'label-primary';
                        if (data === '<?php echo $statusList['complete']; ?>') labelStyle = '';

                        return '<h5><span class="label ' + labelStyle + '">' + data + '</span></h5>';
                    }
                },
                {
                    "name": "actions", "targets": 17, "orderable": false,
                    "render": function (data, type, row) {
                        editButton = '<a class="btn btn-sm btn-success" href="javascript:void(0)" title="Edit" onclick="edit_project(\'' + row[1] + '\')"><i class="glyphicon glyphicon-pencil"></i></a>';
                        deleteButton = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_project(\'' + row[1] + '\')"><i class="glyphicon glyphicon-trash"></i></a>';
                        approveButton = (row[14] === '<?php echo $statusList[SAP_DEFAULTSTATUS] ?>') ? '<a class="btn btn-sm btn-info" href="javascript:void(0)" title="Approve" onclick="edit_project(\'' + row[1] + '\', \'approved\')"><i class="glyphicon glyphicon-thumbs-up"></i></a>' : false;
                        deferButton = (row[14] === '<?php echo $statusList[SAP_DEFAULTSTATUS] ?>') ? '<a class="btn btn-sm btn-info" href="javascript:void(0)" title="Defer" onclick="defer_project(\'' + row[2] + '\')"><i class="glyphicon glyphicon-thumbs-down"></i></a>' : false;
                        notesButton = (row[12]) ? '<a href="#" class="btn btn-sm btn-info" href="javascript:void(0)" data-toggle="popover" data-html="true" data-trigger="focus" title="Project Notes" data-content="' + (row[12]).replace(/(\r\n|\n|\r)/g, "<br />") + '"><i class="glyphicon glyphicon-info-sign"></i></a>' : false;
                        return editButton + '&nbsp;' + deleteButton + (approveButton ? (' ' + approveButton) : '') + (deferButton ? (' ' + deferButton) : '') + (notesButton ? (' ' + notesButton) : '');
                    }
                }
            ],
            //"select": {
            //    "style": 'os',
            //    "selector": 'td:first-child'
            //},
            "initComplete": function () {

                this.api().columns('.search-select').every(function () {
                    var column = this;
                    var select = $('<select><option value="">Filter...</option></select>')
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
                    $('<input type="text" value="" placeholder="Filter...">')
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
                    var select = $('<select><option value="">Filter...</option></select>')
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
                    var select = $('<select><option value="">Filter...</option></select>')
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
                    var select = $('<select><option value="">Filter...</option></select>')
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
                    var select = $('<select><option value="">Filter...</option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
                    var options = [];
<?php
foreach($statusList as $key=>$value){
    echo "select.append('<option value=\"$key\">$value</option>');";
}
?>
                });

                this.api().columns('.search-priority').every(function () {
                    var column = this;
                    var select = $('<select><option value="">Filter...</option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
                    var options = [];
<?php
foreach($priorityList as $key=>$value){
    echo "select.append('<option value=\"$key\">$value</option>');";
}
?>
                });

                this.api().columns('.search-platform').every(function () {
                    var column = this;
                    var select = $('<select><option value="">Filter...</option></select>')
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
                var result = 'Reorder started on row: ' + edit.triggerRow.data()[1] + ' Status: ' + edit.triggerRow.data()[14] + '\n';

                var triggerId = edit.triggerRow.data()[1];
                var status = edit.triggerRow.data()[14];

                var data = 'key=' + edit.triggerRow.data()[1];

                for (var i = 0, ien = diff.length ; i < ien ; i++) {
                    var rowData = table.row(diff[i].node).data();

                    result += rowData[1] + ' updated to be in position ' + diff[i].newData + ' (was ' + diff[i].oldData + ')\n';
                    data += ('&' + rowData[1] + '=' + diff[i].newData);
                }

                if (confirm('Reorder this Project Request?\n' + result)) {

                    $.ajax({
                        url: "<?php echo site_url('architect/SAView/ajax_reorder')?>",
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
        // Add event listener for opening and closing details

        $('#table').on('draw.dt', function () {

            $('[data-toggle="popover"]').popover({
                trigger: 'hover',
                placement: 'left',
            });

            $(document).on("click", ".popover-footer .btn", function () {
                $(this).parents(".popover").popover('hide');
            });

            $(document).ready(function () {
                $('[data-toggle="tooltip"]').tooltip();
            });

        });

        $('#table tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var icon = $(this).find('i');
            var row = table.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                //tr.removeClass('shown');
                icon.removeClass('glyphicon-triangle-bottom');
                icon.addClass('glyphicon-triangle-right');
            }
            else {
                // Open this row
                row.child(format_row_details(row.data())).show();
                //tr.addClass('shown');
                icon.removeClass('glyphicon-triangle-right');
                icon.addClass('glyphicon-triangle-bottom');
            }
        });

        $('#table tbody').on('click', 'td.reorder', function () {
            var tr = $(this).closest('tr');
            var icon = $(this).find('i');
            var row = table.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                //tr.removeClass('shown');
                icon.removeClass('glyphicon-triangle-bottom');
                icon.addClass('glyphicon-triangle-right');
            }
            else {
                // Open this row
                row.child(format_row_details(row.data())).show();
                //tr.addClass('shown');
                icon.removeClass('glyphicon-triangle-right');
                icon.addClass('glyphicon-triangle-bottom');
            }
        });

        $('[data-toggle="popover"]').popover({
            placement: 'top'
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

    /* Formatting function for row details - modify as you need */
    function format_row_details(d) {
        // `d` is the original data object for the row
        return '<div style="padding-left: 50px;">' +
            '<strong>Selected Project Tasks:</strong><br>' +
            '<div style="padding-left: 20px;">' +
            d[10] +
            '</div>' +
            '</div>';
    }

    function search_changed() {
        window.clearTimeout(timeoutHandle);
        timeoutHandle = window.setTimeout(reload_table, 2000);
    }

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
        $('.modal-title').text('New Project Request');
        $('#modal_form').modal('show');
    }

    function edit_project(id, newStatus) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('architect/SAView/ajax_edit/')?>/" + id,
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
                getEffortOutput(data.effortoutput_id ? data.effortoutput_id.split('||') : null);
                $('[name="desired_completion_date"]').val(data.desired_completion_date);
                $('[name="projected_start_date"]').val(data.projected_start_date);
                $('[name="estimated_completion_date"]').val(data.estimated_completion_date);
                $('[name="estimated_work_days"]').val(data.estimated_work_days);
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
            url = "<?php echo site_url('architect/SAView/ajax_add')?>";
        } else {
            url = "<?php echo site_url('architect/SAView/ajax_update')?>";
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
                url: "<?php echo site_url('architect/SAView/ajax_defer')?>/" + id,
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
                url: "<?php echo site_url('architect/SAView/ajax_delete')?>/" + id,
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

<!-- Bootstrap modal Project edit form -->
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
                        <?php echo form_label('Projected Start Date', 'projected_start_date', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_input(array('name'=>'projected_start_date', 'placeholder'=>'mm/dd/yyyy', 'class'=>'form-control'), '', array('id'=>'projected_start_date')); ?>
                            <script>$(function () { $('#projected_start_date').datepicker(); });</script>
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
                        <?php echo form_label('Estimated Work Days', 'estimated_work_days', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_input(array('name'=>'estimated_work_days', 'class'=>'form-control'), '', array('id'=>'estimated_work_days')); ?>
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
<!-- End Bootstrap modal Project edit form-->

<script type="text/javascript">
    function workday_count(startDate, endDate) {

        // Validate input
        if (endDate < startDate)
            return 0;

        // Calculate days between dates
        var millisecondsPerDay = 86400 * 1000; // Day in milliseconds
        startDate.setHours(0, 0, 0, 1);  // Start just after midnight
        endDate.setHours(23, 59, 59, 999);  // End just before midnight
        var diff = endDate - startDate;  // Milliseconds between datetime objects
        var days = Math.ceil(diff / millisecondsPerDay);

        // Subtract two weekend days for every week in between
        var weeks = Math.floor(days / 7);
        days = days - (weeks * 2);

        // Handle special cases
        var startDay = startDate.getDay();
        var endDay = endDate.getDay();

        // Remove weekend not previously removed.
        if (startDay - endDay > 1)
            days = days - 2;

        // Remove start day if span starts on Sunday but ends before Saturday
        if (startDay == 0 && endDay != 6)
            days = days - 1

        // Remove end day if span ends on Saturday but starts after Sunday
        if (endDay == 6 && startDay != 0)
            days = days - 1

        return days;
    }

    function getWorkingDays() {
        var start = $('#projected_start_date').val().split('/');
        if (start && start.length == 3) {
            var startDate = new Date(start[0], start[1], start[2]);
            var end = $('#estimated_completion_date').val().split('/');
            if (end && end.length == 3) {
                var endDate = new Date(end[0], end[1], end[2]);
                //$('#estimated_work_days').val(workday_count(startDate, endDate));
            }
        }
    }

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
        $("#projected_start_date").on('change', function () {
            getWorkingDays();
        });
        $("#estimated_completion_date").on('change', function () {
            getWorkingDays();
        });
    });
</script>
