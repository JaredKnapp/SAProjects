<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        <?PHP echo SAP_APPLICATIONTITLE; ?>
    </title>

    <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.0/themes/redmond/jquery-ui.css" type="text/css" >
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" type="text/css" >
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css" >
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.1/css/buttons.bootstrap.min.css" type="text/css" >
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.0/css/select.bootstrap.min.css" type="text/css" >
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.1.2/css/rowReorder.dataTables.min.css" type="text/css" >
    <link rel="stylesheet" href="<?php echo $this->config->base_url('assets/css/editor.bootstrap.min.css'); ?>" type="text/css" >
    <link rel="stylesheet" href="<?php echo $this->config->base_url('assets/css/theme.css'); ?>" type="text/css" >
    <link rel="stylesheet" href="<?php echo $this->config->base_url('Content/themes/base/all.css'); ?>" type="text/css" >

    <script src="http://code.jquery.com/jquery-3.1.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.1.2/js/dataTables.rowReorder.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.1/js/buttons.bootstrap.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js" type="text/javascript"></script>
    <script src="<?php echo $this->config->base_url('assets/js/dataTables.editor.min.js'); ?>" type="text/javascript"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>

    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r; i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date(); a = s.createElement(o),
            m = s.getElementsByTagName(o)[0]; a.async = 1; a.src = g; m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-81661557-1', 'auto');
        ga('require', 'linkid');
        ga('send', 'pageview');
    </script>

    <div id="container">
        <div id="wrapper">
            <div id="header">
                <h1>
                    <?PHP echo SAP_APPLICATIONTITLE; ?>
                </h1>
                <div id="accountinfo" class="nav topcorner">
                    <ul>
                        <li>
                            <?php
                            if($this->authorization->is_logged_in()){
                            ?>

                            <link rel="stylesheet" href="<?php echo $this->config->base_url('assets/css/accountinfo_theme.css'); ?>" type="text/css" />

                            <a href="#" id="accountinfoButton">
                                <span>
                                    <?php echo $this->session->userdata('email'); ?>
                                    <i class="glyphicon glyphicon-collapse-down"></i>
                                </span>
                            </a>
                            <div style="clear:both"></div>
                            <div id="accountinfoBox">
                                <div id="accountinfoForm">
                                    <div id="body">
                                        <div class="span2">
                                            <p>
                                                <button id="logoutButton" class="btn btn-primary btn-block">
                                                    Logout&nbsp;
                                                    <span class="glyphicon glyphicon-log-out"></span>
                                                </button>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                $(function () {
                                    var button = $('#accountinfoButton');
                                    var box = $('#accountinfoBox');
                                    var form = $('#accountinfoForm');

                                    var logoutButton = $('#logoutButton');

                                    button.removeAttr('href');

                                    //Show/Hide the accountinfo form
                                    button.mouseup(function (accountinfo) {
                                        box.toggle();
                                        button.toggleClass('active');

                                        button.find('i.glyphicon').toggleClass('glyphicon-collapse-up', 'glyphicon-collapse-down');
                                    });

                                    form.mouseup(function () {
                                        return false;
                                    });

                                    //Hide accountinfo form if clicked anywhere other than on the form
                                    $(this).mouseup(function (accountinfo) {
                                        if (!($(accountinfo.target).parent('#accountinfoButton').length > 0)) {
                                            button.removeClass('active');
                                            button.find('i.glyphicon').toggleClass('glyphicon-collapse-up', 'glyphicon-collapse-down');
                                            box.hide();
                                        }
                                    });

                                    logoutButton.click(function () {
                                        window.location = "<?php echo $this->config->site_url('logout'); ?>";
                                    });
                                });
                            </script>

                            <?php
                            } else {
                            ?>
                            <link rel="stylesheet" href="<?php echo $this->config->base_url('assets/css/login_theme.css'); ?>" type="text/css" />

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
                                    <a href="javascript:document.getElementById('loginForm').submit();" class="btn btn-primary">
                                        <i class="glyphicon glyphicon-log-in"></i>&nbsp;&nbsp;Sign&nbsp;In
                                    </a>
                                </div>
                                <?php echo form_close(); ?>
                            </div>

                            <script>
                                $(function () {
                                    var button = $('#loginButton');
                                    var box = $('#loginBox');
                                    var form = $('#loginForm');

                                    button.removeAttr('href');

                                    //Show/Hide the login form
                                    button.mouseup(function (login) {
                                        box.toggle();
                                        button.toggleClass('active');

                                        button.find('i.glyphicon').toggleClass('glyphicon-collapse-up', 'glyphicon-collapse-down');
                                    });

                                    form.keypress(function (e) {
                                        if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
                                            form.submit();
                                            return false;
                                        } else {
                                            return true;
                                        }
                                    });


                                    form.mouseup(function () {
                                        return false;
                                    });

                                    //Hide login form if clicked anywhere other than on the form
                                    $(this).mouseup(function (login) {
                                        if (!($(login.target).parent('#loginButton').length > 0)) {
                                            button.removeClass('active');
                                            button.find('i.glyphicon').toggleClass('glyphicon-collapse-up', 'glyphicon-collapse-down');
                                            box.hide();
                                        }
                                    });
                                });
                            </script>



                            <?php
                            }
                            ?>

                        </li>
                    </ul>
                </div>
                <nav id="primary_nav_wrap">
                    <ul>
                        <li>
                            <a href="<?php echo $this->config->site_url('project/create'); ?>" class="<?php echo ($topmenu && $topmenu == 'project')?'active':'notactive'; ?>">Project Requests</a>
                            <ul>
                                <li>
                                    <a href="<?php echo $this->config->site_url('project/create'); ?>">Create</a>
                                </li>
                                <li>
                                    <a href="<?php echo $this->config->site_url('project'); ?>">List</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="<?php echo $this->config->site_url('reports/nna'); ?>" class="<?php echo ($topmenu && $topmenu==='report')?'active':'notactive'; ?>">Reports</a>
                            <ul>
                                <li>
                                    <a href="<?php echo $this->config->site_url('reports/nna'); ?>">Now Next After</a>
                                </li>
                            </ul>
                        </li>
                        <?php if($this->authorization->is_member(array('managergroup', 'architectgroup'))){ ?>
                        <li>
                            <a href="<?php echo $this->config->site_url('architect/saview'); ?>" class="<?php echo ($topmenu && $topmenu=='architects')?'active':'notactive'; ?>">Architects</a>
                            <ul>
                                <li>
                                    <a href="<?php echo $this->config->site_url('architect/SAView'); ?>">Project Administration</a>
                                </li>
                            </ul>
                        </li>
                        <?php }?>
                    </ul>
                </nav>

            </div>
            <div id="content">
                <h1>
                    <?php echo $title; ?>
                </h1>

                <?php

                if(!empty($body_content)){
                    $this->load->view($body_content);
                }

                if(!empty($body_error)){
                    echo $body_error;
                }
                ?>

            </div>
        </div>
        <div id="footer">
            <div id="copyright">
                <p>
                    &copy; <?php echo date("Y"); ?> - EMC Corporation. All rights reserved. (Project Engine: v1.3.15a)
                </p>
            </div>
            <div id="contactlink">
                <a href="mailto:ETD.Solutions.Architecture@emc.com?subject=SA Project Tracker">Contact Us</a>
            </div>
        </div>
    </div>
</body>
</html>




