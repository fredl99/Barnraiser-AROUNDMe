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

<h3><?php $this->getLanguage('plugin_barnraiser_forum_subject_contributions');?></h3>

<?php
if (isset($barnraiser_forum_subjects)) {
?>
<ul>
	<?php
	foreach ($barnraiser_forum_subjects as $key => $i):
	if (isset($plugin_barnraiser_forum_default_webpage)) {
	?>
		<li><a href="index.php?wp=<?php echo $plugin_barnraiser_forum_default_webpage;?>&amp;subject_id=<?php echo $i['subject_id'];?>"><?php echo strftime("%d %b %G %H:%M", $i['subject_create_datetime']);?></a>: <?php echo $i['subject_title'];?></li>
	<?php
	}
	else {
	?>
		<li><?php echo strftime("%d %b %G %H:%M", $i['subject_create_datetime']);?>: <?php echo $i['subject_title'];?></li>
	<?php }?>
	
	<?php
	endforeach;
	?>
</ul>
<?php
}
else {
?>
<p>
	<?php $this->getLanguage('common_no_list_items');?><br />
</p>
<?php }?>