<?php
$hidden = array('t_rowindex'=>$rowIndex);
    echo form_open('#', array('id'=>'taskform'), $hidden);
?>
<input type="hidden" value="" name="t_rowindex" />
<div class="form-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Save as Project Task?', 't_selected', array('class'=>'control-label')); ?>&nbsp;
                <?php echo form_checkbox('t_selected', 'on', ($is_selected=='true'), array('id'=>'t_selected')); ?>
                <span class="help-block"></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?php echo form_label('Projected Start Date Date', 't_projected_start_date', array('class'=>'control-label')); ?>
                <?php echo form_input(array('name'=>'t_projected_start_date', 'placeholder'=>'mm/dd/yyyy', 'class'=>'form-control'), $projected_start_date, array('id'=>'t_projected_start_date')); ?>
                <script>$(function () { $('#t_projected_start_date').datepicker(); });</script>
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <?php echo form_label('Estimated Completion Date', 't_estimated_completion_date', array('class'=>'control-label')); ?>
                <?php echo form_input(array('name'=>'t_estimated_completion_date', 'placeholder'=>'mm/dd/yyyy', 'class'=>'form-control'), $estimated_completion_date, array('id'=>'t_estimated_completion_date')); ?>
                <script>$(function () { $('#t_estimated_completion_date').datepicker(); });</script>
                <span class="help-block"></span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <?php echo form_label('Estimated Work Days', 't_duration', array('class'=>'control-label')); ?>
                <?php echo form_input(array('name'=>'t_duration', 'class'=>'form-control'), $duration, array('id'=>'t_duration')); ?>
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <?php echo form_label('Date Completed', 't_completion_date', array('class'=>'control-label')); ?>
                <?php echo form_input(array('name'=>'t_completion_date', 'placeholder'=>'mm/dd/yyyy', 'class'=>'form-control'), $completion_date, array('id'=>'t_completion_date')); ?>
                <script>$(function () { $('#t_completion_date').datepicker(); });</script>
                <span class="help-block"></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Collateral URL', 't_collateral_url', array('class'=>'control-label')); ?>
                <?php echo form_input(array('name'=>'t_collateral_url', 'placeholder'=>'http://my.stuff.com/docs/docid...', 'class'=>'form-control'), $collateral_url, array('id'=>'t_collateral_url')); ?>
                <span class="help-block"></span>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>