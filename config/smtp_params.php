<?php
/**
 * Returns an array of SMTP parameters
 **/
return array(
    // SMTP Host
    'host'          =>  'smtp.gmail.com',
    
    // SMTP Port
    'port'          =>  465,
    
    // SMTP Encryption Protocol
    'encryption'    =>  'ssl',
    
    // SMTP Username
    'username'      =>  '',     // E.g. demo@gmail.com
    
    // SMTP Password
    'password'      =>  '',

    // Usage SMTP or mail() php function | If true - will be used SMTP Mail Server, otherwise - mail() function
    'usesmtp'       =>  false
);