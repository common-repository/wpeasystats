<?php
require_once(dirname(__FILE__) . '/../../../wp-config.php');
require_once(dirname(__FILE__) . '/includes/aggregator_class.php');

pbl_beckup_all();
 
function pbl_beckup_all() 
{
	global $wpdb;
	header("Cache-Control: public, must-revalidate");
	header("Pragma: hack"); // WTF? oh well, it works...
	header("Content-Type: text/plain");
	header('Content-Disposition: attachment; filename="WP-Easy-Stats-'.date("Ymd").'.dat"');
	$qry = "SELECT option_value FROM {$wpdb->options} WHERE option_name = 'pbl_db_ver'";
	$results = $wpdb->get_var($qry);
	echo "db@@|@@".$results."\r\n";
	
	$qry = "SELECT option_value FROM {$wpdb->options} WHERE option_name = 'pbl_options'";
	$results = $wpdb->get_var($qry);
	echo "options@@|@@pbl_options@@=@@".$results."\r\n";
	
	$qry = "SELECT * FROM {$wpdb->prefix}pbl_sites";
	$results = $wpdb->get_results($qry);
	foreach ($results as $record) {
		echo "pbl_sites@@|@@name@@=@@'".$record->name."';ctype@@=@@'".$record->ctype."';domain@@=@@'".$record->domain."';regurl@@=@@'".$record->regurl."';username@@=@@'".$record->username."';password@@=@@'".$record->password."';email@@=@@'".$record->email."';nofollow@@=@@".$record->nofollow.";category@@=@@".$record->category.";captcha@@=@@".$record->captcha.";pause@@=@@".$record->pause.";deleted@@=@@".$record->deleted.";spec@@=@@".$record->spec."\r\n";
	}
}
?>