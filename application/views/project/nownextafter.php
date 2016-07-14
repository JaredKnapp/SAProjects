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
        </tr>
    </tfoot>
</table>
<script type="text/javascript">
    var table;

    $(document).ready(function () {
        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "searching": true,
            "order": [],
            "ajax": {
                url: "<?php echo site_url('NowNextAfter/ajax_list')?>",
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
</script>
