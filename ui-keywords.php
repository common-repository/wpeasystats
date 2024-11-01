<script>
function hideK(obj)
{
	document.location = 'admin.php?page=pbl-keywords&hide='+obj.checked+'&order=<?=$_GET["order"]?>';
}
</script>
<div class="wrap">
<style type="text/css">
h3 a,h2 a {font-size:80%;text-decoration:none;margin-left:10px;}
label, input { display:inline; }
fieldset { padding:0; border:0; margin-top:5px; }
.ui-dialog .ui-state-error { padding: .3em; }
.validateTips { border: 1px solid transparent; padding: 0.3em; }


div.pagination {
	padding:3px;
	margin:3px;
	text-align:center;
}

div.pagination a {
	padding: 2px 5px 2px 5px;
	margin-right: 2px;
	border: 1px solid #ddd;
	
	text-decoration: none; 
	color: #aaa;
}
div.pagination a:hover, div.pagination a:active {
	padding: 2px 5px 2px 5px;
	margin-right: 2px;
	border: 1px solid #a0a0a0;
}
div.pagination span.current {
	padding: 2px 5px 2px 5px;
	margin-right: 2px;
	border: 1px solid #e0e0e0;
	font-weight: bold;
	background-color: #f0f0f0;
	color: #aaa;
}
div.pagination span.disabled {
	padding: 2px 5px 2px 5px;
	margin-right: 2px;
	border: 1px solid #f3f3f3;
	color: #ccc;
}
</style>

<h2><?php _e("WP Easy Stats Keywords","wpeasystats") ?></h2>

	<?php 
	echo  $_REQUEST["pbl_hideempty"];
	$orderby = 'keyword';
	$orderbyPage = 'keyword';
	if($_GET["order"] != '')
	{
		if($_GET["order"] == 'position')
		{
			$orderby = "CAST(position AS SIGNED)"; 
			$orderbyPage = $_GET["order"];
		}
		else
		{
			$orderby = $_GET["order"];
			$orderbyPage = $orderby;
		}
	}
	
	$hideempty = '';
	if($_GET["hide"] == 'true')
	{
		$hideempty = " where position<>'0' ";
	}

	$totalitems = mysql_num_rows(mysql_query("SELECT id FROM $pbl_table_keywords")); 
	
	$items = mysql_num_rows(mysql_query("SELECT * FROM $pbl_table_keywords".$hideempty)); 
 
	if($items > 0) {
			$p = new pagination;
			$p->items($items);
			$p->limit(30); // Limit entries per page
			$p->target("admin.php?page=pbl-keywords&hide=".$_GET["hide"]."&order=".$orderbyPage);
			$p->currentPage($_GET[$p->paging]); // Gets and validates the current page
			$p->calculate(); // Calculates what to show
			$p->parameterName('paging');
			$p->adjacents(1); //No. of page away from the current page
	 
			if(!isset($_GET['paging'])) {
				$p->page = 1;
			} else {
				$p->page = $_GET['paging'];
			}
	 
			//Query for limit paging
			$limit = "LIMIT " . ($p->page - 1) * $p->limit  . ", " . $p->limit;
			
	}
	
?>
<form id="keywordsForm" method="post">	
<?
if($totalitems > 0)
{
?>
<span style="color:green;"><b>Hide empty keywords:</b></span> <input name="pbl_hideempty" type="checkbox" id="pbl_hideempty" tytle="Hide keywords without position" value="Yes" <?php if ($_GET["hide"] == 'true') {echo "checked";} ?> onclick="hideK(this)"/>	
<?
}
	$records = $wpdb->get_results("SELECT * FROM $pbl_table_keywords $hideempty ORDER BY ".$orderby." ".$limit);
	if ($records) {
	?>
	
<table class="widefat post fixed" cellspacing="0">	
	<thead>
		<tr>
			<th id="cb" class="manage-column column-cb check-column" style="" scope="col">
				<input type="checkbox"/>
			</th>
			<th id="keyword" class="manage-column column-keyword" style="width:280px;" scope="col"><a href="admin.php?page=pbl-keywords&hide=<?=$_GET["hide"]?>&order=keyword"><?php _e("Keyword","wpeasystats") ?></a></th>
			<th id="date" class="manage-column column-date" style="width:190px;" scope="col"><a href="admin.php?page=pbl-keywords&hide=<?=$_GET["hide"]?>&order=date,time"><?php _e("Checked On","wpeasystats") ?></a></th>
			<th id="page" class="manage-column column-page" style="" scope="col"><a href="admin.php?page=pbl-keywords&hide=<?=$_GET["hide"]?>&order=urlrequested"><?php _e("Page","wpeasystats") ?></a></th>
			<th id="page" class="manage-column column-page" style="" scope="col"><a href="admin.php?page=pbl-keywords&hide=<?=$_GET["hide"]?>&order=category"><?php _e("Category","wpeasystats") ?></a></th>
			<th id="position" class="manage-column column-position" style="width:60px;" scope="col"><a href="admin.php?page=pbl-keywords&hide=<?=$_GET["hide"]?>&order=position"><?php _e("Position","wpeasystats") ?></a></th>	
			<th class="manage-column column-position" style="width:30px;" scope="col"></th>				
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th class="manage-column column-cb check-column" style="" scope="col"><input type="checkbox"/>
			</th>
			<th class="manage-column column-keyword" style="" scope="col"><a href="admin.php?page=pbl-keywords&hide=<?=$_GET["hide"]?>&order=keyword"><?php _e("Keyword","wpeasystats") ?></a></th>
			<th class="manage-column column-date" style="" scope="col"><a href="admin.php?page=pbl-keywords&hide=<?=$_GET["hide"]?>&order=date,time"><?php _e("Checked On","wpeasystats") ?></a></th>
			<th class="manage-column column-page" style="" scope="col"><a href="admin.php?page=pbl-keywords&hide=<?=$_GET["hide"]?>&order=urlrequested"><?php _e("Page","wpeasystats") ?></a></th>
			<th class="manage-column column-page" style="" scope="col"><a href="admin.php?page=pbl-keywords&hide=<?=$_GET["hide"]?>&order=category"><?php _e("Category","wpeasystats") ?></a></th>
			<th class="manage-column column-position" style="" scope="col"><a href="admin.php?page=pbl-keywords&hide=<?=$_GET["hide"]?>&order=position"><?php _e("Position","wpeasystats") ?></a></th>	
			<th class="manage-column column-position" style="" scope="col"></th>			
		</tr>
	</tfoot>	
	<tbody>	
      <?php
		$red = 0;
         
         foreach ($records as $record) {

		 ?>	
	
		<tr class="alternate author-self status-publish iedit" valign="top">		
			<th class="check-column" scope="row">
				<input type="checkbox" value="<?php echo $record->id; ?>" name="delete[]"/>
			</th>
			<td class="post-title column-title">
				<strong><span id="key<?php echo $record->id; ?>"><?php echo $record->keyword; ?></span></strong>
							
				<div class="row-actions">	
					<span class="edit">
					<a title="<?php _e("Edit Keyword","wpeasystats") ?>" href="#" id="editlink" name="<?php echo $record->id; ?>"><?php _e("Edit","wpeasystats") ?></a>
					|
					</span>
					<span class="delete">
					<a class="submitdelete" onclick="return confirm('<?php _e("Are you sure you want to delete this keyword?","wpeasystats") ?>')" href="admin.php?page=pbl-keywords&delete=<?php echo $record->id; ?>&hide=<? echo $_GET["hide"];?>&order=<? echo $orderbyPage; ?>" title="<?php _e("Delete this keyword","wpeasystats") ?>"><?php _e("Delete","wpeasystats") ?></a>
					</span>
					
				</div>				
				
			</td>
			
			<td class="type column-type"><?php 
				
				if($record->date != '')
				{
					echo gethdate($record->date).' '.$record->time;
				}
			?></td>	
			
			<td class="lasttitle column-lasttitle">
			<?php 
				echo '<a href="'.$record->urlrequested.'" target="_blank">'.utf8_urldecode($record->urlrequested).'</a>';
			?>
			</td>
			<td class="lasttitle column-lasttitle">
				<span id="cat<?php echo $record->id; ?>"><?php echo $record->category; ?></span>
				<input id="url<?php echo $record->id; ?>" type="text" value="<?php echo $record->urls; ?>" name="url<?php echo $record->id; ?>" style="display: none;" />
			</td>
			<td class="laststatus column-laststatus">
				<?php 
				$posCount = '';
				if($record->position != '0') {$posCount = $record->position;}
				echo '<strong>'.$posCount.'</strong>'; 
				?>		
			</td>
			<td class="laststatus column-laststatus">
				<?php 
				$posOldCount = 0;
				$imgsrc = '';
				if($record->position != 0)
				{
					$posOldCount = $record->positionOld - $record->position;
					
					if($record->position < $record->positionOld)
					{
						$imgsrc = '<img src="'.WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/images/arrgreen.png" style="margin:2px; float:right;" />';
						$pcolor = "green";
					}
					
					if($record->position > $record->positionOld)
					{
						$imgsrc = '<img src="'.WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/images/arrred.png" style="margin:2px; float:right;" />';
						$pcolor = "red";
					}
				}
				else
				{
					if ($record->positionOld != 100)
					{
						$imgsrc = '<img src="'.WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/images/arrred.png" style="margin:2px; float:right;" />';
						$pcolor = "red";
						$posOldCount = 100 - $record->positionOld;
					}
				}
				
				if($posOldCount == 0){$posOldCount = '';} else {$posOldCount = abs($posOldCount);}
				$posOldImg = '<strong style="color:'.$pcolor.';">'.$posOldCount.'</strong> '.$imgsrc;
				if($record->positionOld == '100')
				{
					$posOldImg = '';
				}
				echo $posOldImg; 
				?>		
			</td>
		</tr>	
		<?php } ?>
	</tbody>
</table>	
<?php echo $p->show(); ?>
	<div>
		<div style="margin-top: 20px;height:70px;padding:5px;float:left;width:50%;border:0px solid #e3e3e3;-moz-border-radius:4px;">
			<ul style="display: inline;">
				<li style="display: inline;"><input class="button-secondary" type="submit" onclick="return confirm('<?php _e("Are you sure you want to delete all the selected keywords?","wpeasystats") ?>')" name="deleteall" title="Delete Keywords" value="<?php _e("Delete Selected Keywords","wpeasystats") ?>"/></li>
                <li style="display: inline;"><input class="button-secondary" type="button" name="addkeywords" id="addkeywords" title="Add Keywords" value="<?php _e("Add Keywords","wpeasystats") ?>"/></li>
				<li style="display: inline;"><input class="button-secondary" type="submit" onclick="return confirm('<?php _e("Are you sure you want to import keywords from the stats?","wpeasystats") ?>')" name="importkeywords" title="Import keywords from keywords stats" value="<?php _e("Import Keywords","wpeasystats") ?>"/></li>
			</ul>
		</div>	
	</div>
	<div style="clear:both;"></div>

</form>	
		 <?php } else {
		 if($totalitems == 0)
		 {
			_e('<br/><br/>You don\'t have any keywords yet.',"wpeasystats");
		 }
		 ?>
		 <form id="keywordsForm" method="post">
		 <div>
		<div style="margin-top: 20px;height:70px;padding:5px;float:left;width:50%;border:0px solid #e3e3e3;-moz-border-radius:4px;">
			<ul style="display: inline;">
                <li style="display: inline;"><input class="button-secondary" type="button" name="addkeywords" id="addkeywords" title="Add Keywords" value="<?php _e("Add Keywords","wpeasystats") ?>"/></li>
				<li style="display: inline;"><input class="button-secondary" type="submit" onclick="return confirm('<?php _e("Are you sure you want to import keywords from the stats?","wpeasystats") ?>')" name="importkeywords" title="Import keywords from keywords stats" value="<?php _e("Import Keywords","wpeasystats") ?>"/></li>
			</ul>
		</div>	
	</div>
	</form>
		 <?} ?>

<div class="demo">
	<div id="dialog-form" title="Add Keywords" style="display:none;">
		<p class="validateTipsEdit">Add keywords, one per line.</p>

		<form>
		<fieldset>
			<label for="name">New Keywords</label>
			<textarea rows="9" cols="63" name="keyList" id="keyList" class="text ui-widget-content ui-corner-all"></textarea>
		</fieldset>
		</form>
	</div>
	
	<div id="dialog-edit-form" title="Edit Keyword" style="display:none;">
	<?
		$options = get_options_fix();
		$divLabel = 'You must use Back Links Widget and select your site Category.';
		$labelClass = 'ui-state-error';
		$readonly = ' readonly';
			
		if(is_active_widget('wp_pbl_WidgetShow') && $options["wp_pbl_category"] != '')
		{
			$divLabel = 'Edit keyword.';
			$labelClass = 'validateTipsEdit';
			$readonly = '';
		}
	?>
		<p class="<?= $labelClass?>"><?= $divLabel?></p>
		<form>
		<fieldset>
			<label for="name">Keyword</label><br />
			<input id="oneKeyword" size="50" type="text" value="" name="oneKeyword" /><br />
			<label for="name">Back URLs (One for each line)</label><br />
			<textarea rows="5" cols="48" name="urls" id="urls" class="text ui-widget-content ui-corner-all" dir="ltr"<?=$readonly?>></textarea><br />
			<label for="name">Category</label><br />
			<input id="urlcategory" style="font-size:12px; width:387px;" type="text" value="" name="urlcategory" readonly/>
		</fieldset>
		</form>
	</div>
</div>

</div>
