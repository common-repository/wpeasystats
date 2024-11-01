
<script>
	function fvalidateSett(obj)
	{
		if(obj.pbl_category.value == '0')
		{
			alert('You must select Category.');
			return false;
		}

		return true;
	}
</script>

	<div style="clear:both;"></div>
	<h3><?php _e("Settings","pbacklinks") ?></h3>
	<div> 
		<form action="admin.php?page=pbl-edit-single&edit=<?php echo $result->id; ?>" id="campaigns" method="post" onsubmit="return fvalidateSett(this)">	
		<div style="height:80px;padding:5px;float:left;margin-right: 2%;width:65%;border:1px solid #e3e3e3;-moz-border-radius:4px;">
			<div style="float:left;margin-right: 50px;">
			<table>
            	 <tr>
                    <td>
                        <span style="color:#FF1C1C">*</span> <?php _e("Category:","pbacklinks") ?> 
                    </td>
                    <td>
                         <select name="pbl_category" id="pbl_category" style="font-size:14px; width:300px;">
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
                 <tr>
                    <td>
                        <input style="margin: 2px;" class="button-secondary" type="submit" name="pbl_seveSet" value="<?php _e("Save","pbacklinks") ?>" />	
                    </td>
                    <td></td>
                </tr>
                
            </table>
			</div>				
		</div>
		</form>
	</div>