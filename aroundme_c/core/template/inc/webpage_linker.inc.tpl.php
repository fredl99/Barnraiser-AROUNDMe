<a name="webpage_linker"></a>
<div class="box" id="core_webpage_linker" style="display:none;">
	<div class="box_header">
		<h1><?php echo $lang['core_webpage_helper'];?></h1>
	</div>
	
	<div class="box_body">
		<?php
		if (isset($webpages)) {
		?>
		<p>
			<?php echo $lang['core_webpage_helper_intro'];?>
		</p>

		<table cellspacing="0" cellpadding="2" border="0" width="100%">
			<?php
			foreach ($webpages as $key => $i):
			?>
			<tr>
				<td valign="top">
					<?php echo $i;?>
				</td>
				<td>
					<input type="text" style="width:30em;" name="show_tag" value='<a href="index.php?wp=<?php echo $i;?>">link description</a>' onclick="javascript:this.focus();this.select();" readonly="true" />
				</td>
			</tr>
			<?php
			endforeach;
			?>
		</table>
		<?php }?>
	</div>
</div>