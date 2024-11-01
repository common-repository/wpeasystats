<?
			
	if(isset($_POST['pbl_userneme']))
	{
		$username = $_POST['pbl_userneme'];
	}
	else
	{
		if($result->username == '') 
			$username = '';
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
			$password = '';
		else
			$password = $result->password;
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
        	<input style="margin: 2px;" class="button-secondary" type="submit" name="pbl_runnowSave" value="<?php _e("Save","pbacklinks") ?>" />	
        </td>
        <td></td>
    </tr>

</table>