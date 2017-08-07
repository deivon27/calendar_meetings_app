<?php
$app = include("config/app_params.php");
require_once $app['root'] . "includes/db_connect.php";
require_once $app['root'] . "handlers/auth/is_logged.php";
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>Calendar Meetings App</title>
    <meta name="description" content="Calendar Meetings App" />

    <meta name="viewport" content="width=1000, initial-scale=1.0, maximum-scale=1.0">

    <!-- Loading Bootstrap -->
    <link href="vendor/flat-ui/dist/css/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Loading Flat UI -->
    <link href="vendor/flat-ui/dist/css/flat-ui.css" rel="stylesheet">

    <!-- Monthly Plugin CSS -->
    <link href="vendor/monthly/css/monthly.css" rel="stylesheet">

    <!-- Bootstrap Datetimepicker Plugin CSS -->
    <link href="vendor/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    
    <!-- Bootstrap Colorpicker Plugin CSS -->
    <link href="vendor/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="assets/css/custom.css" rel="stylesheet">
    
    <link rel="shortcut icon" href="vendor/flat-ui/img/favicon.ico">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
    <script src="vendor/flat-ui/dist/js/vendor/html5shiv.js"></script>
    <script src="vendor/flat-ui/dist/js/vendor/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container">
    <div class="row demo-row">
        <div class="col-xs-12">
            <nav class="navbar navbar-inverse navbar-embossed" role="navigation">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-01">
                        <span class="sr-only">Toggle navigation</span>
                    </button>
                    <a class="navbar-brand">Calendar of Meetings</a>
                </div>
                <div class="collapse navbar-collapse" id="navbar-collapse-01">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <?=$_SESSION['email'] . " (<i>" . ucfirst($_SESSION['role']) . "</i>)";?>
                                <b class="caret"></b>
                            </a>
                            <span class="dropdown-arrow"></span>
                            <ul class="dropdown-menu">
                                <li><a href="handlers/auth/logout_user.php">Log Out</a></li>
                            </ul>
                        </li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </nav><!-- /navbar -->
        </div>
    </div> <!-- /row -->
    <div class="row demo-row">
        <div class="col-xs-<?php echo ($_SESSION['role'] == 'admin') ? '6' : '12'; ?>">
            <a class="btn btn-block btn-lg btn-info"
               id="createEvent"
               href="javascript:;" onclick="openModal('#eventForm', 'Create new event', 1)">
                <span class="fui-plus"></span>&nbsp;
                Create New Event
            </a>
        </div>
        <?php if ($_SESSION['role'] == 'admin') : ?>
        <div class="col-xs-6">
            <a class="btn btn-block btn-lg btn-primary"
               id="createEvent"
               href="javascript:;" onclick="openModal('#newUser', 'Invite New User', 1)">
                <span class="fui-user"></span>&nbsp;
                Invite New User
            </a>
        </div>
        <?php endif; ?>
    </div>
    <div class="row demo-row">
        <div class="col-xs-12">
            <div style="width:100%; max-width:1000px; display:inline-block;">
                <div class="monthly" id="mycalendar"></div>
            </div>
        </div>
    </div>
</div> <!-- /container -->

<div class="overlay-modal">
    <div class="event-form">
        <form id="eventForm" action="" onsubmit="return false" method="post">
            <h4>
                <span class="name-modal">Create new Event</span>
                <span class="close-thik"></span>
            </h4>
            <p class="response"></p>
            <div class="form-group">
                <label for="">Event Name</label>
                <input type="text" class="form-control" value="" name="name" required />
            </div>
            <div class="form-group">
                <label for="">Start Datetime</label>
                <input type="text" class="form-control datetimepicker" value="" name="startdate" required />
            </div>
            <div class="form-group">
                <label for="">End Datetime</label>
                <input type="text" class="form-control datetimepicker" value="" name="enddate" required />
            </div>
            <div class="form-group">
                <label for="">Event Description</label>
                <textarea class="form-control" value="" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="">Color</label>
                <input type="text" class="form-control colorpicker" value="" name="color" />
            </div>
            <input type="hidden" value="1" name="formAction">
            <input type="hidden" value="<?=$_SESSION['token'];?>" name="token">
            <input type="hidden" value="<?=$_SESSION['idUser'];?>" name="id_user">
            <a class="btn btn-primary btn-lg submit-btn" href="javascript:;" onclick="createNewEvent()">Submit</a>
        </form>

        <form id="newUser" action="" onsubmit="return false" method="post">
            <h4>
                <span class="name-modal">Invite New User</span>
                <span class="close-thik"></span>
            </h4>
            <p class="response"></p>
            <div class="form-group">
                <label for="">User's email</label>
                <input type="email" class="form-control" value="" name="email" required />
            </div>
            <input type="hidden" value="<?=$_SESSION['token'];?>" name="token">
            <a class="btn btn-primary btn-lg" href="javascript:;" onclick="inviteNewUser()">Send invite</a>
        </form>
    </div> <!-- /create event modal -->
</div> <!-- /overlay modal -->

<!-- JS ======================================================= -->
<script type="text/javascript" src="vendor/flat-ui/dist/js/vendor/jquery.min.js"></script>
<script type="text/javascript" src="vendor/flat-ui/dist/js/flat-ui.min.js"></script>
<script type="text/javascript" src="vendor/monthly/js/monthly.js"></script>
<script type="text/javascript" src="vendor/bootstrap-datetimepicker/moment.js"></script>
<script type="text/javascript" src="vendor/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="vendor/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="assets/js/custom.js"></script>
</body>
</html>
