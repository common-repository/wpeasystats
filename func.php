<?php
@include_once("lic.php");
define('COOKIEJAR', dirname(__FILE__) . '/includes/cookiejar.dat');
define('BROWSER', 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; en-US; rv:1.5) Gecko/20031026 Firebird/0.7');
define('CURL_PREFERRED', 1);
define('DUMP_DIR', dirname(__FILE__) . '/includes/debug');
define('DEBUG', 3);

function pbl_check_updates() {

	$options = get_options_fix();

	$pbl_rversion = $options["pbl_rversion"];
   
	$version = checkUpdates();

	if($version != 'old')
	{
		if($options['pbl_license'] == '')
		{
			$pro = ' <a style="color:#cc0000;" href="http://wpbacklinks.net/landing.html" target="_blank"><b>Get Pro Version, 100+ Resources!</b></a>';
		}
		?>
		<div style="float:right;margin-top: 25px;">Version <?php echo $pbl_rversion; ?><?php if($pbl_rversion != $version) {?> - <a style="color:#cc0000;" href="admin.php?page=pbl-sites&getupdates=true"><b><?php _e("Get Updates!","wpeasystats") ?></b></a><?php } echo ' '.$pro; ?>
		</div>
	<?php
	}
	else
	{
		echo '<div style="float:right;margin-top: 25px;"><a style="color:#cc0000;" href="plugins.php" target="_blank"><b>You are using old version of the plugin, Please update to newer version.</b></a></div>';
	}
}

function get_options_fix(){
	$opt = get_option("pbl_options");
	if(is_array($opt))
		return $opt;
	else
		return unserialize(get_option("pbl_options"));
}

function pbl_check_unique_domain($domain) {
	global $wpdb,$pbl_table_sites;
	$domain = $wpdb->escape($domain);
	$check = $wpdb->get_var("SELECT domain FROM $pbl_table_sites WHERE domain = '$domain' ");

	if($check != false) {
		return true;
	} else {
		return false;			
	}
}

function pbl_check_unique($unique) {
	global $wpdb,$pbl_table_posts;
	$unique = $wpdb->escape($unique);
	$check = $wpdb->get_var("SELECT unique_id FROM ".$pbl_table_posts." WHERE unique_id LIKE '$unique' ");

	if($check != false) {
		return $check;
	} else {
		return false;			
	}
}

function pbl_delete_site() {
   global $wpdb, $pbl_table_sites;

	$delete = $_POST["delete"];
	$array = implode(",", $delete);

	foreach ($_POST['delete']  as $key => $value) {
		$i = $value;
		$sql = "SELECT * FROM " . $pbl_table_sites . " WHERE id = '$i' LIMIT 1";
		$result = $wpdb->get_row($sql);	

		$cr_interval = $result->cinterval;	
		$cr_period = $result->period;	
	
		$delete = "update ".$pbl_table_sites." set deleted = 1 WHERE id = $i";
		$results = $wpdb->query($delete);
	}	
	if ($results) {
		echo '<div class="updated"><p>'.__('Resources has been deleted.', 'pbacklinks').'</p></div>';
	}
}

function pbl_delete_keyword() {
	global $wpdb, $pbl_table_keywords;
	$domain = GetDomain(get_bloginfo('url'));
	
	foreach ($_POST['delete']  as $key => $value) {
		$i = $value;
		
		$record = $wpdb->get_row("SELECT keyword FROM $pbl_table_keywords where id=".$i);  
		$keyword = $record->keyword;

		$urlGI = 'http://wpbacklinks.net/pbladmin/deletekeyword.php';
		$data = 'domain='.$domain.'&keyword='.$keyword;
		$pageSource = get_web_page($urlGI,$data,false,true);
		
		$delete = "delete from ".$pbl_table_keywords." WHERE id = $i";
		$results = $wpdb->query($delete);
	}	
	if ($results) {
		echo '<div class="updated"><p>'.__('Keyword has been deleted.', 'pbacklinks').'</p></div>';
	}
}

function pbl_import_keywords() {
   global $wpdb, $pbl_table_keywords, $pbl_table_stats;
	
	$records = $wpdb->get_results("select distinct search from ".$pbl_table_stats." where search <> ''");
	
	if ($records) {
		foreach ($records as $record) {
			$results = $wpdb->query("INSERT IGNORE INTO $pbl_table_keywords SET keyword = '".$record->search."'");
		}
		echo '<div class="updated"><p>'.__('Keyword has been imported.', 'pbacklinks').'</p></div>';
	}
	else
		echo '<div class="updated"><p>'.__('No keywords to import.', 'pbacklinks').'</p></div>';

}

function pbl_register_site() {
	global $wpdb, $pbl_table_sites, $pbl_table_errors;
	
	$options = get_options_fix();

	foreach ($_POST['delete']  as $key => $value) {
		$i = $value;
		$sql = "SELECT * FROM " . $pbl_table_sites . " WHERE id = '$i' LIMIT 1";
		$result = $wpdb->get_row($sql);	
		$dontAdd = 0;
		
		if(substr($result->ctype, 0,3) == 'RSS')
		{
			$dontAdd = 1;
		}
	
		if($result->username == '' && $dontAdd == 0)
		{
			$catArray = array( 
						"27" => "Adult", 
						"8" => "Automative", 
						"15" => "Blogs",
						"14" => "Computers & Internet",
						"9" => "Education",
						"6" => "Entertainment",
						"12" => "Finance",
						"10" => "Food & Drink",
						"19" => "Games",
						"7" => "Gaming",
						"24" => "Health",
						"16" => "Home & Garden",
						"13" => "Hotels & Resorts",
						"17" => "Legal",
						"20" => "Music",
						"11" => "News",
						"26" => "Others",
						"18" => "Real Estate",
						"2" => "Science",
						"22" => "Shopping & Product",
						"21" => "Society & Culture",
						"4" => "Sports",
						"1" => "Technology",
						"25" => "Travels",
						"5" => "Videos",
						"3" => "World & Business"
					  );
						
						
			$user =  $options['pbl_defusername'];
			$pass =  $options['pbl_defpassword'];
			$email =  $options['pbl_defemail'];
			$category =  array_search($options['pbl_defcategory'], $catArray);
				
			$domain = $result->domain;
			$type =  $result->ctype;
			$spec =  $result->spec;
			 		
			if($user == '' || $pass == '' || $email == '' || $category == '')
			{
				echo '<div class="updated"><p>'.__('Please fill Username, Password, E-Mail and Category in the Options page.', 'pbacklinks').'</p></div>';
			}
			else
			{
				$hook = register($domain,$user,$pass,$email,$type,$spec);
				if ($hook == 'registered') {
					$endreg = "update ".$pbl_table_sites." set username = '".$user."', password = '".$pass."',email = '".$email."',category = ".$category.", pause = 0 WHERE id = ".$i;
					$results = $wpdb->query($endreg);	
				} else {
					$endreg = "insert into ".$pbl_table_errors." set site = ".$i.", sitename = '".$result->name."', title = '', myurl = '', message = '".$hook."', reason = 'Error'";
					$result = $wpdb->query($endreg);			
				}
			}
		}
	}	

	echo '<div class="updated"><p>'.__('Resources has been registered.', 'pbacklinks').'</p></div>';

}

function pbl_getSiteName($id) {
   global $wpdb, $pbl_table_sites;

		$sql = "SELECT name FROM " . $pbl_table_sites . " WHERE id = '$id'";
		$result = $wpdb->get_row($sql);	

		return $result->name;	
		
}

function trim_excerpt_without_filters($text,$max,$stratfrom,$striptags) {

	$startPos = 0;
	
	if($stratfrom != '')
	{
		$pos = strpos($text, $stratfrom);
		if ($pos != false) 
		{
			$text = substr($text, $pos + strlen($stratfrom) + 1);
		}
	}
		
	$text = str_replace(']]>', ']]&gt;', $text);
	$text = strip_selected_tags($text,$striptags,true);
	$text = strip_tags($text);

	if ($max < strlen($text)) {
		while($text[$max] != ' ' && $max > 1) {
			$max--;
		}
	}
	
	$text = substr($text, 0, $max);
	return trim(stripcslashes($text));
}

function strip_selected_tags($str, $tags = "", $stripContent = false){
    preg_match_all("/<([^>]+)>/i", $tags, $allTags, PREG_PATTERN_ORDER);
    foreach ($allTags[1] as $tag) {
        $replace = "%(<$tag.*?>)(.*?)(<\/$tag.*?>)%is";
        $replace2 = "%(<$tag.*?>)%is";
        if ($stripContent) {
            $str = preg_replace($replace,' ',$str);
            $str = preg_replace($replace2,' ',$str);
        }
            $str = preg_replace($replace,'${2}',$str);
            $str = preg_replace($replace2,'${2}',$str);
    }
    return $str;
} 

function convert_datetime($str){
	list($date, $time) = explode(' ', $str);
	list($year, $month, $day) = explode('-', $date);
	list($hour, $minute, $second) = explode(':', $time);
	
	$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
	
	return $timestamp;
}

function ConvertStrToInt($Str, $Check, $Magic){
    $Int32Unit = 4294967296;  // 2^32

    $length = strlen($Str);
    for ($i = 0; $i < $length; $i++) {
        $Check *= $Magic; 	
        if ($Check >= $Int32Unit) {
            $Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit));
        }
        $Check += ord($Str{$i}); 
    }
    return $Check;
}

function HashURL($String){
    $Check1 = ConvertStrToInt($String, 0x1505, 0x21);
    $Check2 = ConvertStrToInt($String, 0, 0x1003F);

    $Check1 >>= 2; 	
    $Check1 = (($Check1 >> 4) & 0x3FFFFC0 ) | ($Check1 & 0x3F);
    $Check1 = (($Check1 >> 4) & 0x3FFC00 ) | ($Check1 & 0x3FF);
    $Check1 = (($Check1 >> 4) & 0x3C000 ) | ($Check1 & 0x3FFF);	
	
    $T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) <<2 ) | ($Check2 & 0xF0F );
    $T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000 );
	
    return ($T1 | $T2);
}

function CheckHash($Hashnum){
    $CheckByte = 0;
    $Flag = false;

    $HashStr = sprintf('%u', $Hashnum) ;
    $length = strlen($HashStr);
	
    for ($i = $length - 1;  $i >= 0;  $i --) {
        $Re = $HashStr{$i};
        if ($Flag) {              
            $Re += $Re;     
            $Re = (int)($Re / 10) + ($Re % 10);
        }
        $CheckByte += $Re;
        $Flag = !$Flag;	
    }

    $CheckByte %= 10;
    if (0 !== $CheckByte) {
        $CheckByte = 10 - $CheckByte;
        if ($Flag) {
            if (1 === ($CheckByte % 2)) {
                $CheckByte += 9;
            }
            $CheckByte >>= 1;
        }
    }

    return '7'.$CheckByte.$HashStr;
}

function GetDomain($url){
$nowww = ereg_replace('www\.','',$url);
$domain = parse_url($nowww);
if(!empty($domain["host"]))
    {
     return $domain["host"];
     } else
     {
     return $domain["path"];
     }
}

function REQUEST_URL() {
    $urlRequested = (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '' );
	if ( $urlRequested == "" ) { 
	    $urlRequested = (isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '' );
	}
	if(substr($urlRequested,0,2) == '/?') { $urlRequested=substr($urlRequested,2); }
	if($urlRequested == '/') { $urlRequested=''; }
	return $urlRequested;
}

function isrobot($agent = null){
    $agent=str_replace(" ","",$agent);
	$key = null;
	$lines = file(ABSPATH.'wp-content/plugins/'.dirname(plugin_basename(__FILE__)).'/db/robots.dat');
	foreach($lines as $line_num => $robot) {
		list($nome,$key)=explode("|",$robot);
		if(strpos($agent,$key)===FALSE) continue;
		# trovato
		return $nome;
	}
	return null;
}

function isfeed($url) {
	if (stristr($url,get_bloginfo('rdf_url')) != FALSE) { return 'RDF'; }
	if (stristr($url,get_bloginfo('rss2_url')) != FALSE) { return 'RSS2'; }
	if (stristr($url,get_bloginfo('rss_url')) != FALSE) { return 'RSS'; }
	if (stristr($url,get_bloginfo('atom_url')) != FALSE) { return 'ATOM'; }
	if (stristr($url,get_bloginfo('comments_rss2_url')) != FALSE) { return 'COMMENT'; }
	if (stristr($url,get_bloginfo('comments_atom_url')) != FALSE) { return 'COMMENT'; }
	if (stristr($url,'wp-feed.php') != FALSE) { return 'RSS2'; }
	if (stristr($url,'/feed/') != FALSE) { return 'RSS2'; }
	return '';
}

function getOS($arg){
    $arg=str_replace(" ","",$arg);
	$lines = file(ABSPATH.'wp-content/plugins/'.dirname(plugin_basename(__FILE__)).'/db/os.dat');
	foreach($lines as $line_num => $os) {
		list($nome_os,$id_os)=explode("|",$os);
		if(strpos($arg,$id_os)===FALSE) continue;
    	return $nome_os; // riconosciuto
	}
    return '';
}

function getBrowser($arg){
    $arg=str_replace(" ","",$arg);
	$lines = file(ABSPATH.'wp-content/plugins/'.dirname(plugin_basename(__FILE__)).'/db/browser.dat');
	foreach($lines as $line_num => $browser) {
		list($nome,$id)=explode("|",$browser);
		if(strpos($arg,$id)===FALSE) continue;
    	return $nome; // riconosciuto
	}
    return '';
}

function getQueryString($url){
	$parsed_url = parse_url($url);
	$tab=parse_url($url);
	$host = $tab['host'];
	if(key_exists("query",$tab)){
	 $query=$tab["query"];
	 return explode("&",$query);
	}
	else{return null;}
}

function getSE($referrer = null){
	$key = null;
	$lines = file(ABSPATH.'wp-content/plugins/'.dirname(plugin_basename(__FILE__)).'/db/searchengines.dat');
	foreach($lines as $line_num => $se) {
		list($nome,$url,$key)=explode("|",$se);
		if(strpos($referrer,$url)===FALSE) continue;
		# trovato se
		$variables = getQueryString(html_entity_decode($referrer));
		$i = count($variables);
		while($i--){
		   $tab=explode("=",$variables[$i]);
			   if($tab[0] == $key){return ($nome."|".urldecode($tab[1]));}
		}
	}
	return null;
}

function getLanguage($accepted) {
	return substr($accepted,0,2);
}

function gethdate($dt = "00000000") {
	return mysql2date(get_option('date_format'), substr($dt,0,4)."-".substr($dt,4,2)."-".substr($dt,6,2));
}

function getDistribution($fld,$fldtitle,$limit = 0,$param = "", $queryfld = "", $exclude= "") {
	global $wpdb, $pbl_table_stats;
	if($limit > 10) {$cartlimit = 10;} else {$cartlimit = $limit;}
	if ($queryfld == '') { $queryfld = $fld; }
	$resp =  "<div class='wrap'><table class='widefat'><thead><tr><th scope='col' style='width:400px;'><h2>$fldtitle</h2></th><th scope='col' style='width:100px;'>".__('Visits','statpress')."</th><th></th></tr></thead>";
	$rks = $wpdb->get_var("SELECT count($param $queryfld) as rks FROM $pbl_table_stats WHERE 1=1 $exclude;"); 
	if($rks > 0) {
		$sql="SELECT count($param $queryfld) as pageview, $fld FROM $pbl_table_stats WHERE 1=1 $exclude GROUP BY $fld ORDER BY pageview DESC";
		if($limit > 0) { $sql=$sql." LIMIT $limit"; }
		$qry = $wpdb->get_results($sql);
	    $tdwidth=450;
		
		// Collects data
		$data=array();
		foreach ($qry as $rk) {
			$pc=round(($rk->pageview*100/$rks),1);
			if($fld == 'nation') { $rk->$fld = strtoupper($rk->$fld); }
			if($fld == 'date') { $rk->$fld = gethdate($rk->$fld); }
			if($fld == 'urlrequested' || $fld == 'referrer') { $rk->$fld = getStatPressDecode($rk->$fld); }
        	$data[substr($rk->$fld,0,100)]=$rk->pageview;
		}
		
		$sql="SELECT count($param $queryfld) as pageview, $fld FROM $pbl_table_stats WHERE 1=1 $exclude GROUP BY $fld ORDER BY pageview DESC";
		if($limit > 0) { $sql=$sql." LIMIT $cartlimit"; }

		$qry = $wpdb->get_results($sql);
		
		// Collects data for chart
		$dataChart=array();
		foreach ($qry as $rk) {
			$pc=round(($rk->pageview*100/$rks),1);
			if($fld == 'nation') { $rk->$fld = strtoupper($rk->$fld); }
			if($fld == 'date') { $rk->$fld = gethdate($rk->$fld); }
			if($fld == 'urlrequested' || $fld == 'referrer') { $rk->$fld = getStatPressDecode($rk->$fld); }
        	$dataChart[substr($rk->$fld,0,100)]=$rk->pageview;
		}
	}

	// Draw table body
	$resp .= "<tbody id='the-list'>";
	if($rks > 0) {  // Chart!
		if($fld == 'nation') {
			$chart=getGoogleGeo("","",$dataChart);
		} else {
			$chart=getGoogleChart("","500x200",$dataChart);
		}
		$resp .= "<tr><td></td><td></td><td rowspan='".($limit+2)."'>$chart</td></tr>";
		foreach ($data as $key => $value) {
    	   	$resp .= "<tr><td style='width:500px;overflow: hidden; white-space: nowrap; text-overflow: ellipsis;'>".$key;
        	$resp .= "</td><td style='width:100px;text-align:center;'>".$value."</td>";
			$resp .= "</tr>";
		}
	}
	$resp .= "</tbody></table></div><br>\n";
	
	return $resp;
}

function getStatPressAbbrevia($s,$c) {
	$res=""; if(strlen($s)>$c) { $res="..."; }
	return substr($s,0,$c).$res;
}

function getStatPressDecode($out_url) {
	if($out_url == '') { $out_url=__('Page','statpress').": Home"; }
	if(substr($out_url,0,4)=="cat=") { $out_url=__('Category','statpress').": ".get_cat_name(substr($out_url,4)); }
	if(substr($out_url,0,2)=="m=") { $out_url=__('Calendar','statpress').": ".substr($out_url,6,2)."/".substr($out_url,2,4); }
	if(substr($out_url,0,2)=="s=") { $out_url=__('Search','statpress').": ".substr($out_url,2); }
	if(substr($out_url,0,2)=="p=") {
		$post_id_7 = get_post(substr($out_url,2), ARRAY_A);
		$out_url = $post_id_7['post_title'];
	}
	if(substr($out_url,0,8)=="page_id=") {
		$post_id_7=get_page(substr($out_url,8), ARRAY_A);
		$out_url = __('Page','statpress').": ".$post_id_7['post_title'];
	}
	return  utf8_urldecode($out_url);
}

function googlePosition($keyword){
	
	$options = get_options_fix();
	$googleExt = $options['pbl_googleex'];
	
	$enURL = 'http://www.google.'.$googleExt.'/search?q='.$keyword.'&ie=UTF-8&num=100';
	$pageSource = get_web_page($enURL);
	$html = $pageSource['content'];

	$myDomain = GetDomain(get_option( 'siteurl' ));

	$dom = new domDocument; 
	$dom->strictErrorChecking = false; 
	$dom->preserveWhiteSpace = true; 
	@$dom->loadHTML($html); 
	$lists=$dom->getElementsByTagName('li');

	$num=0;
				
	foreach ($lists as $list)
	{
		unset($ar);unset($divs);unset($div);unset($cont);unset($result);unset($tmp);
		$result['main_keyword']=$main_keyword;
		$result['sub_keyword']=$keyword;
		$ar=pbldom2array_full($list);
		if (count($ar) < 2) 
		{
			//echo "S";
			continue; // skipping advertisement and similar spam
		}
				if ((!isset($ar['class'])) || ($ar['class'] != 'g')) 
		{
			//echo "?";
			continue; // skipping non-search results
		}
		
		$divs=$list->getElementsByTagName('div');
		$div=$divs->item(0);
		pblgetContent($cont,$div);	
		$num++;
		if($googleExt != 'co.il')
		{
			$result['title']=&$ar['h3']['a']['textContent'];
			$tmp=strstr(&$ar['h3']['a']['@attributes']['href'],"http");
			$result['url']=$tmp;
			//if (strstr(&$ar['h3']['a']['@attributes']['href'],"interstitial")) echo "!";
			
			$tmp=parse_url(&$result['url']);
			$result['host']=&$tmp['host'];
			if (strstr($cont,"<br ></br><cite >")) // remove some dirt behind the description
			{
				$result['desc']=substr($cont,0,strpos($cont,"<br ></br><cite >"));
			} else
				$result['desc']=$cont;
		}
		else
		{
			$result['title']=&$ar[div][div]['h3']['a']['textContent'];
			$tmp=strstr(&$ar[div][div]['h3']['a']['@attributes']['href'],"http");
			$result['url']=$tmp;
			
			$tmp=parse_url(&$result['url']);
			$result['host']=&$tmp['host'];
			if (strstr($cont,"<br ></br><cite >")) // remove some dirt behind the description
			{
				$result['desc']=substr($cont,0,strpos($cont,"<br ></br><cite >"));
			} else
				$result['desc']=$cont;
		}
				
		$results[]=$result; // This adds the result to our large result array

	}

	$resultsNew=array();

	foreach ($results as $result)
	{
		$serp=array(
				"Rank"            => $Rank,
				"Url"            => $result[url],
				"Title"            => trim(html_entity_decode(strip_tags($result[title]))),
				"Host"            => $result[host],
				"Protocol"        => $Http,
				"Path"            => $Rel,
				"Summary"        => trim(html_entity_decode(strip_tags($result[desc]))),
			);

		if(trim($result[host]) != '')
		{
			$posg = strrpos($result[host], 'google');
			if($posg == false)
			{
				array_push($resultsNew,$serp);
			}
			
		}
	}

	$myPosition['position'] = 0;
	$myPosition['url'] = '';
	$urlPosition = 0;
	
	foreach ($resultsNew as $k => $v) 
	{
		$urlPosition++ ;
		$b = $resultsNew[$k];
		
		$pos = strrpos($b[Host], $myDomain);
		
		if(!is_bool($pos))
		{
			$myPosition['position'] = $urlPosition;
			$myPosition['url'] = $b[Url];
			break;
		}
	}

	return $myPosition;
}

function pbldom2array_full($node){

    $result = array();
    if($node->nodeType == XML_TEXT_NODE) 
    {
    	$result = $node->nodeValue;
    } else 
    {
    	if($node->hasAttributes()) 
    	{
    		$attributes = $node->attributes;
    		if((!is_null($attributes))&&(count($attributes))) 
    			foreach ($attributes as $index=>$attr) 
    		  	$result[$attr->name] = $attr->value;
    	}
    	if($node->hasChildNodes())
    	{
    		$children = $node->childNodes;
    		for($i=0;$i<$children->length;$i++) 
    		{
    			$child = $children->item($i);
    			if($child->nodeName != '#text')
    			if(!isset($result[$child->nodeName]))
    				$result[$child->nodeName] = pbldom2array($child);
    			else 
    			{
    				$aux = $result[$child->nodeName];
    				$result[$child->nodeName] = array( $aux );
    				$result[$child->nodeName][] = pbldom2array($child);
    			}
    		}
    	}
    }
    return $result;
} 

function pbldom2array($node){

  $res = array();
  if($node->nodeType == XML_TEXT_NODE)
  {
  	$res = $node->nodeValue;
  } else
  {
  	if($node->hasAttributes())
  	{
  		$attributes = $node->attributes;
  		if(!is_null($attributes))
  		{
  			$res['@attributes'] = array();
  			foreach ($attributes as $index=>$attr) 
  			{
  				$res['@attributes'][$attr->name] = $attr->value;
  			}
  		}
  	}
  	if($node->hasChildNodes())
  	{
  		$children = $node->childNodes;
  		for($i=0;$i<$children->length;$i++)
  		{
  			$child = $children->item($i);
  			$res[$child->nodeName] = pbldom2array($child);
  		}
  		$res['textContent']=$node->textContent;
  	}
  }
  return $res;
}

function pblgetContent($NodeContent="",$nod){  
  
	$NodList=$nod->childNodes;
	for( $j=0 ;  $j < $NodList->length; $j++ )
	{ 
		$nod2=$NodList->item($j);
		$nodemane=$nod2->nodeName;
		$nodevalue=$nod2->nodeValue;
		if($nod2->nodeType == XML_TEXT_NODE)
		    $NodeContent .= $nodevalue;
		else
		{     $NodeContent .= "<$nodemane ";
		   $attAre=$nod2->attributes;
		   foreach ($attAre as $value)
		      $NodeContent .= "{$value->nodeName}='{$value->nodeValue}'" ;
		    $NodeContent .= ">";                    
		    pblgetContent($NodeContent,$nod2);                    
		    $NodeContent .= "</$nodemane>";
		}
	}
   
}

function get_web_page($url, $data='',$get=true,$getheaders=false) {
	if (wpbl_is_curl_preferred()) {
		return get_web_page_curl($url, $data, $get, $getheaders);
	} else {
		
		$header = array();
		$last_url = '';
		if($get)
		{
			return wpbl_request_url_php_nocurl($url, 'GET', $data, $header, $last_url);
		}
		else
		{
			return wpbl_request_url_php_nocurl($url, 'POST', $data, $header, $last_url);
		}
	}
}

function wpbl_request_url_php_nocurl($url, $method, $post_vars, &$returned_headers, &$last_url, $max_redirect = 10){
	$redirect = 0;
	$post_vars_array = explode("&",trim($post_vars));
	do {
		$returned_headers = array();
		$last_url = '';
		$need_redirect = false; // akb & rrb

		$u = @parse_url($url);
		if ($u == false) return false;  // not a valid url
		if (empty($u['port'])) $u['port'] = 80;

		$fp = fsockopen($u['host'], $u['port']);
		if (!$fp) return false; // connection fail
		
		$uri = (empty($u['path'])?'/':$u['path']) . (empty($u['query'])?'': '?' . $u['query']);

		$request = "";
		$request .= strtoupper($method) . " $uri HTTP/1.1\r\n";
		$request .= 'Host: ' . $u['host'] . ':' . $u['port'] . "\r\n";

		// add cookies header from cookie file
		$cookies = wpbl_parse_cookie_file_to_cookies();
		$cook_str = 'Cookie: ';
		foreach ($cookies as $k => $v) {
			$cook_str .= "{$k}={$v};";
		}

		// add user cookies
		if (!empty($GLOBALS['config']['usercookie'])) {
			foreach ($GLOBALS['config']['usercookie'] as $k => $v) {
				$cook_str .= "{$k}={$v};";
			}
		}

		$request .= "{$cook_str}\r\n";

		// add other header
		$request .= "User-Agent: " . BROWSER . "\r\n";
		if (!empty($GLOBALS['config']['header'])) {
			foreach ($GLOBALS['config']['header'] as $k => $v) {
				$canonicalName = implode('-', array_map('ucfirst', explode('-', $k)));
				$request .= trim($canonicalName) . ': ' . $v . "\r\n";  // very much top1
			}
		}

		// add referer header
		if (!empty($GLOBALS['referer'])) {
			$request .= "Referer: {$GLOBALS['referer']}\r\n";
		}

		// method is post
		if (strtoupper($method) == 'POST')	{
			$request .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$postdata = wpbl_parse_post_array_flat($post_vars_array);
			$request .= 'Content-Length: ' . strlen($postdata) . "\r\n";
		}

		$request .= "\r\n";  // header end

		// body content
		if (strtoupper($method) == 'POST')	{
			$request .= $postdata;
		}

		// send the request
		if (fwrite($fp, $request) === false) { // error while send the request
			fclose($fp);
			return false;
		}

		// read response
		$rsp = '';
		while(!feof($fp)) {
			$rsp .= fread($fp,4096);
		}
		fclose($fp);

		wpbl_dump_url_to_file($url, $rsp);

		// process the response
		$hunks = explode("\r\n\r\n",trim($rsp));
		$body    = "";
		for ($i=1; $i<count($hunks); $i++)
		{
			$body = $body.trim($hunks[$i]);
		}
		
		$headers = explode("\n", $hunks[0]);
		$headersFlat = $hunks[0];

		// get response code
		if (!preg_match('!^(HTTP/\d\.\d) (\d{3})(?: (.+))?!', $headers[0], $s)) {
			return false;
		} else {
			$response_code = intval($s[2]);
			array_shift($headers);
		}
		if (preg_match('|Transfer-encoding\s*:\s*chunked|im', $hunks[0])) { // unchunk body
			$body = trim(wpbl_unchunkHttpResponse($body));
		}
		// deal with cookies
		$cookie_header = wpbl_parse_cookie_file_to_cookies();
		foreach( $headers as $response ) {
			$head = explode(':', $response, 2);
			$head = array_map('trim', $head);

			// Check for cookies
			if ( strtolower($head[0]) == 'set-cookie' ) {
				$cookies = explode(';', $head[1]);
				foreach( $cookies as $cookie ) {
					$cook = explode( '=', trim($cookie), 2 );
					if ( isset($cook[0]) ) {
						switch( trim(strtolower($cook[0])) ) {
							case 'expires':
							case 'path':
								break;
							default:
								$cookie_header[ $cook[0] ] = $cook[1];
								break;
						}
					}
				}
			} else {
				if ( count($head) == 2 ) {
					if ( !array_key_exists($head[0], $returned_headers) )
					$returned_headers[ ucfirst($head[0]) ] = array();
					$returned_headers[ ucfirst($head[0]) ][] = ltrim($head[1]);
				}
			}
		}

		// write cookies to cookie file
		file_put_contents( COOKIEJAR, serialize($cookie_header) );

		$last_url = $url;
		$GLOBALS['referer'] = $url;

		// if response code is a redirect
		if ($response_code > 300 && $response_code < 399 && ++$redirect < $max_redirect && !empty($returned_headers['Location'])) { //redirect
			$need_redirect = true;
			$url = wpbl_get_url_relative($url, $returned_headers['Location'][0]);
			$method = 'GET';
			$post_vars_array = array();
		}
	} while ($need_redirect);

	if ($response_code >= 200 && $response_code < 300) {
	
		$header['errno']   = $err;
		$header['errmsg']  = $errmsg;
		$header['content'] = $body;
		$header['headers'] = $headersFlat;
		return $header;
	} else {
		return false;
	}
}

function get_web_page_curl( $url, $data='',$get=true,$getheaders=false ){
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => $getheaders,    // don't return headers
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "spider", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
		CURLOPT_POSTFIELDS	   => $data,
		CURLOPT_HTTPGET		   => $get,
    );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
	if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off'))
	{
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects
	}
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $info  = curl_getinfo( $ch );

	if($getheaders)
	{
		$headers = substr($content, 0, $info['header_size']);
		$content = substr($content, $info['header_size']);
	}
	
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
	$header['headers'] = $headers;
    return $header;
}

function utf8_urldecode($str) {
    $str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
    return html_entity_decode($str,null,'UTF-8');
  }
  
 // add slashes to html if magic quotes is not on
function atf_slashit($stringvar){
    if (!get_magic_quotes_gpc()){
        $stringvar = addslashes($stringvar);
    }
    return $stringvar;
}
// remove slashes if magic quotes is on
function atf_deslashit($stringvar){
    if (1 == get_magic_quotes_gpc()){
        $stringvar = stripslashes($stringvar);
    }
    return $stringvar;
}

function wpbl_process_request($url, $params, $success_regx, $which_form = array()) {

    $header = array();
    $last_url = '';

    $html = wpbl_fetch_url($url, $header, $last_url);

    if (empty($html)) {
        return false;
    }

    return wpbl_process_form($last_url, $html, $params, $success_regx, $which_form);
}

function wpbl_fetch_url($url, &$returned_headers, &$last_url) {
	if (wpbl_is_curl_preferred()) {
		return wpbl_fetch_url_curl($url, $returned_headers, $last_url);
	} else {
		return wpbl_request_url_php($url, 'GET', array(), $returned_headers, $last_url);
	}
}

function wpbl_fetch_url_curl( $url, &$returned_headers, &$last_url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);
	if (CURL_PREFERRED == 1) {
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  //use curl_redir_exec when open_basedir is set
	}
	curl_setopt($ch, CURLOPT_MAXREDIRS,10);
	curl_setopt($ch, CURLOPT_USERAGENT, BROWSER);
	curl_setopt($ch, CURLOPT_HTTPGET, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	if (isset($GLOBALS['referer'])) {
		curl_setopt($ch, CURLOPT_REFERER, $GLOBALS['referer']);
	}
	if (!empty($GLOBALS['config']['header'])) {
		$httpheader = array();
		foreach ($GLOBALS['config']['header'] as $k => $v) {
			$httpheader[] = trim($k) . ': ' . $v;
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
	}

	curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIEJAR);
	curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIEJAR);
	if (CURL_PREFERRED == 1) {
		$txt = curl_exec($ch);
	}
	else {
		$txt = wpbl_curl_redir_exec($ch);
	}

	$GLOBALS['referer'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	$last_url = $GLOBALS['referer'];

	curl_close($ch);

	return $txt;
}

function wpbl_curl_redir_exec($ch) {
	

static $curl_loops = 0;
static $curl_max_loops = 20;
if ($curl_loops++>= $curl_max_loops)
{

	$curl_loops = 0;
	return FALSE;
}

curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($ch);
list($header, $data) = explode("\n\n", $data, 2);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($http_code == 301 || $http_code == 302)
{

	$matches = array();
	preg_match('/Location:(.*?)\n/', $header, $matches);
	$url = @parse_url(trim(array_pop($matches)));
	if (!$url)
	{
		//couldn't process the url to redirect to
		$curl_loops = 0;
		return $data;
	}
	$last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
	if (!$url['scheme'])
	$url['scheme'] = $last_url['scheme'];
	if (!$url['host'])
		$url['host'] = $last_url['host'];
	if (!$url['path'])
		$url['path'] = $last_url['path'];
	$new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ($url['query']?'?'.$url['query']:'');
	curl_setopt($ch, CURLOPT_URL, $new_url);
	return wpbl_curl_redir_exec($ch);
} else {
	$curl_loops=0;
	return $data;

	}
}

function wpbl_parse_cookie_file_to_cookies(){
	if ( file_exists(COOKIEJAR) ) {
		$cookies = unserialize(file_get_contents(COOKIEJAR));
	}
	return empty($cookies)?array():$cookies;
}

function wpbl_parse_post_array($post_data) {
	$o="";
	foreach ($post_data as $k=>$v) {
		$o.= "$k=".urlencode(utf8_encode($v[0]))."&";
	}

	return substr($o,0,-1);
}

function wpbl_parse_post_array_flat($post_data) {
	$o="";
	foreach ($post_data as $oneParam) {
		$param_parts = explode("=",$oneParam);
		$o.= $param_parts[0]."=".urlencode(utf8_encode($param_parts[1]))."&";
	}

	return substr($o,0,-1);
}

function wpbl_unchunkHttpResponse($str=null) {
	if (!is_string($str) or strlen($str) < 1) { return false; }
	$eol = "\r\n";
	$add = strlen($eol);
	$tmp = $str;
	$str = '';

	do {
		$tmp = ltrim($tmp);
		$pos = strpos($tmp, $eol);
		if ($pos === false) { return false; }
		$len = hexdec(substr($tmp, 0, $pos));
		if (!is_numeric($len) or $len < 0) { return false; }
		$str .= substr($tmp, ($pos + $add), $len);
		$tmp  = substr($tmp, ($len + $pos + $add));
		$check = trim($tmp);
	} while(!empty($check));

	unset($tmp);
	return $str;
}

function wpbl_get_url_relative( $url, $lrs ) {
	$url  = parse_url($url);
	$path = explode  ('/', $url['path']);
	$lrs  = trim($lrs);
	$new_url = $url['scheme'] . '://';
	if ( !empty($url['user']) ) {
		$new_url .= $url['user'];
		if ( !empty($url['password']) )
			$new_url .= ':' . $url['password'];
		$new_url .= '@';
	}
	$new_url .= $url['host'];
	if ( $lrs{0} == '/' ) {
		// Full path...
		return $new_url . $lrs;
	} else if ( strpos($lrs, '://') !== false && strpos(substr($lrs, 0,  strpos($lrs, '://')), '?') === false ) {
		return $lrs;
	} else {
		if ( substr($new_url, -1) == '/' )
			return $new_url .= $lrs;
		else
			return $new_url .= '/' . $lrs;
	}
}

function wpbl_is_curl_preferred() {
	if (defined('CURL_PREFERRED') && CURL_PREFERRED && extension_loaded('curl')) {
		return true;
	}
	return false;
}

function wpbl_process_form($last_url, $html, $params, $success_regx, $which_form = array()) {
    $header = array();

	if ($which_form['formIDType'] == 'url') 
	{
		$html = wpbl_fetch_url($which_form['formIDValue'],$header,$last_url);

		if (empty($html) || !preg_match($success_regx, $html)) 
		{
			return false;
		}
		else
		{
			return array('url' => $last_url, 'html' => $html);
		}
	}
	
    $forms = wpbl_parse_forms($html);

    $found = false;

    if (!empty($which_form)) {
        foreach ($forms as $index => $form) {
            if ($which_form['formIDType'] == 'name' && $form['form']['name'] == $which_form['formIDValue']) {
                $found = true;  // arbor
                break;
            } elseif ($which_form['formIDType'] == 'index' && $index == $which_form['formIDValue']) {
                $found = true;
                break;
            } elseif ($which_form['formIDType'] == 'id' && $form['form']['id'] == $which_form['formIDValue']) {
                $found = true;
                break;
            } 
        }
    }
    if (!$found) {
        $form = $forms[0];
    } 

    for ($j = 0; $j < count($params); $j++) {
        // parameter type is callback function
        if (strcmp((string)$params[$j]['@attributes']['type'], 'callbackFunction') == 0) {
            if (preg_match('|(.+)\((.*)\)|', $params[$j]['value'], $matches)) { // function with parameters and 5108
                $function_name = $matches[1];
                $function_args = array_map('trim', explode(',', $matches[2]));
            } else { // function without parameters
                $function_name = $params[$j]['value'];
                $function_args = array();
            }

            $values[(string)$params[$j]['name']] = call_user_func($function_name, array('postID' => $GLOBALS['wpbl']['postID'], 'url' => $last_url, 'html' => $html, 'feed_name' => $GLOBALS['wpbl']['feedName'], 'permLink' => $GLOBALS['wpbl']['permLink'], 'extra_args' => $function_args));
        } else { // parameter type is value
            $values[(string)$params[$j]['name']] = $params[$j]['value'];
        }
    }
	
    wpbl_build_postvars($form, $postvars, $values, $postfiles);

    $return_url = '';

    if (empty($form['form']['action'])) {
    	$form['form']['action'] = $last_url;
    }
    $url = wpbl_get_url_relative($last_url, $form['form']['action']);

    $html = wpbl_submit_url($url, $postvars, $header, $return_url, $which_form['method']);

    if (empty($html) || !preg_match($success_regx, $html)) {
		return false;
    }

    return array('url' => $return_url, 'html' => $html);
}

function wpbl_parse_forms( $text ) {
	$forms = array();
	$page_counter = 0;

	// remove comments
	$text = preg_replace( '|<!--.*-->|iUms', '', $text );

	// remove javascript
	$text = preg_replace( '|<script.*</script>|iUms', '', $text );

	while ( $page_counter < strlen($text) ) {
		$form = wpbl_get_string_between( '<form', '</form', $text, $page_counter, false, true, true );
		if ( !empty($form) ) {
			$tag_name = '';
			$cur_pos  = 0;
			$form_arr = array( 'fields' => array() );
			while ( $cur_pos < strlen($form) ) {
				$attribs = wpbl_get_all_attributes_from_tag( $form, $cur_pos, $tag_name );

				switch( $tag_name ) {
					case 'form':
						$form_arr[$tag_name] = $attribs;
						break;

					case 'input':
						$form_arr['fields'][] = $attribs;
						break;

					case 'textarea':
						$attribs['value'] = wpbl_get_string_between('', '</textarea', $form, $cur_pos, false, false, false);
						$attribs['type'] = 'textarea';
						$form_arr['fields'][] = $attribs;
						break;

					case 'select':
						$attribs['choices'] = wpbl_parse_options( $form, $cur_pos );
						$attribs['type'] = 'select';
						$form_arr['fields'][] = $attribs;
						break;
				}
				$tag_name = '';
			}
			$forms[] = $form_arr;
		}
	}
	return $forms;
}

define('SEARCH_START_ATTRIBUTE', 1);
define('SEARCH_END_ATTRIBUTE'  , 2);
define('SEARCH_FIND_EQUAL'     , 3);
define('SEARCH_START_VALUE'    , 4);
define('SEARCH_END_VALUE'      , 5);

function wpbl_get_string_between_sub($search, $from, $start_pos, &$pos1, &$search1, $case_sensitive) {
	if ( !is_array($search) ) $search = array($search);

	$pos1_curr    = false;
	$search1_curr = '';
	$pos1         = false;
	$search1      = '';
	foreach( $search as $search1_curr ) {
		$pos1_curr = call_user_func( $case_sensitive ? 'strpos' : 'stripos', $from, $search1_curr, $start_pos );
		if ( $pos1_curr === false ) {
			continue;
		} else if ( $pos1 === false or $pos1_curr < $pos1 ) {
			$pos1    = $pos1_curr;
			$search1 = $search1_curr;
		}
	}
}

function wpbl_get_string_between($s1_arr, $s2_arr, $from, &$start_pos, $case_sensitive = false, $including_outsides = false, $stop_if_not_found = true, $add_search2_length = true) {
	wpbl_get_string_between_sub($s1_arr, $from, $start_pos, $pos1, $search1, $case_sensitive);
	wpbl_get_string_between_sub($s2_arr, $from, $pos1 === false ? $start_pos : $pos1 + strlen($search1), $pos2, $search2, $case_sensitive);

	if ( $pos1 !== false ) {
		if ( !$including_outsides )
		$pos1 += strlen($search1);
	} else if ( $stop_if_not_found ) {
		$start_pos = strlen($from);
		return '';
	} else {
		$pos1 = $start_pos;
	}

	if ( $pos2 !== false ) {
		if ( $add_search2_length )
			$start_pos = $pos2 + strlen($search2);
		if ( $including_outsides )
			$pos2 += strlen($search2);
	} else if ( $stop_if_not_found ) {
		$start_pos = strlen($from);  // top1 r-p-p
		return '';
	} else {
		$start_pos = strlen($from);
		$pos2 = strlen($from);
	}
	return substr( $from, $pos1, $pos2 - $pos1 );
}

function wpbl_is_space( $c ) {
	settype($c, 'string');
	return in_array($c{0}, array(" ", "\t", "\r", "\n"));
}

function wpbl_compare_by_name($x, $y) {
    if ( strtolower($x['name']) == strtolower($y['name']) )
        return 0;
    else if ( strtolower($x['name']) < strtolower($y['name']) )
        return -1;
    else
        return 1;
}

function wpbl_compare_by_title($x, $y) {
    if ( strtolower($x['title']) == strtolower($y['title']) )
        return 0;
    else if ( strtolower($x['title']) < strtolower($y['title']) )
        return -1;
    else
        return 1;
}

function wpbl_get_all_attributes_from_tag( $txt, &$start_from, &$tag_name ) {
	$retVal = array();
	if ( $tag_name == '' ) {
		// We don't know what tag it is... Discover...
		while( true ) {
			$pos = strpos($txt, '<', $start_from);
			if ( $pos === false ) {
				$start_from = strlen($txt);
				return $retVal;
			}
			if ( $txt{$pos+1} == '/' ) {// Closing tag... Skip till next '>'
				$pos = strpos($txt, '>', $pos);
				if ( $pos === false ) {
					$start_from = strlen($txt);
					return $retVal;
				}
				$start_from = $pos + 1;
			} else {
				$start_from = $pos + 1;
				break;
			}
		}
		for ( $i = $start_from, $tot = strlen($txt); $i < $tot; $i++ ) {
			$start_from = $i;

			if ( wpbl_is_space($txt{$i}) )
				break;
			else if ( '>' == $txt{$i} )
				break;
			else if ( '/' == $txt{$i} and '>' == $txt{$i+1} )
				break;

			$tag_name .= $txt{$i};
		}
	}
	$tag_name	= strtolower(trim($tag_name));
	$status		= SEARCH_START_ATTRIBUTE;
	$quote		= '';
	$attribute	= '';
	$value		= '';
	for ( $i = $start_from, $tot = strlen($txt); $i < $tot; $i++ ) {
		switch( $txt{$i} ) {
			// Possible end of tag
			case '>':
				switch( $status ) {
					case SEARCH_END_VALUE:
						if ( !empty($quote) ) {
							$value .= $txt{$i};
							break;
						}
						// else --> fall through
					case SEARCH_START_ATTRIBUTE:
					case SEARCH_START_VALUE:
					case SEARCH_FIND_EQUAL:
					case SEARCH_END_ATTRIBUTE:
						$start_from = $i + 1;
						break 3;
				}
				break;

			// whitespace
			case " ":
			case "\r":
			case "\n":
			case "\t":
				switch( $status ) {
					case SEARCH_START_ATTRIBUTE:
					case SEARCH_START_VALUE:
					case SEARCH_FIND_EQUAL:
						break;

					case SEARCH_END_ATTRIBUTE:
						// we're at the end!
						$status = SEARCH_FIND_EQUAL;
						break;

					case SEARCH_END_VALUE:
						if ( empty($quote) ) { // we reached the end... 
							$retVal[ $attribute ] = $value;
							$status    = SEARCH_START_ATTRIBUTE;
							$attribute = '';
							$quote     = '';
							$value     = '';
						} else {
							$value .= $txt{$i};
						}
						break;
				}
				break;

			case "'":
			case '"':
				switch( $status ) {
					case SEARCH_START_ATTRIBUTE:  // Wrong place...
						break; // Don't do anything, just continue searching

					case SEARCH_FIND_EQUAL:       // Wrong place...
					case SEARCH_END_ATTRIBUTE:    // Wrong place...
						$status = SEARCH_END_VALUE; // Interpret it as if the '=' is already passed
						$value  = '';
						$quote  = $txt{$i};
						break;

					case SEARCH_START_VALUE:  // Ok, search the end...
						$status = SEARCH_END_VALUE;
						$value  = '';
						$quote  = $txt{$i};
						break;

					case SEARCH_END_VALUE:
						if ( $quote == $txt{$i} and $txt{$i-1} != "\\" ) {
							$retVal[ $attribute ] = $value;
							$status    = SEARCH_START_ATTRIBUTE;
							$attribute = '';
							$quote     = '';
							$value     = '';
						} else { // escaped quote
							if ( $quote == $txt{$i} and $txt{$i-1} == "\\" )
							$value = substr($value, 0, -1);
							$value .= $txt{$i};
						}
						break;
				}
				break;

			case '=':
				switch( $status ) {
					case SEARCH_START_ATTRIBUTE:  // Wrong place...
					case SEARCH_START_VALUE:
						die('Wrong equal sign!');
						break;

					case SEARCH_FIND_EQUAL:
					case SEARCH_END_ATTRIBUTE:
						$status = SEARCH_START_VALUE;
						$value = '';
						$quote = '';
						break;

					case SEARCH_END_VALUE:
						$value .= $txt{$i};
						break;
				}
				break;

			default:
				switch( $status ) {
					case SEARCH_START_ATTRIBUTE:
						$status    = SEARCH_END_ATTRIBUTE;
						$attribute = $txt{$i};
						$value     = '';
						$quote     = '';
						break;

					case SEARCH_START_VALUE:
						$status    = SEARCH_END_VALUE;
						$value     = $txt{$i};
						$quote     = '';
						break;

					case SEARCH_FIND_EQUAL:
						if ( !empty($attribute) )
							$retVal[$attribute] = $attribute;
						$status    = SEARCH_END_ATTRIBUTE;
						$attribute = $txt{$i};
						$value     = '';
						$quote     = '';
						break;

					case SEARCH_END_ATTRIBUTE:
						$attribute .= $txt{$i};
						break;

					case SEARCH_END_VALUE:
						$value .= $txt{$i};
						break;
				}
				break;
		}
	}
	if ( $status == SEARCH_END_VALUE and !empty($attribute) )
		$retVal[$attribute] = $value;

	return $retVal;
}

function wpbl_parse_options( $text, &$cur_pos ) {
	$select     = wpbl_get_string_between('', '</select', $text, $cur_pos, false, false, false);
	$options    = array();
	$optgroup   = '';
	$select_pos = 0;
	while ( $select_pos < strlen($select) ) {
		$tag = '';
		$option = wpbl_get_all_attributes_from_tag($select, $select_pos, $tag);
		if ( !empty($tag) ) {
			if ( $tag == 'optgroup' ) {
				if ( !isset($option['label']) ) {
					//					echo "Wrong 'label' here!" ;
				}
				$option_group_text = wpbl_get_string_between('', '</optgroup', $select, $select_pos, false, false, false);
				$option_group_text_pos = 0;
				while ( $option_group_text_pos < strlen($option_group_text) ) {
					$val = wpbl_parse_real_options($option_group_text, $option_group_text_pos);
					if ( $val !== false )
						$options[ $option['label'] ][] = $val;
				}
			} else if ( $tag != 'option' ) {
				//				echo  'Wrong tag here!' ;
			} else {
				$option['text'] = trim(wpbl_get_string_between('', array('<option', '</option'), $select, $select_pos, false, false, false, false));
				$options[''][]  = $option; // m-t-a-a-k-j
			}
		}
	}
	return $options;
}

function wpbl_parse_real_options( $text, &$cur_pos, $tag = '' ) {
	$option = wpbl_get_all_attributes_from_tag($text, $cur_pos, $tag);
	if ( empty($tag) ) {
		return false;
	} else if ( $tag != 'option' ) {
		die ( 'parse_real_options: wrong tag' );
	}

	$option['text'] = trim(wpbl_get_string_between('', array('<option', '</option'), $text, $cur_pos, false, false, false, false));
	return $option;
}

function wpbl_build_postvars( $parsed_form, &$postvars, $defaults=array(), &$postfiles ) {
	$postvars = array();
	$postfiles = array();

	if ( is_null($parsed_form) )
		return;

	foreach( $parsed_form['fields'] as $idx => $field ) {
		if ( !isset($field['type']) )
			$field['type'] = 'text';

		switch( strtolower(trim($field['type'])) ) {
			case 'file':
				if ( isset($defaults[ $field['name'] ]) )
					$postfiles[ $field['name'] ][] = $defaults[ $field['name'] ];
				else
					$postfiles[ $field['name'] ][] = '';
				break;

			case 'radio':
			case 'checkbox':
				$is_checked = false;
				foreach( $field as $k => $v ) {
					if ( strtolower(trim($k)) == 'checked' ) {
						$is_checked = true;
						break;
					}
				}

				if ( isset($defaults[$field['name']]) ) {
					$postvars[ $field['name'] ] = array($defaults[$field['name']]);
				} else if ( $is_checked ) {
					if ( strtolower(trim($field['type'])) == 'radio' ) {
						$postvars[ $field['name'] ] = array( isset($field['value']) ? $field['value'] : 'on' );
					} else {
						$postvars[ $field['name'] ][] = isset($field['value']) ? $field['value'] : 'on';
					}
				}
				break;

			case 'select':
				if ( isset($field['choices']) ) {
					// Get the selected choice
					$is_multi_select = false;
					foreach( $field as $k => $v ) {
						if ( strtolower(trim($k)) == 'multiple' ) {
							$is_multi_select = true;
							break;
						}
					}
					$is_selected = false;
					$first_option = null;
					foreach( $field['choices'] as $optgroup => $options ) {
						foreach( $options as $idx => $option ) {
							if ( is_null($first_option) )
								$first_option = $option;

							foreach( $option as $k => $v ) {
								if ( strtolower(trim($k)) == 'selected' ) {
									if ( !isset($defaults[$field['name']]) )
										$postvars[ $field['name'] ][] = isset($field['value']) ? $field['value'] : $field['text'];
									else
										$postvars[ $field['name'] ] = array($defaults[$field['name']]);

									$is_selected = true;

									// Stop if we don't allow multiple selections
									if ( !$is_multi_select )
										break 3;
								}
							}
						}
					}

					if ( !$is_selected ) {
						if ( isset($defaults[$field['name']]) )
							$postvars[ $field['name'] ] = array($defaults[$field['name']]);
						else if ( !is_null($first_option) )
							$postvars[ $field['name'] ][] = isset($first_option['value']) ? $first_option['value'] : $first_option['text'];
						else
							$postvars[ $field['name'] ][] = '';
					}

					break;
				}
				// else --> fall down

			case 'hidden':
			case 'text':
			case 'password':
			case 'input':
			case 'textarea':
			case 'image':
			case 'submit':
				if ( !isset($defaults[$field['name']]) )
					$postvars[ $field['name'] ][] = isset($field['value']) ? $field['value'] : '';
				else
					$postvars[ $field['name'] ] = array($defaults[$field['name']]);
				break;

			default:
				//				var_dump($field);
				//				echo 'Wrong field' ;
				//				break;
		} // switch
	} // foreach
}

function wpbl_submit_url($url, $postvars, &$returned_headers, &$last_url,$method="POST") {

	if (wpbl_is_curl_preferred()) {
		return wpbl_submit_url_curl($url, $postvars, $returned_headers, $last_url);
	} else {
		return wpbl_request_url_php($url, $method, $postvars, $returned_headers, $last_url);
	}
}

function wpbl_submit_url_curl( $url, $postvars, &$returned_headers, &$last_url ) {

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);
	if (CURL_PREFERRED == 1) {
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  //use curl_redir_exec when open_basedir is set
	}
	curl_setopt($ch, CURLOPT_MAXREDIRS,10);
	curl_setopt($ch, CURLOPT_USERAGENT, BROWSER);
	curl_setopt($ch, CURLOPT_HTTPGET, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POSTFIELDS, wpbl_parse_post_array($postvars));

	if (isset($GLOBALS['referer'])) {
		curl_setopt($ch, CURLOPT_REFERER, $GLOBALS['referer']);
	}
	if (!empty($GLOBALS['config']['header'])) {
		$httpheader = array();
		foreach ($GLOBALS['config']['header'] as $k => $v) {
			$httpheader[] = trim($k) . ': ' . $v;
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
	}
	//	add_user_cookie($ch);

	curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIEJAR);
	curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIEJAR);

	if (CURL_PREFERRED == 1) {
		$txt = curl_exec($ch);
	}
	else {
		$txt = wpbl_curl_redir_exec($ch);
	}

	$GLOBALS['referer'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	$last_url = $GLOBALS['referer'];

	curl_close($ch);
	
//echo $txt.'<br>';
//print_r($postvars);
//echo '<br>===========================================================================================================================';

	return $txt;
}

function wpbl_request_url_php($url, $method, $post_vars, &$returned_headers, &$last_url, $max_redirect = 10){
	$redirect = 0;

	do {
		$returned_headers = array();
		$last_url = '';
		$need_redirect = false; // akb & rrb

		$u = @parse_url($url);
		if ($u == false) return false;  // not a valid url
		if (empty($u['port'])) $u['port'] = 80;

		$fp = fsockopen($u['host'], $u['port']);
		if (!$fp) return false; // connection fail
		
		$uri = (empty($u['path'])?'/':$u['path']) . (empty($u['query'])?'': '?' . $u['query']);

		$request = "";
		$request .= strtoupper($method) . " $uri HTTP/1.1\r\n";
		$request .= 'Host: ' . $u['host'] . ':' . $u['port'] . "\r\n";

		// add cookies header from cookie file
		$cookies = wpbl_parse_cookie_file_to_cookies();
		$cook_str = 'Cookie: ';
		foreach ($cookies as $k => $v) {
			$cook_str .= "{$k}={$v};";
		}

		// add user cookies
		if (!empty($GLOBALS['config']['usercookie'])) {
			foreach ($GLOBALS['config']['usercookie'] as $k => $v) {
				$cook_str .= "{$k}={$v};";
			}
		}

		$request .= "{$cook_str}\r\n";

		// add other header
		$request .= "User-Agent: " . BROWSER . "\r\n";
		if (!empty($GLOBALS['config']['header'])) {
			foreach ($GLOBALS['config']['header'] as $k => $v) {
				$canonicalName = implode('-', array_map('ucfirst', explode('-', $k)));
				$request .= trim($canonicalName) . ': ' . $v . "\r\n";  // very much top1
			}
		}

		// add referer header
		if (!empty($GLOBALS['referer'])) {
			$request .= "Referer: {$GLOBALS['referer']}\r\n";
		}

		// method is post
		if (strtoupper($method) == 'POST')	{
			$request .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$postdata = wpbl_parse_post_array($post_vars);
			$request .= 'Content-Length: ' . strlen($postdata) . "\r\n";
		}

		$request .= "\r\n";  // header end

		// body content
		if (strtoupper($method) == 'POST')	{
			$request .= $postdata;
		}

		// send the request
		if (fwrite($fp, $request) === false) { // error while send the request
			fclose($fp);
			return false;
		}

		// read response
		$rsp = '';
		while(!feof($fp)) {
			$rsp .= fread($fp,4096);
		}
		fclose($fp);
		
		wpbl_dump_url_to_file($url, $rsp);

		// process the response
		$hunks = explode("\r\n\r\n",trim($rsp));
		$body    = "";
		for ($i=1; $i<count($hunks); $i++)
		{
			$body = $body.trim($hunks[$i]);
		}
		$headers = explode("\n", $hunks[0]);

		// get response code
		if (!preg_match('!^(HTTP/\d\.\d) (\d{3})(?: (.+))?!', $headers[0], $s)) {
			return false;
		} else {
			$response_code = intval($s[2]);
			array_shift($headers);
		}
		if (preg_match('|Transfer-encoding\s*:\s*chunked|im', $hunks[0])) { // unchunk body
			$body = trim(wpbl_unchunkHttpResponse($body));
		}
		// deal with cookies
		$cookie_header = wpbl_parse_cookie_file_to_cookies();
		foreach( $headers as $response ) {
			$head = explode(':', $response, 2);
			$head = array_map('trim', $head);

			// Check for cookies
			if ( strtolower($head[0]) == 'set-cookie' ) {
				$cookies = explode(';', $head[1]);
				foreach( $cookies as $cookie ) {
					$cook = explode( '=', trim($cookie), 2 );
					if ( isset($cook[0]) ) {
						switch( trim(strtolower($cook[0])) ) {
							case 'expires':
							case 'path':
								break;
							default:
								$cookie_header[ $cook[0] ] = $cook[1];
								break;
						}
					}
				}
			} else {
				if ( count($head) == 2 ) {
					if ( !array_key_exists($head[0], $returned_headers) )
					$returned_headers[ ucfirst($head[0]) ] = array();
					$returned_headers[ ucfirst($head[0]) ][] = ltrim($head[1]);
				}
			}
		}

		// write cookies to cookie file
		file_put_contents( COOKIEJAR, serialize($cookie_header) );

		$last_url = $url;
		$GLOBALS['referer'] = $url;

		// if response code is a redirect
		if ($response_code > 300 && $response_code < 399 && ++$redirect < $max_redirect && !empty($returned_headers['Location'])) { //redirect
			$need_redirect = true;
			$url = wpbl_get_url_relative($url, $returned_headers['Location'][0]);
			$method = 'GET';
			$post_vars = array();
		}
	} while ($need_redirect);

	if ($response_code >= 200 && $response_code < 300) {
		return $body;
	} else {
		return false;
	}
}

function wpbl_callback_rss_feed_url($args) {
	extract($args);
	$wpbl_option = get_option('wpbl_option');
	return  wpbl_detect_site_name() . '/wpbl_feed/post-'.$GLOBALS['wpbl']['postID'].'-rss.xml';
}

function wpbl_callback_rdf_feed_url($args) {
	extract($args);
	$wpbl_option = get_option('wpbl_option');
	return  wpbl_detect_site_name() . '/wpbl_feed/post-'.$GLOBALS['wpbl']['postID'].'-rdf.xml';
}

function wpbl_callback_user($args) {
	return  $GLOBALS['wpbl']['username'];
}

function wpbl_callback_pass($args) {
	return  $GLOBALS['wpbl']['password'];
}

function wpbl_callback_post_url($args) {
	extract($args);
	$wpbl_option = get_option('wpbl_option');
	return  $permLink;
}

function wpbl_callback_rss_feed_tags($args) {
	extract($args);
	$tagnames = array();
	$tags = wp_get_post_tags( $postID );
	if ( !empty( $tags ) ) {
		foreach ( $tags as $tag )
		$tagnames[] = $tag->name;
		$tagnames = implode( ', ', $tagnames );  // top1
	} else {
		$post = get_post($postID);
		$tagnames = $post->post_title;
	}
	return $tagnames;
}

function wpbl_callback_FeedAgg_id_number($args) {
	extract($args);
	$pattern = '|Enter This Number: (\d{4})</td>|';
	if (preg_match($pattern, $html, $result)) {
		return $result[1];
	}
	return false;
}

function wpbl_callback_rss_feed_title($args) {
	extract($args);
	$post = get_post($postID);
	return $post->post_title;
}

function wpbl_callback_rss_random_number($args) {
	extract($args);
	if (empty($extra_args) || count($extra_args) < 2) 
		return 0;
	return rand($extra_args[0], $extra_args[1]);
}

function wpbl_callback_rss_feed_description($args) {
	extract($args);
	$post = get_post($postID);
	/* // too long
	$p = '/((?:\S+\s+){1,100})/s';
	if (preg_match($p, (string)$post->post_content, $result)) {
		return $result[1];
	} else {
		return false;
	}
	*/
	return substr(ent2ncr(trim(strip_tags(preg_replace('|[\r\n]|', '<br/>', $post->post_content)))), 0, 100);
}

function wpbl_detect_site_name() {

	return get_bloginfo('wpurl');
}

function wpbl_dump_url_to_file($url, $txt) {
	if ( DEBUG ) {
		$file = DUMP_DIR .'/' . wpbl_get_filename_from_url($url);
		$ctr = '';
		while ( file_exists($file.$ctr) ) $ctr++;
		@file_put_contents( $file.$ctr, $txt );
	}
}

function wpbl_get_filename_from_url( $url ) {
	$f = @parse_url($url);
	return wpbl_sanitize_name( isset($f['path']) ? $f['path'] : basename($url) );
}

function wpbl_sanitize_name( $url ) {
	$retval = '';
	$url = str_replace('/', '__', $url);
	$url = str_replace('\\', '__', $url);
	for ( $i = 0; $i < strlen($url); $i++ ) {
		if ( strpos('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_ ', $url{$i}) !== false )
		$retval .= $url{$i};
	}
	return $retval. '_' . time() . '_';
}
?>
