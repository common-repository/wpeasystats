<?php
	
	require_once(dirname(__FILE__) . '/../../../wp-config.php');
	@include_once("func.php");
	
	$options = get_options_fix();
	$googleExt = $options['pbl_googleex'];
	if($googleExt == '')
	{
		$googleExt = 'com';
	}
	nocache_headers();

	$url = get_option('home');
	$domain = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
	$pbl_table_keywords = $wpdb->prefix . "pbl_keywords";
	$pbl_table_partners = $wpdb->prefix . "pbl_partners";
	$pbl_table_stats = $wpdb->prefix . "pbl_stats";
	
	//goole PR
	$urlPR = 'http://www.google.com/search?client=navclient-auto&features=Rank:&q=info:' . $url . '&ch=' . CheckHash(HashURL($url));
	$pageSource = get_web_page($urlPR);
	$response = $pageSource['content'];
	$prParts = explode(":", $response);
	
	if(count($prParts) == 3)
	{
		$options['pbl_gpr'] = $prParts[2];
	}
	else
	{
		$options['pbl_gpr'] = 'N/A';
	}
	
	//google index
	$urlGI = 'http://www.google.'.$googleExt.'/search?source=ig&hl=en&rlz=&=&q=site:'.$domain.'&aq=f&oq=&aqi=';
	$pageSource = get_web_page($urlGI);
	$response = $pageSource['content'];
	
	$response = str_replace(',','',$response);
	$response = str_replace('.','',$response);
	
	$ptn = "/\s*\d+\s*res/";
	preg_match($ptn, $response, $matches);

	if(count($matches) > 0)
	{
		$options['pbl_gindex'] = trim($matches[0]," res");
	}
	else
	{
		$options['pbl_gindex'] = 'N/A';
	}
	
	//yahoo index
	$urlGI = 'http://siteexplorer.search.yahoo.com/search?p='.$domain.'&bwm=i&bwmo=d&bwmf=s';
	$pageSource = get_web_page($urlGI);
	$response = $pageSource['content'];
	
	$response = str_replace(',','',$response);
	$response = str_replace('.','',$response);
	
	$ptn = "/Pages\s*\(\s*\d+\s*\)/";
	preg_match($ptn, $response, $matches);
	if(count($matches) > 0)
	{
		$bl = trim($matches[0],"Pages() ");
		if(trim($bl) != '')
			$options['pbl_yindex'] = $bl;
		else
			$options['pbl_yindex'] = '0';
	}
	else
	{
		$options['pbl_yindex'] = 'N/A';
	}
	
	//yahoo back links
	$ptn = "/Inlinks\s*\(\s*\d+\s*\)/";
	preg_match($ptn, $response, $matches1);
	if(count($matches1) > 0)
	{
		$bl = trim($matches1[0],"Inlinks() ");
		if(trim($bl) != '')
			$options['pbl_ybackl'] = $bl;
		else
			$options['pbl_ybackl'] = '0';
	}
	else
	{
		$options['pbl_ybackl'] = 'N/A';
	}
	
	//bing index
	$urlGI = 'http://www.bing.com/search?q=site:'.$domain;
	$pageSource = get_web_page($urlGI);
	$response = $pageSource['content'];
	$ptn = "/of\s*\d+\s*res/";
	preg_match($ptn, $response, $matches);
	if(count($matches) > 0)
	{
		$bl = trim($matches[0],"of res");
		if(trim($bl) != '')
			$options['pbl_bindex'] = $bl;
		else
			$options['pbl_bindex'] = '0';
	}
	else
	{
		$options['pbl_bindex'] = 'N/A';
	}
	
	//keywords position
	$records = $wpdb->get_results("SELECT * FROM $pbl_table_keywords ORDER BY date,time limit 20");
	if ($records) {
		foreach ($records as $record) {
			$keywo = $record->keyword;
			$keyword = str_replace(" ","+",$keywo);
			$qurePos = $record->position;
			$mposition = googlePosition($keyword);
			$addoldpos = '';
			if($qurePos != $mposition['position'])
			{
				if($qurePos == '0'){$qurePos = '100';}
				$addoldpos = ",positionOld=".$qurePos;
			}
			
			$timestamp  = current_time('timestamp');
			$vdate  = gmdate("Ymd",$timestamp);
			$vtime  = gmdate("H:i:s",$timestamp);
			
			$updateKeyword = "update ".$pbl_table_keywords." set position = '".$mposition['position']."'".$addoldpos.",date = '".$vdate."', time = '".$vtime."', urlrequested='".$mposition['url']."' WHERE id = ".$record->id;
			$results = $wpdb->query($updateKeyword);
		}
	}
	
	//stats
	$today = date('Ymd');
	$dayofweek = date("w") + 1;
	$todaystatssql = "SELECT count(id) FROM ".$pbl_table_stats." WHERE date='".$today."' and robot = ''";
	$todaystats = $wpdb->get_var($todaystatssql);  
	$domain = GetDomain(get_bloginfo('url'));
	$urlGI = 'http://wpbacklinks.net/pbladmin/stats.php';
	$data = 'domain='.$domain.'&hits='.$todaystats."&dayofweek=".$dayofweek;
	$pageSource = get_web_page($urlGI,$data,false,true);
	
	// get partners
	if(is_active_widget('wp_pbl_WidgetShow') && $options["wp_pbl_category"] != '')
	{
		$del = "delete from $pbl_table_partners";
		$result = $wpdb->query($del);
				
		$urlGI = 'http://wpbacklinks.net/pbladmin/partners.php';
		$data = 'domain='.$domain.'&category='.$options["wp_pbl_category"].'&License='.$options["pbl_license"];
		$pageSource = get_web_page($urlGI,$data,false,true);
		$response = $pageSource['content'];
		if(trim($response) != '')
		{
			$links = explode("||", $response);
			
			foreach ($links as $oneLink) 
			{
				$pieces = explode("@@", $oneLink);
				if($pieces[3] == "1")
				{
					$insert = "INSERT INTO $pbl_table_partners SET siteid = ".$pieces[0].", keyword  = '".mysql_escape_string($pieces[1])."', url  = '".mysql_escape_string($pieces[2])."'";
					$result = $wpdb->query($insert);
				}
			}
		}
	}
	else
	{
		$urlGI = 'http://wpbacklinks.net/pbladmin/nowidget.php';
		$data = 'domain='.$domain;
		$pageSource = get_web_page($urlGI,$data,false,true);
	}
	
	update_option("pbl_options", serialize($options));

?>
