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

<form action="index.php?p=barnraiser_blog&amp;t=maintain&amp;wp=<?php echo $_REQUEST['wp'];?>" method="POST">

<div id="col_left_50">
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('hdr_maintain_blogs');?></h1>
		</div>
	
		<div class="box_body">
			<?php
			if (isset($blogs)) {
			?>
	
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td valign="top">
						<b><?php $this->getLanguage('common_title');?></b>
					</td>
					<td valign="top">
						<b><?php $this->getLanguage('txt_label_created');?></b>
					</td>
					<td valign="top">
						<b><?php $this->getLanguage('txt_label_archived');?></b>
					</td>
				</tr>
				<?php
				foreach ($blogs as $key => $i):
				?>
				<tr>
					<td valign="top">
						<a href="index.php?wp=<?php echo $_REQUEST['wp'];?>&amp;blog_id=<?php echo $i['blog_id'];?>"><?php echo $i['blog_title'];?></a>
					</td>
					<td valign="top">
						<?php echo strftime("%d %b %G %H:%M", $i['blog_create_datetime']);?>
					</td>
					<td valign="top">
						<?php
						$selected = "";
	
						if (!empty($i['blog_archived'])) {
							$selected = " checked=\"checked\"";
						}
						?>
	
						<input type="checkbox" name="blog_archived[<?php echo $i['blog_id'];?>]" value="1"<?php echo $selected;?> />
						<input type="hidden" name="blog_ids[]" value="<?php echo $i['blog_id'];?>" />
					</td>
				</tr>
				<?php
				endforeach;
				?>
			</table>
			
			<p align="right">
				<input type="submit" name="update_blogs" value="<?php $this->getLanguage('common_save');?>" />
			</p>
			<?php }?>
		</div>
	</div>
</div>

<div id="col_left_50">
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('hdr_preferences'); ?></h1>
		</div>

		<div class="box_body">
			<p>
				<label for="id_rss_title"><?php $this->getLanguage('label_rss_title'); ?></label>
				<input type="text" name="rss_title" id="id_rss_title" value="<?php echo $preferences['rss_title'];?>" />
			</p>
			<p>
				<label for="id_rss_title_reply"><?php $this->getLanguage('label_rss_title_comments'); ?></label>
				<input type="text" name="rss_title_comment" id="id_rss_title_comment" value="<?php echo $preferences['rss_title_comment'];?>" />
			</p>
		
			<p>
				<label for="id_rss_description"><?php $this->getLanguage('label_rss_desc'); ?></label>
				<input type="text" name="rss_description" id="id_rss_description" value="<?php echo $preferences['rss_description'];?>" />
			</p>
			
			<p>
				<label for="id_webpage"><?php $this->getLanguage('label_default_webpage'); ?></label>
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
				<input type="submit" name="save_preferences" value="<?php $this->getLanguage('common_save'); ?>" />
			</p>
		</div>
	</div>
</div>
</form>