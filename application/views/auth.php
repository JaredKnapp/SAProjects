
<div class="container">
    <div class="row login-wrapper">
        <div class="col-md-4 col-xs-6 col-md-offset-4 col-xs-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>Login Form</strong>
                </div>
                <div class="panel-body">
                    <?php $error = $this->session->flashdata("error"); ?>
                    <div class="alert alert-<?php echo $error ? 'warning' : 'info' ?> alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <?php echo $error ? $error : 'Enter your Email Address and Password' ?>
                    </div>

                    <?php
                    $hidden = array(
                        'current_page'=> $current_page
                    );
                    echo form_open('auth', array(), $hidden);
                    ?>
                    <?php $error = form_error("email", "<p class='text-danger'>", '</p>'); ?>
                    <div class="form-group <?php echo $error ? 'has-error' : '' ?>">
                        <label for="email">Email Address:</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-envelope"></i>
                            </span>
                            <input type="text" name="email" value="<?php echo set_value("email") ?>" id="email" class="form-control" />
                        </div>
                        <?php echo $error; ?>
                    </div>
                    <?php $error = form_error("password", "<p class='text-danger'>", '</p>'); ?>
                    <div class="form-group <?php echo $error ? 'has-error' : '' ?>">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-lock"></i>
                            </span>
                            <input type="password" name="password" id="password" class="form-control" />
                        </div>
                        <?php echo $error; ?>
                    </div>
                    <input type="submit" value="Login" class="btn btn-primary" />
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>