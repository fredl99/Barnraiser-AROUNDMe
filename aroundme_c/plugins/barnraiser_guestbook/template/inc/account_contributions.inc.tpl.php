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

<h3><?php $this->getLanguage('plugin_barnraiser_guestbook_latest_contributions');?></h3>

<?php
if (isset($barnraiser_guestbook_entries)){
?>
<ul>
	<?php
	foreach ($barnraiser_guestbook_entries as $key => $i):
	?>
	<li><?php echo strftime("%d %b %G %H:%M", $i['guestbook_create_datetime']);?>: <?php echo strip_tags($i['guestbook_body'])?></li>
	<?php
	endforeach;
	?>
</ul>
<?php
}
else {
?>
<p>
	<?php $this->getLanguage('common_no_list_items');?>
</p>
<?php }?>