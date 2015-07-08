<?php

// ---------------------------------------------------------------------
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
// --------------------------------------------------------------------

?>

<form action="index.php?p=barnraiser_wiki&amp;t=edit_wikipage&amp;wp=<?php echo $_REQUEST['wp'];?>" method="POST">
<input type="hidden" name="wikipage_id" value="<?php if (isset($revision['wikipage_id'])) { echo $revision['wikipage_id'];}?>" />
<input type="hidden" name="wikipage_name" value="<?php if (isset($revision['wikipage_name'])) { echo $revision['wikipage_name'];}?>" />
<input type="hidden" name="revision_id" value="<?php if (isset($revision['revision_id'])) { echo $revision['revision_id'];}?>" />

<div id="am_administration">
	<?php
	if (isset($revisions)) {
	?>
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('hdr_wiki_revisions');?></h1>
		</div>

		<div class="box_body">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<?php
				foreach ($revisions as $key => $i):
				?>
				<tr>
					<td valign="top">
						<a href="index.php?p=barnraiser_wiki&amp;t=edit_wikipage&amp;wp=<?php echo $_REQUEST['wp'];?>&amp;wikipage=<?php echo $_REQUEST['wikipage'];?>&amp;revision_id=<?php echo $i['revision_id'];?>&amp;v=revisions"><?php echo strftime("%d %b %G %H:%M", $i['revision_create_datetime']);?></a>
						<?php
						if ($revision['current_revision_id'] == $i['revision_id']) {
							echo "*";
						}
						
						if ($revision['revision_id'] == $i['revision_id']) {
							$this->getLanguage('txt_currently_selected');
						}
						?>
					</td>
					<td valign="top" valign="right">
						<a href="<?php echo $i['connection_openid'];?>"><?php echo $i['connection_nickname'];?></a>
					</td>
				</tr>
				<?php
				endforeach;
				?>
			</table>
			
			<p align="right">
				<a href="index.php?p=barnraiser_wiki&amp;t=edit_wikipage&amp;wp=<?php echo $_REQUEST['wp'];?>&amp;wikipage=<?php echo $_REQUEST['wikipage_name'];?>"><?php $this->getLanguage('href_return_to_wikipage_editor');?></a>&nbsp;
				<a href="index.php?wp=<?php echo $_REQUEST['wp'];?>&amp;wikipage=<?php echo $_REQUEST['wikipage'];?>"><?php $this->getLanguage('href_return_to_wikipage');?></a>&nbsp;
			 	<input type="submit" name="set_as_current_revision" value="<?php $this->getLanguage('sub_set_current_revision');?>" />
			</p>
		</div>
	</div>
	<?php
	}
	elseif (isset($revision)) {
	?>
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('hdr_edit_wiki_revision');?></h1>
		</div>

		<div class="box_body">
			<p>
				<label for="id_body"><?php $this->getLanguage('common_body');?></label>
				<textarea name="revision_body" id="id_body" cols="80" rows="20"><?php if (isset($revision['revision_body'])) { echo $revision['revision_body'];}?></textarea>
			</p>
			
			<p>
				<label for="id_wikipage_allow_note"><?php $this->getLanguage('label_allow_notes');?></label>
				<input type="checkbox" value="1" <?php if (!isset($revision) || isset($revision['wikipage_allow_note']) && $revision['wikipage_allow_note'] == 1) echo "checked=\"checked\"";?> name="wikipage_allow_note" id="id_wikipage_allow_note" />
			</p>

			<p align="right">
				<input type="submit" name="insert_revision" value="<?php $this->getLanguage('common_save');?>" />&nbsp;
				<input type="submit" name="insert_go_revision" value="<?php $this->getLanguage('common_save_go');?>" />
			</p>
		
			<p>
				<a href="index.php?wp=<?php echo $_REQUEST['wp'];?>&amp;wikipage=<?php echo $revision['wikipage_name'];?>"><?php $this->getLanguage('href_goto_wikipage');?></a>
				<a href="index.php?p=barnraiser_wiki&amp;t=edit_wikipage&amp;wp=<?php echo $_REQUEST['wp'];?>&amp;wikipage=<?php echo $revision['wikipage_name'];?>&amp;v=revisions"><?php $this->getLanguage('href_view_revisions');?></a>
				<a href="#webpage_linker" onclick="javascript:objShowHide('core_webpage_linker');"><?php $this->getLanguage('common_webpage_helper');?></a>
				<a href="#picture_selector" onclick="javascript:objShowHide('core_picture_selector');"><?php $this->getLanguage('common_file_helper');?></a>
				<a href="#wikipage_linker" onclick="javascript:objShowHide('core_wikipage_linker');"><?php $this->getLanguage('href_add_wikilink');?></a>
			</p>
		</div>
	</div>
	<?php
	}
	else {
	?>
	
	<?php }?>
</div>
</form>


<?php
if (isset($revision)) {
	include ('core/template/inc/webpage_linker.inc.tpl.php');
	include ('core/template/inc/picture_selector.inc.tpl.php');
}
?>

<a name="wikipage_linker"></a>
<div class="box" id="core_wikipage_linker" style="display:none;">
	<div class="box_header">
		<h1><?php $this->getLanguage('hdr_wikipage_linker');?></h1>
	</div>
	
	<div class="box_body">
		<?php
		if (isset($wikipages)) {
		?>
		<p>
			<?php $this->getLanguage('txt_wikipage_link');?>
		</p>

		<table cellspacing="0" cellpadding="2" border="0" width="100%">
			<?php
			foreach ($wikipages as $key => $i):
			?>
			<tr>
				<td valign="top">
					<?php echo $i['wikipage_name'];?>
				</td>
				<td>
					<input type="text" style="width:30em;" name="show_tag" value='<wikilink name="<?php echo $i['wikipage_name'];?>"><?php $this->getLanguage('txt_link_description');?></wikilink>' onclick="javascript:this.focus();this.select();" readonly="true" />
				</td>
			</tr>
			<?php
			endforeach;
			?>
			<tr>
				<td valign="top">
					<?php $this->getLanguage('hdr_newpage');?>
				</td>
				<td>
					<input type="text" style="width:30em;" name="show_tag" value='<wikilink name="NewPage"><?php $this->getLanguage('txt_link_description');?></wikilink>' onclick="javascript:this.focus();this.select();" readonly="true" /><br />
					<?php $this->getLanguage('txt_newpage_description');?>
				</td>
			</tr>
		</table>
		<?php }?>
	</div>
</div>