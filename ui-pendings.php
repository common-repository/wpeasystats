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
<h2><?php if($_GET["id"]) {echo pbl_getSiteName($_GET["id"]);} else {echo 'WP Back Links';}?> <?php _e(" Pendings","wpeasystats") ?></h2>

<?php if($_GET["id"]) {?>
<p><a href="?page=pbl-pending"><?php _e("View full pending list.","wpeasystats") ?></a></p>
<?php } ?>

<?php if ($pending) { ?>
<table width="60%" class="widefat post fixed" cellspacing="0">	
	<thead>
		<tr>
			<?php if(!$_GET["id"]) {?><th width="25%"><?php _e("Resource","wpeasystats") ?></th><?php } ?>		
			<th width="15%"><?php _e("Time","wpeasystats") ?></th>		
			<th width="30%"><?php _e("Title","wpeasystats") ?></th>
			<th width="30%"><?php _e("Description","wpeasystats") ?></th>
		</tr>
	</thead>
	<tbody>	
	<?php foreach($pending as $zPost) {?>
		<tr style="background:#E3FDEE;">
			<?php if(!$_GET["id"]) {?><td><a title="<?php _e("View full log for this resource","wpeasystats") ?>" href="?page=pbl-pending&id=<?php echo $zPost->site; ?>"><?php echo $zPost->sitename; ?></a></td><?php } ?>		
			<td><?php echo "<strong>".$zPost->time."</strong>"; ?></td>		
			<td><a href="<?php echo $zPost->url; ?>" target="blank"><?php echo $zPost->title; ?></a></td>
			<td><?php echo $zPost->description; ?></td>
		</tr>
	<?php }?>
	</tbody>	
</table>

<?php } else { ?>
<p><?php _e("Pending list is empty.","wpeasystats") ?></p>
<?php } ?>	 
</div>
