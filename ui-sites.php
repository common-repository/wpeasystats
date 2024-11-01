<div class="wrap">
<style type="text/css">
h3 a,h2 a {font-size:80%;text-decoration:none;margin-left:10px;}
</style>

<?php pbl_check_updates(); ?>
<h2><?php _e("WP Back Links Resources","wpeasystats") ?></h2>

	<?php 
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

	$records = $wpdb->get_results("SELECT * FROM $pbl_table_sites $wheresql ORDER BY id ASC"); 
	if ($records) {
	?>
	<form id="campaigns" method="post">	
<table class="widefat post fixed" cellspacing="0">	
	<thead>
		<tr>
			<th id="cb" class="manage-column column-cb check-column" style="" scope="col">
				<input type="checkbox"/>
			</th>
			<th id="name" class="manage-column column-name" style="width:300px;" scope="col"><?php _e("Name","wpeasystats") ?></th>
			<th id="type" class="manage-column column-type" style="width:160px;" scope="col"><?php _e("Type","wpeasystats") ?></th>
			<th id="lasttitle" class="manage-column column-lasttitle" style="" scope="col"><?php _e("Last Post","wpeasystats") ?></th>
			<th id="totalpost" class="manage-column column-totalpost" style="width:60px;" scope="col"><?php _e("Posts","wpeasystats") ?></th>			
			<th id="date" class="manage-column column-date" style="width:160px;" scope="col"><?php _e("Posted On","wpeasystats") ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th class="manage-column column-cb check-column" style="" scope="col"><input type="checkbox"/>
			</th>
			<th class="manage-column column-name" style="" scope="col"><?php _e("Name","wpeasystats") ?></th>
			<th class="manage-column column-Type" style="" scope="col"><?php _e("Type","wpeasystats") ?></th>
			<th class="manage-column column-lasttitle" style="" scope="col"><?php _e("Last Post","wpeasystats") ?></th>
			<th class="manage-column column-totalpost" style="width:60px;" scope="col"><?php _e("Posts","wpeasystats") ?></th>			
			<th class="manage-column column-date" style="width:160px;" scope="col"><?php _e("Posted On","wpeasystats") ?></th>
		</tr>
	</tfoot>	
	<tbody>	
      <?php
		$red = 0;
         
         foreach ($records as $record) {
			$ctype = $record->ctype;
			$paused = $record->pause;
			$deleted = $record->deleted;
			$username = $record->username;
			if(substr($ctype, 0,3) == 'RSS')
			{
				$typeParts = explode("#",$ctype);
				if($typeParts[2] == "x")
					$username = "abc";
			}
		 ?>	
	
		<tr class="alternate author-self status-publish iedit" valign="top" <?php if($deleted == 1) {echo 'style="background:#FF1C1C;"';} elseif($paused == 1 && $username != '') {echo 'style="background:#FDE7B3;"';} elseif($username == '') {echo 'style="background:#FAEBF0;"';}?>>		
			<th class="check-column" scope="row">
				<input type="checkbox" value="<?php echo $record->id; ?>" name="delete[]"/>
			</th>
			<td class="post-title column-title">
				<strong><a title="Visit Site" href="http://<?php echo $record->domain; ?>" target="_blank"><?php echo $record->name; ?></a></strong>
							
				<div class="row-actions">	
					<?
					if($deleted == 0){
					?>
					<span class="edit">
					<a title="<?php _e("Edit Resource","wpeasystats") ?>" href="admin.php?page=pbl-edit-single&edit=<?php echo $record->id; ?>"><?php _e("Edit","wpeasystats") ?></a>
					|
					<?
					}
					?>
					</span>
					<?
					if($deleted == 1){
					?>
					<span class="restore">
					<a title="<?php _e("Restore Resource","wpeasystats") ?>" href="admin.php?page=pbl-sites&restore=<?php echo $record->id; ?>"><?php _e("Restore","wpeasystats") ?></a>
                    </span>
					<?php } elseif($record->pause == 0) { ?>
					<span class="pause">
					<a title="<?php _e("Pause Resource","wpeasystats") ?>" href="admin.php?page=pbl-sites&pause=<?php echo $record->id; ?>"><?php _e("Pause","wpeasystats") ?></a>        
					|
                    </span>
					<?php } else { ?>
					<span class="pause">
					<a title="<?php _e("Activate Resource","wpeasystats") ?>" href="admin.php?page=pbl-sites&unpause=<?php echo $record->id; ?>"><?php _e("Activate","wpeasystats") ?></a>
					|						
					</span>		
					<?php } 	
					
					if($deleted == 0){
					?>
					<span class="delete">
					<a class="submitdelete" onclick="return confirm('<?php _e("Are you sure you want to delete this resource?","wpeasystats") ?>')" href="admin.php?page=pbl-sites&delete=<?php echo $record->id; ?>" title="<?php _e("Delete this resource","wpeasystats") ?>"><?php _e("Delete","wpeasystats") ?></a>
					|
					<?
					}
					?>
					</span>
					
				</div>				
				
			</td>
			
			<td class="type column-type"><?php 
				
				if($record->ctype == 'phpdug')
				{
					echo  'Bookmarks';
				}
				
				if(substr($ctype, 0,3) == 'RSS')
				{
					echo 'RSS';
				}
			?></td>	
			
			<td class="lasttitle column-lasttitle">
			<?php 
				$sql = "SELECT * FROM " . $pbl_table_posts . " WHERE site = ".$record->id." order by time desc LIMIT 1";

				$result = $wpdb->get_row($sql);	

				$pTitle = $result->title;
				$pTime = $result->time;
				$pMyurl = $result->myurl;
				echo '<a href="'.$pMyurl.'" target="_blank">'.$pTitle.'</a>';
			?>
			</td>
			<td class="laststatus column-laststatus">
				<?php echo '<strong>'.$record->posts_created.'</strong>'; ?>		
			</td>

			<td class="date column-date">
			<?php 
			if($deleted == 1)
			{
				echo "<b>Deleted</b>";
			}
			elseif($record->pause == 1) 
			{
				echo "<b>Paused</b>";	
			} 
			else 
			{ 
				echo $pTime;
			} 
			?>
			</td>
		</tr>	
		<?php } ?>
	</tbody>
</table>	

	<div>
		<div style="margin-top: 20px;height:70px;padding:5px;float:left;width:70%;border:0px solid #e3e3e3;-moz-border-radius:4px;">
			<ul style="display: inline;">
				<li style="display: inline;"><input class="button-secondary" type="submit" onclick="return confirm('<?php _e("Are you sure you want to delete all selected resources?","wpeasystats") ?>')" name="deleteall" value="<?php _e("Delete Selected Resources","wpeasystats") ?>"/></li>
                <li style="display: inline;"><input class="button-secondary" type="submit" onclick="return confirm('<?php _e("This action will take a while please be tolerant.","wpeasystats") ?>')" name="registerall" value="<?php _e("Register To Selected Resources","wpeasystats") ?>"/></li>
			</ul>
		</div>	
	</div>
	<div style="clear:both;"></div>

</form>	
		 <?php } else {_e('<br/><br/>You don\'t have any resources yet. Download resources from <a href="admin.php?page=pbl-sites&getupdates=true"><b>here</b></a>!',"wpeasystats");} ?>

</div>
