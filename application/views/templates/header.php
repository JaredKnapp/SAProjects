<html>
<head>
    <?php echo '<link href="'. $this->config->base_url(). 'Content/themes/base/all.css" rel="stylesheet" type="text/css" />'; ?>

    <?php echo '<link href="'. $this->config->base_url(). 'assets/css/theme.css" rel="stylesheet" type="text/css" />'; ?>

    <?php echo '<script src="'. $this->config->base_url(). 'Scripts/jquery-3.0.0.min.js" type="text/javascript"></script>'; ?>
    <?php echo '<script src="'. $this->config->base_url(). 'Scripts/jquery-ui-1.11.4.min.js" type="text/javascript"></script>'; ?>

    <title>ETD SA Projects</title>

</head>
<body>
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
                            <a href="index.php">&nbsp;</a>
                        </li>
                    </ul>
                </nav>

            </div>
            <div id="content">
                <h1><?php echo $title; ?></h1>

