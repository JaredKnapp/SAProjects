<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <meta name="description" content="SA Project Portal" />
    <meta name="author" content="Jared" />
    <link rel="icon" href="<?php echo $this->config->base_url('assets/images/favicon.ico'); ?>" />

    <title>
        <?PHP echo SAP_APPLICATIONTITLE; ?>
    </title>

    <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css" type="text/css" />
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.1/css/buttons.bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.0/css/select.bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/css/bootstrap-dialog.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.1.2/css/rowReorder.dataTables.min.css" type="text/css" />

    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.1.2/js/dataTables.rowReorder.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js" type="text/javascript"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.1/js/buttons.bootstrap.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-hover-dropdown/2.2.1/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js" integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E=" crossorigin="anonymous"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="
        https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
        /*!
         * IE10 viewport hack for Surface/desktop Windows 8 bug
         * Copyright 2014-2015 Twitter, Inc.
         * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
         */

        /*
         * See the Getting Started docs for more information:
         * http://getbootstrap.com/getting-started/#support-ie10-width
         */
        @-ms-viewport {
            width: device-width;
        }

        @-o-viewport {
            width: device-width;
        }

        @viewport {
            width: device-width;
        }

        /* Sticky footer styles
        -------------------------------------------------- */
        html {
            position: relative;
            min-height: 100%;
        }

        body {
            margin-bottom: 30px;
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 30px;
            background-color: rgba(91, 91, 91, 1);
            color: #ffffff;
        }

        .container {
            width: auto;
        }

        /*==============================================================
        NAVBAR Styling
        ================================================================*/
        .navbar {
            background-color: rgba(91, 91, 91, 1);
        }

            .navbar .navbar-nav {
                /*text-transform: uppercase;*/
            }

                .navbar .navbar-nav > .open > a, .navbar .navbar-nav > .open > a:focus, .navbar .navbar-nav > .open > a:hover {
                    background: #808080;
                }

                .navbar .navbar-nav > .active > a, .navbar .navbar-nav > .active > a:focus, .navbar .navbar-nav > .active > a:hover {
                    background: #2c95dd;
                }

        .dropdown-menu {
            font-size: 12px;
        }

        /*===================================================
            Datatable Styling
        =====================================================*/
        /*Datatable Customizations*/
        table.dataTable th {
            color: #0077AA;
            text-transform: uppercase;
        }


        table.dataTable.display tbody tr.odd > .dragable, table.dataTable.order-column.stripe tbody tr.odd > .dragable {
            background-color: #f1f1f1;
        }

        table.dataTable.display tbody tr.even > .dragable, table.dataTable.order-column.stripe tbody tr.even > .dragable {
            background-color: #fafafa;
        }

        table.dataTable td.reorder {
            text-align: left;
        }

        table.dataTable td.center-horizontal {
            text-align: center;
        }

        table.dataTable td.center-vertical {
            vertical-align: central;
        }

        i.details-control-icon {
            cursor: pointer;
        }

        tr.group,
        tr.group:hover {
            color: white;
            background-color: #2c95dd !important;
        }
    </style>
</head>
<body style="font-size: 12px;">
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

    <nav role="navigation" class="navbar navbar-inverse">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="navbar-brand" href="#">
                <?PHP echo SAP_APPLICATIONTITLE; ?>
            </div>
        </div>
        <!-- Collection of nav links and other content for toggling -->
        <div id="navbarCollapse" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li role="presentation" class="dropdown <?php echo (!empty($topmenu) && $topmenu == 'project')?'active':''; ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="50000" role="button" aria-haspopup="true" aria-expanded="false">
                        Project Requests
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo $this->config->site_url('project/create'); ?>">Create</a>
                        </li>
                        <li>
                            <a href="<?php echo $this->config->site_url('project'); ?>">List</a>
                        </li>
                    </ul>
                </li>
                <li role="presentation" class="dropdown <?php echo (!empty($topmenu) && $topmenu==='report')?'active':''; ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="50000" role="button" aria-haspopup="true" aria-expanded="false">
                        Reports
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo $this->config->site_url('reports/nna'); ?>">Now Next After</a>
                        </li>
                    </ul>
                </li>
                <?php if($this->authorization->is_member(array(SAP_MANAGERGROUP, SAP_ARCHITECTGROUP))){ ?>
                <li role="presentation" class="dropdown <?php echo (!empty($topmenu) && $topmenu=='architects')?'active':''; ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="50000" role="button" aria-haspopup="true" aria-expanded="false">
                        Architects
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo $this->config->site_url('architect/SAView'); ?>">Project Administration</a>
                        </li>
                    </ul>
                </li>
                <?php }?>
                <?php if(true && $this->authorization->is_member(array(SAP_ADMINISTRATORGROUP))){ ?>
                <li role="presentation" class="dropdown <?php echo (!empty($topmenu) && $topmenu=='settings')?'active':''; ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="50000" role="button" aria-haspopup="true" aria-expanded="false">
                        Settings
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo $this->config->site_url('admin/Groups'); ?>">Groups</a>
                        </li>
                    </ul>
                </li>
                <?php }?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown<?php echo ($this->authorization->is_logged_in() ? ' hide' : ''); ?>" id="menuLogin">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="50000" role="button" aria-haspopup="true" aria-expanded="false">
                        Login&nbsp;
                        <i class="glyphicon glyphicon-log-in"></i>
                    </a>
                    <div class="dropdown-menu" style="padding:17px; min-width:250px;">
                        <?php
                        $hidden = array(
                                'current_page'=> base_url(uri_string())
                                );
                        echo form_open('login', array('id'=>'loginForm', 'role'=>'form'), $hidden);
                        ?>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-envelope"></span>
                                </span>
                                <input type="email" name="email" class="form-control" id="email" placeholder="Email Address..." />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-lock"></span>
                                </span>
                                <input type="password" name="password" class="form-control" id="pwd" placeholder="Password..." />
                            </div>
                        </div>
                        <button type="submit" class="btn btn-default">
                            Login
                        </button>
                        <?php echo form_close(); ?>
                        <br />
                        <p>
                            <a href="mailto:ETD.Solutions.Architecture@emc.com?subject=SA Project Tracker">Need Help? Contact Us.</a>
                        </p>
                        <p>
                            <a href="<?php echo $this->config->site_url('about'); ?>">About this site.</a>
                        </p>

                    </div>
                </li>
                <li class="dropdown<?php echo (!$this->authorization->is_logged_in() ? ' hide' : ''); ?>" id="menuUser">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="50000" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class='icon-user icon-xxlarge'></i>
                        <span>
                            <?php echo $this->session->userdata('email'); ?>
                        </span>
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="/logout">Logout</a>
                        </li>
                        <li class="divider"></li>
                        <!--<li>
                            <a href="http://solarch.lab.emc.com">SA Site List</a>
                        </li>-->
                        <li>
                            <a href="mailto:ETD.Solutions.Architecture@emc.com?subject=SA Project Tracker">Contact Us</a>
                        </li>
                        <li>
                            <a href="<?php echo $this->config->site_url('about'); ?>">About</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
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

        <!-- Bootstrap modal Login form -->
        <div class="modal fade" id="modal_loginform" role="dialog">
            <div class="modal-dialog" style="max-width: 350px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">Login</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <p>
                <small>
                    &copy; <?php echo date("Y"); ?> - EMC Corporation. All rights reserved. (Project Engine: v1.5.03)
                </small>
            </p>
        </div>
    </footer>
</body>
</html>




