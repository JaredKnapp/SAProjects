<div class="col-md-12">
    <table id="notes_history_table" class="table table-bordered table-condensed table-hover table-striped" cellpadding="0" width="100%">
        <thead>
            <tr>
                <td>ID</td>
                <td>History:</td>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<script type="text/javascript">
    var noteshistorytable;
    var ajax_projectnotes_url = "<?php echo site_url('Project/ajax_getprojectnotes'); ?>";
    var project_id = "<?php echo $id; ?>";

    $(document).ready(function () {
        noteshistorytable = $('#notes_history_table').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            searching: true,
            searchDelay: 2000,
            scrollCollapse: true,
            paging: false,
            ajax: {
                url: ajax_projectnotes_url,
                data: function (data) {
                    data.projects_id = project_id;
                },
                type: "POST"
            },
            columns: [
                { data: "id", "visible": false },
                {
                    data: null, defaultContent: '', render: function (data, type, row, meta) {
                        var modDatePieces = row['modified'].split(/[- :]/);
                        var modDate = new Date(Date.UTC(modDatePieces[0], modDatePieces[1] - 1, modDatePieces[2], modDatePieces[3], modDatePieces[4], modDatePieces[5]));

                        var user = row['user'];
                        return '<strong>' + (user == null ? 'anonymous' : user) + '</strong><br />' + row['notes'] + '<br />' + modDate.toLocaleDateString() + ' ' + modDate.toLocaleTimeString(); // dd,yyyy hh:mm tt');
                    }
                }
            ]
        });
    });
</script>