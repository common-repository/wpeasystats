<div class="wrap" dir="ltr">
<style type="text/css">
a.tooltip {background:#ffffff;font-weight:bold;text-decoration:none;padding:2px 6px;}
a.tooltip:hover {background:#ffffff; text-decoration:none;} /*BG color is a must for IE6*/
a.tooltip span {display:none;font-weight:normal; padding:2px 3px; margin-left:8px; width:230px;}
a.tooltip:hover span{display:inline; position:absolute; right:145px; background:#ffffff; border:1px solid #cccccc; color:#6c6c6c;}
h3 a,h2 a {font-size:80%;text-decoration:none;margin-left:10px;}
</style>
<script>
	function changeSite(obj)
	{
		document.location = "admin.php?page=pbl-edit-single&edit="+obj.value;
	
	}
	
	function openRes(url)
	{
		if (window.showModalDialog) 
		{
			window.showModalDialog(url,"res","dialogWidth:700px;dialogHeight:350px");
		} 
		else 
		{
			window.open(url,'res','height=350,width=700,toolbar=no,directories=no,status=no,continued from previous linemenubar=no,scrollbars=no,resizable=no ,modal=yes');
		}
	}
</script>
<?php 
	$options = get_options_fix();

	$wheresql = 'where deleted=0';
	if($options['pbl_showdeleted'] == 'Yes'){$wheresql = '';}
	
	if($options['pbl_nofollow'] != 'Yes')
	{
		if($wheresql == '')
		{
			$wheresql = 'where ';
		}
		else
		{
			$wheresql .= ' and ';
		}
		$wheresql .= 'nofollow = 1';
	}
	
	if($result->username != '')
	{
		$registered = true;
	}
	
	$pType = $result->ctype;
	if(substr($pType, 0,3) == 'RSS')
	{
		$typeParts = explode("#",$pType);
		$pType = 'RSS'.$typeParts[2];
	}
	$records = $wpdb->get_results("SELECT * FROM $pbl_table_sites $wheresql ORDER BY name"); 
?>


<h2>Resource:  <select name="pbl_descriptionsource" id="pbl_descriptionsource" style="font-size:14px; width:300px;" onchange="changeSite(this);">
<?php
	foreach ($records as $record)
	{
		$sid = $record->id;
		$sname = $record->name;
		
		$selected = '';
		if($result->id == $sid)
			$selected = 'selected';
			
		echo "<option value='$sid' $selected>$sname</option>";
	}
?>
</select></h2>

<h3><?php _e("Registration","wpeasystats") ?></h3>
<? if($result->captcha == 1 && $result->username == ''){ echo '<span style="font-size:10px; color:#F00;"> This resource require captcha apon registration please fill the form bellow after <a href="javascript:openRes(\''.$result->regurl.'\');">registering on the resource</a></span>';} ?>
<? if($result->regurl != '' && $result->captcha == 0 && $result->username == ''){ echo '<span style="font-size:10px; color:#F00;"> This resource require manual registration please fill the form bellow after <a href="javascript:openRes(\''.$result->regurl.'\');">registering on the resource</a></span>';} ?>

	<div>
		<form action="admin.php?page=pbl-edit-single&edit=<?php echo $result->id; ?>" id="campaigns" method="post" onsubmit="return fvalidate(this)">	
		<div style="height:150px;padding:5px;float:left;margin-right: 2%;width:65%;border:1px solid #e3e3e3;-moz-border-radius:4px;">
			<div style="float:left;margin-right: 50px;">
			<? 
				@include_once("includes/".$pType.".php"); 
			?>
			</div>				
		</div>
		</form>
		<div style="height:150px;padding:5px;float:left;width:30%;border:1px solid #e3e3e3;-moz-border-radius:4px;">
			<ul>
				<li>
					<?php if($result->pause == 0) { ?>
					<a href="?page=pbl-edit-single&edit=<?php echo $result->id; ?>&pause=<?php echo $result->id; ?>"><?php _e("Pause Resource","wpeasystats") ?></a>
					<?php } else { ?>
					<a href="?page=pbl-edit-single&edit=<?php echo $result->id; ?>&unpause=<?php echo $result->id; ?>"><?php _e("Activate Resource","wpeasystats") ?></a>
					<?php } ?>
				</li>
				<li><a class="submitdelete" onclick="return confirm('<?php _e("Are you sure you want to delete this resource?","wpeasystats") ?>')" href="admin.php?page=pbl-sites&delete=<?php echo $result->id; ?>" title="<?php _e("Delete this resource","wpeasystats") ?>"><?php _e("Delete Resource","wpeasystats") ?></a></li>
			</ul>
		</div>	
	</div>
	
    <?
		if($registered)
		{
			@include_once("includes/".$pType."Option.php"); 
		}
	?>
    
	<div style="clear:both;"></div>

<h3><?php _e("Post History","wpeasystats") ?></h3>
	
	<form id="campaigns" method="post">	
<table width="60%" class="widefat post fixed" cellspacing="0">	
	<thead>
		<tr>	
			<th id="title" class="manage-column column-title" style="" scope="col"><?php _e("Title","wpeasystats") ?></th>
			<th id="time" class="manage-column column-time" style="width:160px;" scope="col"><?php _e("Posted on","wpeasystats") ?></th>			
		</tr>
	</thead>
	<tfoot>
		<tr>	
			<th id="title" class="manage-column column-title" style="" scope="col"><?php _e("Title","wpeasystats") ?></th>
			<th id="time" class="manage-column column-time" style="" scope="col"><?php _e("Posted on","wpeasystats") ?></th>			
		</tr>
	</tfoot>	
	<tbody>	
			
      <?php 
		$sql = "SELECT * FROM " . $pbl_table_posts . " WHERE site = ".$result->id." order by time desc";
		$posts = $wpdb->get_results($sql);
				
	   foreach ($posts  as $post) 
	   { 
			$pTitle = $post->title;
			$pTime = $post->time;
			$pMyurl = $post->myurl;
	  ?>	
	
		<tr id="post-1575" class="alternate author-self status-publish iedit" valign="top">

			<td><a href="<?php echo $pMyurl; ?>" target="_blank"><?php echo $pTitle; ?></a></td>	
			
			<td class="author column-author"><?php echo $pTime; ?>
			</td>

		</tr>	
		<?php } ?>
	</tbody>
</table>	
<p class="input">
	<input onclick="return confirm('<?php _e("Are you sure you want to delete all the history?","wpeasystats") ?>')" class="button-secondary" type="submit" name="deletehistory" value="<?php _e("Delete History","wpeasystats") ?>"/>	
</p>
</form>
<br />
<h3><?php _e("Last 10 Error Logs","wpeasystats") ?><a title="<?php _e("View full log for this resource","wpeasystats") ?>" href="?page=pbl-log&id=<?php echo $result->id; ?>">View full log</a></h3>
<table width="60%" class="widefat post fixed" cellspacing="0">	
	<thead>
		<tr>
			<th width="15%"><?php _e("Time","wpeasystats") ?></th>		
			<th width="20%"><?php _e("Title","wpeasystats") ?></th>
			<th width="10%"><?php _e("Type","wpeasystats") ?></th>
			<th width="30%"><?php _e("Message","wpeasystats") ?></th>
		</tr>
	</thead>
	<tbody>	
	<?php foreach($errors as $error) {?>
		<tr <?php if($error->reason == "Error") {echo 'style="background:#FAEBF0;"';} ?>>		
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

<br />
<h3><?php _e("Last 10 Pending Posts","wpeasystats") ?><a title="<?php _e("View full list for this resource","wpeasystats") ?>" href="?page=pbl-pending&id=<?php echo $result->id; ?>">View full list</a></h3>
<table width="60%" class="widefat post fixed" cellspacing="0">	
	<thead>
		<tr>
			<th width="15%"><?php _e("Time","wpeasystats") ?></th>		
			<th width="40%"><?php _e("Title","wpeasystats") ?></th>
			<th width="45%"><?php _e("Description","wpeasystats") ?></th>
		</tr>
	</thead>
	<tbody>	
	<?php foreach($pending as $zPost) {?>
		<tr style="background:#E3FDEE;">		
			<td><?php echo "<strong>".$zPost->time."</strong>"; ?></td>		
			<td><a href="<?php echo $zPost->url; ?>" target="blank"><?php echo $zPost->title; ?></a></td>
			<td><?php echo $zPost->description; ?></td>
		</tr>
	<?php }?>
	</tbody>	
</table>
</div>
