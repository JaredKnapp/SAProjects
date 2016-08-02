<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?PHP echo SAP_APPLICATIONTITLE; ?></title>

    <link rel="stylesheet" href="https://code.jquery.com/ui/jquery-ui-git.css" type="text/css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.1/css/buttons.bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.0/css/select.bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.1.2/css/rowReorder.dataTables.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/css/bootstrap-dialog.min.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo $this->config->base_url('assets/css/editor.bootstrap.min.css'); ?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo $this->config->base_url('assets/css/theme.css'); ?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo $this->config->base_url('assets/css/login_theme.css'); ?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo $this->config->base_url('Content/themes/base/all.css'); ?>" type="text/css" />

    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js" integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.1.2/js/dataTables.rowReorder.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.1/js/buttons.bootstrap.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js" type="text/javascript"></script>
    <script src="<?php echo $this->config->base_url('assets/js/login.js'); ?>" type="text/javascript"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>

    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-81661557-1', 'auto');
      ga('require', 'linkid');
      ga('send', 'pageview');
    </script>

    <div id="container">
        <div id="wrapper">
            <div id="header">
                <h1><?PHP echo SAP_APPLICATIONTITLE; ?></h1>
                <div id="accountinfo" class="nav topcorner">
                    <ul>
                        <li>
                            <a href="#" id="loginButton"><span>Login <i class="glyphicon glyphicon-collapse-down"></i></span></a>
                            <div style="clear:both"></div>
                            <div id="loginBox">
                                <!--<form id="loginForm" role="form">-->
                                <?php echo form_open('auth', array('id'=>'loginForm', 'role'=>'form')); ?>
                                    <?php $error = form_error("email", "<p class='text-danger'>", '</p>'); ?>
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
                                    <a href="javascript:document.getElementById('loginForm').submit();" class="btn btn-info"><i class="glyphicon glyphicon-log-in"></i>&nbsp;&nbsp;Sign&nbsp;In</a>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </li>
                    </ul>
                </div>
                <nav id="primary_nav_wrap">
                    <ul>
                        <li>
                            <a href="<?php echo $this->config->site_url('project/create'); ?>" class="<?php echo ($topmenu && $topmenu==='project')?'active':'notactive'; ?>">Project Requests</a>
                            <ul>
                                <li><a href="<?php echo $this->config->site_url('project/create'); ?>">Create</a></li>
                                <li><a href="<?php echo $this->config->site_url('project'); ?>">List</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="<?php echo $this->config->site_url('reports/nna'); ?>" class="<?php echo ($topmenu && $topmenu==='report')?'active':'notactive'; ?>">Reports</a>
                            <ul>
                                <li><a href="<?php echo $this->config->site_url('reports/nna'); ?>">Now Next After</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>

            </div>
            <div id="content">
                <h1><?php echo $title; ?></h1>

