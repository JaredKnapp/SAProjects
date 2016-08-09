<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ETD SA Projects</title>

    <!--<link rel="stylesheet" href="<?php echo $this->config->base_url('assets/css/theme.css'); ?>" type="text/css" />-->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" type="text/css">

    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
    <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->

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
                <h1>ETD SA Projects</h1>
                <div id="accountinfo" class="nav topcorner">
                    <!--<ul>
                        <li>Login</li>
                    </ul>-->
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

