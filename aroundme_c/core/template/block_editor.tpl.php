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


<form action="index.php?t=block_editor" method="POST">
<input type="hidden" name="block_id" value="<?php if (isset($block['block_id'])) { echo $block['block_id'];}?>" />
<input type="hidden" name="block_plugin" value="<?php if (isset($block['block_plugin'])) { echo $block['block_plugin'];}?>" />
<input type="hidden" name="block_name" value="<?php if (isset($block['block_name'])) { echo $block['block_name'];}?>" />

<div class="box">
	<div class="box_header">
	    <h1><?php $this->getLanguage('core_edit_plugin_block');?></h1>
	</div>
	
	<div class="box_body">

		<?php
		if (isset($block['block_plugin']) && $block['block_plugin'] == "custom") {
		?>
		<p>
			<label for="id_block_name"><?php $this->getLanguage('core_name');?></label><br />
			<input type="text" name="block_name" id="id_block_name" value="<?php if (isset($block['block_name'])) { echo $block['block_name'];}?>" />
		</p>
		<?php }?>
		
		<p>
			<label for="id_block_body"><?php $this->getLanguage('core_block');?></label><br />
			<textarea id="id_block_body" rows="20" cols="100" name="block_body" wrap="off"><?php if (isset($block['block_body'])) { echo $block['block_body'];}?></textarea>
			<br />
		</p>

		<p align="right">
			<input type="submit" value="<?php $this->getLanguage('common_delete');?>" name="delete_block" />

			<?php
			if (isset($block['block_plugin']) && $block['block_plugin'] != "custom") {
			?>
				<input type="submit" value="<?php $this->getLanguage('common_reset');?>" name="reset_block" />
			<?php }?>
			
			<input type="submit" name="save_block" value="<?php $this->getLanguage('common_save');?>" />
			<input type="submit" name="save_go_block" value="<?php $this->getLanguage('common_save_go');?>" />
		</p>
		
		<p>
			<a href="#" onclick="javascript:objShowHide('core_webpage_linker');"><?php $this->getLanguage('common_webpage_helper');?></a>
			<a href="#" onclick="javascript:objShowHide('core_picture_selector');"><?php $this->getLanguage('common_file_helper');?></a>
		</p>
	</div>
</div>
</form>

<?php
include ('core/template/inc/picture_selector.inc.tpl.php');
?>

<?php
include ('core/template/inc/webpage_linker.inc.tpl.php');
?>