<?php
// -----------------------------------------------------------------------
// This file is part of AROUNDMe
// 
// Copyright (C) 2003-2008 Barnraiser
// http://www.barnraiser.org/
// info@barnraiser.org
// 
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; see the file COPYING.txt.  If not, see
// <http://www.gnu.org/licenses/>
// -----------------------------------------------------------------------

?>

<form action="index.php?t=setup" method="POST">

<div id="col_left_50">
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('core_webspace_intro');?></h1>
		</div>

		<div class="box_body">
			<p>
				<label for="id_webspace_title"><?php $this->getLanguage('common_title');?></label>
				<input type="text" id="id_webspace_title" name="webspace_title" value="<?php if(isset($webspace['webspace_title'])) { echo $webspace['webspace_title'];}?>" />
			</p>
			<?php
			if (count($arr_language['pack']) > 1) {
			?>

			<p>
				<label for="id_language_id"><?php $this->getLanguage('common_language');?></label>
				<select name="language_code" id="id_language_code">
					<?php
					foreach($arr_language['pack'] as $key => $i):
						$selected = "";
						if (isset($webspace['language_code']) && $webspace['language_code'] == $key) {
							$selected = "selected=\"selected\"";
						}

						if (isset($this->lang['arr_language'][$key])) {
							$language_name = ucfirst(strtolower($this->lang['arr_language'][$key]));
						}
						else {
							$language_name = $i;
						}
					?>
					<option value="<?php echo $key;?>" <?php echo $selected; ?>><?php echo $language_name;?></option>
					<?php
					endforeach;
					?>
				</select>
			</p>
			<?php }?>
			
			<p>
				<label for="id_webspace_lock"><?php $this->getLanguage('core_locked');?></label>
				<input type="checkbox" name="webspace_locked" id="id_webspace_lock" value="1"<?php if (!empty($webspace['webspace_locked'])) { echo " checked=\"checked\"";}?> />
			</p>

			<p class="note">
				<?php $this->getLanguage('core_locked_intro');?>
			</p>
			
			<p class="buttons">
				<input type="submit" name="save_webspace" value="<?php $this->getLanguage('common_save');?>" />
			</p>
		</div>
	</div>
</div>

<div id="col_right_50">
	<?php
	if (isset($webpages)) {
	?>
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('core_webpages');?></h1>
		</div>

		<div class="box_body">
			<table cellspacing="0" cellpadding="2" border="0" width="100%">
				<tr>
					<td valign="top">
						<b><?php $this->getLanguage('core_webspace_name');?></b>
					</td>
					<td valign="top">
						<b><?php $this->getLanguage('core_tag');?></b>
					</td>
					<td align="center" valign="top">
						<b><?php $this->getLanguage('core_start');?></b>
					</td>
					<td align="center" valign="top">
						<br />
					</td>
				</tr>
				<?php
				foreach ($webpages as $key => $i):
				?>
				<tr>
					<td valign="top">
						<a href="index.php?wp=<?php echo $i['webpage_name'];?>"><?php echo $i['webpage_name'];?></a>
					</td>
					<td>
						<input type="text" name="show_tag" value='<a href="index.php?wp=<?php echo $i['webpage_name'];?>">link description</a>' onclick="javascript:this.focus();this.select();" readonly="true"/>
					</td>
					<td align="center" valign="top">
						<?php
						$checked = "";
						if (isset($webspace['default_webpage_id']) && $webspace['default_webpage_id'] == $i['webpage_id']) {
							$checked = " checked=\"checked\"";
						}
						?>
						<input type="radio" name="default_webpage_id" value="<?php echo $i['webpage_id'];?>"<?php echo $checked;?> />
					</td>
					<td align="right" valign="top">
						<?php
						if (isset($webspace['default_webpage_id']) && $webspace['default_webpage_id'] != $i['webpage_id']) {
						?>
						<input type="checkbox" name="delete_webpage_ids[]" value="<?php echo $i['webpage_id'];?>" />
						<?php }?>
						<br />
					</td>
				</tr>
				<?php
				endforeach;
				?>
			</table>
	

			<p class="buttons">
				<input type="submit" name="set_default_webpage" value="<?php $this->getLanguage('core_set_start');?>" />&nbsp;
				<input type="submit" name="delete_webpages" value="<?php $this->getLanguage('common_delete');?>" />
			</p>
		</div>
	</div>
	<?php }?>


	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('core_plugins');?></h1>
		</div>

		<div class="box_body">
			<?php
			if (isset($blocks)) {
			?>
			<ul>
			<?php
			foreach ($blocks as $key => $i):

			unset($block_name);
		
			if (!empty($this->lang["plugin_"  . $i['block_plugin'] . "_block_" . $i['block_name']])) {
				$block_title = $this->lang["plugin_"  . $i['block_plugin'] . "_block_" . $i['block_name']];
			}
			elseif ($i['block_plugin'] == "custom") {
				$block_title = $this->lang['core_custom_block'];
				$block_name = $i['block_name'];
			}
			else {
				$block_title = $i['block_name'];
			}
			?>
			<li><a href="index.php?t=block_editor&amp;block_id=<?php echo $i['block_id'];?>"><?php echo $block_title;?></a><?php if (isset($block_name)) { echo " (" . $block_name . ")";}?></li>
			<?php
			endforeach;
			?>
			</ul>
			<?php }?>
		
		</div>	
		
		<div class="box_footer">
			<a href="index.php?t=block_editor&amp;add_block=1"><?php $this->getLanguage('core_add_block');?></a>
		</div>
	</div>
</div>
</form>