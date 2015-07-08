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

<form name="upload_file" action="index.php?t=file<?php if (isset($_REQUEST['view']) && $_REQUEST['view'] == 'list') echo '&view=list';?>" method="POST" enctype="multipart/form-data">
<input type="hidden" name="webpage_id" value="<?php if (isset($webpage['webpage_id'])) { echo $webpage['webpage_id'];}?>" />


	<?php
if (isset($arr_group) && isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $arr_group['publisher']) {
?>

<div class="box">
	<div class="box_header">
		<h1><?php $this->getLanguage('core_upload_file');?></h1>
	</div>
	<div class="box_body">
		<p>
			<label for="frm_file"><?php $this->getLanguage('core_file'); ?></label>
			<input type="file" name="frm_file" id="frm_file" />
		</p>

		<p class="note">
			<?php
			$note = $this->lang['core_note_max_file_size'];
			$note = str_replace('SYS_KEYWORD_MAX_FILE_SIZE', ini_get('upload_max_filesize'), $note);
			echo $note;
			?>
		</p>
	
		<p>
			<label for="frm_title"><?php $this->getLanguage('common_title'); ?></label>
			<input type="text" name="frm_title" id="frm_title" value=""/>
		</p>
			
		<p class="note">
			<?php $this->getLanguage('core_width_intro'); ?>
		</p>
		
		<p>
			<label for="frm_file_name"><?php $this->getLanguage('core_width'); ?></label>
			<input type="text" name="file_width" size="4" value=""/>
			&nbsp;<img src="<?php echo AM_TEMPLATE_PATH;?>img/measure.png" width="150" height="12" border="0" alt="" />
			&nbsp;<?php $this->getLanguage('core_pixels');?>
		</p>
	
		<p align="right">
			<input type="submit" name="submit_file_upload" value="<?php $this->getLanguage('common_upload');?>" />
		</p>
	</div>
</div>
<?php }?>

<?php if (isset($file)) { ?>
<div class="box" id="id_selected_file">
	<div class="box_header">
		<h1><?php $this->getLanguage('core_selected_file');?></h1>
	</div>
	<div class="box_body">
		<table cellspacing="4" cellpadding="0" border="0">
			<tr>
				<td align="left" valign="top">
					<?php if (isset($file['thumb_90'])) { ?>
					<table width="100%" cellspacing="4">
						<tr>
							<td align="left" valign="top" colspan="2">
								<img id="id_file_1" src="core/get_file.php?file=<?php echo $file['file_name']; ?>" class="picture" style="cursor: pointer;" title="click to view img tag" onclick="viewTag('id_file_1', 1);"/>
							</td>
						</tr>
						<tr>
							<td align="right" valign="top">
								<img id="id_file_2" src="core/get_file.php?file=<?php echo $file['thumb_90']; ?>" class="picture" style="cursor: pointer;" title="click to view img tag" onclick="viewTag('id_file_2', 1);"/>
							</td>
							<td align="left" valign="top">
								<img id="id_file_3" src="core/get_file.php?file=<?php echo $file['thumb_35']; ?>" class="picture" style="cursor: pointer;" title="click to view img tag" onclick="viewTag('id_file_3', 1);"/>
							</td>
						</tr>
					</table>
					<?php } else { ?>
						<img id="id_file_1" style="border: 1px solid black; cursor: pointer;" src="<?php echo AM_TEMPLATE_PATH; ?><?php echo $arr_file_type[$file['file_type']]['image'][1];?>" />
					<?php } ?>
				</td>
				<td valign="top" align="left">
					<b><?php $this->getLanguage('common_title'); ?></b>: <?php echo $file['file_title']; ?><br />
					<b><?php $this->getLanguage('core_uploaded'); ?></b>: <?php echo $file['file_create_datetime']; ?><br />
					<b><?php $this->getLanguage('core_size'); ?></b>: <?php echo $file['file_size']; ?> kb<br />
					<b><?php $this->getLanguage('core_type'); ?></b>: <?php echo $file['file_type']; ?><br />
					<b><?php $this->getLanguage('core_file_tag'); ?></b>: <input type="text" value="" id="file_tag" onclick="javascript:this.focus();this.select();" readonly="true"/><br />
					<b><?php $this->getLanguage('common_view'); ?></b>: <a href="core/get_file.php?file=<?php echo $file['file_name']; ?>"><?php echo $file['file_title']; ?></a><br />
					
					<?php if (!isset($in_use)) { ?>
						<input type="hidden" name="file_to_delete" value="<?php echo $file['file_name'];?>"/>
						<input type="submit" name="delete_file" value="<?php $this->getLanguage('common_delete'); ?>" />
					<?php } else { ?>
						<p>
							<?php echo $lang['core_file_in_use'];?>:<br />
							<?php foreach($in_use as $key => $val) { ?>
								<b><?php echo $val['type'];?>: </b><a href="<?php echo $val['link']; ?>"><?php echo $val['name']; ?></a>
							<?php } ?>
						</p>
					<?php } ?>
				</td>
			</tr>
		</table>
	</div>
</div>
<script type="text/javascript">
	function viewTag(id, t) {
		if (t == 1) {
			path = document.getElementById(id).src;
			document.getElementById('file_tag').value = "<img src=\"" + path + "\" alt=\"\" />";
		}
		else {
			document.getElementById('file_tag').value = "<a href=\"core/get_file.php?file=<?php echo $file['file_name']; ?>\"><?php echo $file['file_title']; ?></a>";
		}
	}
	<?php if (isset($file['thumb_90'])) { ?>
		viewTag('id_file_1', '1');
	<?php } else { ?>
		viewTag('id_file_1', '0');
	<?php } ?>
</script>
<?php } ?>

<div class="box">
	<div class="box_header">
		<h1><?php $this->getLanguage('core_files');?></h1>
		<div style="text-align: right;">
			<?php if (isset($_REQUEST['view']) && $_REQUEST['view'] == 'list') { ?>
				<b><a href="index.php?t=file#files"><?php $this->getLanguage('common_thumb');?></a> / <?php $this->getLanguage('common_list');?></b>
			<?php } else { ?>
				<b><?php $this->getLanguage('common_thumb');?> / <a href="index.php?t=file&amp;view=list#files"><?php $this->getLanguage('common_list');?></a></b>
			<?php } ?>
		</div>
	</div>
	<div class="box_body">
		<?php if (isset($files)) { ?>
			<?php if (isset($_REQUEST['view']) && $_REQUEST['view'] == 'list') { ?>
				<table width="100%" border="1">
					<tr>
						<th></th>
						<th align="left"><?php $this->getLanguage('common_title');?></th>
						<th align="left"><?php $this->getLanguage('core_size');?></th>
						<th align="left"><?php $this->getLanguage('core_type');?></th>
						<th align="left"><?php $this->getLanguage('core_upload_datetime');?></th>
					</tr>
				<?php foreach($files as $i): ?>
					<tr>
					<?php if (isset($i['thumb_35'])) { ?>
						<td><a href="index.php?t=file&amp;file_name=<?php echo $i['file_name']; ?>&amp;view=list"><img src="core/get_file.php?file=<?php echo $i['thumb_35']; ?>" style="border: none;"/></a></td>
						<td><a href="index.php?t=file&amp;file_name=<?php echo $i['file_name']; ?>&amp;view=list"><?php echo wordwrap($i['file_title'], 20,"<br />\n", 1); ?></a></td>
						<td><?php echo $i['file_type']; ?></td>
						<td><?php echo $i['file_size']; ?></td>
						<td><?php echo $i['file_create_datetime']; ?></td>
					<?php } else { ?>
						<td><img src="<?php echo AM_TEMPLATE_PATH; ?><?php echo $arr_file_type[$i['file_type']]['image'][2];?>" style="border: none;"/></td>
						<td><a href="index.php?t=file&amp;file_name=<?php echo $i['file_name']; ?>"><?php echo wordwrap($i['file_title'], 20,"<br />\n", 1); ?></a></td>
						<td><?php echo $i['file_type']; ?></td>
						<td><?php echo $i['file_size']; ?></td>
						<td><?php echo $i['file_create_datetime']; ?></td>
					<?php } ?>
					</tr>
				<?php endforeach; ?>
				</table>
			<?php } else { ?>
				<?php foreach($files as $i): ?>
					<div style="float: left; padding-right: 10px; padding-bottom: 10px;">
						<a href="index.php?t=file&amp;file_name=<?php echo $i['file_name']; ?>">
						<?php if (isset($i['thumb_90'])) { ?>
							<img style="border: 1px solid black; cursor: pointer;" src="core/get_file.php?file=<?php echo $i['thumb_90']; ?>" title="<?php echo $i['file_title']; ?>.  <?php echo $i['file_create_datetime']; ?>" />
						<?php } else { ?>
							<img style="border: 1px solid black; cursor: pointer;" src="<?php echo AM_TEMPLATE_PATH; ?><?php echo $arr_file_type[$i['file_type']]['image'][1];?>" title="<?php echo $i['file_title']; ?>. <?php echo $i['file_create_datetime']; ?>" />
						<?php } ?>
						</a>
						<br />
						<span style="font-weight: bold;"><?php echo wordwrap($i['file_title'], 11,"<br />\n", 1); ?></span>
					</div>
				<?php endforeach; ?>
			<?php } ?>
		<?php } ?>
		<div style="clear: both;"></div>
	</div>
</div>