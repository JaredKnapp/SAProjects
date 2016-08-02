
<br />
<div id="validationerrors">
    <?php echo validation_errors(); ?>
</div>
<?php echo form_open('project/create'); ?>
<br />
<?php echo form_label('Email Address *', 'author_email'); ?>
<br />
<?php echo form_input(array('name'=>'author_email', 'placeholder'=>'Enter your email address...', 'class'=>'required'), $author_email, array('id'=>'author_email', 'style'=>'width:500px;')); ?>
<br />
<br />
<?php echo form_label('Industry *', 'industries_id'); ?>
<br />
<?php echo form_dropdown('industries_id', $choicesIndustry, $industries_id, 'class="required" id="industry"'); ?>
<br />
<br />
<?php echo form_label('Workload *', 'workloads_id'); ?>
<br />
<?php echo form_dropdown('workloads_id', $choicesWorkload, $workloads_id, 'class="required" id="workload"'); ?>
<br />
<br />
<?php echo form_label('Product *', 'platforms_id'); ?>
<br />
<?php echo form_dropdown('platforms_id', $choicesPlatform, $platforms_id, 'class="required" id="platform"'); ?>
<br />
<br />
<?php echo form_label('Effort Target *', 'effort_target'); ?>
<br />
<?php echo form_textarea(array('name'=>'effort_target', 'id'=>'effort_target', 'placeholder'=>'Enter a short description of this project...', 'style'=>'height: 75px; width: 500px'), $effort_target);?>
<br />
<br />
<?php echo form_label('Effort Type *', 'efforttypes_id'); ?>
<br />
<?php echo form_dropdown('efforttypes_id', $choicesEffortType, $efforttypes_id, 'class="required" id="efforttype"'); ?>
<br />
<br />
<?php echo form_label('Effort Output *', 'effortoutputs_id'); ?>
<br />
<div id="effortoutput">Select an Effort Type...</div>
<br />
<?php echo form_label('Desired Completion Date *', 'desired_completion_date'); ?>
<br />
<?php echo form_input(array('name'=>'desired_completion_date', 'placeholder'=>'mm/dd/yyyy', 'class'=>'required'), $desired_completion_date, array('id'=>'desired_completion_date', 'style'=>'width:150px;')); ?>
<script>$(function () { $('#desired_completion_date').datepicker(); });</script>
<br />
<br />
<?php echo form_label('Effort Justification *', 'effort_justification'); ?>
<br />
<?php echo form_textarea(array('name'=>'effort_justification', 'id'=>'effort_justification', 'placeholder'=>'Explain why this project should be considered...', 'style'=>'height: 150px; width: 500px'), $effort_justification);?>
<br />
<br />
<?php echo form_label('Notes', 'notes'); ?>
<br />
<?php echo form_textarea(array('name'=>'notes', 'id'=>'notes', 'placeholder'=>'Provide any additional information that may be relevant...', 'style'=>'height: 150px; width: 500px'), $notes);?>
<br />
<br />
<?php
echo form_submit(array('type'=>'submit', 'class'=>'submit', 'value'=>'Submit Request'));
echo form_close();
?>
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
