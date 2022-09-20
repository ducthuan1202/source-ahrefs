<?php

$servername = "45.32.145.32";
$username = "toolbuyseod883";
$password_server = "9ce1885dd2036037";
$dbname = "tool_buyseo_d5b3";

function checkhis_addhist_updatecredit($user_id, $ahrefs_credits, $domain, $type_check)
{
    $history = check_history($user_id, $type_check, $domain);
    $date_check = date('mdYHi', ((int)$history['date_check'] + 900));
    $date_today = date('mdYHi', time());
    $history_id = $history['history_id'];
    if (!$history) {
        add_history($user_id, $type_check, $domain);
        if ($ahrefs_credits > 0) {
            update_credit_ahrefs($user_id, $ahrefs_credits);
            $_SESSION['user_credits']['ahrefs_credits'] = $ahrefs_credits - 1;
        }
    } else if ($date_check < $date_today) {
        add_history($user_id, $type_check, $domain);
        if ($ahrefs_credits > 0) {
            update_credit_ahrefs($user_id, $ahrefs_credits);
            $_SESSION['user_credits']['ahrefs_credits'] = $ahrefs_credits - 1;
        }
    }
}

function add_history($id_user, $type_check = '', $domain)
{
    global $servername, $username, $password_server, $dbname;
    $date_check = time();
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("INSERT INTO `check_history` (`history_id`, `id_user`, `type_check`, `domain`, `date_check`) 
									VALUES (NULL, '" . $id_user . "', '" . $type_check . "', '" . $domain . "', '" . $date_check . "')");
        $stmt->execute();

        // set the resulting array to associative
        $result = $stmt->fetch();
        return $result;
    } catch (PDOException $e) {
        //echo "Error: " . $e->getMessage()."add_history";
    }
    $conn = null;
}

function check_history($id_user, $type_check = '', $domain)
{
    global $servername, $username, $password_server, $dbname;
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT * FROM `check_history` WHERE id_user = '" . $id_user . "' AND type_check = '" . $type_check . "' AND domain = '" . $domain . "' ORDER BY `check_history`.`date_check` DESC");
        $stmt->execute();

        // set the resulting array to associative
        $result = $stmt->fetch();
        return $result;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "check_history";;
    }
    $conn = null;
}

function update_history($history_id)
{
    global $servername, $username, $password_server, $dbname;
    $date_check = time();
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("UPDATE `check_history` SET `date_check` = '" . $date_check . "' WHERE `check_history`.`history_id` = " . $history_id);
        $stmt->execute();

        // set the resulting array to associative
        $result = $stmt->rowCount();
        return $result;
    } catch (PDOException $e) {
        //echo "Error: " . $e->getMessage();
    }
    $conn = null;
}

function update_credit_ahrefs($user_id, $original_credit)
{
    global $servername, $username, $password_server, $dbname;
    $new_credit = $original_credit - 1;
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("UPDATE `check_credits` SET `ahrefs_credits` = '" . $new_credit . "' WHERE `check_credits`.`id_user` = '" . $user_id . "';");
        $stmt->execute();

        // set the resulting array to associative
        $result = $stmt->rowCount();
        return $result;
    } catch (PDOException $e) {
        //echo "Error: " . $e->getMessage();
    }
    $conn = null;
}

function get_plans($plan_id)
{
    global $servername, $username, $password_server, $dbname;
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT * FROM plans WHERE plan_id = " . $plan_id);
        $stmt->execute();

        // set the resulting array to associative
        $result = $stmt->fetch();
        return $result;
    } catch (PDOException $e) {
        //echo "Error: " . $e->getMessage();
    }
    $conn = null;
}

function user_credits($access_key)
{
    global $servername, $username, $password_server, $dbname;
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT * FROM check_credits INNER JOIN members ON check_credits.id_user = members.memberID WHERE check_credits.id_user = (SELECT id_user FROM secret WHERE key_code = '" . $access_key . "')");
        $stmt->execute();

        // set the resulting array to associative
        $result = $stmt->fetch();
        return $result;
    } catch (PDOException $e) {
        //echo "Error: " . $e->getMessage();
    }
    $conn = null;
}

function get_plan_user($id_user)
{
    global $servername, $username, $password_server, $dbname;
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare('SELECT * FROM user_plans INNER JOIN plans ON user_plans.plan_id = plans.plan_id WHERE user_plans.id_user = :id_user AND skip_plan = "false"');
        $stmt->execute(array('id_user' => $id_user));

        return $stmt->fetch();
    } catch (PDOException $e) {
        echo '<p class="btn btn-danger btn-xs">' . $e->getMessage() . '</p>';
    }
}

function update_ahrefs_export($id_user, $usage = 0)
{
    global $servername, $username, $password_server, $dbname, $domain_root;
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("UPDATE `check_credits` SET `ahrefs_export` = ahrefs_export + '" . $usage . "' WHERE `check_credits`.`id_user` = '" . $id_user . "'");
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    $conn = null;
}

function update_domains_proj($id_user, $domain)
{
    global $servername, $username, $password_server, $dbname, $domain_root;
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("UPDATE `check_credits` SET `domains_proj` = CASE WHEN `domains_proj` = '' THEN '" . $domain . "' ELSE CONCAT(`domains_proj`, '|" . $domain . "' ) END WHERE id_user = '" . $id_user . "' ");

        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    $conn = null;
}