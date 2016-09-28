
<div id="formwrapper" style="text-align: center">
    <div class="panel panel-default" style="display: inline-block; text-align: left; width: 600px; ">
        <div class="panel-heading">
            <h1>Thanks for submitting your request.</h1>
        </div>
        <div class="panel-body">
            As your request is processed, notifications of your request status changes will be sent to
            <strong>
                '<?php echo $author_email; ?>'
            </strong>.<br />
            <br />
            Your request SAPID is <?php echo str_pad($project_id, 5, '0', STR_PAD_LEFT); ?>, and can be tracked using this 
<a href="<?php echo $this->config->site_url('project'); ?>?search=<?php echo str_pad($project_id, 5, '0', STR_PAD_LEFT); ?>" target="_blank">link</a></div>
    </div>
</div>
