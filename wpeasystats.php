<?php
/**
 Plugin Name: WP Easy Stats
 Plugin URI: http://wpbacklinks.net/plugin-capabilities/
 Version: 1.9
 Description: Automatically generates statistics about visitors and google keywords position of your blog.
 Author: John
 Author URI: http://www.wpbacklinks.net/
 License: GPL
*/
/*  Copyright 2010 WP Easy Stats
*/

if (version_compare(PHP_VERSION, '4.0.0.', '<'))
{
	die(__("WP Easy Stats requires php 4 or a greater version to work.", "wpeasystats"));
}

if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
if ( ! defined( 'WPMU_PLUGIN_URL' ) )
      define( 'WPMU_PLUGIN_URL', WP_CONTENT_URL. '/mu-plugins' );
if ( ! defined( 'WPMU_PLUGIN_DIR' ) )
      define( 'WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins' );

	  
define('pbl_DIRPATH', WP_PLUGIN_DIR.'/'.plugin_basename( dirname(__FILE__) ).'/' );

function pbl_plugin_init () {
	$plugin_dir = basename(dirname(__FILE__));
	load_plugin_textdomain( 'wpeasystats', 'wp-content/plugins/' . $plugin_dir, $plugin_dir );
	wpbl_feed();
}

add_action ('init', 'pbl_plugin_init');

// Global Variables
$pbl_table_sites = $wpdb->prefix . "pbl_sites";	
$pbl_table_posts = $wpdb->prefix . "pbl_posts";	
$pbl_table_errors = $wpdb->prefix . "pbl_errors";	
$pbl_table_pending= $wpdb->prefix . "pbl_pending";
$pbl_table_stats = $wpdb->prefix . "pbl_stats";
$pbl_table_keywords = $wpdb->prefix . "pbl_keywords";
$pbl_table_partners = $wpdb->prefix . "pbl_partners";

@include_once("func.php");
include ABSPATH.'wp-content/plugins/'.dirname(plugin_basename(__FILE__)).'/includes/charts.php';
include ABSPATH.'wp-content/plugins/'.dirname(plugin_basename(__FILE__)).'/includes/pagination.class.php';

function pbl_default_options($update=0) {
	
		$options = get_options_fix();
		
		$options['pbl_nofollow'] = "Yes";
		$options['pbl_showdeleted'] = "No";
		$options['pbl_descriptionsource'] = 'description';
		$options['pbl_excerptnum'] = '400';
		$options['pbl_defusername'] = '';
		$options['pbl_defpassword'] = '';
		$options['pbl_defemail'] = '';
		$options['pbl_defcategory'] = 'Others';
		$options['pbl_rversion'] = '1.0.1';
		$options['pbl_stratfrom'] = '';
		$options['pbl_striptags'] = '';
		$options['pbl_numerposts'] = '5';
		$options['pbl_freq'] = '20m';
		$options['pbl_undone'] = '48';
		$options['pbl_freqVal'] = '1200';
		$nextRun = time()+600;
		$options['pbl_nextRun'] = $nextRun;
		$options['pbl_nextRunStats'] = $nextRun;
		$options['pbl_googleex'] = 'com';
		$options['pbl_usecron'] = "No";
		
	if($update == 1) {
		update_option("pbl_options", serialize($options));	
		return $options;
	} else {
		add_option("pbl_options", serialize($options));	
	}
}

function pbl_activate() {
   global $wpdb;
   
    $pbl_db_ver = 2.0;
	$pbl_table_sites = $wpdb->prefix . "pbl_sites";	
	$pbl_table_posts = $wpdb->prefix . "pbl_posts";	
	$pbl_table_errors = $wpdb->prefix . "pbl_errors";		
    $pbl_table_pending = $wpdb->prefix . "pbl_pending";
	$pbl_table_stats = $wpdb->prefix . "pbl_stats";
    $pbl_table_keywords = $wpdb->prefix . "pbl_keywords";
	$pbl_table_partners = $wpdb->prefix . "pbl_partners";
	
	if(get_option('pbl_db_ver') != $pbl_db_ver) {

	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	
    $sql[] = "CREATE TABLE ".$pbl_table_sites." (
        id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		name VARCHAR(255) NOT NULL,
		ctype VARCHAR(255) NOT NULL,
		nofollow INT(1) NOT NULL DEFAULT 0,
		domain VARCHAR(255) NOT NULL,
		regurl VARCHAR(255) NOT NULL,
		username VARCHAR(255) NOT NULL,
		password VARCHAR(255) NOT NULL,
		email VARCHAR(255) NOT NULL,
		category INT(2) NOT NULL DEFAULT 0,
		captcha INT(1) NOT NULL DEFAULT 0,
		posts_created BIGINT(20) NOT NULL DEFAULT 0,
		pause INT(1) NOT NULL DEFAULT 0,
		deleted INT(1) NOT NULL DEFAULT 0,
		spec INT(1) NOT NULL DEFAULT 0
		) {$charset_collate};";
		
    $sql[] = "CREATE TABLE ".$pbl_table_posts." (
        id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		site BIGINT(20) NOT NULL,
		title VARCHAR(255) NOT NULL,		
		myurl VARCHAR(255) NOT NULL,
		time TIMESTAMP(8)
		) {$charset_collate};";		
		
    $sql[] = "CREATE TABLE ".$pbl_table_errors." (
        id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		site BIGINT(20) NOT NULL,
		sitename VARCHAR(255) NOT NULL,
		title VARCHAR(255) NOT NULL,
		myurl VARCHAR(255) NOT NULL,	
		reason VARCHAR(255) NOT NULL,			
		message longtext NOT NULL,
		time TIMESTAMP(8)
		) {$charset_collate};";			

	$sql[] = "CREATE TABLE ".$pbl_table_pending." (
		id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		site BIGINT(20) NOT NULL,
		sitename VARCHAR(255) NOT NULL,
		domain VARCHAR(255) NOT NULL,
		ctype VARCHAR(255) NOT NULL,
		category INT(2) NOT NULL DEFAULT 0,
		username VARCHAR(255) NOT NULL,
		password VARCHAR(255) NOT NULL,
		url VARCHAR(255) NOT NULL,
		title VARCHAR(255) NOT NULL,
		description longtext NOT NULL,
		tags longtext NOT NULL,
		time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		err_time TIMESTAMP NULL
		) {$charset_collate};";
	
	$sql[] = "CREATE TABLE " . $pbl_table_stats." (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		date char(8),
		time char(8),
		ip char(15),
		urlrequested varchar(250),
		agent varchar(250),
		referrer varchar(250),
		search varchar(250),
		nation varchar(2),
		os varchar(30),
		browser varchar(32),
		searchengine varchar(16),
		robot varchar(32),
		feed varchar(8),
		user varchar(16),
		timestamp varchar(10),
		UNIQUE KEY id (id)
		) {$charset_collate};";
	
	$sql[] = "CREATE TABLE " . $pbl_table_keywords." (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		date char(8),
		time char(8),
		keyword varchar(250),
		urlrequested varchar(250),
		position varchar(5) DEFAULT '0',
		positionOld INT(3) NOT NULL DEFAULT 100,
		category varchar(100),
		urls text,
		UNIQUE KEY id (id),
		PRIMARY KEY  (keyword)
		) {$charset_collate};";
	
	$sql[] = "CREATE TABLE " . $pbl_table_partners." (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		siteid mediumint(9),
		keyword varchar(250),
		url varchar(250),
		UNIQUE KEY id (id),
		PRIMARY KEY  (siteid)
		) {$charset_collate};";
		
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);	
	
	if (get_option("pbl_options") == false || get_option("pbl_options") == "") { 	
		pbl_default_options();
	}
	
	update_option('pbl_db_ver',$pbl_db_ver);
	
	}			
}

register_activation_hook(__FILE__, 'pbl_activate');

function pbl_add_pages(){

    add_menu_page('wpeasystats', 'WP Easy Stats', 8, 'pbl-sites', 'pbl_toplevel');
    add_submenu_page('pbl-sites', __('Resources', 'wpeasystats'), __('Resources', 'wpeasystats'), 8, 'pbl-sites', 'pbl_toplevel');	
    add_submenu_page('pbl-sites', __('Options', 'wpeasystats'), __('Options', 'wpeasystats'), 8, 'pbl-options', 'pbl_sub_options');
	add_submenu_page('pbl-sites', __('Pendings', 'wpeasystats'), __('Pendings', 'wpeasystats'), 8, 'pbl-pending', 'pbl_pending');
	add_submenu_page('pbl-sites', __('Statistic', 'wpeasystats'), __('Statistic', 'wpeasystats'), 8, 'pbl-stat', 'pbl_stat');
	add_submenu_page('pbl-sites', __('Keywords', 'wpeasystats'), __('Keywords', 'wpeasystats'), 8, 'pbl-keywords', 'pbl_keywords');
	add_submenu_page('pbl-sites', __('Error Log', 'wpeasystats'), __('Error Log', 'wpeasystats'), 8, 'pbl-log', 'pbl_errors');	
    add_submenu_page('pbl-edit-sites', '', '', 8, 'pbl-edit-single', 'pbl_single');	

	add_action('admin_head-wp-easy-stats_page_pbl-keywords', 'jqui_script' );
	add_action('admin_head-wp-easy-stats_page_pbl-options', 'jqui_script' );
}


add_action('admin_menu', 'pbl_add_pages');
add_action('admin_print_footer_scripts', 'ajax_request');
add_action('wp_ajax_get_stats', 'statsDatast' );
add_action('wp_ajax_add_keywords', 'addKeywordsAction' );
add_action('wp_ajax_edit_keyword', 'editKeywordAction' );
add_action('in_admin_footer', 'wpes_adminmenu_footer');
function ajax_request() {

$options = get_options_fix();
  ?>
  <script type="text/javascript">
	
	function get_stats_table(type,limit,distribution)  {
	 
		jQuery('#contentstat').fadeOut(500 ,function (){ 
		
			var data = {
				action: 'get_stats',
				pType: type,
				pLimit: limit,
				pDistr: distribution
			};
			
			jQuery.post(ajaxurl, data, function(response) {
			jQuery("#contentstat").html(response);
			jQuery('#contentstat').fadeIn(500);
			});
		});

		return true;
	}
	
	function addKeywords(keywords)  {
		var data = {
			action: 'add_keywords',
			pType: keywords
		};
		
		jQuery.post(ajaxurl, data, function(response) {
		jQuery("#keywordsForm").submit();
		});

		return true;
	}
	
	function editKeyword(keyword,urlcategory,urls,kid)  {
		var data = {
			action: 'edit_keyword',
			pType: keyword,
			pCategory: urlcategory,
			pUrls: urls,
			pID: kid,
		};
		
		jQuery.post(ajaxurl, data, function(response) {
		jQuery("#keywordsForm").submit();
		});

		return true;
	}
	
	jQuery(document).ready(function() {
		
		var keyList = jQuery( "#keyList" ),
		oneKeyword = jQuery( "#oneKeyword" ),
		urls = jQuery( "#urls" ),
		urlcategory = jQuery( "#urlcategory" ),
		allFields = jQuery( [] ).add( keyList ).add(oneKeyword).add(urlcategory),
		tips = jQuery( ".validateTips" ),
		tipsEdit = jQuery( ".validateTipsEdit" ),
		clickedid = 0;
		
		if(jQuery( "#webmenu" ).length)
		{
			jQuery("#webmenu").msDropDown();
		}
		
		function updateTips( t ) {
			tips
				.text( t )
				.addClass( "ui-state-highlight" );
			setTimeout(function() {
				tips.removeClass( "ui-state-highlight", 1500 );
			}, 500 );
		}
		
		function updateTipsEdit( t ) {
			tipsEdit
				.text( t )
				.addClass( "ui-state-highlight" );
			setTimeout(function() {
				tipsEdit.removeClass( "ui-state-highlight", 1500 );
			}, 500 );
		}
		
		function checkLength( o, n, min, max ) {
			if ( o.val().length > max || o.val().length < min ) {
				o.addClass( "ui-state-error" );
				if(n == 'keywords')
					updateTips( "Length of " + n + " must be between " + min + " and " + max + "." );
				else
					updateTipsEdit( "Length of " + n + " must be between " + min + " and " + max + "." );
				return false;
			} else {
				return true;
			}
		}
		
		if(jQuery( "#dialog-form" ).length)
		{
			jQuery( "#dialog-form" ).dialog({
				autoOpen: false,
				height: 400,
				width: 450,
				modal: true,
				buttons: {
					"Add Keywords": function() {
						var bValid = true;
						allFields.removeClass( "ui-state-error" );
	 
						bValid = bValid && checkLength( keyList, "keywords", 1, 1000 );
	 
						if ( bValid ) {
							addKeywords(keyList.val());
							jQuery( this ).dialog( "close" );
						}
					},
					Cancel: function() {
						jQuery( this ).dialog( "close" );
					}
				},
				close: function() {
					allFields.val( "" ).removeClass( "ui-state-error" );
					tips.text( "" ).removeClass( "ui-state-highlight");
				}
			});
		}
		
		if(jQuery( "#addkeywords" ).length)
		{
			jQuery("#addkeywords")
				.button()
				.click(function() {
					jQuery( "#dialog-form" ).dialog( "open" );
				});
		}
		
		jQuery("a")
			.click(function(e) {
				if(jQuery(this).attr('id') == 'editlink')
				{
					e.preventDefault();
					clickedid = jQuery(this).attr('name');
					jQuery( "#dialog-edit-form" ).dialog( "open" );
					oneKeyword.val(jQuery( "#key"+clickedid ).text());
					urlcategory.val('<?=$options['wp_pbl_category']?>');
					urls.val(jQuery( "#url"+clickedid ).val());
				}
			});
		
		if(jQuery( "#dialog-edit-form" ).length)
		{
			jQuery( "#dialog-edit-form" ).dialog({
				autoOpen: false,
				height: 400,
				width: 450,
				modal: true,
				buttons: {
					"Save": function() {
						var bValid = true;
						allFields.removeClass( "ui-state-error" );
	 
						bValid = bValid && checkLength( oneKeyword, "keyword", 1, 1000 );
						
						if(urls.val() != '' && urlcategory.val() == '')
						{
							bValid = bValid && checkLength( urlcategory, "category", 1, 1000 );
						}
						if ( bValid ) {
							editKeyword(oneKeyword.val(),urlcategory.val(),urls.val(),clickedid);
							jQuery( this ).dialog( "close" );
						}
					},
					Cancel: function() {
						jQuery( this ).dialog( "close" );
					}
				},
				close: function() {
					allFields.val( "" ).removeClass( "ui-state-error" );
					tipsEdit.text( "" ).removeClass( "ui-state-highlight");
					urls.val( "" );
				}
			});
		}
			
	});

  </script>
  <?
}

function addKeywordsAction(){
	
	global $wpdb, $pbl_table_keywords;
	
	$keywords = $_POST['pType'];
	
	$arr = preg_split('/[\n\r]+/', $keywords);
	foreach( $arr as $oneKeyword) 
	{	
		if($oneKeyword != '')
		{
			$insert = "INSERT IGNORE INTO $pbl_table_keywords SET keyword = '$oneKeyword'";
			$result = $wpdb->query($insert);
		}
	}
	die('ok');
}

function editKeywordAction(){
	
	global $wpdb, $pbl_table_keywords;
	
	$keyword = $_POST['pType'];
	$id = $_POST['pID'];
	$category = $_POST['pCategory'];
	$urls = $_POST['pUrls'];
	$domain = GetDomain(get_bloginfo('url'));
	
	$record = $wpdb->get_row("SELECT * FROM $pbl_table_keywords where id=".$id);	
	$oldUrls = $record->urls;
	$oldKeyword = $record->keyword;
	$oldCategory = $record->category;
	
	$urlGI = 'http://wpbacklinks.net/pbladmin/partnersadd.php';
	$data = 'domain='.$domain.'&urls='.$urls.'&category='.$category.'&keyword='.$keyword.'&oldurls='.$oldUrls.'&oldkeyword='.$oldKeyword.'&oldcategory='.$oldCategory;
	$pageSource = get_web_page($urlGI,$data,false,true);
	
	$update = "UPDATE $pbl_table_keywords SET keyword = '$keyword',category = '$category',urls = '$urls' WHERE id=$id";
	$result = $wpdb->query($update);
			
	die('ok');
}

function pbl_stat() {
	global $wpdb;
	
	include("ui-stat.php");

}

function pbl_keywords() {
	global $wpdb, $pbl_table_keywords;
	
	if($_GET['delete'] && !$_POST['deleteall']) {
		$_POST["delete"] = array($_GET["delete"]);
		pbl_delete_keyword();
	}
	
	if($_POST['deleteall']) {
		if($_POST["delete"] == "" || $_POST["delete"] == 0 || $_POST["delete"] == null) 
		{
			echo '<div class="updated"><p>Please select at least one keyword!</p></div>';
		} 
		else 
		{
			pbl_delete_keyword();
		}
	}
	
	if($_POST['importkeywords']) {
		pbl_import_keywords();
	}
	
	include("ui-keywords.php");

}

function pbl_errors() {
	global $wpdb, $pbl_table_sites, $pbl_table_errors;
	
	$options = get_options_fix();		

	if(!$_GET['id']) {
		$where = "";
	} else {
		$id = $_GET['id'];
		$where = " WHERE site = '$id'";
	}
	
	if($_POST['pbl_clear_log']) {
		$results = $wpdb->query("DELETE FROM $pbl_table_errors$where;");	
		if($results) {
			echo '<div class="updated"><p>'.__('Log has been cleared.', 'pbacklinks').'</p></div>';		
		} else {
			echo '<div class="updated"><p>'.__('Log could not be cleared.', 'pbacklinks').'</p></div>';		
		}
	}			

	$errors = $wpdb->get_results("SELECT * FROM " . $pbl_table_errors . "$where ORDER BY id DESC LIMIT 100");  
	
	include("ui-errors.php");

}

function pbl_pending() {
	global $wpdb, $pbl_table_sites, $pbl_table_pending;
	
	$options = get_options_fix();		

	if(!$_GET['id']) {
		$where = "";
	} else {
		$id = $_GET['id'];
		$where = " WHERE site = '$id'";
	}			

	$pending = $wpdb->get_results("SELECT * FROM " . $pbl_table_pending . "$where ORDER BY id DESC LIMIT 100");  
	
	include("ui-pendings.php");

}

function pbl_site_controls() {
	global $wpdb, $pbl_table_sites;
	
	if($_GET['pause']) {
		$pause = $_GET['pause'];
		$wpdb->update( $pbl_table_sites, array( 'pause' => 1 ), array( 'ID' => $pause ), array( '%d' ), array( '%d' ) );
		echo '<div class="updated"><p>'.__('Resource has been paused.', 'pbacklinks').'</p></div>';	
	}	
	
	if($_GET['restore']) {
		$restore = $_GET['restore'];
		$wpdb->update( $pbl_table_sites, array( 'deleted' => 0 ), array( 'ID' => $restore ), array( '%d' ), array( '%d' ) );
		echo '<div class="updated"><p>'.__('Resource has been restored.', 'pbacklinks').'</p></div>';	
	}
	
	if($_GET['getupdates'] == 'true') {
		
		$sitesList = getUpdates();
		$sites = explode("@@",$sitesList);
		foreach( $sites as $oneSite) 
		{
			$parts = explode("|",$oneSite);

			if(!pbl_check_unique_domain($parts[3]))
			{
				$pause = "1";
				$sname = $parts[0];
				$stype = $parts[1];
				$sfollow = $parts[2];
				$sdomain = $parts[3];
				$scaptcha = $parts[4];
				$sregurl = $parts[5];
				$sour = $parts[6];
				if(substr($stype, 0,3) == 'RSS');
				{
					$typeParts = explode("#", $stype);
					if($typeParts[2] == 'x')
					{
						$pause = "0";
					}
				}
				$insert = "INSERT INTO $pbl_table_sites SET name = '$sname', ctype = '$stype', nofollow = $sfollow, domain = '$sdomain', regurl = '$sregurl', pause = $pause, captcha = $scaptcha, spec = $sour";
				$result = $wpdb->query($insert);	
			}
		}
		$options = get_options_fix();
			
		$options["pbl_rversion"] = checkUpdates();
		update_option("pbl_options", serialize($options));
		
		if(!$_POST['registerall'])
		{
			echo '<div class="updated"><p>'.__('Resources have been updated.', 'pbacklinks').'</p></div>';
		}
	}
	
	if($_GET['unpause']) {
		$pause = $_GET['unpause'];
		
		$result = $wpdb->get_row("SELECT * FROM " . $pbl_table_sites . " WHERE id = '$pause'");  
		$user = $result->username;	
		$pType = $result->ctype;
		$needUser = 1;
		
		if(substr($pType, 0,3) == 'RSS')
		{
			$typeParts = explode("#",$pType);
			if($typeParts[2]=="x")
			{
				$needUser = 0;
			}
		}
		
		if($user == '' && $needUser == 1)
		{
			echo '<div class="updated"><p>Error: '.__('You can\'t activate resource that you didn\'t registered to. Please edit the resource to register.', 'pbacklinks').'</p></div>';	
		}
		else
		{
			$wpdb->update( $pbl_table_sites, array( 'pause' => 0 ), array( 'ID' => $pause ), array( '%d' ), array( '%d' ) );
			echo '<div class="updated"><p>'.__('Resource has been activated.', 'pbacklinks').'</p></div>';
		}
	}		
	
	if($_GET['delete'] && !$_POST['deleteall']) {
		$_POST["delete"] = array($_GET["delete"]);
		pbl_delete_site();
	}
	
	if($_POST['deleteall']) {
		if($_POST["delete"] == "" || $_POST["delete"] == 0 || $_POST["delete"] == null) 
		{
			echo '<div class="updated"><p>Please select at least one resource!</p></div>';
		} 
		else 
		{
			pbl_delete_site();
		}
	}	
	
	if($_POST['registerall']) {
		if($_POST["delete"] == "" || $_POST["delete"] == 0 || $_POST["delete"] == null) 
		{
			echo '<div class="updated"><p>Please select at least one resource!</p></div>';
		}
		else
		{
			pbl_register_site();
		}
	}

}

function pbl_single() {
	global $wpdb, $pbl_table_sites, $pbl_table_errors, $pbl_table_posts, $pbl_table_pending;
	
	pbl_site_controls();
	
	if(!$_GET['edit']) {
		_e("Error: No Resource ID specified", 'pbacklinks');
	} else {
		$id = $_GET['edit'];
		$result = $wpdb->get_row("SELECT * FROM " . $pbl_table_sites . " WHERE id = '$id'");  	
		
		if($_POST['deletehistory']){

			$sql = "delete from " . $pbl_table_posts . " WHERE site = ".$id;

			$results = $wpdb->query($sql);
			if ($results) {				
				echo '<div class="updated"><p>'.__('History have been deleted.', 'pbacklinks').'</p></div>';		
			} else {
				echo '<div class="updated"><p>'.__('Error: History could not be deleted!', 'pbacklinks').'</p></div>';				
			}
		}
		
		if($_POST['pbl_runnow']){

			$user =  $_POST['pbl_userneme'];
			$pass =  $_POST['pbl_password'];
			$email =  $_POST['pbl_email'];
			
			if(isset($_POST['pbl_category']))
				$category =  $_POST['pbl_category'];
			else
				$category = "0";
				
			$domain = $result->domain;
			$type =  $result->ctype;
			$spec =  $result->spec;
			
			$hook = register($domain,$user,$pass,$email,$type,$spec);
			if ($hook == 'registered') {
				$endreg = "update ".$pbl_table_sites." set username = '".$user."', password = '".$pass."',email = '".$email."',category = ".$category.", pause = 0 WHERE id = ".$id;
				$results = $wpdb->query($endreg);
				$result->pause = 0;
				$registered = true;
				echo '<div class="updated"><p>'.__('Resource registered successfully.', 'pbacklinks').'</p></div>';		
			} else {
				echo '<div class="updated"><p>'.$hook.'</p></div>';				
			}
		}
		
		if($_POST['pbl_runnowSave']){

			$user =  $_POST['pbl_userneme'];
			$pass =  $_POST['pbl_password'];
			$email =  $_POST['pbl_email'];
			
			if(isset($_POST['pbl_category']))
				$category =  $_POST['pbl_category'];
			else
				$category = "0";
				
			$endreg = "update ".$pbl_table_sites." set username = '".$user."', password = '".$pass."',email = '".$email."',category = ".$category.", pause = 0 WHERE id = ".$id;
			$results = $wpdb->query($endreg);
			$result->pause = 0;
			echo '<div class="updated"><p>'.__('Resource saved successfully.', 'pbacklinks').'</p></div>';		
		}
		
		if($_POST['pbl_seveSet']){

			$category =  $_POST['pbl_category'];

			$endreg = "update ".$pbl_table_sites." set category = ".$category." WHERE id = ".$id;
			$results = $wpdb->query($endreg);

			echo '<div class="updated"><p>'.__('Resource saved successfully.', 'pbacklinks').'</p></div>';		
		}
		
		if($_POST['pbl_clear_log']) {
			$results = $wpdb->query("DELETE FROM $pbl_table_errors WHERE site = '$id';");	
			if($results) {
				echo '<div class="updated"><p>'.__('Error Log has been cleared.', 'pbacklinks').'</p></div>';		
			} else {
				echo '<div class="updated"><p>'.__('Error Log could not be cleared.', 'pbacklinks').'</p></div>';		
			}
		}
		$errors = $wpdb->get_results("SELECT * FROM " . $pbl_table_errors . " WHERE site = '$id' ORDER BY id DESC LIMIT 10");  
		$pending = $wpdb->get_results("SELECT * FROM " . $pbl_table_pending . " WHERE site = '$id' ORDER BY id DESC LIMIT 10");
		include("ui-single.php");
	}	
}

function pbl_update_license($currentlicense,$newlicense,$updatecore=0) {

	if($currentlicense == $newlicense && $updatecore == 0) {
		echo '<div class="updated"><p>'.__('Error: License has not been changed.', 'pbacklinks').'</p></div>';		
		return $currentlicense;
	} elseif(empty($newlicense)) {
		echo '<div class="updated"><p>'.__('Error: License can not be empty.', 'pbacklinks').'</p></div>';	
		return $currentlicense;
	}
	
	return updateLic($newlicense);
}

function pbl_toplevel() {
	global $wpdb, $pbl_table_sites,$pbl_table_posts;
	
	$options = get_options_fix();
		
	pbl_site_controls();	
   
	include("ui-sites.php");

}

function pbl_sub_options() {
	global $wpdb, $pbl_table_sites, $pbl_table_errors, $pbl_table_posts, $pbl_table_pending, $pbl_table_stats, $pbl_table_keywords, $pbl_table_partners;
	
	$options = get_options_fix();
	
	if($_POST['pbl_restore']) {

		if ($_FILES["filerestore"]["error"] > 0){
			if(count($_FILES["filerestore"] == 0))
				echo '<div class="updated"><p>'.__("Error: Please select file for restore.", 'wpeasystats').'</p></div>';
			else
				echo '<div class="updated"><p>'.__("Error: " . $_FILES["filerestore"]["error"], 'wpeasystats').'</p></div>';
		}
		else{
			$rawdata = file_get_contents($_FILES["filerestore"]["tmp_name"]);
			$rest_data = explode("\r\n",trim($rawdata));
			foreach ($rest_data as $oneRow) {
				$rowType = explode("@@|@@",trim($oneRow));
				
				if($rowType[0] == 'db')
				{
					if(get_option('pbl_db_ver') != $rowType[1]) {
						
						echo '<div class="updated"><p>'.__("Error: Wrong Data Base version.", 'wpeasystats').'</p></div>';
						include("ui-options.php");
						exit();
					}
				}
				
				if($rowType[0] == 'options')
				{
					$optionsParts = explode("@@=@@",trim($rowType[1]));
					$updateOption = "update {$wpdb->options} set option_value = '".mysql_real_escape_string($optionsParts[1])."' WHERE option_name = '".$optionsParts[0]."'";
					$results = $wpdb->query($updateOption);
				}
				
				if($rowType[0] == 'pbl_sites')
				{
					$dataParts = explode(";",trim($rowType[1]));
					$sqlString = "";
					$domain = "";
					foreach ($dataParts as $oneDataRow)
					{
						$dataVals = explode("@@=@@",trim($oneDataRow));
						$sqlString = $sqlString.$dataVals[0]."=".$dataVals[1].",";
						if($dataVals[0] == "domain")
							$domain = $dataVals[1];
					}

					if(!pbl_check_unique_domain(substr($domain,1,-1)))
					{
						$sqlToRun = "insert into ".$pbl_table_sites." set ".substr($sqlString,0,-1);
						$result = $wpdb->query($sqlToRun);	
					}
					else
					{
						$sqlToRun = "update ".$pbl_table_sites." set ".substr($sqlString,0,-1)." where domain=".$domain;
						$result = $wpdb->query($sqlToRun);
					}
				}
			}

			$location = get_option( 'siteurl' ).'/wp-admin/admin.php?page=pbl-options';
			//echo "<script>document.location='".$location."'</script>";
            //exit();

		}
	}
	
	if($_POST['pbl_uninstall']) {
		$results = $wpdb->query("DROP TABLE $pbl_table_posts,$pbl_table_sites,$pbl_table_errors,$pbl_table_pending,$pbl_table_stats,$pbl_table_keywords,$pbl_table_partners;");
		delete_option("pbl_options");	
		delete_option('pbl_db_ver');
		$options = "";
		echo '<div class="updated"><p>'.__('WP Easy Stats has been uninstalled. You can now disable and delete the plugin from your blogs "Plugins" page.<br/><br/><strong>If you intend to reinstall WP Easy Stats please first disable and reenable the plugin on your blogs "Plugins" page - otherwise the installation will not work!</strong>', 'wpeasystats').'</p></div>';		
	}		
	
	if($_POST['pbl_options_default']) {
		$options = pbl_default_options(1);
		echo '<div class="updated"><p>'.__('Options have been reset.', 'wpeasystats').'</p></div>';			
	}			

	if($_POST['pbl_clear_log']) {
		$results = $wpdb->query("TRUNCATE TABLE $pbl_table_errors;");
		echo '<div class="updated"><p>'.__('Log has been cleared.', 'wpeasystats').'</p></div>';		
	}		

	if($_POST['pbl_clear_posts']) {
		$results = $wpdb->query("TRUNCATE TABLE $pbl_table_posts;");			
		echo '<div class="updated"><p>'.__('History has been cleared.', 'wpeasystats').'</p></div>';		
	}
	
	if($_POST['pbl_update_license']) {
		$options['pbl_license'] = pbl_update_license($options['pbl_license'],$_POST['pbl_license']);
	}
	

	if($_POST['pbl_options_save']) {

		$options['pbl_nofollow'] = $_POST['pbl_nofollow'];
		$options['pbl_showdeleted'] = $_POST['pbl_showdeleted'];
		$options['pbl_usecron'] = $_POST['pbl_usecron'];
		$options['pbl_descriptionsource'] = $_POST['pbl_descriptionsource'];
		$options['pbl_googleex'] = $_POST['webmenu'];
		
		if(is_numeric($_POST['pbl_excerptnum']))
			$options['pbl_excerptnum'] = $_POST['pbl_excerptnum'];
		else
			$options['pbl_excerptnum'] = "100";
			
		$options['pbl_defusername'] = $_POST['pbl_defusername'];
		$options['pbl_defpassword'] = $_POST['pbl_defpassword'];
		$options['pbl_defemail'] = $_POST['pbl_defemail'];
		$options['pbl_defcategory'] = $_POST['pbl_defcategory'];
		$options['pbl_stratfrom'] = $_POST['pbl_stratfrom'];
		$options['pbl_striptags'] = $_POST['pbl_striptags'];
		
		if(is_numeric($_POST['pbl_numerposts']))
			$options['pbl_numerposts'] = $_POST['pbl_numerposts'];
		else
			$options['pbl_numerposts'] = "5";
			
		$options['pbl_freq'] = $_POST['pbl_freq'];
		
		if (!preg_match('@^(\d+)\s*([mhMH]?)$@',$_POST['pbl_freq'],$matches))
		{
			$freq = "10m";
			preg_match('@^(\d+)\s*([mhMH]?)$@',$_POST['pbl_freq'],$matches);
		}
		$freqValue=$matches[1]*60;
		
		if ( 'm'!=$matches[2] && 'M'!=$matches[2] )
			$freqValue *= 60;
		
		$options['pbl_freqVal'] = $freqValue;
		
		if(is_numeric($_POST['pbl_undone']))
		{
			$options['pbl_undone'] = $_POST['pbl_undone'];
		}
		else
		{
			$options['pbl_undone'] = "48";
		}
		
		$options['pbl_nextRun'] = time()+$freqValue;
		
		
		update_option("pbl_options", serialize($options));	
		echo '<div class="updated"><p>'.__('Options have been updated.', 'pbacklinks').'</p></div>';				
	}	
	
	include("ui-options.php");	
}

function makePost($post_ID){

	global $wpdb, $pbl_table_sites, $pbl_table_errors, $pbl_table_posts, $pbl_table_pending;
	
	$records = $wpdb->get_results("SELECT * FROM $pbl_table_sites where deleted=0 and pause=0"); 
	if ($records) {
		
		$options = get_options_fix();	
		$maxdescription = $options["pbl_excerptnum"];
		$descriptionType = $options['pbl_descriptionsource'];
		$stratfrom = $options['pbl_stratfrom'];
		$striptags = $options['pbl_striptags'];
		
		if($maxdescription == '')
			$maxdescription = '50';
		
		$post=get_post($post_ID);
		$permalink = get_permalink($post_ID);
		$postUrl = get_permalink($post_ID);
		$tagsArea = get_the_tags($post_ID);
		$pTitle = $post->post_title;
		
		if ($tagsArea) {
			foreach($tagsArea as $tag) {
			
				if($tags != '')
					$seperator = ',';
					
				$tags .= $seperator.$tag->name; 
			}
		}

		if($descriptionType == 'description')
			$pExcerpt = $post->post_content;
		else
			$pExcerpt = $post->post_excerpt;
		
		if($pExcerpt != '')
			$pExcerpt = trim_excerpt_without_filters($pExcerpt,$maxdescription,$stratfrom,$striptags);
		
		foreach ($records as $record) {
			$id = $record->id;
			$user = $record->username;
			$pass = $record->password;
			$domain = $record->domain;
			$sitename = $record->name;
			$posts = $record->posts_created;
			$ctype = $record->ctype;
			$category = $record->category;
		
		if($ctype == 'phpdug')
		{
			$insert = "INSERT INTO $pbl_table_pending SET site = '$id', sitename = '$sitename', domain='$domain', username='$user', password='$pass', url='$permalink', title='$pTitle', description='$pExcerpt', tags='$tags', ctype='$ctype', category=$category";
		}
		
		if(substr($ctype, 0,3) == 'RSS')
		{
			$ctype = $ctype."#".$post_ID."#".$postUrl;
			$permalink = get_bloginfo('wpurl')."/wpbl_feed/post-".$post_ID."-rss.xml";
			$pExcerpt = "RSS";
			$insert = "INSERT INTO $pbl_table_pending SET site = '$id', sitename = '$sitename', domain='$domain', username='$user', password='$pass', url='$permalink', title='$pTitle', description='$pExcerpt', tags='$tags', ctype='$ctype', category=$category";
		}
		$result = $wpdb->query($insert);

		}
		
	}
	
    return $post_ID;
}

function pbl_cron(){
	
	$options = get_options_fix();
	
	if($options['pbl_usecron']!='Yes')
	{
		$now = time();

		if(	$now > $options['pbl_nextRun'])
		{
			$options['pbl_nextRun'] = $now + $options['pbl_freqVal'];
			update_option("pbl_options", serialize($options));
			
			$phpExec = exec("which php-cli");
			if ($phpExec[0] != '/') {
					$phpExec = exec("which php");
			}
			if ($phpExec[0] == '/')
			{
				$phpExec = escapeshellarg($phpExec);
				$fileEXE = escapeshellarg(pbl_DIRPATH.'cron.php');
				$exec = $phpExec." ".$fileEXE;
				
				exec($exec . " > /dev/null 2>&1 &");
			}
		}

		if(	$now > $options['pbl_nextRunStats']) 
		{
			$options['pbl_nextRunStats'] = $now + 4020;
			update_option("pbl_options", serialize($options));
			
			$phpExec = exec("which php-cli");
			if ($phpExec[0] != '/') {
					$phpExec = exec("which php");
			}
			if ($phpExec[0] == '/')
			{
				$phpExec = escapeshellarg($phpExec);
				$fileEXE = escapeshellarg(pbl_DIRPATH.'stats.php');
				$exec = $phpExec." ".$fileEXE;
				
				exec($exec . " > /dev/null 2>&1 &");
			}
		}
	}
}

function addStats(){
	global $wpdb, $pbl_table_stats, $userdata;
    get_currentuserinfo();
	$feed='';
	
	$timestamp  = current_time('timestamp');
	$vdate  = gmdate("Ymd",$timestamp);
	$vtime  = gmdate("H:i:s",$timestamp);

    $ipAddress = $_SERVER['REMOTE_ADDR'];
	
	$urlRequested=REQUEST_URL();
	$referrer = (isset($_SERVER['HTTP_REFERER']) ? htmlentities($_SERVER['HTTP_REFERER']) : '');
	$userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? htmlentities($_SERVER['HTTP_USER_AGENT']) : '');
	$refDomain = GetDomain($referrer);
	
	if (eregi(".ico$", $urlRequested)) { return ''; }
	if (eregi("favicon.ico", $urlRequested)) { return ''; }
	if (eregi(".css$", $urlRequested)) { return ''; }
	if (eregi(".js$", $urlRequested)) { return ''; }
	if (stristr($urlRequested,"/wp-content/plugins") != FALSE) { return ''; }
	if (stristr($urlRequested,"/wp-content/themes") != FALSE) { return ''; }
	if (stristr($urlRequested,"/wp-admin/") != FALSE) { return ''; }
	if (!trim($urlRequested))
	{
		if ($referrer != '') {$urlRequested = '/';} else {return '';}
	}
	if ((eregi(".gif", $urlRequested) || eregi(".jpg", $urlRequested) || eregi(".png", $urlRequested)) && stristr($referrer,$refDomain) != FALSE) { return ''; }
	$robot=isrobot($userAgent);

    
   	if($robot != '') {
	    $os=''; $browser='';
	} else {
		$feed=isfeed(get_bloginfo('url').$_SERVER['REQUEST_URI']);
		$os=getOS($userAgent);
		$browser=getBrowser($userAgent);
		list($searchengine,$search_phrase)=explode("|",getSE($referrer));
	}
	$countrylang="";

	if($countrylang == '') {
		$countrylang=getLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	}
	
    if ((!is_user_logged_in()) OR (get_option('statpress_collectloggeduser')=='checked')) {
		$insert = "INSERT INTO " . $pbl_table_stats .
            " (date, time, ip, urlrequested, agent, referrer, search,nation,os,browser,searchengine,robot,feed,user,timestamp) " .
            "VALUES ('$vdate','$vtime','$ipAddress','$urlRequested','".addslashes(strip_tags($userAgent))."','$referrer','" .
            addslashes(strip_tags($search_phrase))."','".$countrylang."','$os','$browser','$searchengine','$robot','$feed','$userdata->user_login','$timestamp')";
		$results = $wpdb->query( $insert );
	}
	
}

function statsDatast(){
	
	global $wpdb, $pbl_table_stats;
	
	$querytype = $_POST['pType'];
	$querylimit = trim($_POST['pLimit']);
	$querydistr = trim($_POST['pDistr']);
	
	if($querylimit != '0')
	{
		$qlimit = 'LIMIT '.$querylimit;
	}
	else
	{
		$qlimit = '';
	}
	
	if($querytype == 'keyword')
	{
		if($querydistr == 'false')
		{
			$keywords =  "<div class='wrap'><h2>" . __('Keywords','statpress') . "</h2><table class='widefat'><thead><tr><th scope='col'>".__('Date','statpress')."</th><th scope='col'>".__('Time','statpress')."</th><th scope='col'>".__('Keyword','statpress')."</th><th scope='col'>". __('Engine','statpress'). "</th><th scope='col'>". __('Page','statpress'). "</th></tr></thead>";
			$keywords .= "<tbody id='the-list'>";	
			$qry = $wpdb->get_results("SELECT date,time,referrer,urlrequested,search,searchengine FROM $pbl_table_stats WHERE search<>'' ORDER BY id DESC $qlimit");
			foreach ($qry as $rk) {
				$keywords .= "<tr><td>".gethdate($rk->date)."</td><td>".$rk->time."</td><td>".$rk->search."</td><td>".$rk->searchengine."</td><td><a href='".get_bloginfo('url').$rk->urlrequested."' target='_blank'>".utf8_urldecode($rk->urlrequested). "</a></td></tr>\n";
			}
			$keywords .= "</table></div>";
		}
		else
		{
			if($querylimit == '0'){$querylimit='99999';}
			
			$keywords = getDistribution("search","Top keywords",$querylimit,"","","AND search<>''");
		}
	}
	
	if($querytype == 'hits'){
		if($querydistr == 'false')
		{
			$keywords = "<div class='wrap'><h2>". __('Hits','statpress'). "</h2><table class='widefat'><thead><tr><th scope='col'>". __('Date','statpress'). "</th><th scope='col'>". __('Time','statpress'). "</th><th scope='col'>IP</th><th scope='col'>". __('Country','statpress').'/'.__('Language','statpress'). "</th><th scope='col'>". __('Page','statpress'). "</th><th scope='col'>Feed</th><th></th><th scope='col' style='width:120px;'>OS</th><th></th><th scope='col' style='width:120px;'>Browser</th></tr></thead>";
			$keywords .= "<tbody id='the-list'>";	

			$fivesdrafts = $wpdb->get_results("SELECT * FROM $pbl_table_stats WHERE (os<>'' OR feed<>'') order by id DESC $qlimit");
			foreach ($fivesdrafts as $fivesdraft) {
				$keywords .= "<tr>";
				$keywords .= "<td>". gethdate($fivesdraft->date) ."</td>";
				$keywords .= "<td>". $fivesdraft->time ."</td>";
				$keywords .= "<td>". $fivesdraft->ip ."</td>";
				$keywords .= "<td>". $fivesdraft->nation ."</td>";
				$keywords .= "<td>". getStatPressAbbrevia(getStatPressDecode($fivesdraft->urlrequested),60) ."</td>";
				$keywords .= "<td>". $fivesdraft->feed . "</td>";
				if($fivesdraft->os != '') {
					$img=str_replace(" ","_",strtolower($fivesdraft->os)).".png";
					$keywords .= "<td><IMG style='border:0px;width:16px;height:16px;' SRC='../wp-content/plugins/".dirname(plugin_basename(__FILE__))."/images/os/$img'> </td>";
				} else {
					$keywords .= "<td></td>";
				}
				$keywords .= "<td>". $fivesdraft->os . "</td>";
				if($fivesdraft->browser != '') {
					$img=str_replace(" ","",strtolower($fivesdraft->browser)).".png";
					$keywords .= "<td><IMG style='border:0px;width:16px;height:16px;' SRC='../wp-content/plugins/".dirname(plugin_basename(__FILE__))."/images/browsers/$img'></td>";
				} else {
					$keywords .= "<td></td>";
				}
				$keywords .= "<td>".$fivesdraft->browser."</td></tr>\n";
				$keywords .= "</tr>";
			}
			$keywords .= "</table></div>";
		}
		else
		{
			if($querylimit == '0'){$querylimit='99999';}
			
			$keywords = getDistribution("date","Hits count",$querylimit);
		}
	}
	
	if($querytype == 'ref')
	{
		if($querydistr == 'false')
		{
			$keywords = "<div class='wrap'><h2>".__('Referrers','statpress')."</h2><table class='widefat'><thead><tr><th scope='col'>".__('Date','statpress')."</th><th scope='col'>".__('Time','statpress')."</th><th scope='col'>".__('Referrer','statpress')."</th><th scope='col'>".__('Page','statpress')."</th></tr></thead>";
			$keywords .= "<tbody id='the-list'>";	
			$qry = $wpdb->get_results("SELECT date,time,referrer,urlrequested FROM $pbl_table_stats WHERE ((referrer NOT LIKE '".get_option('home')."%') AND (referrer <>'')) ORDER BY id DESC $qlimit");
			foreach ($qry as $rk) {
				$keywords .= "<tr><td>".gethdate($rk->date)."</td><td>".$rk->time."</td><td><a href='".$rk->referrer."' target='_blank'>".getStatPressAbbrevia($rk->referrer,80)."</a></td><td><a href='".get_bloginfo('url').$rk->urlrequested."' target='_blank'>". utf8_urldecode($rk->urlrequested). "</a></td></tr>\n";
			}
			$keywords .= "</table></div>";
		}
		else
		{
			if($querylimit == '0'){$querylimit='99999';}
			
			$keywords = getDistribution("referrer","Top referrer",$querylimit,"","","AND referrer<>'' AND referrer NOT LIKE '%".get_bloginfo('url')."%'");
		}
	}	

	if($querytype == 'pages')
	{
		if($querydistr == 'false')
		{
			$keywords =  "<div class='wrap'><h2>".__('Last pages','statpress')."</h2><table class='widefat'><thead><tr><th scope='col'>".__('Date','statpress')."</th><th scope='col'>".__('Time','statpress')."</th><th scope='col'>".__('Page','statpress')."</th></tr></thead>";
			$keywords .= "<tbody id='the-list'>";	
			$qry = $wpdb->get_results("SELECT date,time,urlrequested,os,browser,robot FROM $pbl_table_stats WHERE (robot='' AND feed='') ORDER BY id DESC $qlimit");
			
			foreach ($qry as $rk) {
			
			if (substr($rk->urlrequested,0,1) == '/')
			{
				$urlPage = $rk->urlrequested;
			}
			else
			{	
				$urlPage = '/'.$rk->urlrequested;
			}
				$keywords .= "<tr><td>".gethdate($rk->date)."</td><td>".$rk->time."</td><td><a href='".get_bloginfo('url').$urlPage."' target='_blank'>". utf8_urldecode($urlPage). "</a></td>";			
			}
			$keywords .= "</table></div>";
		}
		else
		{
			if($querylimit == '0'){$querylimit='99999';}
			
			$keywords = getDistribution("urlrequested","Top pages",$querylimit,"","urlrequested","AND feed='' and robot=''");
		}
	}
	
	if($querytype == 'robot')
	{
		if($querydistr == 'false')
		{
			$keywords = "<div class='wrap'><h2>".__('Last spiders','statpress')."</h2><table class='widefat'><thead><tr><th scope='col'>".__('Date','statpress')."</th><th scope='col'>".__('Time','statpress')."</th><th scope='col'>".__('Spider','statpress')."</th><th scope='col'>".__('Agent','statpress')."</th></tr></thead>";
			$keywords .= "<tbody id='the-list'>";	
			$qry = $wpdb->get_results("SELECT date,time,agent,os,browser,robot FROM $pbl_table_stats WHERE (robot<>'' and robot<>'Google AdSense') ORDER BY id DESC $qlimit");
			foreach ($qry as $rk) {
				$keywords .= "<tr><td>".gethdate($rk->date)."</td><td>".$rk->time."</td><td>".$rk->robot."</td><td> ".$rk->agent."</td></tr>\n";
			}
			$keywords .= "</table></div>";
		}
		else
		{
			if($querylimit == '0'){$querylimit='99999';}
			
			$keywords = getDistribution("robot","Top robots",$querylimit,"","","AND robot<>'' and robot<>'Google AdSense'");
		}
	}
	
	die($keywords);
}

function jqui_script(){
	$pluginfolder = WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__));
	echo '
		<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/themes/ui-lightness/jquery-ui.css">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.js"></script>
		<script language="javascript" src="'.$pluginfolder.'/js/msdropdown/js/jquery.dd.js" type="text/javascript"></script>
		<link rel="stylesheet" type="text/css" href="'.$pluginfolder.'/js/msdropdown/dd.css" />
	';
}

function wp_pbl_WidgetInit() {
		// Tell Dynamic Sidebar about our new widget and its control
	register_sidebar_widget(array('Back Links', 'widgets'), 'wp_pbl_WidgetShow');
	register_widget_control(array('Back Links', 'widgets'), 'wp_pbl_form');
	
}

function wp_pbl_WidgetShow($args) 
{	
	global $wpdb, $pbl_table_partners;
	extract($args);
	$options = get_options_fix();
	
	echo $before_widget;

	if($options["pbl_wd_title"] != '')
		echo $before_title.$options["pbl_wd_title"].$after_title;
	
	$partners = $wpdb->get_results("SELECT * FROM " . $pbl_table_partners); 
	if ($partners) 
	{
		echo '<ul>';
		foreach($partners as $partner)
		{
			echo '<li><a href="'.$partner->url.'" target="_blank">'.$partner->keyword.'</a></li>';
		}
		echo '</ul>';
	}
	

	echo $after_widget;

}

function wp_pbl_form() {
	global $wpdb, $pbl_table_partners;
	$options = get_options_fix();
	
	if (isset($_POST['action1'])) 
	{
		if($options["wp_pbl_category"] != $_POST['wp_pbl_category'])
		{
			$domain = GetDomain(get_bloginfo('url'));
		
			$urlGI = 'http://wpbacklinks.net/pbladmin/changecat.php';
			$data = 'domain='.$domain.'&category='.$_POST['wp_pbl_category'];
			$pageSource = get_web_page($urlGI,$data,false,true);
			
			$wpdb->query("DELETE FROM $pbl_table_partners");
		}
        $options["pbl_wd_title"] = $_POST['wp_pbl_title'];
		$options["wp_pbl_category"] = $_POST['wp_pbl_category'];
		update_option("pbl_options", serialize($options));
	}
	
?>
	Title:
	<br/><input type="text" name="wp_pbl_title" value="<?php echo $options["pbl_wd_title"];?>" /><br />
	Category:<br />
			<?
					$catArray = array('',
						'Adult',
						'Automative',
						'Blogs',
						'Computers & Internet',
						'Education',
						'Entertainment',
						'Finance',
						'Food & Drink',
						'Games',
						'Gaming',
						'Health',
						'Home & Garden',
						'Hotels & Resorts',
						'Legal',
						'Music',
						'News',
						'Real Estate',
						'Science',
						'Shopping & Product',
						'Society & Culture',
						'Sports',
						'Technology',
						'Travels',
						'Videos',
						'World & Business'
					  );
				?>
                	<select name="wp_pbl_category" id="wp_pbl_category" style="font-size:12px;">
					<?php
                        foreach ($catArray as $val)
                        {     
							$selected = '';
                            if($val == $options['wp_pbl_category'])
                                $selected = 'selected';
								
                            echo "<option value='".$val."' ".$selected.">".$val."</option>";
                        }
                    ?>
                    </select>
	<br/><br/>
    Thank you for using <br />Back Links system.
    <input type="hidden" name="action1" value="wdupdate" />

<?php

}

function wpes_adminmenu_footer() {
	echo <<<HTML
<p id="footer-left">Thank you for using <a href="http://wpbacklinks.net">WP Easy Stats</a>, <span style="color:red;">Get more Back Links from <a href="http://wpbacklinks.net/landing.html">WPBL</a></span></p>
HTML;
}

function wpbl_feed() 
{
	if(preg_match('&wpbl_feed&', $_SERVER['REQUEST_URI']) )
	{
		$wpbl_feed = explode("wpbl_feed/", $_SERVER['REQUEST_URI'], 2);
		$wpbl_feed = trim(strip_tags($wpbl_feed[1]));
		if($wpbl_feed == '')return;

		$wpbl_feed 	= explode('-', $wpbl_feed);

		$postID 	= is_array($wpbl_feed)?	$wpbl_feed[1]:0;
		$feedType 	= is_array($wpbl_feed)?	$wpbl_feed[2]:'';

		$feedType 	= $feedType? explode('.', $feedType):'';
		$feedType 	= is_array($feedType)? 	$feedType[0]:'';
	}
	else
		return;

	if( !$feedType || ! in_array($feedType, array('rss','rss2', 'rdf', 'atom')))
		$feedType = 'rss2';

	if( ! is_numeric($postID) ) return;

	query_posts(array('p' => $postID));
	if ( ! have_posts()) return false;

	global $posts, $post;
	ob_start();

	if ($feedType == 'rss')
		require pbl_DIRPATH. 'includes/feed-rss.php';
	elseif ($feedType == 'atom')
		require pbl_DIRPATH. 'includes/feed-atom.php';
	elseif ($feedType == 'rdf')
		require pbl_DIRPATH. 'includes/feed-rdf.php';
	else
		require pbl_DIRPATH. 'includes/feed-rss2.php';
	
	$feed_content = ob_get_contents();
	ob_end_clean();
	echo $feed_content;
	die();
}

function wpbl_get_version() 
{
	$plugin_data = get_plugin_data( __FILE__ );
	$plugin_version = $plugin_data['Version'];
	return $plugin_version;
}
	
add_action('publish_post', 'makePost');
add_action('send_headers', 'addStats');
add_action('wp_footer','pbl_cron');
add_action('plugins_loaded', 'wp_pbl_WidgetInit');
Remove_Action ('wp_head', 'wp_generator');
?>
