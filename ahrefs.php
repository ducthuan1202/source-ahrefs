<?php
//die("Chúng tôi đang cập nhật tài khoản mới. Vui lòng quay lại sau vài tiếng. Xin lỗi vì sự bất tiện này.");
error_reporting(0);
// error_reporting(E_ALL | E_STRICT);
// ini_set('display_errors', 1);
set_time_limit(0);

$email_account = "murphygerardo01565@gmail.com"; //OCeuwb23m
$password_account = "OCeuwb23m";


date_default_timezone_set("Asia/Ho_Chi_Minh");

$dir = dirname(__FILE__);


//$get_setting = get_setting();
//$domain_root = $get_setting['site_url'];

$today = date("Ymd");

$config['cookie_file'] = $dir . '/cookies/' . md5(1) . '.txt';
$config['useragent'] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.75 Safari/537.36";

if (!file_exists($config['cookie_file'])) {
    $fp = @fopen($config['cookie_file'], 'w');
    @fclose($fp);
}

if (!function_exists('getallheaders')) {
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
}

if (isset($_GET["access_token"]) && !empty($_GET["access_token"])) {
    $website_access = base64_decode(trim($_GET["access_token"]));
} else {
    $website_access = "";
}

if (!isset($_COOKIE['buyseotools']) || !empty($_COOKIE['buyseotools'])) {
    session_name('buyseotools');
    session_start();

    $_COOKIE['buyseotools'] = $website_access;

    if (!isset($_SESSION['access_token'])) {
        $data_session = curlx("http://tool.buyseotools.io/session.php?session=" . $_COOKIE['buyseotools']);
        session_decode($data_session);

        require('config.php');

        $_SESSION["user_credits"] = user_credits($_SESSION['access_token']['key_code']);
        $_SESSION["plan_user"] = get_plan_user($_SESSION["memberID"]);
    }
}

if (!$_SESSION['access_token']['key_code']) {
    unset($_SESSION["access_token"]);
    header('Location: http://tool.buyseotools.io/');
} else {
    $user_id = $_SESSION["memberID"];
    $access_key = $_SESSION['access_token']['key_code'];
    $ahrefs_credits = $_SESSION['user_credits']['ahrefs_credits'];
    $plan_id = $_SESSION['plan_user']['plan_id'];
    $xtimestamp_end = $_SESSION['plan_user']['timestamp_end'];
}

////////////////////////////// FUNCTION ///////////////////////////////////////
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

function checking_domain_get($url_explorer, $headers)
{
    //usleep(rand(1000,3000));
    //refesh_new_ip();
    global $config;
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
        checking_domain_get($url_explorer, $headers);
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
    if (function_exists('ob_flush') AND function_exists('ob_get_length') AND ob_get_length() !== false) {
        @ob_flush();
    } else if (function_exists('ob_end_flush') AND function_exists('ob_start') AND function_exists('ob_get_length') AND ob_get_length() !== FALSE) {
        @ob_end_flush();
        @ob_start();
    }
}

function strposa(string $haystack, array $needles, int $offset = 0): bool
{
    foreach ($needles as $needle) {
        if (strpos($haystack, $needle, $offset) !== false) {
            return true; // stop on first true result
        }
    }

    return false;
}

function token_ahref($new_token = false)
{
    if (!isset($get_ahref['token_value']) || $new_token) {
        $url_extract = 'https://ahrefs.com/user/login';
        $load_extract = curl($url_extract);
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

function login_new_ahref($email, $pass)
{
    global $config;
    $cookie_file = $config['cookie_file'];
    $url_login = 'https://auth.ahrefs.com/auth/login';
    $cookie_login = token_ahref(true);
    if (!$cookie_login['login']) {
        $post_login = '{"remember_me":true,"auth":{"password":"' . $pass . '","login":"' . $email . '"}}';
        $loginz = curl2($url_login, $post_login);
        $login = json_decode($loginz['result'], true);

        if ($login["success"]) {
            $session_auth_ahrefs = "https://app.ahrefs.com/v4/loginCheckCompletion";
            $session_id = '{"session_id":"' . $login["result"]["session_id"] . '"}';
            $session_login = curl2($session_auth_ahrefs, $session_id);
        } else {
            die("Tài khoản ahrefs.com có sự thay đổi. Vui lòng liên hệ QTV để hướng dẫn.");
        }
    }
    return $login;
}

function curl($url = '', $var = '')
{
    global $config;
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

function curl2($url = '', $var = '')
{
    global $config;
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

// Gâu Gâu
$servername = "45.32.145.32";
$username = "toolbuyseod883";
$password_server = "9ce1885dd2036037";
$dbname = "tool_buyseo_d5b3";
function get_ahrefs_export($id_user)
{
    global $servername, $username, $password_server, $dbname;
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare('SELECT ahrefs_export FROM check_credits WHERE id_user = :id_user');
        $stmt->execute(array('id_user' => $id_user));

        return $stmt->fetch();
    } catch (PDOException $e) {
        echo '<p class="btn btn-danger btn-xs">' . $e->getMessage() . '</p>';
    }
}

function get_limit_ahrefs_export($id_user)
{
    global $servername, $username, $password_server, $dbname;
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare('SELECT limit_ahrefs_export FROM check_credits WHERE id_user = :id_user');
        $stmt->execute(array('id_user' => $id_user));

        return $stmt->fetch();
    } catch (PDOException $e) {
        echo '<p class="btn btn-danger btn-xs">' . $e->getMessage() . '</p>';
    }
}

function select_domains_proj($id_user)
{
    global $servername, $username, $password_server, $dbname;
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare('SELECT domains_proj FROM check_credits WHERE id_user = :id_user');
        $stmt->execute(array('id_user' => $id_user));

        return $stmt->fetch();
    } catch (PDOException $e) {
        echo '<p class="btn btn-danger btn-xs">' . $e->getMessage() . '</p>';
    }
}

// End Gâu Gâu

//echo $_SERVER['REQUEST_URI'];
if ($user_id === "15882") :
    $allow_uri = [
        '/site-explorer',
        '/positions-explorer',
        '/ajax',
        '/scripts',
        '/se',
        '/ke',
        '/tk',
        '/ce',
        '/keywords-explorer',
        '/batch-analysis',
        '/v2-site-explorer',
        '/content-explorer',
        '/site-audit',
        '/sa',
        '/new-project',
        '/add-project',
        '/pmAddProject',
        '/dashboard',
        '/daHealth',
        '/daGetTracked',
        '/daGetSeMetrics',
    ];
else :
    $allow_uri = [
        '/site-explorer',
        '/positions-explorer',
        '/ajax',
        '/scripts',
        '/se',
        '/ke',
        '/tk',
        '/ce',
        '/keywords-explorer',
        '/batch-analysis',
        '/v2-site-explorer',
        '/content-explorer',
        '/site-audit',
        '/sa',
        '/new-project',
        '/add-project',
    ];
endif;

$report_uri = [
    '?target=',
    '?keyword=',
];

// var_dump($_SERVER['REQUEST_URI']);

$timestamp_now = date('Y-m-d', time());
$date1 = new DateTime($timestamp_now);
$date2 = new DateTime($timestamp_end);
$diff_date = $date1->diff($date2)->format("%r%a");
///////////////////// AHREFS PACKED /////////////////////
///////////////////// AHREFS CREDIT /////////////////////
$timestamp_now = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
$time_end = date('Y-m-d H:i:s', $xtimestamp_end);
$timestamp_end = new DateTime($time_end, new DateTimeZone('Asia/Ho_Chi_Minh'));
$diff_datex = $timestamp_end->getTimestamp() - $timestamp_now->getTimestamp();
///////////////////// AHREFS CREDIT /////////////////////

if (($ahrefs_credits > 0 && $plan_id > 1 && $diff_datex > 0)) {

    if (strpos($_SERVER['REQUEST_URI'], '/ajax/') !== false && strpos($_SERVER['REQUEST_URI'], 'items-per-page') === false && $_SERVER['REQUEST_METHOD'] === 'GET') {
        header('Location: http://bacninh.rq.34mcnd.xyz/' . md5(1) . $_SERVER['REQUEST_URI']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && (strpos($_SERVER['REQUEST_URI'], '.css') === false || strpos($_SERVER['REQUEST_URI'], '.gif') === false)) {
        // check link allow
        if (!strposa($_SERVER['REQUEST_URI'], $allow_uri)) {
            die("sai url");
        }

        $url_explorer = 'https://app.ahrefs.com' . $_SERVER['REQUEST_URI'];

        $header_data = getallheaders();
        $csrf_token = $header_data['X-Csrf-Token'];

        if ((strpos($url_explorer, 'ajax') !== false) || (strpos($url_explorer, 'scripts') !== false)) {
            $headers = [
                "Host: app.ahrefs.com",
                "X-CSRF-Token: " . $csrf_token . "",
                "Origin: https://app.ahrefs.com",
                "X-Requested-With: XMLHttpRequest",
                "Referer: " . $url_explorer . "",
                "Connection: keep-alive",
                "If-Modified-Since: *"
            ];
            header('Content-Type: application/json');
        } else {
            $headers = [];
        }

        if (strpos($url_explorer, 'csv-download') !== false || strpos($url_explorer, 'pdf-download') !== false) {

            global $config;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url_explorer);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 600);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']);
            curl_setopt($curl, CURLOPT_REFERER, $url_explorer);
            curl_setopt($curl, CURLOPT_COOKIEFILE, $config['cookie_file']);
            curl_setopt($curl, CURLOPT_COOKIEJAR, $config['cookie_file']);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $site_explorer = curl_exec($curl);
            $header_data = curl_getinfo($curl);
            curl_close($curl);

            header('Location:' . $header_data['redirect_url']);
            die;
            //////////////////////////////////////////////////////

        }

        if (strpos($url_explorer, '&export') !== false) {

            //////////////////////////////////////////////////////
            global $config;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url_explorer);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 600);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']);
            curl_setopt($curl, CURLOPT_REFERER, $url_explorer);
            curl_setopt($curl, CURLOPT_COOKIEFILE, $config['cookie_file']);
            curl_setopt($curl, CURLOPT_COOKIEJAR, $config['cookie_file']);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $site_explorer = curl_exec($curl);
            $header_data = curl_getinfo($curl);
            curl_close($curl);

            $content_type = "charset=UTF-8";

            header('Expires: 0'); // no cache
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
            header('Cache-Control: private', false);
            header('Content-Type: ' . $content_type);
            header('Content-Disposition: attachment; filename="export_file_custome.csv"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . strlen($site_explorer)); // provide file size
            header('Connection: close');
            echo $site_explorer;
            exit();
            //////////////////////////////////////////////////////

        }

        $checking_get = checking_domain_get($url_explorer, $headers);

        $header_data = $checking_get["headers"];
        $site_explorer = $checking_get["contents"];

        if (strpos($site_explorer, 'Ahrefs user login') !== false) {
            // remote login
            $xlogin = login_new_ahref($email_account, $password_account);

            if ($xlogin["success"]) {
                header('Location: http://bacninh.34mcnd.xyz' . $_SERVER['REQUEST_URI']);
                exit;
            } else {
                die("Lien he admin");
            }
        }

        $content_type = $header_data["content_type"];
        header('Content-type: ' . $content_type);

        $site_explorer = str_replace("</head>", "<style>ul.navbar-nav:nth-child(1)>li.nav-item:nth-child(1),ul.navbar-nav:nth-child(1)>li.nav-item:nth-child(7),ul.navbar-nav:nth-child(1)>li.nav-item:nth-child(8),ul.navbar-nav.navbar-header>li.nav-item:nth-child(1),ul.navbar-nav.navbar-header>li.nav-item:nth-child(7),ul.navbar-nav.navbar-header>li.nav-item:nth-child(8){display:none !important;}</style><script>var myAction = setInterval(rmControlButton, 200); var countTimer = 0; function rmControlButton() { var nodeList = document.querySelectorAll('button.css-10aujhm-controlButton'); var idx=0; if(nodeList.length>0){ nodeList[0].remove(); } if(typeof($) !='undefined'){ $('button:contains(\"Buy more\")').remove(); $('button:contains(\"Upgrade\")').remove(); $('button .css-1a0m7nx-controlButtonText').closest('button').remove(); $('button.css-wj6lix-subscribeButton').remove(); $(\"button.btn.css-10mx88t-button.css-p7uoog-button.btn--primary\").remove(); $('span.css-1oauuj8-saSetting').remove(); $('button.css-15qe8gh-button:contains(\"Add keywords\")').remove(); } countTimer++; if(countTimer>50){ clearInterval(myAction); } } setInterval(function(){ $('button:contains(\"Upgrade plan\")').remove(); },2000); setInterval(function(){ $('#start_full_export').remove(); $('#start_custom_export').remove(); },1);</script></head>", $site_explorer);

        $account_options = fetch_value($site_explorer, 'id="userAccountOptions" title="', '"');
        $email_current = extract_emails($account_options);
        $yourname_current = trim(str_replace($email_current, '', $account_options));
        $site_explorer = str_replace('/user/logout', '//' . $domain_root . '/logout.php', $site_explorer);
        $site_explorer = preg_replace("#name: '.*',#m", "name: '" . $credit['username'] . "',", $site_explorer);
        $site_explorer = preg_replace("#email:'.*',#m", "email:'" . $credit['email'] . "',", $site_explorer);
        $site_explorer = preg_replace("#<var user-email>.*</var>#m", "<var user-email>" . $credit['email'] . "</var>", $site_explorer);
        $site_explorer = preg_replace('#<h5 class="dropdown-item--title"><strong>.*</strong></h5>#m', '<h5 class="dropdown-item--title"><strong>' . $credit['username'] . '</strong></h5>', $site_explorer);
        $site_explorer = preg_replace('#https://cdn.ahrefs.com/app/keywords-explorer/main.*css#m', '/main.css', $site_explorer);
        $site_explorer = str_replace('<a class="logo navbar-brand" href="/dashboard">', '<a class="logo navbar-brand" href="/site-explorer">', $site_explorer);
        $site_explorer = str_replace('static.ahrefs.com', 'cdn.ahrefs.com', $site_explorer);
        $site_explorer = str_replace("/rank-tracker", "/batch-analysis", $site_explorer);
        $site_explorer = str_replace("Rank Tracker", "Batch Analysis", $site_explorer);
        $site_explorer = str_replace('media="print"', "", $site_explorer);
        $site_explorer = str_replace($email_current, $credit['email'], $site_explorer);
        $site_explorer = str_replace($yourname_current, $credit['username'], $site_explorer);
        $account_id_current = fetch_value($site_explorer, "user_id:'", "'");
        $site_explorer = str_replace($account_id_current, rand(111111, 999999), $site_explorer);
        $site_explorer = str_replace("analytics.ahrefs.com", "google.com", $site_explorer);
        $site_explorer = str_replace('https://static-app.ahrefs.com/assets/css/font-awesome.min.css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', $site_explorer);


        $target_domain = fetch_value($site_explorer, 'TargetForCurrentReport = "', '"');
        if (strpos($target_domain, '\/') !== false) {
            $extract_target_domain = explode("\/", $target_domain);
            $target = $extract_target_domain[0];
        } else {
            $target = $target_domain;
        }

        $user_hash_fake = fetch_value($site_explorer, "user_hash: '", "'");
        $md5_encode = md5(time() . rand(111, 999)) . md5(rand(1111111111, time()));
        $site_explorer = str_replace($user_hash_fake, $md5_encode, $site_explorer);
        $site_explorer = str_replace("widget.intercom.io", "google.com", $site_explorer);
        $site_explorer = str_replace("LastNavigationReferrer = 'loadAJAX';", "window.location.href = href;", $site_explorer);
        $site_explorer = str_replace("iubenda.com", "google.com", $site_explorer);
        //////////////////////////////////////////////////////
        //$request_url  = $_SERVER['REQUEST_URI'];

        // Gâu Gâu
        // Export: Top pages | Referring domains
        if (strpos($url_explorer, 'seGetTopPagesExport?input=') !== false || strpos($url_explorer, 'seRefdomainsExport?input=') !== false) {

            require('unset_session.php');

            $xahrefs_export = isset($_SESSION['user_credits']['ahrefs_export']) ? $_SESSION['user_credits']['ahrefs_export'] : 0;

            $query_str = parse_url($url_explorer, PHP_URL_QUERY);
            parse_str($query_str, $query_params);
            $data_export = json_decode($query_params['input'], true);
            $limit_export = $data_export["params"]["drop_for_report__size"];

            $value_arr = get_limit_ahrefs_export($user_id);
            $limit_ahrefs_export = $value_arr['limit_ahrefs_export'];

            if (empty($limit_ahrefs_export) || $limit_ahrefs_export == 0) :
                $exportahrefs = 5000;
            else :
                $exportahrefs = $limit_ahrefs_export;
            endif;

            $xtotal_export = $xahrefs_export + $limit_export;

            if ($exportahrefs >= $xtotal_export && $ahrefs_credits > 0) {
                require('config.php');
                update_ahrefs_export($user_id, $limit_export);
                $_SESSION['user_credits']['ahrefs_export'] = $xtotal_export;
                checkhis_addhist_updatecredit($user_id, $ahrefs_credits, $url_explorer . ' (Export)', 'ahrefs_export');
            } else {
                echo "Limit Plans or End of reports!";
                exit;
            }
        }

        // Export: Backlinks | Organic keywords (Không trừ Report và không cộng SL Export nếu Export SL giống nhau)
        if (strpos($url_explorer, 'seBacklinksExport?input=') !== false || strpos($url_explorer, 'seGetOrganicKeywordsExport?input=') !== false) {

            require('unset_session.php');

            $xahrefs_export = isset($_SESSION['user_credits']['ahrefs_export']) ? $_SESSION['user_credits']['ahrefs_export'] : 0;

            $query_str = parse_url($url_explorer, PHP_URL_QUERY);
            parse_str($query_str, $query_params);
            $data_export = json_decode($query_params['input'], true);
            $limit_export = $data_export["params"]["drop_for_report__size"];

            $value_arr = get_limit_ahrefs_export($user_id);
            $limit_ahrefs_export = $value_arr['limit_ahrefs_export'];

            if (empty($limit_ahrefs_export) || $limit_ahrefs_export == 0) :
                $exportahrefs = 5000;
            else :
                $exportahrefs = $limit_ahrefs_export;
            endif;

            $xtotal_export = $xahrefs_export + $limit_export;

            if ($exportahrefs >= $xtotal_export && $ahrefs_credits > 0) {
                require('config.php');

                $header_data = getallheaders();
                $history = check_history($user_id, 'ahrefs_export', $header_data['Referer'] . ' (Quanlity Export: ' . $limit_export . ')');
                $history_id = $history['history_id'];
                $date_check = date('mdYHi', ((int)$history['date_check'] + 900));
                $date_today = date('mdYHi', time());

                if (!$history) {
                    add_history($user_id, 'ahrefs_export', $header_data['Referer'] . ' (Quanlity Export: ' . $limit_export . ')');

                    update_ahrefs_export($user_id, $limit_export);
                    $_SESSION['user_credits']['ahrefs_export'] = $xtotal_export;
                } else if ($date_check < $date_today) {
                    add_history($user_id, 'ahrefs_export', $header_data['Referer'] . ' (Quanlity Export: ' . $limit_export . ')');

                    update_ahrefs_export($user_id, $limit_export);
                    $_SESSION['user_credits']['ahrefs_export'] = $xtotal_export;
                }
            } else {
                echo "Limit Plans or End of reports!";
                exit;
            }
        }

        // Search Site Explorer
        if (strpos($url_explorer, 'seGetPagesByTraffic') !== false) {
            require('config.php');
            checkhis_addhist_updatecredit($user_id, $ahrefs_credits, $url_explorer . ' (OVERVIEW 2.0)', 'ahrefs_reports');
        }

        /*----------------------*/
        /*  Site structure
        /*----------------------*/
        if (strpos($url_explorer, 'seGetSiteStructure?input=') !== false) {
            $header_data = getallheaders();
            $domain = $header_data['Referer'];

            require('config.php');
            checkhis_addhist_updatecredit($user_id, $ahrefs_credits, $domain . ' (Site structure)', 'ahrefs_reports');
        }

        /*----------------------*/
        /*  Calendar
        /*  Pages in Calendar
        /*  Anchors (Not Report Paging)
        /*  Top pages (Not Report Paging)
        /*  Organic keywords (Not Report Paging)
        /*  Paid keywords (Not Report Paging
        /*  Ads (Not Report Paging
        /*  Paid pages (Not Report Paging
        /*----------------------*/
        if (strpos($url_explorer, 'seGetPositionsMovements?input=') !== false || strpos($url_explorer, 'seGetPagesMovements?input=') !== false || strpos($url_explorer, 'seAnchors?input=') !== false || strpos($url_explorer, 'seGetTopPages?input=') !== false || strpos($url_explorer, 'seGetOrganicKeywords?input=') !== false || strpos($url_explorer, 'seGetPaidKeywords?input=') !== false || strpos($url_explorer, 'seGetAds?input=') !== false || strpos($url_explorer, 'seGetTopLandingPages?input=') !== false) {
            $header_data = getallheaders();
            $domain = $header_data['Referer'];
            $domain = preg_replace('/&offset=([0-9]+)/', '&offset=0', $domain) . ' (Calendar | Pages in Calendar | Anchors - Backlink profile | Top pages | Organic keywords | Paid keywords | Ads | Paid pages)';

            require('config.php');
            checkhis_addhist_updatecredit($user_id, $ahrefs_credits, $domain, 'ahrefs_reports');
        }

        /*----------------------*/
        /*  Backlinks (Not Report Paging)
        /*  Referring domains (Not Report Paging)
        /*  Links to target in Anchors
        /*  Ref. domains in Anchors
        /*----------------------*/
        if (strpos($url_explorer, 'seBacklinks?input=') !== false || strpos($url_explorer, 'seRefdomains?input=') !== false) {
            $header_data = getallheaders();
            $domain = $header_data['Referer'];
            if (strpos($domain, '/anchors/') !== false) {
                $domain = $url_explorer . ' (Links to target or Ref. domains in Anchors)';
            } else {
                $domain = preg_replace('/&offset=([0-9]+)/', '&offset=0', $domain) . ' (Backlinks | Referring domains | Links to target or Ref. domains in Anchors)';
            }

            require('config.php');
            checkhis_addhist_updatecredit($user_id, $ahrefs_credits, $domain, 'ahrefs_reports');
        }

        // Links to target(Referring domains) Request này giống Similar trong Backlinks
        if (strpos($url_explorer, 'seBacklinksGroup?input=') !== false) {
            $header_data = getallheaders();
            $domain = $header_data['Referer'];
            if (strpos($domain, '/refdomains/') !== false) {
                require('config.php');
                checkhis_addhist_updatecredit($user_id, $ahrefs_credits, $url_explorer . ' (Links to target of Referring Domains)', 'ahrefs_reports');
            }
        }

        // Search Keywords Explorer
        if (strpos($url_explorer, 'keGetSearchVolumeGoogle?input=') !== false && strpos($url_explorer, 'keGetTopPositionsHistory?input=') === false) {
            $header_data = getallheaders();
            $domain = $header_data['Referer'];

            require('config.php');
            checkhis_addhist_updatecredit($user_id, $ahrefs_credits, $domain . ' (Search Keywords Explorer)', 'ahrefs_reports');
        }


        if ($user_id == 15882) :
            if (strpos($url_explorer, 'saProjects') !== false) {
                $domains_proj_arr = select_domains_proj($user_id);
                $domains_proj = $domains_proj_arr['domains_proj'];
                $array = explode('|', $domains_proj);
                // foreach($array as $item) :
                // var_dump($item);
                // endforeach;

                $data_suggestions = file_get_contents('php://input');
                $data_export = json_decode($data_suggestions, true);
                // $projects         = $data_export['projects'];
                var_dump($data_suggestions . '+++');


                // require('config.php');
                // update_domains_proj($user_id, $domain);
            }
        endif;
        // End Gâu Gâu

        echo $site_explorer;

        xflush();

        // Overview
        if ((strposa($_SERVER['REQUEST_URI'], $report_uri) && strpos($url_explorer, '/site-explorer/')) || (strposa($_SERVER['REQUEST_URI'], $report_uri) && strpos($url_explorer, '/positions-explorer/'))) {
            require('config.php');
            $url_explorer = preg_replace('/\/([0-9]+)\//', '/', $url_explorer);
            $url_explorer = preg_replace('/&page=([0-9]+)/', '', $url_explorer);
            $url_explorer = preg_replace('/&limit=([0-9]+)/', '', $url_explorer);
            $url_explorer = preg_replace('/&offset=([0-9]+)/', '', $url_explorer);

            // $header_data = getallheaders();
            checkhis_addhist_updatecredit($user_id, $ahrefs_credits, $url_explorer . ' | (OVERVIEW)', 'ahrefs_reports');
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $url_explorer = 'https://app.ahrefs.com' . $_SERVER['REQUEST_URI'];

        if (strpos($url_explorer, '/ajax/examples/links-for-linked-domain/') !== false || strpos($url_explorer, '/ajax/examples/referring-domains-for-ip/') !== false || strpos($url_explorer, '/ajax/examples/referring-ips-for-network/') !== false) {
            require('config.php');
            // $header_data = getallheaders();
            checkhis_addhist_updatecredit($user_id, $ahrefs_credits, $url_explorer . ' (Ajax - ' . time() . ')', 'ahrefs_reports');
        }

        // Search Keywords Explorer
        // if(strpos($url_explorer, 'keKeywordOverview') !== false){
        // 	$header_data = getallheaders();
        // 	$domain      = $header_data['Referer'];

        // 	require('config.php');
        // 	checkhis_addhist_updatecredit($user_id, $ahrefs_credits, $domain.' (Search Keywords Explorer)', 'ahrefs_reports');
        // }

        // SERP overview for "xxx" in Overview Keywords Explorer
        if (strpos($url_explorer, 'keSerpOverview') !== false) {
            $data_suggestions = file_get_contents('php://input');
            $data_export = json_decode($data_suggestions, true);
            $keywordSeed = $data_export['keywordSeed'];
            $targetDate = $data_export['targetDate'];

            if ($targetDate != 'Latest') :
                $keyword = $keywordSeed['keyword'];
                $date = $targetDate[1];

                require('config.php');
                checkhis_addhist_updatecredit($user_id, $ahrefs_credits, $url_explorer . ' SERP overview for "' . rawurlencode($keyword) . '" (Date: "' . $date . '") ', 'ahrefs_reports');
            endif;
        }

        // Export in Matching terms (keyword Explorer) (OK)
        if (strpos($url_explorer, 'keIdeasExport') !== false) {

            require('unset_session.php');

            $xahrefs_export = isset($_SESSION['user_credits']['ahrefs_export']) ? $_SESSION['user_credits']['ahrefs_export'] : 0;

            /*if($user_id === "16542") :
                var_dump($_SESSION['user_credits']['ahrefs_export']);
                unset($_SESSION['user_credits']['ahrefs_export']);
                var_dump($xahrefs_export.'++++---'); die;
            endif;*/

            $data_suggestions = file_get_contents('php://input');
            $data_export = json_decode($data_suggestions, true);
            // var_dump($data_suggestions.'xxxx'); die;
            $limit_export = $data_export["limit"];

            $value_arr = get_limit_ahrefs_export($user_id);
            $limit_ahrefs_export = $value_arr['limit_ahrefs_export'];

            if (empty($limit_ahrefs_export) || $limit_ahrefs_export == 0) :
                $exportahrefs = 5000;
            else :
                $exportahrefs = $limit_ahrefs_export;
            endif;

            $xtotal_export = $xahrefs_export + $limit_export;

            if ($exportahrefs >= $xtotal_export && $ahrefs_credits > 0) {
                require('config.php');
                update_ahrefs_export($user_id, $limit_export);
                $_SESSION['user_credits']['ahrefs_export'] = $xtotal_export;

                $keyword = utf8_encode($data_export["seed"][1][0]);
                $timestamp_now = time();
                $domain = $url_explorer . ' (Country: ' . $data_export["country"] . ' | IdeasType: ' . $data_export["ideasType"] . ' | Limit: ' . $data_export["limit"] . ' | SearchEngine: ' . $data_export["searchEngine"] . ' | Keyword: ' . $keyword . ' | Timestamp: ' . $timestamp_now . ')';
                checkhis_addhist_updatecredit($user_id, $ahrefs_credits, $domain . ' (Export in Matching terms - Keyword Explorer)', 'ahrefs_export');
            } else {
                echo "Limit Plans or End of reports!";
                exit;
            }
        }

        // Gâu Gâu
        // if($user_id === "15882") :
        if (strpos($url_explorer, 'pmAddProject') !== false) {
            $data_suggestions = file_get_contents('php://input');
            $data_export = json_decode($data_suggestions, true);
            $domain = $data_export['target'];
            // var_dump($data_export['target']); die;

            require('config.php');
            update_domains_proj($user_id, $domain);
        }
        // endif;
        // End Gâu Gâu

        // if(strpos($url_explorer, 'saPan') !== false){
        // 	var_dump('Updating'); die;
        // }

        $id_ahrefs = rand(1000000, 1199999);

        if (strpos($url_explorer, 'asGetWorkspaces') !== false) {
            header('Content-type: application/json; charset=utf-8');
            die('["Ok",{"workspaces":[{"name":"' . $credit['email'] . '","id":"' . $id_ahrefs . '","numMembers":1,"planName":"Agency"}]}]');
        }

        $header_data = getallheaders();
        $csrf_token = $header_data['X-Csrf-Token'];
        $data_suggestions = file_get_contents('php://input');
        $data_suggestions = str_replace("charset=utf-16", "charset=utf-8", $data_suggestions);
        $headers = [
            "Host: app.ahrefs.com",
            "X-CSRF-Token: " . $csrf_token . "",
            "Content-Type: " . $_SERVER["CONTENT_TYPE"] . "",
            "Origin: https://app.ahrefs.com",
            "X-Requested-With: XMLHttpRequest",
            "Referer: " . $url_explorer . "",
            "Connection: keep-alive",
            "If-Modified-Since: *"
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url_explorer);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);


        curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']);
        curl_setopt($curl, CURLOPT_REFERER, $url_explorer);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $config['cookie_file']);
        curl_setopt($curl, CURLOPT_COOKIEJAR, $config['cookie_file']);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_suggestions);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $site_explorer = curl_exec($curl);
        $header_data = curl_getinfo($curl);
        curl_close($curl);
        $content_type = $header_data["content_type"];


        if (strpos($url_explorer, 'kePlan') !== false) {

            //header('Content-type: application/json; charset=utf-8');

            $ahrefs_export = isset($_SESSION['user_credits']['ahrefs_export']) ? $_SESSION['user_credits']['ahrefs_export'] : 0;
            $total_export = 5000 - (int)$ahrefs_export;

            $rowsPerReport = json_decode($site_explorer, true);
            //print_r($rowsPerReport);
            $rowsPerReport["limits"]["batchKeywords"] = 100;
            $rowsPerReport["limits"]["rowsPerReport"] = 5000;
            $rowsPerReport["limits"]["seRowsPerReport"] = 20000;
            //$rowsPerReport["limits"]["monthlyExports"] = 5000;
            // $rowsPerReport["limits"]["monthlyExports"][1]["limit"] = (int)$plan_user["exportahrefs"];
            // $rowsPerReport["limits"]["monthlyExports"][1]["usage"] = (int)$ahrefs_export;
            die(json_encode($rowsPerReport));
        }

        if (strpos($_SERVER['REQUEST_URI'], 'batch-analysis') !== false && strpos($site_explorer, 'URLs have been analyzed') !== false) {

            require('config.php');

            add_history($user_id, $type_check = 'ahrefs_reports', "Check /batch-analysis");
            if ($ahrefs_credits > 0) {
                update_credit_ahrefs($user_id, $ahrefs_credits);
                $_SESSION['user_credits']['ahrefs_credits'] = $ahrefs_credits - 1;
            }
        }
        if (strpos($_SERVER['REQUEST_URI'], 'batch-analysis?export=1') !== false) {

            preg_match('/(.*?); name=\"(.*?)\"/', $content_type, $content_out);

            header('Expires: 0'); // no cache
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
            header('Cache-Control: private', false);
            header('Content-Type: ' . $content_type);
            header('Content-Disposition: attachment; filename="' . $content_out[2] . '"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . strlen($site_explorer)); // provide file size
            header('Connection: close');
            echo $site_explorer;
            exit();
        }

        header('Content-type: ' . $content_type);

        echo $site_explorer;
    }
} else {
    header("Location: http://tool.buyseotools.io/upgrade-plan");
    die();
}