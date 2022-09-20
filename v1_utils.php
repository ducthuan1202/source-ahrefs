<?php

define('DB_HOST', '45.32.145.32');
define('DB_USER', 'toolbuyseod883');
define('DB_PASS', '9ce1885dd2036037');
define('DB_NAME', 'tool_buyseo_d5b3');


function getallheaders()
{
    $headers = [];
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers;
}

function curlx($url = '', $var = '')
{
    global $config, $dir;
    $cookie_file = $dir . '/cookies/ahredfsx.com.txt';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']);
    curl_setopt($curl, CURLOPT_REFERER, "https://ahrefs.com/");
    if ($var) {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $var);
    }
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

function checking_domain_get($url_explorer, $headers, $config)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url_explorer);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 99999);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']);
    curl_setopt($curl, CURLOPT_REFERER, $url_explorer);
    curl_setopt($curl, CURLOPT_COOKIEFILE, $config['cookie_file']);
    curl_setopt($curl, CURLOPT_COOKIEJAR, $config['cookie_file']);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $site_explorer = curl_exec($curl);
    $header_data = curl_getinfo($curl);
    curl_close($curl);

    if ($header_data["http_code"] === 0) {
        checking_domain_get($url_explorer, $headers, $config);
    }

    return array(
        "contents" => $site_explorer,
        "headers" => $header_data,
    );
}

function xflush()
{
    static $output_handler = null;
    if ($output_handler === null) {
        $output_handler = @ini_get('output_handler');
    }
    if ($output_handler == 'ob_gzhandler') {
        // forcing a flush with this is very bad
        return;
    }

    flush();

    if (ob_get_length() !== false) {
        ob_flush();
    } else if (ob_get_length() !== false) {
        ob_end_flush();
        ob_start();
    }
}

function strposa($haystack, $needles, $offset = 0)
{
    foreach ($needles as $needle) {
        if (strpos($haystack, $needle, $offset) !== false) {
            return true; // stop on first true result
        }
    }

    return false;
}

function token_ahref($new_token, $config)
{
    $get_ahref = tokens_server('ahrefs.com');
    if (!isset($get_ahref['token_value']) || $new_token) {
        $url_extract = 'https://ahrefs.com/user/login';
        $load_extract = curl($url_extract, '', $config);
        $token_ahref = fetch_value($load_extract, '<input name="_token" type="hidden" value="', '">');
        if (empty($token_ahref)) {
            $token_ahref = fetch_value($load_extract, '<meta name="_token" content="', '">');
        }
        $login = false;
        if (strpos($load_extract, 'Sign out') !== false) {
            $login = true;
        }
        return array('token' => $token_ahref, 'login' => $login);
    } else {
        return array('token' => $get_ahref['token_value'], 'login' => true);
    }
}

function fetch_value($str, $find_start, $find_end)
{
    $start = stripos($str, $find_start);
    if ($start === false) return "";
    $length = strlen($find_start);
    $end = stripos(substr($str, $start + $length), $find_end);
    return trim(substr($str, $start + $length, $end));
}

function login_new_ahref($email, $pass, $config)
{
    $url_login = 'https://auth.ahrefs.com/auth/login';
    $cookie_login = token_ahref(true, $config);
    if (!$cookie_login['login']) {
        $post_login = '{"remember_me":true,"auth":{"password":"' . $pass . '","login":"' . $email . '"}}';
        $loginz = curl2($url_login, $post_login, $config);
        $login = json_decode($loginz['result'], true);

        if ($login["success"]) {
            $session_auth_ahrefs = "https://app.ahrefs.com/v4/loginCheckCompletion";
            $session_id = '{"session_id":"' . $login["result"]["session_id"] . '"}';
            return $login;
        } else {
            die("Tài khoản ahrefs.com có sự thay đổi. Vui lòng liên hệ QTV để hướng dẫn.");
        }
    }
}

function curl($url, $var, $config)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']);
    curl_setopt($curl, CURLOPT_REFERER, "https://ahrefs.com/");
    if ($var) {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $var);
    }
    curl_setopt($curl, CURLOPT_COOKIEFILE, $config['cookie_file']);
    curl_setopt($curl, CURLOPT_COOKIEJAR, $config['cookie_file']);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

function curl2($url, $var, $config)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']);
    curl_setopt($curl, CURLOPT_REFERER, "https://ahrefs.com/");
    if ($var) {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $var);
    }
    curl_setopt($curl, CURLOPT_COOKIEFILE, $config['cookie_file']);
    curl_setopt($curl, CURLOPT_COOKIEJAR, $config['cookie_file']);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($curl);
    $header_data = curl_getinfo($curl);
    curl_close($curl);
    $resultx = array('result' => $result, 'header' => $header_data);
    return $resultx;
}

function extract_emails($string)
{
    preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $string, $matches);
    if (isset($matches[0][0]))
        return $matches[0][0];
}

function dbConnect()
{
    static $conn;
    if (!$conn) {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_NAME);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $conn;
}

function get_ahrefs_export($id_user)
{
    $conn = dbConnect();
    $stmt = $conn->prepare('SELECT ahrefs_export FROM check_credits WHERE id_user = :id_user');
    $stmt->execute(array('id_user' => $id_user));

    return $stmt->fetch();
}

function get_limit_ahrefs_export($id_user)
{
    $conn = dbConnect();
    $stmt = $conn->prepare('SELECT limit_ahrefs_export FROM check_credits WHERE id_user = :id_user');
    $stmt->execute(array('id_user' => $id_user));
    return $stmt->fetch();
}

function select_domains_proj($id_user)
{
    $conn = dbConnect();
    $stmt = $conn->prepare('SELECT domains_proj FROM check_credits WHERE id_user = :id_user');
    $stmt->execute(array('id_user' => $id_user));

    return $stmt->fetch();
}
