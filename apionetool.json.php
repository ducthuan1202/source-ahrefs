<?php
	header('Content-Type: application/json');
	error_reporting(0);
	//error_reporting(E_ALL | E_STRICT);
	//ini_set('display_errors', 1);
	set_time_limit(0);


	$dir_parent = __DIR__;
	$today = date("Ymd");

	$config['cookie_file'] = $dir_parent . '/cookies/'.md5($today).'.txt';
	$config['useragent'] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.75 Safari/537.36";

	if(isset($_GET["method"]) && isset($_GET["target"]) && isset($_GET["uri"])){

		$method = trim($_GET["method"]);
		$target = trim($_GET["target"]);
		$uri = trim($_GET["uri"]);
		$url_explorer = 'https://app.ahrefs.com/'.$uri.'?target='.$target;
		$header_data = getallheaders();
		$csrf_token = $header_data['X-Csrf-Token'];
		$headers = [
			"Host: app.ahrefs.com",
			"X-CSRF-Token: ".$csrf_token."",
			"X-Requested-With: XMLHttpRequest",
			"Referer: ".$url_explorer."",
			"Connection: keep-alive",
			"If-Modified-Since: *"
		];

		///////////////////////////////////////
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

		header('Content-Type: application/json');
		echo $site_explorer;

	}

	