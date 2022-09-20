<?php
	date_default_timezone_set("Asia/Ho_Chi_Minh");
	//$get_setting = get_setting();
	//$domain_root = $get_setting['site_url'];
	
	$today = date("Ymd");
	$config['cookie_file'] = $dir . '/cookies/'.md5($today).'.txt';
	$config['useragent'] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36";
	if(!file_exists($config['cookie_file'])){
		$fp = @fopen($config['cookie_file'],'w');
		@fclose($fp);
	}
	if (!function_exists('getallheaders')){ 
	    function getallheaders() 
	    { 
	        $headers = []; 
	       foreach ($_SERVER as $name => $value) 
	       { 
	           if (substr($name, 0, 5) == 'HTTP_') 
	           { 
	               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
	           } 
	       } 
	       return $headers; 
	    } 
	} 
	function get_setting(){
		global $servername,$username,$password_server,$dbname;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("SELECT * FROM system_setting WHERE setting_id = 1"); 
			$stmt->execute();

			// set the resulting array to associative
			$result = $stmt->fetch(); 
			return $result;

		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage();
		}
		$conn = null;
		
	}
	function get_account_system($account_type,$account_id='',$account_credit=''){
		global $servername,$username,$password_server,$dbname;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			if($account_id){
				$stmt = $conn->prepare("SELECT * FROM account_system WHERE account_type = '".$account_type."' AND account_id = '".$account_id."'"); 
				$stmt->execute();
				
				$result = $stmt->fetch(); 
				return $result;
			}elseif($account_credit){
				$stmt = $conn->prepare("SELECT * FROM account_system WHERE account_type = '".$account_type."' AND account_credit > '0' AND account_active = 1"); 
				$stmt->execute();
				
				$result = $stmt->fetch(); 
				return $result;
			}else{
				$stmt = $conn->prepare("SELECT * FROM account_system WHERE account_type = '".$account_type."' AND account_active = 1"); 
				$stmt->execute();

				// set the resulting array to associative
				$result = $stmt->fetch(); 
				return $result;
			}
		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage();
		}
		$conn = null;
		
	}
	function get_plans($plan_id){
		global $servername,$username,$password_server,$dbname;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("SELECT * FROM plans WHERE plan_id = ".$plan_id); 
			$stmt->execute();

			// set the resulting array to associative
			$result = $stmt->fetch(); 
			return $result;

		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage();
		}
		$conn = null;
		
	}
	function user_credits($access_key){
		global $servername,$username,$password_server,$dbname;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("SELECT * FROM check_credits INNER JOIN members ON check_credits.id_user = members.memberID WHERE check_credits.id_user = (SELECT id_user FROM secret WHERE key_code = '".$access_key."')"); 
			$stmt->execute();

			// set the resulting array to associative
			$result = $stmt->fetch(); 
			return $result;

		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage();
		}
		$conn = null;
		
	}
	function add_dashboard($id_user,$type_check = '',$domain,$data){
		global $servername,$username,$password_server,$dbname;
		$date_create = date("Y-m-d h:i:s");
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("INSERT INTO `dashboard_domain` (`id_user`, `dashboard_type`, `dashboard_domain`, `dashboard_data`, `date_create`) 
									VALUES ('".$id_user."', '".$type_check."', '".$domain."', '".$data."', '".$date_create."')"); 
			$stmt->execute();

			// set the resulting array to associative
			$result = $stmt->fetch(); 
			return $result;

		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage()."add_history";
		}
		$conn = null;
		
	}
	function get_dashboard($id_user,$type_check = ''){
		global $servername,$username,$password_server,$dbname;
		$date_create = date("Y-m-d h:i:s");
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("SELECT * FROM dashboard_domain WHERE id_user = '".$id_user."' AND dashboard_type = '".$type_check."'"); 
			$stmt->execute();

			// set the resulting array to associative
			$result = $stmt->fetchAll(); 
			return $result;

		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage()."add_history";
		}
		$conn = null;
		
	}
	function tokens_server($token_name,$token_new = false){
		global $servername,$username,$password_server,$dbname;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			if(!$token_new){
				$stmt = $conn->prepare("SELECT * FROM token_server WHERE token_name = '".$token_name."'");
				$stmt->execute();
			}else{
				$stmt = $conn->prepare("UPDATE `token_server` SET `token_value` = '".$token_new."' WHERE `token_server`.`token_name` = '".$token_name."'"); 
				$stmt->execute();
			}
			// set the resulting array to associative
			$result = $stmt->fetch(); 
			return $result;

		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage()."add_history";
		}
		$conn = null;
		
	}
	function check_ahref($domain){
		global $email,$password;
		login_ahref($email,$password);
		$check_ahref = check_ajax($domain);
		$check_ref_domains = check_ajax_ref_domains($domain);
		$check_organic = check_ajax_organic($domain);
		$json_code = json_decode($check_ahref,true);
		$json_ref_domains = json_decode($check_ref_domains,true);
		$json_organic_domains = json_decode($check_organic,true);
		$ur_ahref = $json_code['url_rating']['raw'];
		$dr_ahref = $json_code['domain_rating']['raw'];
		$rank_ahref = $json_code['ahrefs_rank']['formatted'];
		$ranking_ahref = $json_code['ahrefs_rank']['raw'];
		$bl_ahref = $json_code['backlinks']['formatted'];
		$rd_ahref = $json_ref_domains['ref_domains']['formatted'];
		$organic_ahref = $json_organic_domains['organic']['all'];
		return array('DR' => $dr_ahref,'BL' => $bl_ahref,'RD' => $rd_ahref,'RK' => $rank_ahref, 'RAK' => $ranking_ahref,'OR' => $organic_ahref,'UR' => $ur_ahref);
	}
	function check_ajax($website){
		$token = token_ahref();
		$headers = [
			'x-csrf-token: '.$token.'',
			'x-requested-with:XMLHttpRequest'
		];
		global $config;
		$curl = curl_init(); 
		$url_explorer = 'https://ahrefs.com/site-explorer/ajax/get/SE-stats/exact/recent?target='.$website.'';
		curl_setopt($curl, CURLOPT_URL, $url_explorer); 
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']); 
		curl_setopt($curl, CURLOPT_REFERER, "https://ahrefs.com/"); 
		curl_setopt($curl, CURLOPT_COOKIEFILE,$config['cookie_file']); 
		curl_setopt($curl, CURLOPT_COOKIEJAR,$config['cookie_file']); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
		$result = curl_exec($curl); 
		curl_close($curl);
		return $result;
	}
	function token_ahref($new_token = false){
		$get_ahref = tokens_server('ahrefs.com');
		if(!isset($get_ahref['token_value']) || $new_token){
			$url_extract = 'https://ahrefs.com/user/login';
			$load_extract = curl($url_extract);
			$token_ahref = fetch_value($load_extract,'<input name="_token" type="hidden" value="','">');
			if(empty($token_ahref)){
				$token_ahref = fetch_value($load_extract,'<meta name="_token" content="','">');
			}
			$login = false;
			if (strpos($load_extract, 'Sign out') !== false) {
				$login = true;
				tokens_server('ahrefs.com',$token_ahref);
			}
			return array('token' => $token_ahref, 'login' => $login);
		}else{
			return array('token' => $get_ahref['token_value'], 'login' => true);
		}
	}
	function fetch_value($str,$find_start,$find_end){
		$start = stripos($str, $find_start);
		if($start===false) return "";
		$length = strlen($find_start);
		$end = stripos(substr($str, $start+$length), $find_end);
		return trim(substr($str, $start+$length, $end));
	}
	function curl($url='',$var=''){
		global $config;
		$curl = curl_init(); 
		curl_setopt($curl, CURLOPT_URL, $url); 
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']); 
		curl_setopt($curl, CURLOPT_REFERER, "https://ahrefs.com/"); 
		curl_setopt($curl, CURLOPT_PROXY, $_SESSION["ip_ahrefs_v0"]);
		curl_setopt($curl, CURLOPT_PROXYPORT, $_SESSION["port_ahrefs_v0"]);
		if(!empty($_SESSION["user_pass_ahrefs_v0"])){
			curl_setopt($curl, CURLOPT_PROXYUSERPWD, $_SESSION["user_pass_ahrefs_v0"]);
		}
		if($var) {
			curl_setopt($curl, CURLOPT_POST, true); 
			curl_setopt($curl, CURLOPT_POSTFIELDS, $var); 
		}
		curl_setopt($curl, CURLOPT_COOKIEFILE,$config['cookie_file']); 
		curl_setopt($curl, CURLOPT_COOKIEJAR,$config['cookie_file']); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
		$result = curl_exec($curl); 
		curl_close($curl);
		return $result;
	}
	function curl2($url='',$var=''){
		global $config;
		$curl = curl_init(); 
		curl_setopt($curl, CURLOPT_URL, $url); 
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']); 
		curl_setopt($curl, CURLOPT_REFERER, "https://ahrefs.com/"); 
		curl_setopt($curl, CURLOPT_PROXY, $_SESSION["ip_ahrefs_v0"]);
		curl_setopt($curl, CURLOPT_PROXYPORT, $_SESSION["port_ahrefs_v0"]);
		if(!empty($_SESSION["user_pass_ahrefs_v0"])){
			curl_setopt($curl, CURLOPT_PROXYUSERPWD, $_SESSION["user_pass_ahrefs_v0"]);
		}
		if($var) {
			curl_setopt($curl, CURLOPT_POST, true); 
			curl_setopt($curl, CURLOPT_POSTFIELDS, $var); 
		}
		curl_setopt($curl, CURLOPT_COOKIEFILE,$config['cookie_file']); 
		curl_setopt($curl, CURLOPT_COOKIEJAR,$config['cookie_file']); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
		$result = curl_exec($curl);
		$header_data= curl_getinfo($curl);
		curl_close($curl);
		$resultx = array('result' => $result, 'header' => $header_data);
		return $resultx;
	}
	function check_ajax_ref_domains($website){
		$token = token_ahref();
		$headers = [
			'x-csrf-token: '.$token.'',
			'x-requested-with:XMLHttpRequest'
		];
		global $config;
		$curl = curl_init(); 
		$url_explorer = 'https://ahrefs.com/site-explorer/ajax/get/ref-domains/subdomains/recent?target='.$website.'';
		curl_setopt($curl, CURLOPT_URL, $url_explorer); 
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']); 
		curl_setopt($curl, CURLOPT_REFERER, "https://ahrefs.com/"); 
		curl_setopt($curl, CURLOPT_COOKIEFILE,$config['cookie_file']); 
		curl_setopt($curl, CURLOPT_COOKIEJAR,$config['cookie_file']); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
		$result = curl_exec($curl); 
		curl_close($curl);
		return $result;
	}
	function check_ajax_organic($website){
		$token = token_ahref();
		$headers = [
			'x-csrf-token: '.$token.'',
			'x-requested-with:XMLHttpRequest'
		];
		global $config;
		$curl = curl_init(); 
		$url_explorer = 'https://ahrefs.com/site-explorer/ajax/get/PE-stats/subdomains?target='.$website.'';
		curl_setopt($curl, CURLOPT_URL, $url_explorer); 
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']); 
		curl_setopt($curl, CURLOPT_REFERER, "https://ahrefs.com/"); 
		curl_setopt($curl, CURLOPT_COOKIEFILE,$config['cookie_file']); 
		curl_setopt($curl, CURLOPT_COOKIEJAR,$config['cookie_file']); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
		$result = curl_exec($curl); 
		curl_close($curl);
		return $result;
	}
	function login_ahref($email,$pass){
		global $config;
		$cookie_file = $config['cookie_file'];
		//unlink($cookie_file);
		$url_login = 'https://auth.ahrefs.com/auth/login';
		$cookie_login = token_ahref(true);
		if(!$cookie_login['login']){
			$post_login = '{"remember_me":true,"auth":{"password":"'.$pass.'","login":"'.$email.'"}}';
			$loginz = curl2($url_login,$post_login);
			$login = json_decode($loginz['result'],true);
			if($login["success"]){
				$session_auth_ahrefs = "https://ahrefs.com/api/v3/auth/session/login";
				$session_id = '{"session_id":"'.$login["result"]["session_id"].'"}';
				$session_login = curl2($session_auth_ahrefs,$session_id);
			}else{
				die("Tài khoản ahrefs.com có sự thay đổi. Vui lòng liên hệ QTV để hướng dẫn.");
			}
		}
		return true;
	}
	function login_new_ahref($email,$pass){
		global $config;
		$cookie_file = $config['cookie_file'];
		//unlink($cookie_file);
		$url_login = 'https://auth.ahrefs.com/auth/login';
		$cookie_login = token_ahref(true);
		if(!$cookie_login['login']){
			$post_login = '{"remember_me":true,"auth":{"password":"'.$pass.'","login":"'.$email.'"}}';
			$loginz = curl2($url_login,$post_login);
			$login = json_decode($loginz['result'],true);

			if($login["success"]){
				$session_auth_ahrefs = "https://app.ahrefs.com/v4/loginCheckCompletion";
				$session_id = '{"session_id":"'.$login["result"]["session_id"].'"}';
				$session_login = curl2($session_auth_ahrefs,$session_id);
			}elseif($login["error"] === "user_blocked"){
				curl("https://api.telegram.org/bot878758294:AAErfWWVM1d-ZOAgpjjPUcvEmPU2qBxzXts/sendMessage?chat_id=-291214321&text=" . urlencode($email . " đã bị khóa. VPS 1"));
				die("Tài khoản ahrefs.com có sự thay đổi. Vui lòng liên hệ QTV để hướng dẫn.");
			}else{
				die("Tài khoản ahrefs.com có sự thay đổi. Vui lòng liên hệ QTV để hướng dẫn.");
			}
		}
		return true;
	}
	function cookie_ahrefs($url_explorer){
		global $config;
		$curl = curl_init($url_explorer);
		curl_setopt($curl, CURLOPT_COOKIEFILE,$config['cookie_file']); 
		curl_setopt($curl, CURLOPT_COOKIEJAR,$config['cookie_file']);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER, 1);	
		$site_explorer = curl_exec($curl);
		curl_close($curl);
		
		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $site_explorer, $matches);
		$cookies = array();
		foreach($matches[1] as $item) {
			parse_str($item, $cookie);
			$cookies = array_merge($cookies, $cookie);
		}
		return $cookies;
	}
	function update_dashboard_ahrefs($access_key,$user_id,$dashboard_credit){
		global $servername,$username,$password_server,$dbname;
		$new_credit = $dashboard_credit - 1;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("UPDATE `check_credits` SET `ahrefs_dashboard` = '".$new_credit."' WHERE `check_credits`.`id_user` = '".$user_id."';"); 
			$stmt->execute();

			// set the resulting array to associative
			$result = $stmt->rowCount();
			return $result;

		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage();
		}
		$conn = null;
		
	}
	function add_history($id_user,$type_check = '',$domain){
		global $servername,$username,$password_server,$dbname;
		$date_check = time();
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("INSERT INTO `check_history` (`history_id`, `id_user`, `type_check`, `domain`, `date_check`) 
									VALUES (NULL, '".$id_user."', '".$type_check."', '".$domain."', '".$date_check."')"); 
			$stmt->execute();

			// set the resulting array to associative
			$result = $stmt->fetch(); 
			return $result;

		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage()."add_history";
		}
		$conn = null;
		
	}
	function check_history($id_user,$type_check = '',$domain){
		global $servername,$username,$password_server,$dbname;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("SELECT * FROM `check_history` WHERE id_user = '".$id_user."' AND type_check = '".$type_check."' AND domain = '".$domain."'"); 
			$stmt->execute();

			// set the resulting array to associative
			$result = $stmt->fetch(); 
			return $result;

		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage()."check_history";;
		}
		$conn = null;
		
	}
	function update_history($history_id){
		global $servername,$username,$password_server,$dbname;
		$date_check = time();
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("UPDATE `check_history` SET `date_check` = '".$date_check."' WHERE `check_history`.`history_id` = ".$history_id.""); 
			$stmt->execute();

			// set the resulting array to associative
			$result = $stmt->rowCount();
			return $result;

		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage();
		}
		$conn = null;
		
	}
	function update_credit_ahrefs($user_id,$original_credit){
		global $servername,$username,$password_server,$dbname;
		$new_credit = $original_credit - 1;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("UPDATE `check_credits` SET `ahrefs_credits` = '".$new_credit."' WHERE `check_credits`.`id_user` = '".$user_id."';"); 
			$stmt->execute();

			// set the resulting array to associative
			$result = $stmt->rowCount();
			return $result;

		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage();
		}
		$conn = null;
		
	}
	function extract_emails($string){
		preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $string, $matches);
		if(isset($matches[0][0]))
			return $matches[0][0];
	}
	///////////// ADD NEW ///////////////
	function user_packeds($access_key){
		global $servername,$username,$password_server,$dbname;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("SELECT * FROM user_packed INNER JOIN members ON user_packed.id_user = members.memberID WHERE user_packed.id_user = (SELECT id_user FROM secret WHERE key_code = '".$access_key."')"); 
			$stmt->execute();

			// set the resulting array to associative
			$result = $stmt->fetch(); 
			return $result;

		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage();
		}
		$conn = null;
		
	}
	function update_packeds_ahrefs($user_id,$packed_id,$original_credit){
		global $servername,$username,$password_server,$dbname;
		$new_credit = $original_credit - 1;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("UPDATE `user_packed` SET `packed_check_today` = '".$new_credit."' WHERE `user_packed`.`id_user` = '".$user_id."' AND `user_packed`.`id_packed` = '".$packed_id."';"); 
			$stmt->execute();

			// set the resulting array to associative
			$result = $stmt->rowCount();
			return $result;

		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage();
		}
		$conn = null;
		
	}
	function get_plan_user( $id_user ) {
		global $servername,$username,$password_server,$dbname;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare('SELECT * FROM user_plans INNER JOIN plans ON user_plans.plan_id = plans.plan_id WHERE user_plans.id_user = :id_user AND skip_plan = "false"');
			$stmt->execute(array('id_user' => $id_user));

			return $stmt->fetch();

		} catch(PDOException $e) {
		    echo '<p class="btn btn-danger btn-xs">'.$e->getMessage().'</p>';
		}
	}
	function get_subscription_user() {
		$url_explorer = "https://ahrefs.com/user/subscriptions";
		$token = token_ahref();
		$headers = [
			"Host: app.ahrefs.com",
			"X-CSRF-Token: ".$token['token']."",
			"X-Requested-With: XMLHttpRequest",
			"Referer: ".$url_explorer."",
			"Connection: keep-alive",
			"If-Modified-Since: *"
		];
		
		global $config;
		$curl = curl_init(); 
		curl_setopt($curl, CURLOPT_URL, $url_explorer); 
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 600);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']); 
		curl_setopt($curl, CURLOPT_REFERER, $url_explorer); 
		curl_setopt($curl, CURLOPT_COOKIEFILE,$config['cookie_file']); 
		curl_setopt($curl, CURLOPT_COOKIEJAR,$config['cookie_file']); 	
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
		$site_explorer = curl_exec($curl);
		$header_data= curl_getinfo($curl);
		curl_close($curl);
		$domain_per_day = trim(strip_tags(fetch_value($site_explorer,"Domains per day","URLs per day")));

		return $domain_per_day;
	}
	function disable_account_checked($id_account){
		$time_update = strtotime("tomorrow 07:00:00");
		global $servername,$username,$password_server,$dbname;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("UPDATE `account_system` SET `account_credit` = '0',`account_reset_time` = '".$time_update."' WHERE `account_system`.`account_id` = '".$id_account."'"); 
			$stmt->execute();
		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage()."add_history";
		}
		$conn = null;
		
	}
	function suspend_account_checked($id_account){
		global $servername,$username,$password_server,$dbname;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("UPDATE account_system SET account_active = 0 WHERE account_system.account_id = '".$id_account."'"); 
			$stmt->execute();
		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage()."add_history";
		}
		$conn = null;
		
	}
	function service_status_realtime($account_type,$status_code){
		global $servername,$username,$password_server,$dbname;
		$time_update = time() + 5;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("INSERT INTO `service_status` (`status_id`, `status_type`, `status_code`, `status_time`) VALUES (NULL, '".$account_type."', '".$status_code."', '".$time_update."')"); 
			$stmt->execute();
		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage()."add_history";
		}
		$conn = null;
		
	}
	function service_status_remove($account_type){
		global $servername,$username,$password_server,$dbname;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("DELETE FROM service_status WHERE service_status.status_type = '".$account_type."'"); 
			$stmt->execute();
		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage()."add_history";
		}
		$conn = null;
		
	}
	function service_status_check($account_type){
		global $servername,$username,$password_server,$dbname;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("SELECT * FROM service_status WHERE service_status.status_type = '".$account_type."'");
			$stmt->execute();
			$result = $stmt->fetch();
			return $result;
		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage()."add_history";
		}
		$conn = null;
		
	}
	function active_account_checked($account_type){
		$time_update = time();
		$getall_account = getall_account_checked($account_type);
		if($getall_account){
			foreach($getall_account as $getaccount => $account){
				if($account['account_reset_time'] < $time_update){
					global $servername,$username,$password_server,$dbname;
					try {
						$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
						$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						$stmt = $conn->prepare("UPDATE `account_system` SET `account_credit` = '100' WHERE `account_system`.`account_type` = '".$account_type."' AND `account_system`.`account_id` = '".$account['account_id']."'"); 
						$stmt->execute();
					}
					catch(PDOException $e) {
						//echo "Error: " . $e->getMessage()."add_history";
					}
					$conn = null;
				}
			}
		}
	}
	function getall_account_checked($account_type){
		global $servername,$username,$password_server,$dbname;
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_server);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("SELECT * FROM account_system WHERE account_type = '".$account_type."' AND account_active = '1'"); 
			$stmt->execute();
			$result = $stmt->fetchAll(); 
			return $result;
		}
		catch(PDOException $e) {
			//echo "Error: " . $e->getMessage()."add_history";
		}
		$conn = null;
		
	}
	/////////////////////////////////////////////////////////////////////////////////////////
	function login_ahrefx($email,$pass){
		global $config;
		$url_login = 'https://ahrefs.com/user/login';
		$cookie_login = token_ahrefx(true);
		var_dump($cookie_login);
		if(!$cookie_login['login']){
			$post_login = '_token='.$cookie_login['token'].'&email='.$email.'&password='.$pass.'';
			$login = curlx($url_login,$post_login);
		}
		return $post_login;
	}
	function curlx($url='',$var=''){
		global $config,$dir;
		$cookie_file = $dir . '/cookies/ahredfsx.com.txt';
		$curl = curl_init(); 
		curl_setopt($curl, CURLOPT_URL, $url); 
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']); 
		curl_setopt($curl, CURLOPT_REFERER, "https://ahrefs.com/"); 
		if($var) {
			curl_setopt($curl, CURLOPT_POST, true); 
			curl_setopt($curl, CURLOPT_POSTFIELDS, $var); 
		}
		curl_setopt($curl, CURLOPT_COOKIEFILE,$cookie_file); 
		curl_setopt($curl, CURLOPT_COOKIEJAR,$cookie_file); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
		$result = curl_exec($curl); 
		curl_close($curl);
		return $result;
	}
	function token_ahrefx($new_token = false){
		$get_ahref = tokens_server('ahrefs.com');
		if(!isset($get_ahref['token_value']) || $new_token){
			$url_extract = 'https://ahrefs.com/user/login';
			$load_extract = curlx($url_extract);
			echo $load_extract;
			exit;
			$token_ahref = fetch_value($load_extract,'<input name="_token" type="hidden" value="','">');
			if(empty($token_ahref)){
				$token_ahref = fetch_value($load_extract,'<meta name="_token" content="','">');
			}
			$login = false;
			if (strpos($load_extract, 'Sign out') !== false) {
				$login = true;
				tokens_server('ahrefs.com',$token_ahref);
			}
			return array('token' => $token_ahref, 'login' => $login);
		}else{
			return array('token' => $get_ahref['token_value'], 'login' => true);
		}
	}
	function realtime_service(){
		$service_status_check = service_status_check($account_type="ahrefs_v1");
		if($service_status_check){
			die("Tài khoản nguồn ahrefs.com trên link http://ahrefs1.tool.buyseotools.io hiện đang quá tải. Vui lòng thử lại sau ít phút! Hoặc bạn có thể check thông qua các đường link phía dưới để tiếp tục:<br>
				-------------------------------------------------------------------------------<br>
				http://ahrefs.tool.buyseotools.io<br>
				http://ahrefs2.tool.buyseotools.io<br>
				http://ahrefs3.tool.buyseotools.io<br><br>Cảm ơn!");
		}
	}
	function default_lang(){
	    curlx("https://ahrefs.com/site-explorer/ajax/set/default-language","language=en");
	}
	function test_account_checked(){
		$time_update = strtotime("tomorrow 07:00:00");
		echo time()."---------------";
		echo $time_update." ";
		echo date("Y-m-d h:i:s", $time_update);
	}

	function extractCookies($string) {

        $lines = explode(PHP_EOL, $string);

        foreach ($lines as $line) {

            $cookie = array();

            // detect httponly cookies and remove #HttpOnly prefix
            if (substr($line, 0, 10) == '#HttpOnly_') {
                $line = substr($line, 10);
                $cookie['httponly'] = true;
            } else {
                $cookie['httponly'] = false;
            } 

            // we only care for valid cookie def lines
            if( strlen( $line ) > 0 && $line[0] != '#' && substr_count($line, "\t") == 6) {

                // get tokens in an array
                $tokens = explode("\t", $line);

                // trim the tokens
                $tokens = array_map('trim', $tokens);

                // Extract the data
                $cookie['domain'] = $tokens[0]; // The domain that created AND can read the variable.
                $cookie['flag'] = $tokens[1];   // A TRUE/FALSE value indicating if all machines within a given domain can access the variable. 
                $cookie['path'] = $tokens[2];   // The path within the domain that the variable is valid for.
                $cookie['secure'] = $tokens[3]; // A TRUE/FALSE value indicating if a secure connection with the domain is needed to access the variable.

                $cookie['expiration-epoch'] = $tokens[4];  // The UNIX time that the variable will expire on.   
                $cookie['name'] = urldecode($tokens[5]);   // The name of the variable.
                $cookie['value'] = urldecode($tokens[6]);  // The value of the variable.

                // Convert date to a readable format
                $cookie['expiration'] = date('Y-m-d h:i:s', $tokens[4]);

                // Record the cookie.
                $cookies[] = $cookie;
            }
        }

        return $cookies;
    }