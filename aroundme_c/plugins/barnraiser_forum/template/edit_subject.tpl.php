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
	function insertTag(t) {
	
		if (document.getElementById('id_'+t).checked) {
			if (document.getElementById('id_tags').value != "") {
				document.getElementById('id_tags').value += ',' + t;
			}
			else {
				document.getElementById('id_tags').value = t;
			}
		}
		else {
			if (document.getElementById('id_tags').value != "") {
				arr = document.getElementById('id_tags').value.split(',');
				
				for(i=0;i<arr.length;i++) {
					if (t == arr[i]) {
						arr[i] = "";
					}
				}
				
				out = "";
				for(i=0;i<arr.length;i++) {
					if (arr[i] != "") {
						out += arr[i];
					}
					if (i != arr.length-1) {
						out += ',';
					}
				}
				document.getElementById('id_tags').value = out;
			}
		}
	}
</script>

<form action="index.php?p=barnraiser_forum&amp;t=edit_subject&amp;wp=<?php echo $_REQUEST['wp'];?>" method="POST">
<input type="hidden" name="subject_id" value="<?php if (isset($subject['subject_id'])) { echo $subject['subject_id'];}?>" />


<div class="box">
	<div class="box_header">
		<h1><?php $this->getLanguage('hdr_create_subject');?></h1>
	</div>
	
	<div class="box_body">
		<p>
			<label for="id_subject_title"><?php $this->getLanguage('common_title');?></label>	
			<input type="text" name="subject_title" id="id_subject_title" value="<?php if (isset($subject['subject_title'])) { echo $subject['subject_title'];}?>" />
		</p>
		
		<?php if (isset($new_subject)) { ?>
			<p>
				<label for="id_subject_body"><?php $this->getLanguage('common_body');?></label>
				<textarea name="subject_body" id="id_subject_body" cols="80" rows="20"><?php if (isset($subject['subject_body'])) { echo $subject['subject_body'];}?></textarea>
			</p>
			
			<?php if (isset($popular_tags)) { ?>
			
			<p>
				<label><?php $this->getLanguage('label_popular_tags');?></label>
				<?php foreach($popular_tags as $p) {?>
				<?php $checked=""; ?>
					<?php if (isset($subject['tags'])) { ?>
						<?php foreach(explode(',', $subject['tags']) as $t) { ?>
							<?php if ($p['tag_name'] == $t) { $checked=" checked=\"checked\"";} ?>
						<?php } ?>
					<?php } ?>
					<input<?php echo $checked; ?> onchange="insertTag('<?php echo $p['tag_name']; ?>');" type="checkbox" id="id_<?php echo $p['tag_name']; ?>"/><label for="id_<?php echo $p['tag_name']; ?>" style="float: none; font-weight: normal; padding-right: 15px;"><?php echo $p['tag_name'];?></label>
				<?php } ?>
			</p>
			<?php } ?>
			
			<p>
				<label for="id_tags"><?php $this->getLanguage('label_tags');?></label>
				<input type="text" name="tags" id="id_tags" value="<?php if (isset($subject['tags'])) echo $subject['tags']; ?>" />
			</p>

			<p align="right">
				<input type="submit" name="save_subject" value="<?php $this->getLanguage('common_save');?>" />
				<input type="submit" name="save_go_subject" value="<?php $this->getLanguage('common_save_go');?>" />
			</p>

			<p>
				<a href="index.php?wp=<?php echo $_REQUEST['wp'];?>"><?php $this->getLanguage('href_goto_webpage');?></a>
				<a href="#webpage_linker" onclick="javascript:objShowHide('core_webpage_linker');"><?php $this->getLanguage('common_webpage_helper');?></a>
				<a href="#picture_selector" onclick="javascript:objShowHide('core_picture_selector');"><?php $this->getLanguage('common_file_helper');?></a>
			</p>
		<?php } elseif (!isset($subjects)) { ?>
			<p align="right">
				<input type="submit" name="create_discussion" id="id_create_discussion" value="<?php $this->getLanguage('sub_create_subject');?>"/>
			</p>
		<?php } ?>
	</div>
</div>

<?php
if (isset($subjects)) { 
?>
<div class="box">
	<div class="box_header">
		<h1><?php $this->getLanguage('hdr_similar_subjects');?></h1>
	</div>
	
	<div class="box_body">
		<p>
			<?php $this->getLanguage('txt_matched_subjects_prompt');?>
		</p>
		
		<ul>
			<?php foreach($subjects as $s) { ?>
				<li><b><a href="index.php?wp=<?php echo $s['wp']?>&amp;subject_id=<?php echo $s['subject_id'] ?>"><?php echo $s['subject_title'] ?></a></b><br /><?php echo $s['subject_body'] ?></li>
			<?php } ?>
		</ul>
			
		<p align="right">
			<input type="submit" name="continue_create_discussion" id="id_continue_create_discussion" value="<?php $this->getLanguage('sub_create_subject');?>" />
		</p>
	</div>
</div>
<?php }?>
</form>

<?php
include ('core/template/inc/webpage_linker.inc.tpl.php');
include ('core/template/inc/picture_selector.inc.tpl.php');
?>