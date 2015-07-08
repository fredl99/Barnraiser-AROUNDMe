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

<script type="text/javascript">
	function viewTag(t, s) {
		document.getElementById('id_selected_tag_1').value = t;
		document.getElementById('id_selected_tag_2').value = t;
		if (s == 1) {
			document.getElementById('id_sticky_tag').checked = true;
		}
		else {
			document.getElementById('id_sticky_tag').checked = false;
		}
		document.getElementById('manage_tag').style.display = "block";
	}
</script>

<form action="index.php?p=barnraiser_forum&amp;t=maintain&amp;wp=<?php echo $_REQUEST['wp'];?>" method="POST">

<div id="col_left_50">
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('hdr_maintain_subjects');?></h1>
		</div>

		<div class="box_body">
			<?php
			if (isset($subjects)) {
			?>
			
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td valign="top">
						<b><?php $this->getLanguage('common_title');?></b>
					</td>
					<td valign="top">
						<b><?php $this->getLanguage('txt_label_created');?></b>
					</td>
					<td valign="top" align="right">
						<b><?php $this->getLanguage('txt_label_archived');?></b>
					</td>
				</tr>
				<?php
				foreach ($subjects as $key => $i):
				?>
				<tr>
					<td valign="top">
						<a href="index.php?wp=<?php echo $i['wp'];?>&amp;subject_id=<?php echo $i['subject_id'];?>"><?php echo $i['subject_title'];?></a><br />
					</td>
					<td valign="top">
						<?php echo strftime("%d %b %G", $i['subject_create_datetime']);?><br />
					</td>
					<td valign="top" align="right">
						<?php
						$selected = "";
						
						if (!empty($i['subject_archived'])) {
							$selected = " checked=\"checked\"";
						}
						?>

						<input type="checkbox" name="subject_archived[<?php echo $i['subject_id'];?>]" value="1"<?php echo $selected;?> /><br />
						<input type="hidden" name="subject_ids[]" value="<?php echo $i['subject_id'];?>" />
					</td>
				</tr>
				<?php
				endforeach;
				?>
				</table>
				
				<p align="right">
					<input type="submit" name="update_subjects" value="<?php $this->getLanguage('common_save');?>" />
				</p>
			<?php }?>
		</div>
	</div>
</div>

<div id="col_right_50">
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('hdr_tag_management');?></h1>
		</div>

		<div class="box_body">
			<?php if (isset($output_tags)) { ?>
				<?php
					$tags = ""; 
					foreach($output_tags as $key => $t):
						$tags .= "<a href=\"#\" onclick=\"viewTag('" . $t['tag_name'] . "', " . $t['sticky'] . ");\">" . $t['tag_name'] . "</a>(" .$t['tag_total'] . "), ";
					endforeach;
					echo "<p>" . rtrim($tags, ', ') . "</p>";
				?>
			<?php } else { ?>
				<p><?php $this->getLanguage('common_no_list_items');?></p>
			<?php } ?>
			<div id="manage_tag" style="display: none;">
				<input type="hidden" value="" name="selected_tag_1" id="id_selected_tag_1"/>
				<label for="id_selected_tag_2"><?php $this->getLanguage('label_rename');?></label><input type="text" value="" name="selected_tag_2" id="id_selected_tag_2"/><br />
				<label for="id_sticky_tag"><?php $this->getLanguage('label_sticky');?></label><input type="checkbox" value="1" name="sticky_tag" id="id_sticky_tag"/>
				<p align="right">
					<input type="submit" value="<?php $this->getLanguage('common_delete');?>" name="delete_tag" /><input type="submit" value="<?php $this->getLanguage('common_save');?>" name="save_tag" />
				</p>
			</div>
		</div>
	</div>

	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('hdr_preferences');?></h1>
		</div>

		<div class="box_body">
			<p>
				<label for="id_webpage"><?php $this->getLanguage('label_default_webpage');?></label>
				<select id="id_webpage" name="default_webpage_id">
					<option value="0" selected="selected"></option>
					<?php
					if (isset($webpages)) {
					foreach ($webpages as $key => $i):

					$selected = "";

					if ($preferences['default_webpage_id'] == $i['webpage_id']) {
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

			<p>
				<i><?php $this->getLanguage('label_default_webpage_hint');?></i>
			</p>
		
			<p align="right">
				<input type="hidden" name="preference_id" value="<?php if (isset($preferences['preference_id'])) { echo $preferences['preference_id'];}?>" />
				<input type="submit" name="save_preferences" value="<?php $this->getLanguage('common_save');?>" />
			</p>
		</div>
	</div>
</div>
</form>