
<br />
<div id="validationerrors">
    <?php echo validation_errors(); ?>
</div>
<?php echo form_open('project/create'); ?>
<br />
<?php echo form_label('Email Address *', 'author_email'); ?>
<br />
<?php echo form_input(array('name'=>'author_email', 'placeholder'=>'Enter your email address', 'class'=>'required'), $author_email, array('id'=>'author_email', 'style'=>'width:500px;')); ?>
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
<div id="effortoutput">Select an Effort Type...</div>
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
    $(document).ready(function () {
        $("#efforttype").on('change load', function () {
            $.ajax({
                url: "<?php echo base_url();?>index.php/ListFactory/GetEffortOutputsCheckbox",
                data: { id: $(this).val() },
                type: "POST",
                success: function (data) {
                    $("#effortoutput").html(data);
                }
            }).fail(function () {
                alert("ERROR: problem populating Effort Output checkboxes.");
            });
        });
        $("#industry").on('change load', function () {
            $.ajax({
                url: "<?php echo base_url();?>index.php/ListFactory/GetWorkloadDropdown",
                data: { id: $(this).val() },
                type: "POST",
                success: function (data) {
                    $("#workload").html(data);
                }
            }).fail(function () {
                alert("ERROR: problem populating Workload dropdown.");
            });
        });
    });
</script>
