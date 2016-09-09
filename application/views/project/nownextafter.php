<?php
$statusList = unserialize(SAP_ACTIVESTATUSLIST);
$priorityList = unserialize(SAP_PRIORITYLIST);
?>
<div id="filters">
    <div id="accordion">
        <span>
            <span>
                <strong>Additional&nbsp;Filters</strong>
            </span>
        </span>
        <div id="accordion-body">
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
<br />
<table id="table" class="table table-hover table-striped table-bordered" cellspacing="0">
    <thead>
        <tr>
            <th class="search-industry">Industry</th>
            <th class="search-sauser">Architect</th>
            <th class="search-priority">Priority</th>
            <th class="search-input">Workload</th>
            <th class="search-platform">Product</th>
            <th class="search-input">Effort Target</th>
            <th class="search-efforttype">Effort Type</th>
            <th class="search-input">Effort Output</th>
            <th class="search-input">Effort Justification</th>
            <th class="search-input">Notes</th>
            <th>Estimated Complete Date</th>
            <th class="search-status">Status</th>
        </tr>
    </thead>
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
            <th></th>
            <th>Status</th>
        </tr>
    </tfoot>
    <tbody></tbody>
</table>
<script type="text/javascript">
    var accordion;
    var table;
    var timeoutHandle;

    function QueryStringToHash(query) {

        if (query == '') return null;

        var hash = {};

        var vars = query.split("&");

        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split("=");
            var k = decodeURIComponent(pair[0]);
            var v = decodeURIComponent(pair[1]);

            // If it is the first entry with this name
            if (typeof hash[k] === "undefined") {

                if (k.substr(k.length - 2) != '[]')  // not end with []. cannot use negative index as IE doesn't understand it
                    hash[k] = v;
                else
                    hash[k] = [v];

                // If subsequent entry with this name and not array
            } else if (typeof hash[k] === "string") {
                hash[k] = v;  // replace it

                // If subsequent entry with this name and is array
            } else {
                hash[k].push(v);
            }
        }
        return hash;
    };

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

        var test = QueryStringToHash(window.location.href.slice(window.location.href.indexOf('?') + 1));

        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "searching": true,
            "ajax": {
                url: "<?php echo site_url('NowNextAfter/ajax_list')?>",
                data: function (data) {
                    data.searchIndustries = $("input[name='industries[]']:checked:enabled").map(function () { value = $(this).val(); return value; }).get();
                    data.searchPriorities = $("input[name='priorities[]']:checked:enabled").map(function () { value = $(this).val(); return value; }).get();
                    data.searchStatuses = $("input[name='statuses[]']:checked:enabled").map(function () { value = $(this).val(); return value; }).get();
                    data.searchPlatforms = $("input[name='platforms[]']:checked:enabled").map(function () { value = $(this).val(); return value; }).get();
                },
                type: "POST"
            },
            "columnDefs": [
                { "name": "industries.name", "targets": 0, "visible": false },
                { "name": "sa_users_id", "targets": 1 },
                { "name": "priority", "targets": 2 },
                { "name": "workloads.name", "targets": 3 },
                { "name": "platforms.name", "targets": 4 },
                { "name": "effort_target", "targets": 5 },
                { "name": "efforttypes.name", "targets": 6 },
                { "name": "vflatprojecttasks.effortoutput", "targets": 7 },
                { "name": "effort_justification", "targets": 8 },
                {
                    "name": "notes", "targets": 9,
                    "render": function (data, type, row) {
                        return data ? data.replace(/(\r\n|\n|\r)/g, "<br />") : '';
                    }
                },
                {
                    "name": "estimated_complete_date", "targets": 10, "orderable": false,
                    "render": function (data, type, row, meta) {
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
                                return '<div>' + dataArray[0] + '&nbsp;<i class="glyphicon glyphicon-comment" aria-hidden="true"  data-toggle="popover" data-html="true" data-trigger="focus" title="Overridden in Project" data-content="' + message + '." style="cursor: pointer;"></i></div>';
                            }
                        }
                        else {
                            return '';
                        }
                    }
                },

                {
                    "name": "status", "targets": 11,
                    "render": function (data, type, row) {
                        labelStyle = 'label-default';

                        if (data === '<?php echo array_key_exists('draft', $statusList) ? $statusList['draft'] : 'draft'; ?>') labelStyle = 'label-default';
                        if (data === '<?php echo array_key_exists('approved', $statusList) ? $statusList['approved'] : 'approved'; ?>') labelStyle = 'label-info';
                        if (data === '<?php echo array_key_exists('deferred', $statusList) ? $statusList['deferred'] : 'deferred'; ?>') labelStyle = 'label-danger';
                        if (data === '<?php echo array_key_exists('inprocess', $statusList) ? $statusList['inprocess'] : 'inprocess'; ?>') labelStyle = 'label-success';
                        if (data === '<?php echo array_key_exists('scheduled', $statusList) ? $statusList['scheduled'] : 'scheduled'; ?>') labelStyle = 'label-primary';
                        if (data === '<?php echo array_key_exists('complete', $statusList) ? $statusList['complete'] : 'complete'; ?>') labelStyle = '';

                        return '<h5><span class="label ' + labelStyle + '">' + data + '</span></h5>';
                    }
                }
            ],
            "drawCallback": function () {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;

                api.column(0, { page: 'current' }).data().each(function (group, i) {
                    if (last !== group) {
                        $(rows).eq(i).before(
                            '<tr class="group"><td colspan="11" style="background: #2c95dd; color: #fff;"><p class="text-uppercase"><strong>' + group + '</strong><p></td></tr>'
                        );

                        last = group;
                    }
                });
            },
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
foreach($priorityList as $key=>$value){
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

    function search_changed() {
        window.clearTimeout(timeoutHandle);
        timeoutHandle = window.setTimeout(reload_table, 2000);
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }
</script>
