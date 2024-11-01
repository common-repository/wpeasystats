<?
		
	$catArray = array(
				"27" => "Adult", 
				"8" => "Automative", 
				"15" => "Blogs",
				"14" => "Computers & Internet",
				"9" => "Education",
				"6" => "Entertainment",
				"12" => "Finance",
				"10" => "Food & Drink",
				"19" => "Games",
				"7" => "Gaming",
				"24" => "Health",
				"16" => "Home & Garden",
				"13" => "Hotels & Resorts",
				"17" => "Legal",
				"20" => "Music",
				"11" => "News",
				"26" => "Others",
				"18" => "Real Estate",
				"2" => "Science",
				"22" => "Shopping & Product",
				"21" => "Society & Culture",
				"4" => "Sports",
				"1" => "Technology",
				"25" => "Travels",
				"5" => "Videos",
				"3" => "World & Business"
				);
			
	if(isset($_POST['pbl_userneme']))
	{
		$username = $_POST['pbl_userneme'];
	}
	else
	{
		if($result->username == '') 
			$username = $options['pbl_defusername'];
		else
			$username = $result->username;
	}
	
	if(isset($_POST['pbl_password']))
	{
		$password = $_POST['pbl_password'];
	}
	else
	{
		if($result->password == '') 
			$password = $options['pbl_defpassword'];
		else
			$password = $result->password;
	}
	
	if(isset($_POST['pbl_email']))
	{
		$email = $_POST['pbl_email'];
	}
	else
	{
		if($result->email == '') 
			$email = $options['pbl_defemail'];
		else
			$email = $result->email;
	}
	
	if(isset($_POST['pbl_category']))
	{
		$cat = $_POST['pbl_category'];
	}
	else
	{
		if($result->category == '0') 
			$cat = array_search($options['pbl_defcategory'], $catArray);
		else
			$cat = $result->category;
	}

?>
<script>
	function fvalidate(obj)
	{
		if(obj.pbl_userneme.value == '')
		{
			alert('You must to fill Username.');
			return false;
		}
		
		if(obj.pbl_password.value == '')
		{
			alert('You must to fill Password.');
			return false;
		}
		
		if(obj.pbl_email.value == '')
		{
			alert('You must to fill E-Mail.');
			return false;
		}
		<? if(!$registered) {?>
		if(obj.pbl_category.value == '0')
		{
			alert('You must select Category.');
			return false;
		}
		<? }?>
		return true;
	}
</script>

<table>
    <tr>
        <td>
       		<span style="color:#FF1C1C">*</span> <?php _e("Username:","pbacklinks") ?> 
        </td>
        <td>
        	<input style="background:#fff;font-size:13px; width:250px;" name="pbl_userneme" type="text" value="<?php echo $username; ?>"/>
        </td>
    </tr>
    <tr>
        <td>
        	<span style="color:#FF1C1C">*</span> <?php _e("Password:","pbacklinks") ?> 
        </td>
        <td>
        	<input style="background:#fff;font-size:13px; width:250px;" name="pbl_password" type="text" value="<?php echo $password; ?>"/>
        </td>
    </tr>
    <tr>
        <td>
        	<span style="color:#FF1C1C">*</span> <?php _e("E-mail:","pbacklinks") ?> 
        </td>
        <td>
        	<input style="background:#fff;font-size:13px; width:250px;" name="pbl_email" type="text" value="<?php echo $email; ?>"/>
        </td>
    </tr>
    <? if(!$registered) {?>
    <tr>
        <td>
        	<span style="color:#FF1C1C">*</span> <?php _e("Category:","pbacklinks") ?> 
        </td>
        <td>
        	 <select name="pbl_category" id="pbl_category" style="font-size:13px; width:250px;">
			<?php
				
				while (list($key, $value) = each($catArray))  
				{
                    $selected = '';
                    if($key == $cat)
                        $selected = 'selected';
                        
                    echo "<option value='".$key."' $selected>".$value."</option>";
                }
            ?>
            </select>
        </td>
    </tr>
    <? }?>
    <? if($result->captcha == 0) { ?>
    <tr>
        <td>
        	<input style="margin: 2px;" class="button-secondary" type="submit" name="pbl_runnow" value="<?php _e("Register","pbacklinks") ?>" />	
        </td>
        <td></td>
    </tr>
	<?
	}
	else
	{
	?>
    <tr>
        <td>
        	<input style="margin: 2px;" class="button-secondary" type="submit" name="pbl_runnowSave" value="<?php _e("Save","pbacklinks") ?>" />	
        </td>
        <td></td>
    </tr>
    <? }?>
</table>