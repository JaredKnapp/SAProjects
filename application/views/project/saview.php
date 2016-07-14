
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

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function add_project_request() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#modal_form').modal('show');
        $('.modal-title').text('New Project Request');
    }

</script>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="" name="id" />
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">First Name</label>
                            <div class="col-md-9">
                                <input name="firstName" placeholder="First Name" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Last Name</label>
                            <div class="col-md-9">
                                <input name="lastName" placeholder="Last Name" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Gender</label>
                            <div class="col-md-9">
                                <select name="gender" class="form-control">
                                    <option value="">--Select Gender--</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Address</label>
                            <div class="col-md-9">
                                <textarea name="address" placeholder="Address" class="form-control"></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Date of Birth</label>
                            <div class="col-md-9">
                                <input name="dob" placeholder="yyyy-mm-dd" class="form-control datepicker" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->