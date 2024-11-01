<?
function updateLic($newlicense) { 
	$url = parse_url(get_option('siteurl'));
	$request = 'http://wpbacklinks.net/pbladmin/lic.php?domain='.$url['host'].'&License='.$newlicense;
	$response = get_web_page($request);
	if( $response['content'] == "false") 
	{
		echo '<div class="updated"><p>'.__('Error: No record was found for the license you entered.', 'pbacklinks').'</p></div>';
		return $newlicense;
	}
	
	if( $response['content'] == "true") 
	{	
		$options = get_options_fix();		
		$options["pbl_license"] = $newlicense;
				
		update_option("pbl_options", serialize($options));	
		echo '<div class="updated"><p>'.__('License has been updated successfully.', 'pbacklinks').'</p></div>';
		return $options["pbl_license"];	
	}
	else
	{
		echo '<div class="updated"><p>'.__('Error: Comunication error to the license server, please try again.', 'pbacklinks').'</p></div>';
		return $newlicense;
	}
}

function checkUpdates() { 
	$qver = wpbl_get_version();
	$options = get_options_fix();
	$url = parse_url(get_option('siteurl'));
	$license = $options['pbl_license'];
	$request = 'http://wpbacklinks.net/pbladmin/checkupdate.php?qver='.$qver.'&domain='.$url['host'].'&License='.$license;
	$response = get_web_page($request);
	return $response['content'];
}

function getUpdates() { 
	$qver = wpbl_get_version();
	$options = get_options_fix();
	$url = parse_url(get_option('siteurl'));
	$license = $options['pbl_license'];
	$request = 'http://wpbacklinks.net/pbladmin/getupdate.php?qver='.$qver.'&domain='.$url['host'].'&License='.$license;
	$response = get_web_page($request);
	return $response['content'];
}

function register($domain,$user,$pass,$email,$type,$spec){

	if($type == 'scuttle')
		return scuttle($domain,$user,$pass,$email);
		
	if($type == 'phpdug')
		return phpdug($domain,$user,$pass,$email,$spec);
}

function phpdug($domain,$user,$pass,$email,$spec){
	$data = array(
		'username' => $user,
		'password' => $pass,
		'password2' => $pass,
		'email' => $email,
		'agree' => '1',
		'Submit' => 'Signup'
		);
	
	$pageSource = get_web_page("http://".$domain);
	$sourse = $pageSource['content'];
	$pos = strpos($sourse, 'Low Level Error');
	if ($pos !== false)
	{
		return 'Too many connections. Please try again later.';
	}
	if($spec == 1)
	{
		list($header, $content) = wpblPostRequest(
			"http://".$domain."/wpbsignup.php",    // the url to post to
			"http://".$domain."/wpbsignup.php",         // the url of the post script "this file"
			$data,''
		);
	}
	else
	{
		list($header, $content) = wpblPostRequest(
				"http://".$domain."/signup.php",    // the url to post to
				"http://".$domain."/signup.php",         // the url of the post script "this file"
				$data,''
			);
	}
	
	if($content == 'Connection error') {return 'Connection error.';}

	$pos = strpos($content, 'alphanumeric characters');
		if ($pos !== false)
		{
			return 'Username may only contain alphanumeric characters.';
		}
		
	$pos = strpos($content, 'Username must be between');
		if ($pos !== false)
		{
			return 'Username must be between 4 and 15 characters long.';
		}
		
	$pos = strpos($content, 'username is unavailable');
		if ($pos !== false)
		{
			return 'This username has been reserved, please make another choice.';
		}
	
	$pos = strpos($content, 'Password must be between');
		if ($pos !== false)
		{
			return 'Password must be between 4 and 15 characters long.';
		}
		
	$pos = strpos($content, 'not a valid email address');
		if ($pos !== false)
		{
			return 'E-mail address is not valid. Please try again.';
		}
		
	$pos = strpos($content, 'email address is unavailable');
		if ($pos !== false)
		{
			return 'E-mail address is unavailable. Please try again.';
		}
		
	$pos = strpos($content, 'Too many connections');
		if ($pos !== false)
		{
			return 'Too many connections. Please try again later.';
		}
		
	$pos = strpos($content, 'Error writing file');
		if ($pos !== false)
		{
			return 'Too many connections. Please try again later.';
		}	
		
	$pos = strpos($content, 'Low Level Error');
		if ($pos !== false)
		{
			return 'Too many connections. Please try again later.';
		}	
		
	$hederArea =  http_parse_headers($header);
	$hLocation = $hederArea["Location"];
	
	$pos = strpos($hLocation, '404');
	if ($pos !== false)
	{
		return 'Incorect URL';
	}
	
	if($spec == 1)
	{
		$pos = strpos($content, 'added');
		if ($pos !== false)
		{
			return 'registered';
		}
	}
	else
	{
		$pos = strpos($hLocation, 'index.php');
		if ($pos !== false)
		{
			return 'registered';
		}
	}
			
	return 'Registration failed. Please try again.'; 
		
}

function scuttle($domain,$user,$pass,$email){
		$data = array(
		'username' => $user,
		'password' => $pass,
		'email' => $email,
		'submitted' => 'Register'
		);

		list($header, $content) = wpblPostRequest(
			"http://".$domain."/register.php/",    // the url to post to
			"http://".$domain."/register.php/",         // the url of the post script "this file"
			$data,''
		);
		
		if($content == 'Connection error') {return 'Connection error.';}
		
		$pos = strpos($content, 'username has been reserved');
		if ($pos !== false)
		{
			return 'This username has been reserved, please make another choice.';
		}
		
		$pos = strpos($content, 'username already exists');
		if ($pos !== false)
		{
			return 'This username already exists, please make another choice.';
		}
		
		$pos = strpos($content, 'E-mail address is not valid');
		if ($pos !== false)
		{
			return 'E-mail address is not valid. Please try again.';
		}
		
		$pos = strpos($content, 'answer is not valid');
		if ($pos !== false)
		{
			return 'Antispam answer is not valid.';
		}	
		
		$pos = strpos($content, 'successfully registered');
		if ($pos !== false)
		{
			return 'registered';
		}
		
		$hederArea =  http_parse_headers($header);
		$hLocation = $hederArea["Location"];
		
		$pos = strpos($hLocation, '404');
		if ($pos !== false)
		{
			return 'Incorect URL';
		}
	
		$pos = strpos($hLocation, $user);
		if ($pos !== false)
		{
			return 'registered';
		}
				
		return 'Registration failed. Please try again.'; 
}

function wpblPostRequest($url, $referer='', $_data, $cooki) {

    // convert variables array to string:
    $data = array();    
    while(list($n,$v) = each($_data))
	{
        $data[] = urlencode($n)."=".urlencode($v);
    }    
    $data = implode('&', $data);

    // parse the given URL
    $url = parse_url($url);
    if ($url['scheme'] != 'http') 
	{ 
        die('Only HTTP request are supported !');
    }

    // extract host and path:
    $host = $url['host'];
    $path = $url['path'];

    // open a socket connection on port 80
    $fp = fsockopen($host, 80, $errno, $errstr, 30);
	if (!$fp) {
		return array($errstr, 'Connection error');
	}
    // send the request headers:
    fputs($fp, "POST $path HTTP/1.1\r\n");
    fputs($fp, "Host: $host\r\n");
	if ($referer != '')
	{
		fputs($fp, "Referer: $referer\r\n");
	}
    fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
	if($cooki != '')
	{
		fputs($fp, "Cookie: ".$cooki."\r\n");
	}
    fputs($fp, "Content-length: ". strlen($data) ."\r\n");
    fputs($fp, "Connection: close\r\n\r\n");
    fputs($fp, $data);

    $result = ''; 

    while(!feof($fp)) 
	{
        // receive the results of the request
		$line = fgetss($fp, 128);
        $result .= $line;
    }

    // close the socket connection:
    fclose($fp);

    // split the result header from the content
    $result = explode("\r\n\r\n", $result, 2);
    $header = isset($result[0]) ? $result[0] : '';
    $content = isset($result[1]) ? $result[1] : '';

    // return as array:
    return array($header, $content);
}

function http_parse_headers( $header ){
	$retVal = array();
	$fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
	foreach( $fields as $field ) {
		if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
			$match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
			if( isset($retVal[$match[1]]) ) {
				$retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
			} else {
				$retVal[$match[1]] = trim($match[2]);
			}
		}
	}
	return $retVal;
}

function doPost($domain,$user,$pass,$url,$title,$description,$tags,$type,$category,$spec){
	
	if($type == 'scuttle')
		return doPostScuttle($domain,$user,$pass,$url,$title,$description,$tags);
		
	if($type == 'phpdug')
		return doPostPhpdug($domain,$user,$pass,$url,$title,$description,$tags,$category,$spec);
		
	if(substr($type, 0,3) == 'RSS');
	{
		$typeParts = explode("#", $type);
		return doPostRSS($typeParts[3],$title,$typeParts[1],$typeParts[4],$user,$pass);
	}
}

function doPostScuttle($domain,$user,$pass,$url,$title,$description,$tags){
	$datalogin = array(
    'username' => $user,
    'password' => $pass,
	'submitted' => 'Log In'
	);
	
	$sourse = get_web_page("http://".$domain);
	$pos = strpos($sourse['content'], 'Low Level Error');
		if ($pos !== false)
		{
			return 'Too many connections. Please try again later.';
		}
		
	list($header, $content) = wpblPostRequest(
		"http://".$domain."/login.php",    // the url to post to
		"http://".$domain."/login.php",         // the url of the post script "this file"
		$datalogin,''
	);
	
	if($content == 'Connection error') {return 'Connection error.|Error';}
	
	$pos = strpos($header, '200 OK');
	if ($pos !== false)
	{
		return 'Wrong login data.|Error';
	}
	
	$pos = strpos($header, '404 Not Found');
	if ($pos !== false)
	{
		return 'URL of the resource not found.|Error';
	}

	$hederArea =  http_parse_headers($header);
	$hCookie = $hederArea["Set-Cookie"];
	$cookieParts = split(';',$hCookie);
	$oneCookie = split('=',$cookieParts[0]);

	$logCookie = $oneCookie[0].'='.$oneCookie[1];

	$data = array(
		'address' => $url,
		'title' => $title,
		'description' => $description,
		'tags' => $tags,
		'status' => '0',
		'submitted' => 'Add Bookmark'
	);

	list($header, $content) = wpblPostRequest(
		"http://".$domain."/bookmarks.php/".$user,    // the url to post to
		"http://".$domain."/bookmarks.php/".$user,         // the url of the post script "this file"
		$data,$logCookie
	);
	
	if($content == 'Connection error') {return 'Connection error.|Error';}
	
	$pos = strpos($content, 'Bookmark saved');
	if ($pos !== false)
	{
		return 'Bookmark saved';
	}
	
	$hederArea =  http_parse_headers($header);
	$hLocation = $hederArea["Location"];
	
	$urlParts = parse_url($url);
	$posMove = strpos($header, '302');
	$posLoc = strpos($hLocation, $urlParts['host']);
	
	if ($posMove !== false && $posLoc !== false)
	{
		return 'Bookmark saved';
	}
	
	return 'Error posting to the resource.|Error'; 
}

function doPostPhpdug($domain,$user,$pass,$url,$title,$description,$tags,$category,$spec){
	
	$options = get_options_fix();		
	$zlicense = $options["pbl_license"];
		
	$datalogin = array(
    'username' => $user,
    'password' => $pass,
	'redirect' => '',
	'Submit' => 'Login'
	);
	
	if($spec == 1)
	{
		list($header, $content) = wpblPostRequest(
			"http://".$domain."/wpblogin.php",    // the url to post to
			"http://".$domain."/wpblogin.php",         // the url of the post script "this file"
			$datalogin,''
		);
	}
	else
	{
		list($header, $content) = wpblPostRequest(
			"http://".$domain."/login.php",    // the url to post to
			"http://".$domain."/login.php",         // the url of the post script "this file"
			$datalogin,''
		);
	}
	if($content == 'Connection error') {return 'Connection error.|Error';}

	$pos = strpos($content, 'Too many connections');
	if ($pos !== false)
	{
		return 'Too many connections. Please try again later.|Error';
	}
	
	if($spec == 1)
	{
		$pos = strpos($content, 'loginerror');
		if ($pos !== false)
		{
			return 'Wrong login data.|Error';
		}
	}
	else
	{
		$pos = strpos($header, '200 OK');
		if ($pos !== false)
		{
			return 'Wrong login data.|Error';
		}
	}
	
	$pos = strpos($header, '404 Not Found');
	if ($pos !== false)
	{
		return 'URL of the resource not found.|Error';
	}
	
	if($spec == 1)
	{	
		$data = array(
			'story_url' => $url,
			'story_title' => $title,
			'story_desc' => $description,
			'story_category' => $category,
			'dupe' => '1',
			'username' => $user,
			'password' => $pass,
			'password' => $pass,
			'zlicense' => $zlicense,
			'zdomain' => $domain,
			'Submit' => 'Submit Story'
		);
		
		list($header, $content) = wpblPostRequest(
			"http://".$domain."/wpbadd_story.php",    // the url to post to
			"http://".$domain."/wpbadd_story.php",         // the url of the post script "this file"
			$data,''
		);
		
		$pos = strpos($content, 'nolicense');
		if ($pos !== false)
		{
			return 'Wrong license.|Error';
		}
		
		$pos = strpos($content, 'urlerror');
		if ($pos !== false)
		{
			return 'Wrong URL format.|Error';
		}
		
		$pos = strpos($content, 'exist');
		if ($pos !== false)
		{
			return 'Post already exist.|Error';
		}
		
		$pos = strpos($content, 'titleshort');
		if ($pos !== false)
		{
			return 'The title of the post must be more then 4 characters long.|Error';
		}
		
		$pos = strpos($content, 'nocat');
		if ($pos !== false)
		{
			return 'Post without a category.|Error';
		}
		
		$pos = strpos($content, 'descshort');
		if ($pos !== false)
		{
			return 'The content of the post must be more then 10 characters long.|Error';
		}
		
		$pos = strpos($content, 'wronguser');
		if ($pos !== false)
		{
			return 'Wrong login data.|Error';
		}
		
		$pos = strpos($content, 'Bookmark saved');
		if ($pos !== false)
		{
			return 'Bookmark saved';
		}
		
		return 'Error posting to the resource.|Error';
	}
	else
	{
		$hederArea =  http_parse_headers($header);
		$hCookie = $hederArea["Set-Cookie"];
		$cookieParts = split(';',$hCookie);
		$oneCookie = split('=',$cookieParts[0]);

		$logCookie = $oneCookie[0].'='.$oneCookie[1];
		
		$data = array(
			'story_url' => $url,
			'story_title' => $title,
			'story_desc' => $description,
			'story_category' => $category,
			'dupe' => '1',
			'Submit' => 'Submit Story'
		);

		list($header, $content) = wpblPostRequest(
			"http://".$domain."/add_story.php",    // the url to post to
			"http://".$domain."/add_story.php",         // the url of the post script "this file"
			$data,$logCookie
		);
		
		if($content == 'Connection error') {return 'Connection error.|Error';}
		
		$hederArea =  http_parse_headers($header);
		$hLocation = $hederArea["Location"];
		
		$urlParts = parse_url($url);
		$posMove = strpos($header, '302');
		$posLoc = strpos($hLocation, 'index.php');
		
		if ($posMove !== false && $posLoc !== false)
		{
			return 'Bookmark saved';
		}
	}
	
	return 'Error posting to the resource.|Error'; 
	
}

function doPostRSS($post_id, $feed_name, $aggregator_id, $permLink,$user,$pass) {

	$GLOBALS['wpbl']['postID'] = $post_id;
	$GLOBALS['wpbl']['feedName'] = $feed_name;
	$GLOBALS['wpbl']['permLink'] = $permLink;
	$GLOBALS['wpbl']['username'] = $user;
	$GLOBALS['wpbl']['password'] = $pass;
	$GLOBALS['config']['usercookie'] = array();
	$GLOBALS['config']['header'] = array("Accept" => 'image/png,image/jpeg,image/gif;q=0.2,*/*;q=0.1', "Accept-Language" => 'en-us,en;q=0.5', "ACCEPT-CHARSET" => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7', "KEEP-ALIVE" => '300', "Connection" => 'Close');


	$ag = new WPBL_Aggregator();
	$ag->init();

	$aggregator = $ag->get_aggregator($aggregator_id);

	$fail = false; 
	$steps = $aggregator['step'];

	$tmp = array();
	for ($key = 0; $key < count(wpbl_to_array($steps)); $key++) {
		$tmp[] = intval($steps[$key]['order']);
	}
	sort($tmp, SORT_NUMERIC);

	foreach ($tmp as $k) {
		$step = $ag->get_aggregator_step_by_order($aggregator['id'], $k);
		$url = $step['url'];

		$which_form = array('formIDType' => $url['@attributes']['formIDType'], 'formIDValue' => $url['@attributes']['formIDValue'], 'method' => $url['@attributes']['method'] );
		$success_regx = $step['successRegx'];
		$params = wpbl_to_array($step['params']);

		if (count($params) > 1) { 
			$profile_index = rand(0, count($params) - 1);
		} else {
			$profile_index = 0;
		}

		$params = wpbl_to_array($params[$profile_index]['param']);

		if (strcmp((string)$url['@content'], '[auto]') == 0) {
			$result = wpbl_process_form($result['url'], $result['html'], $params, $success_regx, $which_form);
		} else {
			$result = wpbl_process_request($url['@content'], $params, $success_regx, $which_form);
		}

		if (empty($result)) {
			$fail = true;
			break;
		}
	}
	
	if ($fail) {
		return 'Error posting to the resource.|Error';
	} else {
		return 'Bookmark saved';
	}
}
?>
