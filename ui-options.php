<div class="wrap" dir="ltr">
<h2><?php _e("WP Easy Stats Options","wpeasystats") ?></h2>
<style type="text/css">
table.addt {padding:5px;background:#F5F5F5;border:1px dotted #F0F0F0;}
table.addt:hover {background:#F2F2F2;border:1px dotted #d9d9d9;}
div.expld {padding:5px;margin-bottom:10px;background:#fffff0;border:1px dotted #e5dd83;}
div.expld:hover {background:#ffffe5;border:1px dotted #e5db6c;} 

a.tooltip {background:#ffffff;font-weight:bold;text-decoration:none;padding:2px 6px;}
a.tooltip:hover {background:#ffffff; text-decoration:none;} /*BG color is a must for IE6*/
a.tooltip span {display:none;font-weight:normal; padding:2px 3px; margin-left:8px; width:230px;}
a.tooltip:hover span{display:inline; position:absolute; background:#ffffff; border:1px solid #cccccc; color:#6c6c6c;}

.messpan
{
border-top-width: 1px;
border-right-width: 1px;
border-bottom-width: 1px;
border-left-width: 1px;
border-top-style: solid;
border-right-style: solid;
border-bottom-style: solid;
border-left-style: solid;
padding-top: 0pt;
padding-right: 0.6em;
padding-bottom: 0pt;
padding-left: 0.6em;
border-top-left-radius: 3px;
border-top-right-radius: 3px;
border-bottom-right-radius: 3px;
border-bottom-left-radius: 3px;
margin-top: 5px;
margin-right: 3pt;
margin-bottom: 25px;
margin-left: 3pt;
background-color: #ffffe0;
border-top-color: #e6db55;
border-right-color: #e6db55;
border-bottom-color: #e6db55;
border-left-color: #e6db55;
}
</style>
	<div style="width:25%;float:right;";>
	
		<div class="expld">
			<strong><?php _e("Documentation","wpbacklinks") ?></strong><br/>
			<?php _e('Have <a href="http://wpbacklinks.net/documentation/">a look at the <b>documentation</b></a> to view an explanation of all available settings.',"wpbacklinks") ?>
		</div>			
	
		<div class="expld">
			<strong><?php _e("Quick Links","wpbacklinks") ?></strong><br/>
			<?php _e('- <a target="_blank" href="http://wpbacklinks.net/landing.html">Full Version Download Link</a><br/>- <a target="_blank" href="http://wpbacklinks.net/">WP Back Links</a><br/>- <a target="_blank" href="http://wpbacklinks.net/documentation/">Online Documentation</a><br/>- <a target="_blank" href="http://wpbacklinks.net/contact-us/">Contact Support</a> ',"wpbacklinks") ?>
		</div>			

		<div class="expld">
			<strong><?php _e("Other Services","wpeasystats") ?></strong><br/>
			<div align="left" style="margin-top:5px;" id="plugin"></div>
			<script language='JavaScript' type='text/javascript' src='http://wpbacklinks.net/add/wpadd.php?zone=plugin&type=global'></script>
		</div>		
		
	</div>
	<div style="width:70%;">		
	<form method="post" id="pbl_options" enctype="multipart/form-data">	
	
	<p class="submit"><input class="button-primary" type="submit" name="pbl_options_save" value="<?php _e("Save Options","wpeasystats") ?>" /></p>
	
	
	<h3 style="text-transform:uppercase;border-bottom: 1px solid #ccc;"><?php _e("License Options","wpeasystats") ?></h3>	
		<table class="addt" width="100%" cellspacing="2" cellpadding="5"> 	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("License Code:","wpeasystats") ?></td> 
				<td><input id="pbl_license" size="40" type="text" value="<?php echo $options['pbl_license']; ?>" name="pbl_license" />
				 <input class="button" type="submit" name="pbl_update_license" value="<?php _e("Update","wpeasystats") ?>" />
				
				</td> 
			</tr>		
		</table>		

	<h3 style="text-transform:uppercase;border-bottom: 1px solid #ccc;"><?php _e("General Options","wpeasystats") ?></h3>	
		<table class="addt" width="100%" cellspacing="2" cellpadding="5"> 		
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Show \"follow\" Resources:","wpeasystats") ?></td> 
				<td>
				<input name="pbl_nofollow" type="checkbox" id="pbl_nofollow" value="Yes" <?php if ($options['pbl_nofollow']=='Yes') {echo "checked";} ?>/>		
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('If checked, resources with "nofollow" tags will be not available for posting.',"wpeasystats") ?></span></a>
				</td> 
			</tr>
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Show deleted Resources:","wpeasystats") ?></td> 
				<td>
				<input name="pbl_showdeleted" type="checkbox" id="pbl_showdeleted" value="Yes" <?php if ($options['pbl_showdeleted']=='Yes') {echo "checked";} ?>/>		
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('When you delete a resource it\'s actualy not deleted permanently, just getting a "deleted" status.<br /><br />By checking this option you can show those records and restore them from the resources list.',"wpeasystats") ?></span></a>
				</td> 
			</tr>			
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("For posts description use:","wpeasystats") ?></td>
				<td>
				<select name="pbl_descriptionsource" id="pbl_descriptionsource">
					<option value="excerpt" <?php if ($options['pbl_descriptionsource']=='excerpt') {echo 'selected';} ?>><?php _e("Excerpt","wpeasystats") ?></option>
					<option value="description" <?php if ($options['pbl_descriptionsource']=='description') {echo 'selected';} ?>><?php _e("Content","wpeasystats") ?></option>
				</select>
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('The description of the post can be taken from the content or from the excerpt of the original post.',"wpeasystats") ?></span></a>
				</td> 
			</tr>	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Length of the description:","wpeasystats") ?></td>
				<td>
				<input id="pbl_excerptnum" size="40" type="text" value="<?php echo $options['pbl_excerptnum']; ?>" name="pbl_excerptnum" />
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('Sets the number of characters for the posts description.<br />The default value is 50 and maximum is 400.<br /><br />Please make sure that you filled the excerpts of your original posts if you selected it as a source for the description.',"wpeasystats") ?></span></a>
				</td> 
			</tr>	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Start description after this string:","wpeasystats") ?></td>
				<td>
				<input id="pbl_stratfrom" size="40" type="text" value="<?php echo htmlentities(stripslashes($options['pbl_stratfrom'])); ?>" name="pbl_stratfrom" />
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('You can specify a string located on your source code, hidden or visible that the description will start after it.<br /><br />If nothing defined, the description will start from the first character of your content or the excerpt.',"wpeasystats") ?></span></a>
				</td> 
			</tr>
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Strip tags from the description:","wpeasystats") ?></td>
				<td>
				<input id="pbl_striptags" size="40" type="text" value="<?php echo htmlentities(stripslashes($options['pbl_striptags'])); ?>" name="pbl_striptags" />
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('You can strip desired tags from the description.<br /><br/>For example you have a description that looks like this:<br />"&lt;p&gt;this is &lt;h6&gt; <b>A</b> &lt;/h6&gt; test string&lt;/p&gt;"<br />if you will define &lt;h6&gt; in this option the description will look like this:<br />"this is test string".<br /><br />The &lt;p&gt; tag removed also but the content of it remains.',"wpeasystats") ?></span></a>
				</td> 
			</tr>
			
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Number of posts in every event:","wpeasystats") ?></td>
				<td>
				<input id="pbl_numerposts" size="40" type="text" value="<?php echo $options['pbl_numerposts']; ?>" name="pbl_numerposts" />
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('Every time a visitor enters your site he will activate a trigger to publish pending posts.<br />This is the amount of the post that will be published on every event.<br /><br />Don\'t set this too high to not overload your server.',"wpeasystats") ?></span></a>
				</td> 
			</tr>
			
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Interval betwen posts, no less then:","wpeasystats") ?></td>
				<td>
				<input id="pbl_freq" size="40" type="text" value="<?php echo $options['pbl_freq']; ?>" name="pbl_freq" />
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('You can define "m"-for minutes or "h"-for hours.<br /><br />For example, if defined "20m" the posts will be published no less then 20 minutes between the activation of the trigger.',"wpeasystats") ?></span></a>
				</td> 
			</tr>
            
            <tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Delete undone posts after:","wpeasystats") ?></td>
				<td>
				<input id="pbl_undone" size="40" type="text" value="<?php echo $options['pbl_undone']; ?>" name="pbl_undone" />
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('Posts which could not be executed because of an error will be deleted from the pending list after defined hours.',"wpeasystats") ?></span></a>
				</td> 
			</tr>
			
			<tr valign="top">
				<td width="40%" scope="row"><?php _e("Default User Name:","wpeasystats") ?></td>
				<td>
				<input id="pbl_defusername" size="40" type="text" value="<?php echo $options['pbl_defusername']; ?>" name="pbl_defusername" />
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('When you register to a new resources by bulk action this username will be used as default.<br /><br />You can change it manually if you register to a new resource one by one. ',"wpeasystats") ?></span></a>
				</td> 
			</tr>	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Defaul Password:","wpeasystats") ?></td>
				<td>
				<input id="pbl_defpassword" size="40" type="text" value="<?php echo $options['pbl_defpassword']; ?>" name="pbl_defpassword" />
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('When you register to a new resources by bulk action this password will be used as default.<br /><br />You can change it manually if you register to a new resource one by one. ',"wpeasystats") ?></span></a>
				</td> 
			</tr>	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Default E-Mail:","wpeasystats") ?></td>
				<td>
				<input id="pbl_defemail" size="40" type="text" value="<?php echo $options['pbl_defemail']; ?>" name="pbl_defemail" />
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('When you register to a new resources by bulk action this email will be used as default.<br /><br />You can change it manually if you register to a new resource one by one. ',"wpeasystats") ?></span></a>
				</td> 
			</tr>	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Default Category:","wpeasystats") ?></td>
				<td>
                <?
					$catArray = array('Adult',
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
						'Others',
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
                	<select name="pbl_defcategory" id="pbl_defcategory" style="font-size:12px; width:250px;">
					<?php
                        foreach ($catArray as $val)
                        {
                            $selected = '';
                            if($val == $options['pbl_defcategory'])
                                $selected = 'selected';
                                
                            echo "<option value='".$val."' $selected>".$val."</option>";
                        }
                    ?>
                    </select>
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('When you register to a new resources by bulk action this category will be used as default.<br /><br />You can change it manually if you register to a new resource one by one.',"wpeasystats") ?></span></a>
				</td> 
			</tr>
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Google Location:","wpeasystats") ?></td>
				<td>
				<?
				$countryArr = array(
				"USA" => array("country" => "USA", "ext" => "com", "img" => "usaflag.gif"),
"United Arab Emirates" => array("country" => "United Arab Emirates", "ext" => "ae", "img" => "ae_flag.gif"),
"Afghanistan" => array("country" => "Afghanistan", "ext" => "com.af", "img" => "af_flag.gif"),
"Antigua and Barbuda" => array("country" => "Antigua and Barbuda", "ext" => "com.ag", "img" => "ag_flag.gif"),
"Anguilla" => array("country" => "Anguilla", "ext" => "off.ai", "img" => "ai_flag.gif"),
"Armenia" => array("country" => "Armenia", "ext" => "am", "img" => "am_flag.gif"),
"Argentina" => array("country" => "Argentina", "ext" => "com.ar", "img" => "ar_flag.gif"),
"American Samoa" => array("country" => "American Samoa", "ext" => "as", "img" => "as_flag.gif"),
"Austria" => array("country" => "Austria", "ext" => "at", "img" => "at_flag.gif"),
"Australia" => array("country" => "Australia", "ext" => "com.au", "img" => "au_flag.gif"),
"Azerbaijan" => array("country" => "Azerbaijan", "ext" => "az", "img" => "az_flag.gif"),
"Bosna i Hercegovina" => array("country" => "Bosna i Hercegovina", "ext" => "ba", "img" => "ba_flag.gif"),
"Bangladesh" => array("country" => "Bangladesh", "ext" => "com.bd", "img" => "bd_flag.gif"),
"Belgium" => array("country" => "Belgium", "ext" => "be", "img" => "be_flag.gif"),
"Bulgaria" => array("country" => "Bulgaria", "ext" => "bg", "img" => "bg_flag.gif"),
"Bahrain" => array("country" => "Bahrain", "ext" => "com.bh", "img" => "bh_flag.gif"),
"Burundi" => array("country" => "Burundi", "ext" => "bi", "img" => "bi_flag.gif"),
"Bolivia" => array("country" => "Bolivia", "ext" => "com.bo", "img" => "bo_flag.gif"),
"Brasil" => array("country" => "Brasil", "ext" => "com.br", "img" => "br_flag.gif"),
"Bahamas" => array("country" => "Bahamas", "ext" => "bs", "img" => "bs_flag.gif"),
"Botswana" => array("country" => "Botswana", "ext" => "co.bw", "img" => "bw_flag.gif"),
"Belize" => array("country" => "Belize", "ext" => "com.bz", "img" => "bz_flag.gif"),
"Canada" => array("country" => "Canada", "ext" => "ca", "img" => "ca_flag.gif"),
"Rep. Dem. du Congo" => array("country" => "Rep. Dem. du Congo", "ext" => "cd", "img" => "cd_flag.gif"),
"Rep. du Congo" => array("country" => "Rep. du Congo", "ext" => "cg", "img" => "cg_flag.gif"),
"Schweiz" => array("country" => "Schweiz", "ext" => "ch", "img" => "ch_flag.gif"),
"Cote D'Ivoire" => array("country" => "Cote D'Ivoire", "ext" => "ci", "img" => "ci_flag.gif"),
"Cook Islands" => array("country" => "Cook Islands", "ext" => "co.ck", "img" => "ck_flag.gif"),
"Chile" => array("country" => "Chile", "ext" => "cl", "img" => "cl_flag.gif"),
"China" => array("country" => "China", "ext" => "cn", "img" => "cn_flag.gif"),
"Colombia" => array("country" => "Colombia", "ext" => "com.co", "img" => "co_flag.gif"),
"Costa Rica" => array("country" => "Costa Rica", "ext" => "co.cr", "img" => "cr_flag.gif"),
"Cuba" => array("country" => "Cuba", "ext" => "com.cu", "img" => "cu_flag.gif"),
"Czech Republic" => array("country" => "Czech Republic", "ext" => "cz", "img" => "cz_flag.gif"),
"Deutschland" => array("country" => "Deutschland", "ext" => "de", "img" => "de_flag.gif"),
"Djibouti" => array("country" => "Djibouti", "ext" => "dj", "img" => "dj_flag.gif"),
"Danmark" => array("country" => "Danmark", "ext" => "dk", "img" => "dk_flag.gif"),
"Dominica" => array("country" => "Dominica", "ext" => "dm", "img" => "dm_flag.gif"),
"Rep. Dominicana" => array("country" => "Rep. Dominicana", "ext" => "com.do", "img" => "do_flag.gif"),
"Ecuador" => array("country" => "Ecuador", "ext" => "com.ec", "img" => "ec_flag.gif"),
"Eesti" => array("country" => "Eesti", "ext" => "ee", "img" => "ee_flag.gif"),
"Egypt" => array("country" => "Egypt", "ext" => "com.eg", "img" => "eg_flag.gif"),
"Spain" => array("country" => "Spain", "ext" => "es", "img" => "es_flag.gif"),
"Ethiopia" => array("country" => "Ethiopia", "ext" => "com.et", "img" => "et_flag.gif"),
"Suomi" => array("country" => "Suomi", "ext" => "fi", "img" => "fi_flag.gif"),
"Fiji" => array("country" => "Fiji", "ext" => "com.fj", "img" => "fj_flag.gif"),
"Micronesia" => array("country" => "Micronesia", "ext" => "fm", "img" => "fm_flag.gif"),
"France" => array("country" => "France", "ext" => "fr", "img" => "fr_flag.gif"),
"Georgia" => array("country" => "Georgia", "ext" => "ge", "img" => "ge_flag.gif"),
"Guernsey" => array("country" => "Guernsey", "ext" => "gg", "img" => "gg_flag.gif"),
"Gibraltar" => array("country" => "Gibraltar", "ext" => "com.gi", "img" => "gi_flag.gif"),
"Greenland" => array("country" => "Greenland", "ext" => "gl", "img" => "gl_flag.gif"),
"Gambia" => array("country" => "Gambia", "ext" => "gm", "img" => "gm_flag.gif"),
"Greece" => array("country" => "Greece", "ext" => "gr", "img" => "gr_flag.gif"),
"Guatemala" => array("country" => "Guatemala", "ext" => "com.gt", "img" => "gt_flag.gif"),
"Guyana" => array("country" => "Guyana", "ext" => "gy", "img" => "gy_flag.gif"),
"Hong Kong" => array("country" => "Hong Kong", "ext" => "com.hk", "img" => "hk_flag.gif"),
"Honduras" => array("country" => "Honduras", "ext" => "hn", "img" => "hn_flag.gif"),
"Haiti" => array("country" => "Haiti", "ext" => "ht", "img" => "ht_flag.gif"),
"Hungary" => array("country" => "Hungary", "ext" => "hu", "img" => "hu_flag.gif"),
"Indonesia" => array("country" => "Indonesia", "ext" => "co.id", "img" => "id_flag.gif"),
"Ireland" => array("country" => "Ireland", "ext" => "ie", "img" => "ie_flag.gif"),
"Israel" => array("country" => "Israel", "ext" => "co.il", "img" => "il_flag.gif"),
"India" => array("country" => "India", "ext" => "co.in", "img" => "in_flag.gif"),
"Iceland" => array("country" => "Iceland", "ext" => "is", "img" => "is_flag.gif"),
"Italia" => array("country" => "Italia", "ext" => "it", "img" => "it_flag.gif"),
"Jamaica" => array("country" => "Jamaica", "ext" => "com.jm", "img" => "jm_flag.gif"),
"Jordan" => array("country" => "Jordan", "ext" => "jo", "img" => "jo_flag.gif"),
"Japan" => array("country" => "Japan", "ext" => "co.jp", "img" => "jp_flag.gif"),
"Kenya" => array("country" => "Kenya", "ext" => "co.ke", "img" => "ke_flag.gif"),
"Kyrghyzstan" => array("country" => "Kyrghyzstan", "ext" => "kg", "img" => "kg_flag.gif"),
"Korea" => array("country" => "Korea", "ext" => "co.kr", "img" => "kr_flag.gif"),
"Kazakhstan" => array("country" => "Kazakhstan", "ext" => "kz", "img" => "kz_flag.gif"),
"Sri Lanka" => array("country" => "Sri Lanka", "ext" => "lk", "img" => "lk_flag.gif"),
"Lietuvos" => array("country" => "Lietuvos", "ext" => "lt", "img" => "lt_flag.gif"),
"Luxemburg" => array("country" => "Luxemburg", "ext" => "lu", "img" => "lu_flag.gif"),
"Latvija" => array("country" => "Latvija", "ext" => "lv", "img" => "lv_flag.gif"),
"Libya" => array("country" => "Libya", "ext" => "com.ly", "img" => "ly_flag.gif"),
"Morocco" => array("country" => "Morocco", "ext" => "co.ma", "img" => "ma_flag.gif"),
"Moldova" => array("country" => "Moldova", "ext" => "md", "img" => "usaflag.gif"),
"Malta" => array("country" => "Malta", "ext" => "com.mt", "img" => "mt_flag.gif"),
"Mexico" => array("country" => "Mexico", "ext" => "com.mx", "img" => "mx_flag.gif"),
"Malaysia" => array("country" => "Malaysia", "ext" => "com.my", "img" => "my_flag.gif"),
"Namibia" => array("country" => "Namibia", "ext" => "com.na", "img" => "na_flag.gif"),
"Nigeria" => array("country" => "Nigeria", "ext" => "com.ng", "img" => "ng_flag.gif"),
"Nicaragua" => array("country" => "Nicaragua", "ext" => "com.ni", "img" => "ni_flag.gif"),
"Nederland" => array("country" => "Nederland", "ext" => "nl", "img" => "nl_flag.gif"),
"Norge" => array("country" => "Norge", "ext" => "no", "img" => "no_flag.gif"),
"Nepal" => array("country" => "Nepal", "ext" => "com.np", "img" => "np_flag.gif"),
"New Zealand" => array("country" => "New Zealand", "ext" => "co.nz", "img" => "nz_flag.gif"),
"Panama" => array("country" => "Panama", "ext" => "com.pa", "img" => "pa_flag.gif"),
"Peru" => array("country" => "Peru", "ext" => "com.pe", "img" => "pe_flag.gif"),
"Pilipinas" => array("country" => "Pilipinas", "ext" => "com.ph", "img" => "ph_flag.gif"),
"Pakistan" => array("country" => "Pakistan", "ext" => "com.pk", "img" => "pk_flag.gif"),
"Polska" => array("country" => "Polska", "ext" => "pl", "img" => "pl_flag.gif"),
"Puerto Rico" => array("country" => "Puerto Rico", "ext" => "com.pr", "img" => "pr_flag.gif"),
"Portugal" => array("country" => "Portugal", "ext" => "pt", "img" => "pt_flag.gif"),
"Paraguay" => array("country" => "Paraguay", "ext" => "com.py", "img" => "py_flag.gif"),
"Qatar" => array("country" => "Qatar", "ext" => "com.qa", "img" => "qa_flag.gif"),
"Romania" => array("country" => "Romania", "ext" => "ro", "img" => "ro_flag.gif"),
"Russia" => array("country" => "Russia", "ext" => "ru", "img" => "ru_flag.gif"),
"Saudi Arabia" => array("country" => "Saudi Arabia", "ext" => "com.sa", "img" => "sa_flag.gif"),
"Singapore" => array("country" => "Singapore", "ext" => "com.sg", "img" => "sg_flag.gif"),
"Slovenija" => array("country" => "Slovenija", "ext" => "si", "img" => "si_flag.gif"),
"Slovenskej republiky" => array("country" => "Slovenskej republiky", "ext" => "sk", "img" => "sk_flag.gif"),
"Thailand" => array("country" => "Thailand", "ext" => "co.th", "img" => "th_flag.gif"),
"Turkey" => array("country" => "Turkey", "ext" => "com.tr", "img" => "tr_flag.gif"),
"Taiwan" => array("country" => "Taiwan", "ext" => "com.tw", "img" => "tw_flag.gif"),
"Ukraine" => array("country" => "Ukraine", "ext" => "com.ua", "img" => "ua_flag.gif"),
"Uganda" => array("country" => "Uganda", "ext" => "co.ug", "img" => "ug_flag.gif"),
"UK" => array("country" => "UK", "ext" => "co.uk", "img" => "uk_flag.gif"),
"Uruguay" => array("country" => "Uruguay", "ext" => "com.uy", "img" => "uy_flag.gif"),
"Ouzbekiston" => array("country" => "Ouzbekiston", "ext" => "co.uz", "img" => "uz_flag.gif"),
"Venezuela" => array("country" => "Venezuela", "ext" => "co.ve", "img" => "ve_flag.gif"),
"Vietnam" => array("country" => "Vietnam", "ext" => "com.vn", "img" => "vn_flag.gif"),
"Samoa" => array("country" => "Samoa", "ext" => "ws", "img" => "ws_flag.gif"),
"South Africa" => array("country" => "South Africa", "ext" => "co.za", "img" => "za_flag.gif"),
"Zambia" => array("country" => "Zambia", "ext" => "co.zm", "img" => "za_flag.gif"),
"South Africa" => array("country" => "South Africa", "ext" => "co.za", "img" => "zm_flag.gif")
				);
				?>
                	<select name="webmenu" id="webmenu" onchange="showValue(this.value)" style="font-size:12px; width:252px;">
					
					<?php
                        foreach ($countryArr as $partsArr)
                        {
                            $selected = '';
                            if($partsArr["ext"] == $options['pbl_googleex'])
                                $selected = 'selected';
                                
                            echo '<option value="'.$partsArr["ext"].'" '.$selected.' title="'.WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/images/flag/'.$partsArr["img"].'">'.$partsArr["country"].'</option>';
                        }
                    ?>
				  </select> 
				<!--Tooltip--><a class="tooltip" href="#" style="margin-left:7px;">?<span><?php _e('All the queries to Google will be executed to Google site of the selected country.',"wpeasystats") ?></span></a>
				</td> 
			</tr>
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Use cron:","wpeasystats") ?></td> 
				<td>
				<input name="pbl_usecron" type="checkbox" id="pbl_usecron" value="Yes" <?php if ($options['pbl_usecron']=='Yes') {echo "checked";} ?>/>		
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('If you prefer to use a cron jobs, instead of posting on users enter to your site, please check this check box.<br /><br />After you will save the page you will see an examples of the cron jobs.',"wpeasystats") ?></span></a>
				</td> 
			</tr>
		</table>	
				
	
		<p class="submit"><input class="button-primary" type="submit" name="pbl_options_save" value="<?php _e("Save Options","wpeasystats") ?>" /></p>
		
		<div class="messpan" style="margin-top: 20px;">
		<h3><?php _e("Resetting, Backup and Uninstalling","wpeasystats") ?></h3>		
		<p class="submit">
		<input class="button" type="submit" name="pbl_backup" value="<?php _e("Backup settings and resources ","wpeasystats") ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="file" name="filerestore" id="filerestore" /> <input onclick="return confirm('<?php _e("Are you sure you want to restore the DATA?","wpeasystats") ?>')" class="button" type="submit" name="pbl_restore" value="<?php _e("Restore DATA","wpeasystats") ?>" /> ( the uploaded file will overwrite your settings and resources )<br />
		<br />
		<input onclick="return confirm('<?php _e("This will reset all options to their default values. Continue?","wpeasystats") ?>')" class="button" type="submit" name="pbl_options_default" value="<?php _e("Reset Options to Defaults","wpeasystats") ?>" /> 
		<input onclick="return confirm('<?php _e("This will clear the WP Easy Stats log of all messages and errors. Continue?","wpeasystats") ?>')" class="button" type="submit" name="pbl_clear_log" value="<?php _e("Clear Log","wpeasystats") ?>" /> 
		<input onclick="return confirm('<?php _e("This will clear the WP Easy Stats post history. Continue?","wpeasystats") ?>')" class="button" type="submit" name="pbl_clear_posts" value="<?php _e("Clear Post History","wpeasystats") ?>" /> 
		<input onclick="return confirm('<?php _e("Warning: This will uninstall WP Easy Stats and delete all settings and data. Continue?","wpeasystats") ?>')" class="button" type="submit" name="pbl_uninstall" value="<?php _e("Uninstall WP Easy Stats","wpeasystats") ?>" /></p>	
		</div>
		
        </form>	
	</div>
	<?
		$phpExec = exec("which php-cli");
		$ppath = WP_PLUGIN_URL."/".plugin_basename( dirname(__FILE__) )."/";
		
		if ($phpExec[0] != '/') {
				$phpExec = exec("which php");
		}
		if ($phpExec[0] != '/' || $options['pbl_usecron']=='Yes')
		{
		
	?>
		<div class="messpan" style="margin-top: 20px;">
			<h3><?php _e("Cron","wpeasystats") ?></h3>
			<? if ($phpExec[0] != '/') { ?>
			<span style="margin-top:0px; color:red;">Your server dos not support shell execution, you must use the following cron comands:</span><br /><br />
			<? } ?>
			wget -q <?=$ppath?>cron.php >/dev/null 2>&1 (recommended every 30 minutes)<br />
			wget -q <?=$ppath?>stats.php >/dev/null 2>&1 (recommended every 2 hours)<br /><br />
			<? if ($phpExec[0] != '/') { ?>
			<span style="margin-top:0px; color:red;">After you setup your cron jobs, don't forget to check the "Use cron" check box.</span><br /><br />
			<? } ?>
		</div>
	<?
		}
		
		if($_POST['pbl_backup'])
		{
			?>
				<iframe src="<?= $ppath?>export.php" style="border:0px;" scrolling="no" name="dframe" id="dframe" height="1"></iframe>
			<?
		}
	?>
</div>
