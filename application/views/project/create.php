
<br />
<div id="formwrapper" style="text-align: center">
    <?php echo form_open('project/create', array('role'=>'form')); ?>
    <div class="panel panel-default" style="display: inline-block; text-align: left; width:100%; max-width: 600px;">
        <div class="panel-body">
            <div class="form-group">
                <?php echo form_label('Email Address:', 'author_email'); ?>
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-envelope"></span>
                    </span>
                    <?php echo form_input(array('name'=>'author_email', 'placeholder'=>'Enter your email address...', 'class'=>'required form-control', 'required'=>'true'), $author_email, array('id'=>'author_email')); ?>
                </div>
                <?php echo form_error('author_email'); ?>
            </div>
            <div class="form-group">
                <?php echo form_label('Industry:', 'industries_id'); ?>
                <?php echo form_dropdown('industries_id', $choicesIndustry, $industries_id, 'class="required form-control" id="industry" required="true"'); ?>
                <?php echo form_error('industries_id'); ?>
            </div>
            <div class="form-group">
                <?php echo form_label('Workload:', 'workloads_id'); ?>
                <?php echo form_dropdown('workloads_id', $choicesWorkload, $workloads_id, 'class="required form-control" id="workload" required="true"'); ?>
                <?php echo form_error('workloads_id'); ?>
            </div>
            <div class="form-group">
                <?php echo form_label('Product:', 'platforms_id'); ?>
                <?php echo form_dropdown('platforms_id', $choicesPlatform, $platforms_id, 'class="required form-control" id="platform" required="true"'); ?>
                <?php echo form_error('platforms_id'); ?>
            </div>
            <div class="form-group">
                <?php echo form_label('Effort Target:', 'effort_target'); ?>
                <?php echo form_textarea(array('name'=>'effort_target', 'id'=>'effort_target', 'placeholder'=>'Enter a short description of this project...', 'rows'=>10, 'class'=>'required form-control', 'required'=>'true'), $effort_target);?>
                <?php echo form_error('effort_target'); ?>
            </div>
            <div class="form-group">
                <?php echo form_label('Effort Type:', 'efforttypes_id'); ?>
                <?php echo form_dropdown('efforttypes_id', $choicesEffortType, $efforttypes_id, 'class="required form-control" id="efforttype" required="true"'); ?>
                <?php echo form_error('efforttypes_id'); ?>
            </div>
            <div class="form-group">
                <?php echo form_label('Effort Output:', 'effortoutputs_id[]'); ?>
                <div id="effortoutput">Select an Effort Type...</div>
                <?php echo form_error('effortoutputs_id[]'); ?>
            </div>
            <div class="form-group">
                <?php echo form_label('Desired Completion Date:', 'desired_completion_date'); ?>
                <?php echo form_input(array('name'=>'desired_completion_date', 'placeholder'=>'mm/dd/yyyy', 'class'=>'required form-control'), $desired_completion_date, array('id'=>'desired_completion_date', 'style'=>'width:150px;', 'required'=>'true')); ?>
                <?php echo form_error('desired_completion_date'); ?>
                <script>$(function () { $('#desired_completion_date').datepicker(); });</script>
            </div>
            <div class="form-group">
                <?php echo form_label('Effort Justification:', 'effort_justification'); ?>
                <?php echo form_textarea(array('name'=>'effort_justification', 'id'=>'effort_justification', 'placeholder'=>'Explain why this project should be considered...', 'rows'=>10, 'class'=>'form-control', 'required'=>'true'), $effort_justification);?>
                <?php echo form_error('effort_justification'); ?>
            </div>
            <div class="form-group">
                <?php echo form_label('Notes:', 'notes'); ?>
                <?php echo form_textarea(array('name'=>'notes', 'id'=>'notes', 'placeholder'=>'Provide any additional information that may be relevant...', 'rows'=>10, 'class'=>'form-control'), $notes);?>
            </div>

        </div>
        <div class="panel-footer clearfix">
            <div class="pull-right">
                <?php
                echo form_submit(array('type'=>'submit', 'class'=>'btn btn-primary', 'value'=>'Submit Request'));
                ?>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

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
            url: "<?php echo $this->config->site_url('/ListFactory/GetWorkloadDropdown'); ?>",
            data: { id: $('#industry').val() },
            type: "POST",
            success: function (data) {
                $("#workload").html(data);
                if (val) {
                    $("#workload").val(val);
                }
            }
        }).fail(function () {
            alert("ERROR: Problem populating Workload dropdown.");
        });
    }

    $(document).ready(function () {
        $("#efforttype").on('ready change', function () {
            getEffortOutput('');
        });
        $("#industry").on('change load', function () {
            getWorkload('');
        });

        getEffortOutput(['<?php echo is_array($effortoutputs_id)? implode("','", $effortoutputs_id) : ''; ?>']);
        getWorkload('<?php echo $workloads_id; ?>');
    });
</script>
