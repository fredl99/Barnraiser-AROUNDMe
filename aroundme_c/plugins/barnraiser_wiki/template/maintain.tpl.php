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


<form action="index.php?p=barnraiser_wiki&amp;t=maintain&amp;wp=<?php echo $_REQUEST['wp'];?>" method="POST">

<div id="col_left_50">
	<?php
	if (isset($wikipages)) {
	?>
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('hdr_wiki_pages');?></h1>
		</div>
	
		<div class="box_body">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<?php
				foreach ($wikipages as $key => $i):
				?>
				<tr>
					<td valign="top">
					<?php echo $i['wikipage_name'];?>
					</td>
				</tr>
				<?php
				endforeach;
				?>
			</table>
		</div>
	</div>
	<?php }?>
</div>

<div id="col_right_50">
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('hdr_preferences');?></h1>
		</div>

		<div class="box_body">
			<p>
				<label for="id_webpage"><?php $this->getLanguage('label_default_webpage');?></label>
				<select id="id_webpage" name="default_webpage_id">
					<?php
					if (isset($webpages)) {
					foreach ($webpages as $key => $i):

					$selected = "";

					if ($preferences['default_webpage_id'] == $i['webpage_name'] || $preferences['default_webpage_id'] == $i['webpage_id']) {
						$selected = " selected=\"selected\"";
					}
					?>
					<option value="<?php echo $i['webpage_id'];?>"<?php echo $selected;?>><?php echo $i['webpage_name'];?></option>
					<?php
					endforeach;
					}
					?>
				</select>
			</p>
		
			<p align="right">
				<input type="hidden" name="preference_id" value="<?php if (isset($preferences['preference_id'])) { echo $preferences['preference_id'];}?>" />
				<input type="submit" name="save_preferences" value="<?php $this->getLanguage('common_save');?>" />
			</p>
		</div>
	</div>
</div>
</form>