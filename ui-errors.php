<div class="wrap">
<style type="text/css">
a.tooltip {background:#ffffff;font-weight:bold;text-decoration:none;padding:2px 6px;}
a.tooltip:hover {background:#ffffff; text-decoration:none;} /*BG color is a must for IE6*/
a.tooltip span {display:none;font-weight:normal; padding:2px 3px; margin-left:8px; width:230px;}
a.tooltip:hover span{display:inline; position:absolute; right:145px; background:#ffffff; border:1px solid #cccccc; color:#6c6c6c;}
h3 a,h2 a {font-size:80%;text-decoration:none;margin-left:10px;}
</style>
<?
if($options['pbl_license'] == '')
{
?>
	<div style="float:right;margin-top: 25px;">
	<a style="color:#cc0000;" href="http://wpbacklinks.net/landing.html" target="_blank"><b>Get Pro Persion, 100+ Resources!</b></a>
	</div>
<?
}
?>
<h2><?php if($_GET["id"]) {echo pbl_getSiteName($_GET["id"]);} else {echo 'WP Back Links';}?> <?php _e(" Error Log","wpeasystats") ?></h2>

<?php if($_GET["id"]) {?>
<p><a href="?page=pbl-log"><?php _e("View log for all resources","wpeasystats") ?></a></p>
<?php } ?>

<?php if ($errors) { ?>
<table width="60%" class="widefat post fixed" cellspacing="0">	
	<thead>
		<tr>
			<?php if(!$_GET["id"]) {?><th width="25%"><?php _e("Resource","wpeasystats") ?></th><?php } ?>		
			<th width="15%"><?php _e("Time","wpeasystats") ?></th>		
			<th width="20%"><?php _e("Title","wpeasystats") ?></th>
			<th width="10%"><?php _e("Type","wpeasystats") ?></th>
			<th width="30%"><?php _e("Message","wpeasystats") ?></th>
		</tr>
	</thead>
	<tbody>	
	<?php foreach($errors as $error) {?>
		<tr <?php if($error->reason == "Error") {echo 'style="background:#FAEBF0;"';} ?>>
			<?php if(!$_GET["id"]) {?><td><a title="<?php _e("View full log for this resource","wpeasystats") ?>" href="?page=pbl-log&id=<?php echo $error->site; ?>"><?php echo $error->sitename; ?></a></td><?php } ?>		
			<td><?php echo "<strong>".$error->time."</strong>"; ?></td>		
			<td><a href="<?php echo $error->myurl; ?>" target="blank"><?php echo $error->title; ?></a></td>
			<td><?php echo $error->reason; ?></td>
			<td><?php echo $error->message; ?></td>
		</tr>
	<?php }?>
	</tbody>	
</table>
<form method="post" id="pbl_err">
<p class="submit"><input class="button" type="submit" name="pbl_clear_log" value="<?php _e("Clear Log","wpeasystats") ?>" /></p>
</form>
<?php } else { ?>
<p><?php _e("Post log is empty.","wpeasystats") ?></p>
<?php } ?>	 
</div>
