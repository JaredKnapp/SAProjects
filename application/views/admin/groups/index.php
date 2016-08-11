
<button class="btn btn-success" onclick="add_group()">
    <i class="glyphicon glyphicon-plus"></i>Add Group
</button>
<button class="btn btn-default" onclick="reload_table()">
    <i class="glyphicon glyphicon-refresh"></i>Reload
</button>
<br />
<br />

<div class="panel panel-default" style="width: 550px;">
    <div class="panel-body">
        <table id="table" class="table table-hover table-striped table-bordered" cellspacing="0">
            <thead>
                <tr>
                    <th>id</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="panel-footer">
        NOTE: Do not change or delete any group 'Code' label with a plus ('+') sign at the start. This indicates a special purpose group.
    </div>
</div>
<script type="text/javascript">
    var table;
    var membertable;

    $(document).ready(function () {

        $('#modal_form').on('shown.bs.modal', function () {
            reload_membertable();
        });

        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": false,
            "searching": false,
            "ajax": {
                url: "<?php echo site_url('admin/groups/ajax_index')?>",
                type: "POST"
            },
            "columnDefs": [
                { "name": "id", "targets": 0, "visible": false },
                { "name": "name", "targets": 1, "width": "50%" },
                { "name": "code", "targets": 2, "width": "25%" },
                {
                    "name": "actions", "targets": 3, "orderable": false, "width": "25%",
                    "render": function (data, type, row) {
                        editButton = '<a class="btn btn-sm btn-success" href="javascript:void(0)" title="Edit" onclick="edit_group(\'' + row[0] + '\')"><i class="glyphicon glyphicon-pencil"></i></a>';
                        deleteButton = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_group(\'' + row[0] + '\')"><i class="glyphicon glyphicon-trash"></i></a>';
                        return editButton + '&nbsp;' + deleteButton;
                    }
                }
            ]
        });

        membertable = $('#membertable').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": false,
            "searching": false,
            "paging": false,
            "ajax": {
                url: "<?php echo site_url('admin/groups/ajax_memberindex')?>",
                data: function (data) {
                    data.groupId = $('[name="id"]').val();
                },
                type: "POST"
            },
            "columnDefs": [
                { "name": "id", "targets": 0, "visible": false },
                { "name": "code", "targets": 2, "width": "70%" },
                { "name": "name", "targets": 1, "width": "20%" },
                {
                    "name": "actions", "targets": 3, "orderable": false, "width": "10%",
                    "render": function (data, type, row) {
                        editButton = '<a class="btn btn-sm btn-success" href="javascript:void(0)" title="Edit" onclick="edit_group(\'' + row[0] + '\')"><i class="glyphicon glyphicon-pencil"></i></a>';
                        deleteButton = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_group(\'' + row[0] + '\')"><i class="glyphicon glyphicon-trash"></i></a>';
                        return editButton + '&nbsp;' + deleteButton;
                    }
                }
            ]
        });
    });

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function reload_membertable() {
        membertable.ajax.reload(null, false);
    }

    function add_group() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $('.modal-title').text('New Group');
        $('#modal_form').modal('show');
    }

    function edit_group(id, newStatus) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('admin/groups/ajax_edit/')?>/" + id,
            cache: false,
            type: "GET",
            dataType: "JSON",
            success: function (data) {

                $('[name="id"]').val(data.id);
                $('[name="name"]').val(data.name);
                $('[name="code"]').val(data.code);

                $('.modal-title').text('Edit Group');
                $('#modal_form').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function save_group() {
        var url;

        $('#btnSave').text('saving...');
        $('#btnSave').attr('disabled', true);

        if (save_method == 'add') {
            url = "<?php echo site_url('admin/groups/ajax_add')?>";
        } else {
            url = "<?php echo site_url('admin/groups/ajax_update')?>";
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

                    $('.form-group').removeClass('has-error');
                    $('.help-block').empty();

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

    function delete_group(id) {
        if (confirm('Are you sure you wish to delete this Group?')) {
            $.ajax({
                url: "<?php echo site_url('admin/groups/ajax_delete')?>/" + id,
                type: "POST",
                dataType: "JSON",
                success: function (data) {
                    //if success reload ajax table
                    $('#modal_form').modal('hide');
                    reload_table();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Error deleting Group');
                }
            });

        }
    }

</script>

<!-- Bootstrap modal Group edit form -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Group Form</h3>
            </div>
            <div class="modal-body form">
                <?php echo form_open('#', array('id'=>'form', 'class'=>'form-horizontal')); ?>
                <input type="hidden" value="" name="id" />
                <div class="form-body">
                    <div class="form-group">
                        <?php echo form_label('Name *', 'name', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_input(array('name'=>'name', 'placeholder'=>'Enter a name for the group...', 'class'=>'form-control required'), '', array('id'=>'name')); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Code *', 'code', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_input(array('name'=>'code', 'placeholder'=>'Enter a unique code for this group...', 'class'=>'form-control required'), '', array('id'=>'code')); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
                <button class="btn btn-success" onclick="add_member()">
                    <i class="glyphicon glyphicon-plus"></i>Add
                </button>
                <div>
                    <table id="membertable" class="table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Table</th>
                                <th>User</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_group()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal Group edit form-->

<!-- Bootstrap modal Add Group Member form -->
<div class="modal fade" id="modal_member_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Add Group Membership</h3>
            </div>
            <div class="modal-body form">
                <?php echo form_open('#', array('id'=>'form', 'class'=>'form-horizontal')); ?>
                <input type="hidden" value="" name="id" />
                <div class="form-body">
                    <div class="form-group">
                        <?php echo form_label('Name *', 'name', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_input(array('name'=>'name', 'placeholder'=>'Enter a name for the group...', 'class'=>'form-control required'), '', array('id'=>'name')); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label('Code *', 'code', array('class'=>'control-label col-md-3')); ?>
                        <div class="col-md-9">
                            <?php echo form_input(array('name'=>'code', 'placeholder'=>'Enter a unique code for this group...', 'class'=>'form-control required'), '', array('id'=>'code')); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
                <button class="btn btn-success" onclick="add_member()">
                    <i class="glyphicon glyphicon-plus"></i>Add
                </button>
                <div>
                    <table id="membertable" class="table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Table</th>
                                <th>User</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_group()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal Project edit form-->