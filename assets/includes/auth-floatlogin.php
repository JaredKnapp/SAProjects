
<link rel="stylesheet" href="<?php echo $this->config->base_url('assets/css/login_theme.css'); ?>" type="text/css" />
<script src="<?php echo $this->config->base_url('assets/js/login.js'); ?>" type="text/javascript"></script>

<a href="#" id="loginButton">
    <span>
        Login
        <i class="glyphicon glyphicon-collapse-down"></i>
    </span>
</a>
<div style="clear:both"></div>
<div id="loginBox">
    <?php
    $hidden = array(
        'current_page'=> base_url(uri_string())
        );
    echo form_open('login', array('id'=>'loginForm', 'role'=>'form'), $hidden);
    $error = form_error("email", "<p class='text-danger'>", '</p>');
    ?>
    <div id="body">
        <div class="form-group <?php echo $error ? 'has-error' : '' ?>">
            <label for="email">Email Address:</label>
            <div class="input_group">
                <input type="email" class="form-control" name="email" id="email" />
            </div>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" name="password" id="password" />
        </div>
        <a href="javascript:document.getElementById('loginForm').submit();" class="btn btn-info">
            <i class="glyphicon glyphicon-log-in"></i>&nbsp;&nbsp;Sign&nbsp;In
        </a>
    </div>
    <?php echo form_close(); ?>
</div>