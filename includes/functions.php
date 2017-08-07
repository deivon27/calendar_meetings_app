<?php
/**
 * Setting up SMTP config array
 **/
$smtp = include($app['root'] . "config/smtp_params.php");

/**
 * Generating hash with salt
 * @param $password
 * @param int $cost
 * @return string
 */
function generate_hash($password, $cost = 11) {
    /* To generate the salt, first generate enough random bytes. Because
     * base64 returns one character for each 6 bits, the we should generate
     * at least 22*6/8=16.5 bytes, so we generate 17. Then we get the first
     * 22 base64 characters
     */
    $salt = substr(base64_encode(openssl_random_pseudo_bytes(17)), 0, 22);
    /* As blowfish takes a salt with the alphabet ./A-Za-z0-9 we have to
     * replace any '+' in the base64 string with '.'. We don't have to do
     * anything about the '=', as this only occurs when the b64 string is
     * padded, which is always after the first 22 characters.
     */
    $salt = str_replace("+", ".", $salt);
    /* Next, create a string that will be passed to crypt, containing all
     * of the settings, separated by dollar signs
     */
    $param = '$' . implode('$', array(
            "2y", //select the most secure version of blowfish (>=PHP 5.3.7)
            str_pad($cost, 2, "0", STR_PAD_LEFT), //add the cost in two digits
            $salt //add the salt
        ));

    //now do the actual hashing
    return crypt($password, $param);
}

/**
 * Validating hash / password hash
 * @param $password
 * @param $hash
 * @return bool
 */
function validate_pw($password, $hash) {
    /* Regenerating the with an available hash as the options parameter should
     * produce the same hash if the same password is passed.
     */
    return crypt($password, $hash) == $hash;
}

/**
 * Destroying the sessions
 */
function sessionDestroy() {
    // Open the session
    session_start();

    // Remove all the variables in the session
    session_unset();

    // Destroy the session
    session_destroy();
}

/**
 * Setting up parameters for UPDATE Query
 * @param $fields
 * @return string
 */
function updateQuerySet($fields) {
    $set = '';
    foreach ($fields as $field) {
        $set .= "`" . str_replace("`", "``", $field) . "`" . " =:$field, ";
    }
    return substr($set, 0, -2);
}

/**
 * Setting up parameters for WHERE Clause
 * @param $fields
 * @return string
 */
function whereQuerySet($fields) {
    $set = '';
    foreach ($fields as $field) {
        $set .= "`" . str_replace("`", "``", $field) . "`" . " =:$field AND ";
    }
    return substr($set, 0, -5);
}

/**
 * Setting up PDO parameters that defines the type of data to bind
 * @param $value
 * @return int
 */
function setPdoParam($value) {
    if (is_string($value)) {
        return PDO::PARAM_STR;
    } else {
        return PDO::PARAM_INT;
    }
}

/**
 * Setting up PDO SELECT Query snippet
 * @param $db
 * @param $table
 * @param $fieldsToSelect
 * @param array|null $whereValues
 * @return mixed
 */
function selectDb($db, $table, $fieldsToSelect, array $whereValues = null) {
    if (is_array($fieldsToSelect)) {
        $listFieldsToSel = "`" . implode('`, `', $fieldsToSelect) . "`";
    } else {
        $listFieldsToSel = $fieldsToSelect;
    }

    if (!is_null($whereValues) && is_array($whereValues)) {
        $onlyKeys = array_keys($whereValues);
        $whereSet =  " WHERE " . whereQuerySet($onlyKeys);
    } else {
        $whereSet = "";
    }

    $sql = "SELECT " . $listFieldsToSel . " FROM `$table`" . $whereSet;
    $stm = $db->prepare($sql);
    if (!is_null($whereValues) && is_array($whereValues)) {
        foreach ($whereValues as $name => $value) {
            $stm->bindValue(':' . $name, $value, setPdoParam($value));
        }
    }
    $stm->execute();
    return $stm;
}

/**
 * Setting up PDO INSERT Query snippet
 * @param $db
 * @param $table
 * @param array $namesValues
 * @return mixed
 */
function insertDb($db, $table, array $namesValues) {
    $onlyKeys = array_keys($namesValues);
    $paramKeys = implode(',', $onlyKeys);
    $paramValues = ':' . implode(', :', $onlyKeys);

    $sql = "INSERT INTO `$table` (" . $paramKeys . ") VALUES (" . $paramValues . ")";
    $stm = $db->prepare($sql);
    foreach ($namesValues as $name => $value) {
        $stm->bindValue(':' . $name, $value, setPdoParam($value));
    }
    return $stm->execute();
}

/**
 * Setting up the PDO UPDATE Query snippet
 * @param $db
 * @param $table
 * @param array $namesValues
 * @param array $whereValues
 * @return mixed
 */
function updateDb($db, $table, array $namesValues, array $whereValues) {
    $onlySetKeys = array_keys($namesValues);
    $onlyWhereKeys = array_keys($whereValues);

    $sql = "UPDATE `$table` SET " . updateQuerySet($onlySetKeys) . " WHERE " . whereQuerySet($onlyWhereKeys);
    $stm = $db->prepare($sql);
    foreach ($namesValues as $name => $value) {
        $stm->bindValue(':' . $name, $value, setPdoParam($value));
    }
    foreach ($whereValues as $name => $value) {
        $stm->bindValue(':' . $name, $value, setPdoParam($value));
    }
    return $stm->execute();
}

/**
 * Setting up the PDO DELETE Query snippet
 * @param $db
 * @param $table
 * @param array $whereValues
 * @return mixed
 */
function deleteDb($db, $table, array $whereValues) {
    $onlyKeys = array_keys($whereValues);
    $sql = "DELETE FROM `$table` WHERE " . whereQuerySet($onlyKeys);
    $stm = $db->prepare($sql);
    foreach ($whereValues as $name => $value) {
        $stm->bindValue(':' . $name, $value, setPdoParam($value));
    }
    return $stm->execute();
}

/**
 * Sending an email via mail() php function
 * @param $toEmail
 * @param $fromEmail
 * @param $subject
 * @param null $message
 * @return bool
 */
function sendEmail($toEmail, $fromEmail, $subject, $message = null) {
    $headers = 'From: ' . $fromEmail . '' . "\r\n" .
        'Reply-To: ' . $fromEmail . '' . "\r\n" .
        'MIME-Version: 1.0\r\n' .
        'Content-Type: text/html; charset=ISO-8859-1\r\n' .
        'X-Mailer: PHP/' . phpversion();
    if(mail($toEmail, $subject, $message, $headers)) {
        return true;
    }
}

/**
 * Sending an email via SMTP Mail Server
 * @param $host
 * @param $port
 * @param $enc
 * @param $username
 * @param $pass
 * @param int $debug
 * @param $toEmail
 * @param $website
 * @param $subject
 * @param $html
 * @return int|string
 */
function sendEmailSmtp($host, $port, $enc, $username, $pass, $debug = 0, $toEmail, $website, $subject, $html) {
    try {
        /* Definition params for PHPMailer */
        $mail = new PHPMailer(true);
        $mail->IsHTML(true);
        $mail->IsSMTP();
        $mail->SMTPDebug  = $debug;
        $mail->SMTPAuth = true;
        if ($enc != '') {
            $mail->SMTPSecure = $enc;
        }
        $mail->Host = $host;
        $mail->Port = $port;
        $mail->Username = $username;
        $mail->Password = $pass;

        $mail->AddAddress($toEmail, $toEmail);
        $mail->setFrom($username, $website);
        $mail->Subject = $subject;
        $mail->msgHTML($html);

        /* Sending email */
        if ($mail->Send()) {
            return 1;
        }
    } catch (phpmailerException $e) {
        return $e->errorMessage(); //Pretty error messages from PHPMailer
    } catch (Exception $e) {
        return $e->getMessage(); //Boring error messages from anything else!
    }
}