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
    <link rel="stylesheet" href="http://www.bootstrap-switch.org/dist/css/bootstrap3/bootstrap-switch.css" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.1.2/css/rowReorder.dataTables.min.css" type="text/css" />


    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
<!--    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
<!--    <script src="https://cdn.datatables.net/buttons/1.2.1/js/buttons.bootstrap.min.js" type="text/javascript"></script>-->
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js" type="text/javascript"></script>-->
    <script src="http://www.bootstrap-switch.org/dist/js/bootstrap-switch.js" type="text/javascript"></script>
<!--    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>-->
<!--    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js" type="text/javascript"></script>-->
<!--    <script src="https://cdn.datatables.net/rowreorder/1.1.2/js/dataTables.rowReorder.min.js" type="text/javascript"></script>-->
<!--    <script src="https://cdn.datatables.net/buttons/1.2.1/js/dataTables.buttons.min.js" type="text/javascript"></script>-->
<!--    <script src="https://cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js" type="text/javascript"></script>-->
<!--    <script src="<?php echo $this->config->base_url('assets/js/bootstrap-hover-dropdown.js'); ?>" type="text/javascript"></script>-->

    <!--    <script src="http://www.bootstrap-switch.org/docs/js/jquery.min.js"></script>-->
    <script src="http://www.bootstrap-switch.org/docs/js/bootstrap.min.js"></script>
    <script src="http://www.bootstrap-switch.org/docs/js/highlight.js"></script>
    <!--    <script src="http://www.bootstrap-switch.org/dist/js/bootstrap-switch.js"></script>-->


</head>
<body>
    <?php
    if(!empty($body_content)){
        $this->load->view($body_content);
    }
    ?>

</body>
</html>




