<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>Calendar Meetings App - Finish Registration</title>
    <meta name="description" content="Calendar Meetings App"/>
    <meta name="viewport" content="width=1000, initial-scale=1.0, maximum-scale=1.0">

    <!-- Loading Bootstrap -->
    <link href="../../vendor/flat-ui/dist/css/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Loading Flat UI -->
    <link href="../../vendor/flat-ui/dist/css/flat-ui.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../../assets/css/auth.css" rel="stylesheet">

    <link rel="shortcut icon" href="../../vendor/flat-ui/img/favicon.ico">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
    <script src="../../vendor/flat-ui/dist/js/vendor/html5shiv.js"></script>
    <script src="../../vendor/flat-ui/dist/js/vendor/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container">
    <div class="login">
        <div class="login-screen">
            <div class="header">Finish your registration</div>
            <div class="login-icon">
                <img src="../../vendor/flat-ui/img/icons/svg/calendar.svg" alt="Calendar Meetings App" />
                <h4>Welcome to <small>Calendar Meetings App</small></h4>
            </div>
            <form id="registrationForm" action="" method="post" onsubmit="return false">
                <div class="login-form">
                    <p class="response"></p>
                    <div class="form-group">
                        <input type="password" class="form-control login-field" value=""
                               placeholder="Enter your password"
                               id="login-pass"
                               name="password" />
                        <label class="login-field-icon fui-lock" for="login-pass"></label>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control login-field" value=""
                               placeholder="Repeat your password"
                               id="rlogin-pass"
                               name="repeatPassword" />
                        <label class="login-field-icon fui-lock" for="rlogin-pass"></label>
                    </div>
                    <input type="hidden" value="<?=$_GET['token'];?>" name="token">
                    <a class="btn btn-primary btn-lg btn-block" href="javascript:;" onclick="signUp()">Sign Up</a>
                </div>
            </form>
        </div>
    </div>
</div> <!-- /container -->

<!-- JS ======================================================= -->
<script type="text/javascript" src="../../vendor/flat-ui/dist/js/vendor/jquery.min.js"></script>
<script type="text/javascript" src="../../vendor/flat-ui/dist/js/flat-ui.min.js"></script>
<script type="text/javascript" src="../../assets/js/auth.js"></script>
</html>
