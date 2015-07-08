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



<form action="index.php?p=barnraiser_blog&amp;t=edit_blog&amp;wp=<?php echo $_REQUEST['wp'];?>" method="POST">
<input type="hidden" name="blog_id" value="<?php if (isset($blog['blog_id'])) { echo $blog['blog_id'];}?>" />


<div class="box">
	<div class="box_header">
		<h1><?php $this->getLanguage('hdr_blog_entry');?></h1>
	</div>
	
	<div class="box_body">
		<p>
			<label for="id_blog_title"><?php $this->getLanguage('common_title');?></label>
			<input type="text" name="blog_title" id="id_blog_title" value="<?php if (isset($blog['blog_title'])) { echo $blog['blog_title'];}?>" />
		</p>

		<p>
			<label for="id_blog_body"><?php $this->getLanguage('common_body');?></label>
			<textarea name="blog_body" id="id_blog_body" cols="80" rows="20"><?php if (isset($blog['blog_body'])) { echo $blog['blog_body'];}?></textarea>
		</p>
		
		<p>
			<label for="id_blog_allow_comment"><?php $this->getLanguage('label_allow_comment');?></label>
			<input type="checkbox" value="1" <?php if (!isset($blog) || isset($blog['blog_allow_comment']) && $blog['blog_allow_comment'] == 1) echo "checked=\"checked\"";?> name="blog_allow_comment" id="id_blog_allow_comment" />
		</p>
		
		<p align="right">
			<input type="submit" name="save_blog" value="<?php $this->getLanguage('common_save');?>" />&nbsp;
			<input type="submit" name="save_go_blog" value="<?php $this->getLanguage('common_save_go');?>" />
		</p>

		<p>
			<a href="index.php?wp=<?php echo $_REQUEST['wp'];?>"><?php $this->getLanguage('href_goto_webpage');?></a>
			<a href="#webpage_linker" onclick="javascript:objShowHide('core_webpage_linker');"><?php $this->getLanguage('common_webpage_helper');?></a>
			<a href="#picture_selector" onclick="javascript:objShowHide('core_picture_selector');"><?php $this->getLanguage('common_file_helper');?></a>
		</p>
	</div>
</div>
</form>

<?php
include ('core/template/inc/webpage_linker.inc.tpl.php');
include ('core/template/inc/picture_selector.inc.tpl.php');
?>