
<link rel="stylesheet" href="<?php echo $this->config->base_url('assets/css/accountinfo_theme.css'); ?>" type="text/css" />
<script src="<?php echo $this->config->base_url('assets/js/accountinfo.js'); ?>" type="text/javascript"></script>

<a href="#" id="loginButton">
    <span>
        <?php echo $this->session->userdata('email'); ?>
        <i class="glyphicon glyphicon-collapse-down"></i>
    </span>
</a>
<div style="clear:both"></div>
<div id="accountinfoBox">
    <p>hello there</p>
</div>