<?php
	
	require_once(dirname(__FILE__) . '/../../../wp-config.php');
	require_once(dirname(__FILE__) . '/includes/aggregator_class.php');
	@include_once("func.php");
	
	global $wpdb;
	$options = get_options_fix();
	 
	nocache_headers();
	
	$numposts = $options['pbl_numerposts'];
	$undone = $options['pbl_undone'];
	$pbl_table_sites = $wpdb->prefix . "pbl_sites";	
	$pbl_table_posts = $wpdb->prefix . "pbl_posts";	
	$pbl_table_errors = $wpdb->prefix . "pbl_errors";	
	$pbl_table_pending= $wpdb->prefix . "pbl_pending";
	
	
	$pending = $wpdb->get_results("SELECT * FROM " . $pbl_table_pending . " ORDER BY id DESC LIMIT $numposts");
	
	if ($pending)
	{
		foreach($pending as $zPost)
		{
			$jobid = $zPost->id;
			$id = $zPost->site;
			$sitename = $zPost->sitename;
			$domain = $zPost->domain;
			$user = $zPost->username;
			$pass = $zPost->password;
			$permalink = $zPost->url;
			$pTitle = $zPost->title;
			$pExcerpt = $zPost->description;
			$tags = $zPost->tags;
			$ctype = $zPost->ctype;
			$err = $zPost->err_time;
			$category = $zPost->category;
			$spec = $wpdb->get_var("select spec from ".$pbl_table_sites." where id=".$id);
			
			$hook = doPost($domain,$user,$pass,$permalink,$pTitle,$pExcerpt,$tags,$ctype,$category,$spec);

			if ($hook == 'Bookmark saved') 
			{
				$posts = $wpdb->get_var("select posts_created from ".$pbl_table_sites." where id=".$id);
				$posts = $posts + 1;
				$upnumposts = "update ".$pbl_table_sites." set posts_created=".$posts." where id=".$id;
				$result = $wpdb->query($upnumposts);
				
				$endreg = "insert into ".$pbl_table_posts." set site = ".$id.", title = '".$pTitle."', myurl = '".$permalink."'";
				$result = $wpdb->query($endreg);		
			} 
			else 
			{
				$parts = explode("|", $hook);
				$endreg = "insert into ".$pbl_table_errors." set site = ".$id.", sitename = '".$sitename."', title = '".$pTitle."', myurl = '".$permalink."', message = '".$parts[0]."', reason = '".$parts[1]."'";
				$result = $wpdb->query($endreg);				
			}
			
			if($hook != 'Too many connections. Please try again later.|Error' && $hook != 'Connection error.|Error' && $hook != 'Wrong login data.|Error')
			{
				$results = $wpdb->query("DELETE FROM $pbl_table_pending where id=$jobid;");
			}
			else
			{
				if($err == null)
				{
					$results = $wpdb->query("update $pbl_table_pending set err_time = NOW() where id=$jobid;");
				}
				else
				{
					$tNow = time() ;
					$ofPost = convert_datetime($err) + ($undone * 60 * 60);
					
					if($tNow > $ofPost)
					{
						$results = $wpdb->query("DELETE FROM $pbl_table_pending where id=$jobid;");
					}
				}
			}
		}
	}
?>
