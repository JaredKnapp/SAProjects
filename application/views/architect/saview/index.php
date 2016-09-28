
<script src="<?php echo $this->config->base_url('assets/js/sap-getprojecttaskdata.js'); ?>" type="text/javascript"></script>

<script type="text/javascript">
    var filter_accordion = null;
    var table = null;
    var task_table = null;
    var timeoutHandle = null;

    var edit_project_page = "<?php echo site_url('/architect/SAView/load_project'); ?>";

    var ajax_projectlist_url = "<?php echo site_url('architect/SAView/ajax_list'); ?>";
    var ajax_reorder_url = "<?php echo site_url('architect/SAView/ajax_reorder'); ?>";
    var ajax_tasktable_url = "<?php echo site_url('architect/SAView/ajax_gettasktable'); ?>";
    var ajax_addproject_url = "<?php echo site_url('architect/SAView/ajax_add')?>";
    var ajax_updateproject_url = "<?php echo site_url('architect/SAView/ajax_update')?>";
    var ajax_deleteproject_url = "<?php echo site_url('architect/SAView/ajax_delete')?>";
    var ajax_deferproject_url = "<?php echo site_url('architect/SAView/ajax_defer')?>";

    var filter_searchText = "<?php echo (array_key_exists('search', $_GET) && $_GET['search']) ? $_GET['search'] : ""; ?>";

    //Populate Search Boxes using QueryString
    var filter_values = new Array();
    <?php
foreach($_GET as $key=>$value){
    if(substr("$key     ", 0, 4)=="col_"){
        echo "filter_values['$key']='$value'; \r";
        //echo "$('[name=\"$key\"]').val('$value');\r";
        //echo "table.column(".substr($key, 4).").search('$value');\r";
    }
}
?>


</script>

<style>
    .project-dialog .modal-dialog {
        width: 1200px;
    }

    .project-notes-dialog .modal-dialog {
        width: 500px;
    }
</style>

<button class="btn btn-success" onclick="addProjectRequest()">
    <i class="glyphicon glyphicon-plus"></i>Add Project Request
</button>

<button class="btn btn-default" onclick="reloadTable()">
    <i class="glyphicon glyphicon-refresh"></i>Reload
</button>

<button class="btn btn-default pull-right" title="Copy URL to clipboard" onclick="showURL()">
    <i class="glyphicon glyphicon-paperclip"></i>
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
                                $queryValues = array_key_exists('industries', $_GET) ? $_GET['industries'] : array_keys($industries);
                                foreach($industries as $key=>$value){
                                    $isChecked = in_array($key, $queryValues);
                                    echo "<div class='checkbox center-vertical'><label for='industries_$key' class='selected'><input type='checkbox' name='industries[]' id='industries_$key' onchange='searchChanged()' value=\"$key\" " . ($isChecked ? "checked" : "") . ">$value</label></div>";
                                }
                                ?>
                            </td>
                            <td width="200" valign="top">
                                <strong>Priorities:</strong>
                                <?php
                                $queryValues = array_key_exists('priorities', $_GET) ? $_GET['priorities'] : array_keys($priorityList);
                                foreach($priorityList as $key=>$value){
                                    $isChecked = in_array($key, $queryValues);
                                    echo "<div class='checkbox'><label for='priorities_$key' class='selected'><input type='checkbox' name='priorities[]' id='priorities_$key' onchange='searchChanged()' value=\"$key\" " . ($isChecked ? "checked" : "") . ">$value</label></div>";
                                }
                                ?>
                                <br />
                                <strong>Statuses:</strong>
                                <?php
                                $queryValues = array_key_exists('statuses', $_GET) ? $_GET['statuses'] : array_keys($statusList);
                                foreach($statusList as $key=>$value){
                                    $isChecked = in_array($key, $queryValues);
                                    echo "<div class='checkbox'><label for='statuses_$key' class='selected'><input type='checkbox' name='statuses[]' id='statuses_$key' onchange='searchChanged()' value=\"$key\" " . ($isChecked ? "checked" : "") . ">$value</label></div>";
                                }
                                ?>

                            </td>
                            <td width="200" valign="top">
                                <strong>Platforms</strong>
                                <?php
                                $queryValues = array_key_exists('platforms', $_GET) ? $_GET['platforms'] : array_keys($platforms);
                                foreach($platforms as $key=>$value){
                                    $isChecked = in_array($key, $queryValues);
                                    echo "<div class='checkbox'><label for='platforms_$key' class='selected'><input type='checkbox' name='platforms[]' id='platforms_$key' onchange='searchChanged()' value=\"$key\" " . ($isChecked ? "checked" : "") . ">$value</label></div>";
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
            <th>SAPID</th>
            <th class="search-industry">Industry</th>
            <th class="search-sauser">SA</th>
            <th class="search-priority">Priority</th>
            <th class="search-input">Workload</th>
            <th class="search-platform">Product</th>
            <th class="search-input">Effort Target</th>
            <th class="search-efforttype">Effort Type</th>
            <th class="search-input">Effort Justification</th>
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
            <th></th>
            <th>Industry</th>
            <th>SA</th>
            <th>Priority</th>
            <th>Workload</th>
            <th>Product</th>
            <th>Effort Target</th>
            <th>Effort Type</th>
            <th>Effort Justification</th>
            <th></th>
            <th></th>
            <th></th>
            <th>Status</th>
            <th></th>
        </tr>
    </tfoot>
</table>
<script type="text/javascript">
    filterstate = false;

    $(document).ready(function () {
        filter_accordion = $("#filter_accordion").accordion({
            active: false,
            activate: function (event, ui) {
                filterstate = ui.newHeader.text() ? true : false;
                $('#FilterCollapsed').val(ui.newHeader.text() ? false : true);
            },
            heightStyle: "content",
            collapsible: true,
            create: function (event, ui) {
                filterstate = false;
                $("#filter_accordion").show();
            }
        });


        table = $('#table').DataTable({
            rowId: 'id',
            processing: true,
            serverSide: true,
            ordering: false,
            searching: true,
            search: { search: filter_searchText },
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
            columns: [
                {
                    data: null, className: 'details-control center-vertical center-horizontal',
                    defaultContent: '<i id="details-twisty" class="details-control-icon glyphicon glyphicon-triangle-right" data-toggle="tooltip" title="Show Project Tasks" placement="bottom" style="cursor: pointer;"></i>'
                },
                { data: "sapid", className: "reorder dragable", id: "sapid" },
                { data: "industry", name: "industries.name", id: "industry" },
                { data: "sa", name: "sa_users_id" },
                { data: "priority", name: "priority" },
                { data: "workload", name: "workloads.name" },
                { data: "platform", name: "platforms.name" },
                { data: "effort_target", name: "effort_target" },
                { data: "effort_type", name: "efforttypes.name" },
                { data: "effort_justification", name: "effort_justification" },
                { data: "projected_start_date", name: "projected_start_date" },
                {
                    name: "estimated_completion_date",
                    data: "estimated_completion_date",
                    render: function (data, type, row, meta) {
                        if (data) {
                            dataArray = String(data).split("!");
                            if (dataArray.length == 1) {
                                return data;
                            } else {
                                message = 'Latest task completion date = ' + dataArray[1];
                                if (!dataArray[1]) {
                                    message = 'No date set for any of the tasks.'
                                }
                                else if (dataArray[0] == dataArray[1]) {
                                    message = 'Value matches latest task completion date (' + dataArray[1] + ').';
                                }
                                return '<div>' + dataArray[0] + '&nbsp;<i class="glyphicon glyphicon-comment" aria-hidden="true"  data-toggle="popover" data-html="true" data-trigger="focus" title="Overridden Value" data-content="' + message + '." style="cursor: pointer;"></i></div>';
                            }
                        }
                        else {
                            return '';
                        }
                    }
                },
                {
                    name: "estimated_work_days",
                    data: "duration",
                    render: function (data, type, row) {
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
                    name: "status",
                    data: "status",
                    className: "center-vertical center-horizontal",
                    render: function (data, type, row) {
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
                    data: null,
                    render: function (data, type, row) {
                        editButton = '<a class="btn btn-sm btn-success" href="javascript:void(0)" title="Edit" onclick="editProjectRequest(\'' + data['id'] + '\')"><i class="glyphicon glyphicon-pencil"></i></a>';
                        deleteButton = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="deleteProject(\'' + data['id'] + '\')"><i class="glyphicon glyphicon-trash"></i></a>';
                        approveButton = (data['status'] === '<?php echo $statusList[SAP_DEFAULTSTATUS] ?>') ? '<a class="btn btn-sm btn-info" href="javascript:void(0)" title="Approve" onclick="editProjectRequest(\'' + data['id'] + '\', \'approved\')"><i class="glyphicon glyphicon-thumbs-up"></i></a>' : false;
                        deferButton = (data['status'] === '<?php echo $statusList[SAP_DEFAULTSTATUS] ?>') ? '<a class="btn btn-sm btn-info" href="javascript:void(0)" title="Defer" onclick="deferProject(\'' + data['id'] + '\')"><i class="glyphicon glyphicon-thumbs-down"></i></a>' : false;
                        notesButton = (data['notes']) ? '<a href="#" class="btn btn-sm btn-info" href="javascript:void(0)" data-toggle="popover" data-html="true" data-trigger="focus" title="Project Notes" data-content="' + (data['notes']).replace(/(\r\n|\n|\r)/g, "<br />") + '"><i class="glyphicon glyphicon-info-sign"></i></a>' : false;
                        return editButton + '&nbsp;' + deleteButton + (approveButton ? (' ' + approveButton) : '') + (deferButton ? (' ' + deferButton) : '') + (notesButton ? (' ' + notesButton) : '');
                    }
                }
            ],
            initComplete: function () {

                this.api().columns('.search-input').every(function () {
                    var column = this;
                    value = filter_values['col_' + this.index()] ? filter_values['col_' + this.index()] : "";
                    $('<input type="text" value="' + value + '" name="col_' + column.index() + '" placeholder="Filter...">')
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
                    var select = $('<select name="col_' + column.index() + '"><option value="">Filter...</option></select>')
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
                    var select = $('<select name="col_' + column.index() + '"><option value="">Filter...</option></select>')
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
                    var select = $('<select name="col_' + column.index() + '"><option value="">Filter...</option></select>')
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
                    var select = $('<select name="col_' + column.index() + '"><option value="">Filter...</option></select>')
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
                    var select = $('<select name="col_' + column.index() + '"><option value="">Filter...</option></select>')
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
                    var select = $('<select name="col_' + column.index() + '"><option value="">Filter...</option></select>')
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

                var result = 'Reorder started on row: ' + edit.triggerRow.data()['sapid'] + ' Status: ' + edit.triggerRow.data()['status'] + '\n';

                var triggerId = edit.triggerRow.data()['id'];
                var status = edit.triggerRow.data()['status'];

                var data = 'key=' + edit.triggerRow.data()['id'];

                for (var i = 0, ien = diff.length ; i < ien ; i++) {
                    var rowData = table.row(diff[i].node).data();

                    var oldData = table.row(diff[i].oldPosition).data();
                    var newData = table.row(diff[i].newPosition).data();

                    result += rowData['id'] + ' updated to be in position ' + newData['id'] + ' (was ' + oldData['id'] + ')\n';
                    data += ('&' + rowData['id'] + '=' + newData['id']);
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
                row.child.hide();
                icon.removeClass('glyphicon-triangle-bottom');
                icon.addClass('glyphicon-triangle-right');
            } else {
                var control = { row: row, icon: icon };
                var data = row.data();
                getProjectTaskData(ajax_tasktable_url, control, data['id'], null, formatProjectChildrowData);
            }
        });

        //If there are filter search values from the querystring, then reload the table (now that the search values have been set)
        if(filter_values.length > 0) reloadTable();
    });  //<-- END Document Ready Function

    function buildLinkedText(linkText, linkURL) {
        if (linkURL) {
            return '<a href="' + linkURL + '" target="_blank">' + linkText + '</a>';
        } else {
            return linkText
        }
    }

    function showURL() {

        var pageURL = [location.protocol, '//', location.host, location.pathname].join('');
        var separator = "?"

        var searchText = $('.dataTables_filter input').val();
        if (searchText) {
            pageURL += separator + 'search=' + searchText;
            separator = '&';
        }

        $('input[name="industries[]"]:checked').each(function () {
            pageURL += separator + 'industries[]=' + $(this).val()
            separator = '&';
        });

        $('input[name="priorities[]"]:checked').each(function () {
            pageURL += separator + 'priorities[]=' + $(this).val()
            separator = '&';
        });

        $('input[name="statuses[]"]:checked').each(function () {
            pageURL += separator + 'statuses[]=' + $(this).val()
            separator = '&';
        });

        $('input[name="platforms[]"]:checked').each(function () {
            pageURL += separator + 'platforms[]=' + $(this).val()
            separator = '&';
        });


        table.columns().every(function () {
            if (this.search()) {
                searchText = this.search();
                if (searchText.substr(0, 1) != "^" && searchText.substr(-1) != "&") {
                    searchText = $.fn.dataTable.util.escapeRegex(searchText);
                }
                pageURL += separator + 'col_' + this.index() + '=' + searchText;
                separator = '&';
            }
        });

        var prompt = $('<div></div>');
        prompt.append('<textarea readonly class="form-control">' + pageURL + '</textarea><br />');
        prompt.append('Highlight the text in the textbox, then press <kbd>CTRL+C</kbd> (windows), or <kbd>COMMAND+C</kbd> (mac) to copy the URL text to your clipboard.');

        BootstrapDialog.show({
            title: 'Copy your URL link from the following text box',
            message: prompt
        });

    }

    function reloadTable() {
        table.ajax.reload(null, false);
    }

    function searchChanged() {
        window.clearTimeout(timeoutHandle);
        timeoutHandle = window.setTimeout(reloadTable, 2000);
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
                rowData += '<td>' + buildLinkedText(effort['name'], task['collateralurl']) + '</td>';
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
        loadProject('add', null, null);
    }

    function editProjectRequest(id, newStatus) {
        loadProject('edit', id, newStatus);
    }

    function loadProject(editMode, id, newStatus) {
        BootstrapDialog.show({
            cssClass: 'project-dialog',
            draggable: true,
            title: editMode == 'add' ? 'Add Project Request' : 'Edit Project Request',
            message: function (dialogRef) {
                var $message = $('<div><center><i class="icon-spinner icon-spin icon-large"></i>Loading...</center></div>');
                var pageToLoad = dialogRef.getData('pageToLoad');
                var url = pageToLoad;
                $message.load(pageToLoad);

                return $message;
            },
            buttons: [{
                label: 'Save',
                cssClass: 'btn-primary',
                action: function (dialogRef) {
                    var formData = $('#project_form').serialize();
                    var tableData = task_table.data();

                    $.each(tableData, function (index, value) {
                        if (value['is_selected'] == 1) {
                            var taskString = "effort_id|" + value['effort']['id'];
                            var task = value['task'];

                            taskString += '~~id|' + (task['id'] ? task['id'] : '');
                            taskString += '~~projected_start_date|' + (task['projected_start_date'] ? task['projected_start_date'] : '');
                            taskString += '~~estimated_completion_date|' + (task['estimated_completion_date'] ? task['estimated_completion_date'] : '');
                            taskString += '~~duration|' + (task['duration'] ? task['duration'] : '');
                            taskString += '~~completion_date|' + (task['completion_date'] ? task['completion_date'] : '');
                            taskString += '~~collateral_url|' + (task['collateral_url'] ? task['collateral_url'] : '');

                            formData += "&task_data%5B%5D=" + encodeURIComponent(taskString);
                        }
                    });

                    this.spin();
                    dialogRef.enableButtons(false);
                    dialogRef.setClosable(false);

                    saveProjectData(dialogRef, this, editMode, formData);

                }
            },
            {
                label: 'Close',
                action: function (dialogRef) {
                    dialogRef.close();
                }
            }],
            onshow: function (dialogRef) {

            },
            onshown: function (dialogRef) {

            },
            data: {
                'pageToLoad': edit_project_page + (editMode == 'add' ? '' : ('?id=' + id)) + (newStatus ? ('&newstatus=' + newStatus) : ''),
            }
        });
    }

    function saveProjectData(dialogRef, button, saveMethod, jsonProjectData) {
        var url = (saveMethod == 'add') ? ajax_addproject_url : ajax_updateproject_url;

        $.ajax({
            url: url,
            type: "POST",
            data: jsonProjectData,
            dataType: "JSON",
            success: function (data) {

                if (data.status) {
                    dialogRef.close();
                    reloadTable();
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                    }
                }

                dialogRef.enableButtons(true);
                dialogRef.setClosable(true);
                button.stopSpin();
            },
            error: function (jqXHR, textStatus, errorThrown) {

                alert('Error adding / update data (' + (errorThrown ? errorThrown : 'unknown') + ')');

                dialogRef.enableButtons(true);
                dialogRef.setClosable(true);
                button.stopSpin();
            }
        });
    }

    function deferProject(id) {
        if (confirm('Are you sure you wish to defer this Project Request for later?')) {
            $.ajax({
                url: ajax_deferproject_url + '/' + id,
                type: "POST",
                dataType: "JSON",
                success: function (data) {
                    //if success reload ajax table
                    $('#modal_form').modal('hide');
                    reloadTable();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Error deferring Project Request');
                }
            });

        }
    }

    function deleteProject(id) {
        if (confirm('Are you sure you wish to delete this Project Request?')) {
            $.ajax({
                url: ajax_deleteproject_url + '/' + id,
                type: "POST",
                dataType: "JSON",
                success: function (data) {
                    //if success reload ajax table
                    reloadTable();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Error deleting Project Request');
                }
            });

        }
    }
</script>