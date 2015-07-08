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

<form id="tag_builder_form">

<?php
if (isset($_REQUEST['tag']) && $_REQUEST['tag'] == "page") {
?>

	<p>
		<?php $this->getLanguage('tag_builder_create_wikipage');?> 
	</p>
	
	<p>
		<label for="id_wikipage"><?php $this->getLanguage('tag_builder_wikipage');?></label>
		<select id="id_wikipage" name="tag_builder_element_wikipage">
			<option value="Type your wikipage name here"><?php $this->getLanguage('tag_builder_startwikipage');?></option>
			<?php
			if (isset($wikipages)) {
			foreach ($wikipages as $key => $i):
			?>
			<option value="<?php echo $i['wikipage_name'];?>"><?php echo $i['wikipage_name'];?></option>
			<?php
			endforeach;
			}
			?>
		</select>
	</p>

	<p align="right">
		<input type="button" value="<?php $this->getLanguage('tag_builder_create_tag');?>" onClick="javascript:buildPluginBarnaiserWikiTag('page');" />
	</p>
<?php
}
elseif (isset($_REQUEST['tag']) && $_REQUEST['tag'] == "history") {
?>

	<p>
		<?php $this->getLanguage('tag_builder_create_revision_list');?> 
	</p>
	
	<p>
		<label for="id_limit"><?php $this->getLanguage('tag_builder_limit');?></label>
		<input type="text" name="tag_builder_element_limit" id="id_limit" value="" />
	</p>
	
	<p>
		<label for="id_wikipage"><?php $this->getLanguage('tag_builder_wikipage');?></label>
		<select id="id_wikipage" name="tag_builder_element_wikipage">
			<option value="Type your wikipage name here"><?php $this->getLanguage('tag_builder_startwikipage');?></option>
			<?php
			if (isset($wikipages)) {
			foreach ($wikipages as $key => $i):
			?>
			<option value="<?php echo $i['wikipage_name'];?>"><?php echo $i['wikipage_name'];?></option>
			<?php
			endforeach;
			}
			?>
		</select>
	</p>

	<p align="right">
		<input type="button" value="<?php $this->getLanguage('tag_builder_create_tag');?>" onClick="javascript:buildPluginBarnaiserWikiTag('history');" />
	</p>
<?php }?>
</form>