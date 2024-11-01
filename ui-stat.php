<div class="wrap">
<h2><?php _e("WP Easy Stats Statistic","wpeasystats") ?></h2>
<style type="text/css">
table.addt {padding:5px;background:#F5F5F5;border:1px dotted #F0F0F0;}
table.addt:hover {background:#F2F2F2;border:1px dotted #d9d9d9;}
div.expld {padding:5px;margin-bottom:10px;background:#fffff0;border:1px dotted #e5dd83;}
div.expld:hover {background:#ffffe5;border:1px dotted #e5db6c;} 

a.tooltip {background:#ffffff;font-weight:bold;text-decoration:none;padding:2px 6px;}
a.tooltip:hover {background:#ffffff; text-decoration:none;} /*BG color is a must for IE6*/
a.tooltip span {display:none;font-weight:normal; padding:2px 3px; margin-left:8px; width:230px;}
a.tooltip:hover span{display:inline; position:absolute; background:#ffffff; border:1px solid #cccccc; color:#6c6c6c;}

</style>

<script type="text/javascript">

function preperAJAX(pType)
{

	if(pType == 'keyword')
	{	
		get_stats_table('keyword',document.getElementById("key_limit").value,document.getElementById("key_dist").checked);
	}
	
	if(pType == 'hits')
	{	
		get_stats_table('hits',document.getElementById("hits_limit").value,document.getElementById("hits_dist").checked);
	}
	
	if(pType == 'ref')
	{	
		get_stats_table('ref',document.getElementById("ref_limit").value,document.getElementById("ref_dist").checked);
	}
	
	if(pType == 'pages')
	{	
		get_stats_table('pages',document.getElementById("pages_limit").value,document.getElementById("pages_dist").checked);
	}
	
	if(pType == 'robot')
	{	
		get_stats_table('robot',document.getElementById("robot_limit").value,document.getElementById("robot_dist").checked);
	}
}
</script>

<?php 
	global $wpdb,$pbl_table_stats;
	$options = get_options_fix();
	$domain = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
	$googleExt = $options['pbl_googleex'];
	
	$chart = '';
	$pos=0;
	$endDate = strval (date("Y-m-d", strtotime("today")));
	$startDate = strval (date("Y-m-d", strtotime("-1 month today")));
	$dbStart = strtotime("-1 month today");
	$aryDates=createDateRangeArray($startDate,$endDate);
	$cdates = count($aryDates);

	$todel = current_time('timestamp') - 31536000;
	$wpdb->query("delete from ".$pbl_table_stats." where timestamp < ".$todel);
	$records = $wpdb->get_results("select date, count(ip) ins from (SELECT date, count(id) ip FROM ".$pbl_table_stats." where timestamp >=".$dbStart." and robot ='' group by ip) t1 group by date order by date");
	if(count($records) > 0)
	{
		foreach ($records as $record)
		{
			$h_date = $record->date;
			$h_hits = $record->ins;
			$aryDates[$h_date] = $h_hits;
		}
	}
	
	foreach ($aryDates as $i => $value) 
	{
		$chart = $chart."data.setValue(".$pos.", 0, '".date("d/m", strtotime($i))."');\n";
		$chart = $chart."data.setValue(".$pos.", 1, ".$value.");\n";
		$pos++;
	}

	function createDateRangeArray($start, $end) 
	{
		$range = array();

		if (is_string($start) === true) $start = strtotime($start);
		if (is_string($end) === true ) $end = strtotime($end);

		if ($start > $end) return createDateRangeArray($end, $start);

		do {
		$range[date('Ymd', $start)] = 0;
		$start = strtotime("+ 1 day", $start);
		}
		while($start <= $end);

		return $range;
	}
?>	
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Day');
        data.addColumn('number', 'Hits');
        data.addRows(<?=$cdates?>);
		<?	
			echo $chart;
		?>
        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, {width: 1000, height: 250, title: 'Monthly Unique Hits Without Robots'});
      }
    </script>

    <div id="chart_div"></div>
	<form method="post" id="pbl_options">
<table width="100%">
<tr>
	<td width="50%">

	<div style="height:210px;width:98%;padding:5px;border:1px solid #e3e3e3;-moz-border-radius:4px;" dir="ltr">		

	<h3 style="text-transform:uppercase;border-bottom: 1px solid #ccc;"><?php _e("General","wpeasystats") ?></h3>	
	<table>
    <tr>
        <td>
       		<?php _e("Google PR:","wpeasystats") ?> 
        </td>
        <td>
        	<input style="background:#fff;font-size:13px; width:100px;" name="pbl_gpr" type="text" value="<?php echo $options['pbl_gpr']; ?>" readonly/>
        </td>
    </tr>
    <tr>
        <td>
        	<?php _e("Google Index:","wpeasystats") ?> 
        </td>
        <td>
        	<input style="background:#fff;font-size:13px; width:100px;" name="pbl_gindex" type="text" value="<?php echo $options['pbl_gindex']; ?>" readonly/> <a href="http://www.google.<?=$googleExt?>/search?source=ig&hl=en&rlz=&=&q=site:<?=$domain?>&aq=f&oq=&aqi=" target="_blank"><img src="../wp-content/plugins/<?=dirname(plugin_basename(__FILE__))?>/images/google_32.png" border="0"></a>
        </td>
    </tr>
    <tr>
        <td>
        	<?php _e("Yahoo Index:","wpeasystats") ?> 
        </td>
        <td>
        	<input style="background:#fff;font-size:13px; width:100px;" name="pbl_yindex" type="text" value="<?php echo $options['pbl_yindex']; ?>" readonly/> <a href="http://siteexplorer.search.yahoo.com/search?p=<?=$domain?>&bwmo=d&bwmf=s" target="_blank"><img src="../wp-content/plugins/<?=dirname(plugin_basename(__FILE__))?>/images/Yahoo-32.png" border="0"></a>
        </td>
    </tr>
	<tr>
        <td>
        	<?php _e("Bing Index:","wpeasystats") ?> 
        </td>
        <td>
        	<input style="background:#fff;font-size:13px; width:100px;" name="pbl_bindex" type="text" value="<?php echo $options['pbl_bindex']; ?>" readonly/> <a href="http://www.bing.com/search?q=site:<?=$domain?>" target="_blank"><img src="../wp-content/plugins/<?=dirname(plugin_basename(__FILE__))?>/images/bing-icon.png" border="0"></a>
        </td>
    </tr>

	<tr>
        <td>
        	<?php _e("Yahoo Back Links:","wpeasystats") ?> 
        </td>
        <td>
        	<input style="background:#fff;font-size:13px; width:100px;" name="pbl_ybackl" type="text" value="<?php echo $options['pbl_ybackl']; ?>" readonly/> <a href="http://siteexplorer.search.yahoo.com/search?p=<?=$domain?>&bwm=i&bwmo=d&bwmf=s" target="_blank"><img src="../wp-content/plugins/<?=dirname(plugin_basename(__FILE__))?>/images/Yahoo_32.png" border="0"></a>
			
			</td>
			</tr>
			</table>
</td>
<td width="50%">
	<div style="height:210px;width:98%;padding:5px; border:1px solid #e3e3e3;-moz-border-radius:4px;" dir="ltr">
	<h3 style="text-transform:uppercase;border-bottom: 1px solid #ccc;"><?php _e("Details","wpeasystats") ?></h3>
			<table>
    <tr>
        <td>
       		<?php _e("Hits:","wpeasystats") ?> 
        </td>
        <td>
        	<input class="button-primary" type="button" name="pbl_hits_all" id="pbl_hits_all" value="<?php _e("Show","wpeasystats") ?>" onclick="preperAJAX('hits')" /> Limit Last 
			<select name="hits_limit" id="hits_limit" style="font-size:14px; width:100px;">
			<option value='0'>None</option>
			<option value='10' selected>10</option>
			<option value='50'>50</option>
			<option value='1000'>100</option>
			</select>
			Distribution <input name="hits_dist" type="checkbox" id="hits_dist" value="Yes"/>
			<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('Statistics about visits to your site.<br /><br />Please mark the check box to see distributed statistic.',"wpeasystats") ?></span></a>
        </td>
    </tr>
    <tr>
        <td>
        	<?php _e("Keywords:","wpeasystats") ?> 
        </td>
        <td>
        	<input class="button-primary" type="button" name="pbl_keyword_all" id="pbl_keyword_all" value="<?php _e("Show","wpeasystats") ?>" onclick="preperAJAX('keyword')" /> Limit Last 
			<select name="key_limit" id="key_limit" style="font-size:14px; width:100px;">
			<option value='0'>None</option>
			<option value='10' selected>10</option>
			<option value='50'>50</option>
			<option value='1000'>100</option>
			</select>
			Distribution <input name="key_dist" type="checkbox" id="key_dist" value="Yes"/>
			<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('Statistics about keywords used in search engines that found your site.<br /><br />Please mark the check box to see distributed statistic.',"wpeasystats") ?></span></a>
        </td>
    </tr>
    <tr>
        <td>
        	<?php _e("Referers:","wpeasystats") ?> 
        </td>
        <td>
        	<input class="button-primary" type="button" name="pbl_ref_all" id="pbl_ref_all" value="<?php _e("Show","wpeasystats") ?>" onclick="preperAJAX('ref')" /> Limit Last 
			<select name="ref_limit" id="ref_limit" style="font-size:14px; width:100px;">
			<option value='0'>None</option>
			<option value='10' selected>10</option>
			<option value='50'>50</option>
			<option value='1000'>100</option>
			</select>
			Distribution <input name="ref_dist" type="checkbox" id="ref_dist" value="Yes"/>
			<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('Statistics about sites that refered visits to your site.<br /><br />Please mark the check box to see distributed statistic.',"wpeasystats") ?></span></a>
        </td>
    </tr>
	<tr>
        <td>
        	<?php _e("Pages:","wpeasystats") ?> 
        </td>
        <td>
        	<input class="button-primary" type="button" name="pbl_pages_all" id="pbl_pages_all" value="<?php _e("Show","wpeasystats") ?>" onclick="preperAJAX('pages')" /> Limit Last 
			<select name="pages_limit" id="pages_limit" style="font-size:14px; width:100px;">
			<option value='0'>None</option>
			<option value='10' selected>10</option>
			<option value='50'>50</option>
			<option value='1000'>100</option>
			</select>
			Distribution <input name="pages_dist" type="checkbox" id="pages_dist" value="Yes"/>
			<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('Statistics about visited pages on your site.<br /><br />Please mark the check box to see distributed statistic.',"wpeasystats") ?></span></a>
        </td>
    </tr>

	<tr>
        <td>
        	<?php _e("Robots:","wpeasystats") ?> 
        </td>
        <td>
			<input class="button-primary" type="button" name="pbl_robot_all" id="pbl_robot_all" value="<?php _e("Show","wpeasystats") ?>" onclick="preperAJAX('robot')" /> Limit Last 
			<select name="robot_limit" id="robot_limit" style="font-size:14px; width:100px;">
			<option value='0'>None</option>
			<option value='10' selected>10</option>
			<option value='50'>50</option>
			<option value='1000'>100</option>
			</select>
			Distribution <input name="robot_dist" type="checkbox" id="robot_dist" value="Yes"/>
			<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('Statistics about search engine robots visited your site.<br /><br />Please mark the check box to see distributed statistic.',"wpeasystats") ?></span></a>
        </td>
    </tr>

</table>
		
	</div>
	</td>
    </tr>

</table>	

 </form>	
	
</div>
<div id='contentstat'></div>
