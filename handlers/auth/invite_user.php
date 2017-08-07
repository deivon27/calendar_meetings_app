<?php
$app = include("../../config/app_params.php");
require_once $app['root'] . "includes/db_connect.php";
require_once $app['root'] . "includes/functions.php";

if ($_SESSION['token'] == $_POST['token']) {
    date_default_timezone_set($app['timezone']);

    /* Definition params for send email */
    $email = $_POST['email'];
    $website = $_SERVER['HTTP_HOST'];

    $link = $app['url'] . "/handlers/auth/activate_invite_user.php?email=" . $email . "&token=";
    $token = generate_hash(md5(uniqid(rand(), true)), 11);
    $inviteLink = $link.$token;
    $subject = "Someone has invited you to access their account - " . $website;
    $inviteHtml = "<p>Hi,</p>
                    <p>Someone has invited you to access their account.</p>
                    <a href=" . $inviteLink . ">Click here</a> to activate!";

    /* If should use SMTP Mail Server */
    if ($smtp['usesmtp'] == true) {

        /* Include PHP Mailer */
        require_once($app['root'] . "vendor/PHPMailer/PHPMailerAutoload.php");

        /* Sending email using SMTP Mail Server */
        $sendEmail = sendEmailSmtp($smtp['host'], $smtp['port'], $smtp['encryption'], $smtp['username'],
                      $smtp['password'], 0, $email, $website, $subject, $inviteHtml);

        /* Insert new invite in DB */
        $newInvite = insertDb($db, 'invites', ['email' => $email,'token' => $token]);

        echo 1;
    } else {
        if(sendEmail($email, $smtp['username'], $subject, $inviteHtml)) {
            $newInvite = insertDb($db, 'invites', ['email' => $email,'token' => $token]);
            echo 1;
        }
    }
    //echo $mail->Send() ? 1 : 0;
    $db = null;
}
