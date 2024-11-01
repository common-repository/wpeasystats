<?
	
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