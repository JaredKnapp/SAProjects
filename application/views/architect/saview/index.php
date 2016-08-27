
<script src="<?php echo $this->config->base_url('assets/js/sap-getprojecttaskdata.js'); ?>" type="text/javascript"></script>

<script type="text/javascript">
    var filter_accordion;
    var table;
    var timeoutHandle;

    var edit_project_page = "<?php echo site_url('/architect/SAView/load_project'); ?>";

    var ajax_projectlist_url = "<?php echo site_url('architect/SAView/ajax_list'); ?>";
    var ajax_reorder_url = "<?php echo site_url('architect/SAView/ajax_reorder'); ?>";
    var ajax_tasktable_url = "<?php echo site_url('architect/SAView/ajax_gettasktable'); ?>";

</script>

<style>
    .project-dialog .modal-dialog {
        width: 1200px;
    }
</style>

<button class="btn btn-success" onclick="addProjectRequest()">
    <i class="glyphicon glyphicon-plus"></i>Add Project Request
</button>

<button class="btn btn-default" onclick="reloadTable()">
    <i class="glyphicon glyphicon-refresh"></i>Reload
</button>

<br />
<br />
<div id="filters">
    <div id="filter_accordion">
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
                                    echo "<div class='checkbox center-vertical'><label for='industries_$key' class='selected'><input type='checkbox' name='industries[]' id='industries_$key' onchange='searchChanged()' value=\"$key\" checked>$value</label></div>";
                                }
                                ?>
                            </td>
                            <td width="200" valign="top">
                                <strong>Priorities:</strong>
                                <?php
                                foreach($priorityList as $key=>$value){
                                    echo "<div class='checkbox'><label for='priorities_$key' class='selected'><input type='checkbox' name='priorities[]' id='priorities_$key' onchange='searchChanged()' value=\"$key\" checked>$value</label></div>";
                                }
                                ?>
                                <br />
                                <strong>Statuses:</strong>
                                <?php
                                foreach($statusList as $key=>$value){
                                    echo "<div class='checkbox'><label for='statuses_$key' class='selected'><input type='checkbox' name='statuses[]' id='statuses_$key' onchange='searchChanged()' value=\"$key\" checked>$value</label></div>";
                                }
                                ?>

                            </td>
                            <td width="200" valign="top">
                                <strong>Platforms</strong>
                                <?php
                                foreach($platforms as $key=>$value){
                                    echo "<div class='checkbox'><label for='platforms_$key' class='selected'><input type='checkbox' name='platforms[]' id='platforms_$key' onchange='searchChanged()' value=\"$key\" checked>$value</label></div>";
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

    $(document).ready(function () {
        filter_accordion = $("#filter_accordion").accordion({
            active: false,
            activate: function (event, ui) {
                $('#FilterCollapsed').val(ui.newHeader.text() ? false : true);
            },
            heightStyle: "content",
            collapsible: true,
            create: function (event, ui) { $("#filter_accordion").show(); }
        });


        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            searching: true,
            rowReorder: { selector: 'td:nth-child(2)' },
            ajax: {
                url: ajax_projectlist_url,
                data: function (data) {
                    data.searchIndustries = $("input[name='industries[]']:checked:enabled").map(function () { value = $(this).val(); return value; }).get();
                    data.searchPriorities = $("input[name='priorities[]']:checked:enabled").map(function () { value = $(this).val(); return value; }).get();
                    data.searchStatuses = $("input[name='statuses[]']:checked:enabled").map(function () { value = $(this).val(); return value; }).get();
                    data.searchPlatforms = $("input[name='platforms[]']:checked:enabled").map(function () { value = $(this).val(); return value; }).get();
                },
                type: "POST"
            },
            columnDefs: [
                {
                    "name": "details", "targets": 0, "orderable": false, "className": 'details-control center-vertical center-horizontal', "width": '20px',
                    "render": function (data, type, row) {
                        return '<i id="details-twisty" class="details-control-icon glyphicon glyphicon-triangle-right" data-toggle="tooltip" title="Show Project Tasks" placement="bottom" style="cursor: pointer;"></i>';
                    }
                },
                { "name": "id", "targets": 1, "visible": false, "searchable": false },
                { "name": "industries.name", "targets": 2, "orderable": true, "className": "reorder dragable", "width": "30px" },
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
                                return '<div>' + dataArray[0] + '&nbsp;<i class="glyphicon glyphicon-comment" aria-hidden="true"  data-toggle="popover" data-html="true" data-trigger="focus" title="Overridden Value" data-content="Sum of project tasks = ' + dataArray[1] + ' days." style="cursor: pointer;"></i></div>';
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
                    "name": "actions", "targets": 17, "orderable": false, "width": "100px",
                    "render": function (data, type, row) {
                        editButton = '<a class="btn btn-sm btn-success" href="javascript:void(0)" title="Edit" onclick="edit_project(\'' + row[1] + '\')"><i class="glyphicon glyphicon-pencil"></i></a>';
                        deleteButton = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_project(\'' + row[1] + '\')"><i class="glyphicon glyphicon-trash"></i></a>';
                        approveButton = (row[16] === '<?php echo $statusList[SAP_DEFAULTSTATUS] ?>') ? '<a class="btn btn-sm btn-info" href="javascript:void(0)" title="Approve" onclick="edit_project(\'' + row[1] + '\', \'approved\')"><i class="glyphicon glyphicon-thumbs-up"></i></a>' : false;
                        deferButton = (row[16] === '<?php echo $statusList[SAP_DEFAULTSTATUS] ?>') ? '<a class="btn btn-sm btn-info" href="javascript:void(0)" title="Defer" onclick="defer_project(\'' + row[2] + '\')"><i class="glyphicon glyphicon-thumbs-down"></i></a>' : false;
                        notesButton = (row[12]) ? '<a href="#" class="btn btn-sm btn-info" href="javascript:void(0)" data-toggle="popover" data-html="true" data-trigger="focus" title="Project Notes" data-content="' + (row[12]).replace(/(\r\n|\n|\r)/g, "<br />") + '"><i class="glyphicon glyphicon-info-sign"></i></a>' : false;
                        return editButton + '&nbsp;' + deleteButton + (approveButton ? (' ' + approveButton) : '') + (deferButton ? (' ' + deferButton) : '') + (notesButton ? (' ' + notesButton) : '');
                    }
                }
            ],
            initComplete: function () {

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

                //if (confirm('Reorder this Project Request?\n' + result)) {
                if (confirm('Reorder this Project Request?')) {

                    $.ajax({
                        url: ajax_reorder_url,
                        type: "POST",
                        data: data,
                        dataType: "JSON",
                        success: function (data) {
                            if (data.status) reloadTable();
                            else alert(data.errorText);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            alert('Error reordering project. ' + textStatus);
                        }
                    });
                }
            }
        });

        //Enable ALL Popups
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

        //Enable Details twistie
        $('#table tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var icon = $(this).find('i');
            var row = table.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                icon.removeClass('glyphicon-triangle-bottom');
                icon.addClass('glyphicon-triangle-right');
            } else {
                var control = { row: row, icon: icon };
                var data = row.data();
                getProjectTaskData(ajax_tasktable_url, control, data[1], null, formatProjectChildrowData);
            }
        });

    });  //<-- END Document Ready Function

    function reloadTable() {
        table.ajax.reload(null, false);
    }

    function searchChanged() {
        window.clearTimeout(timeoutHandle);
        timeoutHandle = window.setTimeout(reload_table, 2000);
    }

    function formatProjectChildrowData(control, data) {
        var row = control['row'];
        var icon = control['icon'];

        var tableData = row.data();

        var rowData = "<div style='padding-left: 25px; padding-right: 300px;' class='table-responsive'>";
        rowData += "<table class='table table-bordered table-condensed table-hover table-striped' style='table-layout: fixed;'><thead><tr>" +
            "<th style='width: 300px;'>Task</th>" +
            "<th style='width: 150px;'>Output</th>" +
            "<th style='width: 150px;'>Projected Start Date</th>" +
            "<th style='width: 150px;'>Estimated Completion Date</th>" +
            "<th style='width: 75px;'>Duration (Days)</th>" +
            "<th style='width: 150px;'>Date Completed</th>" +
            //"<th style='width: 50px;'><!--Collateral Link--></th>" +
            "</tr></thead><tbody>";

        $.each(data, function (index, value) {
            var task = value['task'];
            var effort = value['effort'];

            if (task && task['id']) {
                rowData += '<tr>';
                rowData += '<td>' + effort['name'] + '</td>';
                rowData += '<td>' + effort['produce'] + '</td>';
                rowData += '<td>' + task['projected_start_date'] + '</td>';
                rowData += '<td>' + task['estimated_completion_date'] + '</td>';
                rowData += '<td>' + task['duration'] + '</td>';
                rowData += '<td>' + task['completion_date'] + '</td>';
                //rowData += '<td>' + (task['collateralurl'] ? task['collateralurl'] : null) + '</td>';
                rowData += '</tr>';

            }
        });

        rowData += '</tbody></table></div>';

        row.child(rowData).show();
        icon.removeClass('glyphicon-triangle-right');
        icon.addClass('glyphicon-triangle-bottom');
    }

    function addProjectRequest() {
        BootstrapDialog.show({
            cssClass: 'project-dialog',
            draggable: true,
            title: 'Add Project',
            message: function (dialog) {
                var $message = $('<div><center><i class="icon-spinner icon-spin icon-large"></i>Loading...</center></div>');
                var pageToLoad = dialog.getData('pageToLoad');
                $message.load(pageToLoad);

                return $message;
            },
            data: {
                'pageToLoad': edit_project_page
            }
        });
    }
</script>