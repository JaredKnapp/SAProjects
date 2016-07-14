
<button class="btn btn-success" onclick="add_person()">
    <i class="glyphicon glyphicon-plus"></i>Add Person
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
            <th>Priority</th>
            <th>Workflow</th>
            <th>SA</th>
            <th>Effort Target</th>
            <th>Effort Type</th>
            <th>Effort Output</th>
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
            <th>Priority</th>
            <th>Workflow</th>
            <th>SA</th>
            <th>Effort Target</th>
            <th>Effort Type</th>
            <th>Effort Output</th>
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
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "ajax": {
                "url": "<?php echo site_url('NowNextAfter/ajax_list')?>",
                "type": "POST"
            },
            "columnDefs": [
            {
                "targets": [-1], //last column
                "orderable": false, //set not orderable
            },
            ],

        });
    });
</script>