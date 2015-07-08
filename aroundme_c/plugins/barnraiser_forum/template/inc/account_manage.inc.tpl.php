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

<h3><?php $this->getLanguage('plugin_barnraiser_forum_forum_options');?></h3>

<form action="plugins/barnraiser_forum/set_tracking_notification.php" method="post">

<div class="block">
	<?php
	if (isset($plugin_barnraiser_forum_subject_tracking)) {
	?>
	<div class="block_body">
		<table cellspacing="2" cellpadding="0" border="0" width="100%">
			<?php
			foreach ($plugin_barnraiser_forum_subject_tracking as $key => $i):
			?>
			<tr>
				<td valign="top">
					<a href="index.php?wp=<?php echo $plugin_barnraiser_forum_default_webpage;?>&amp;subject_id=<?php echo $i['subject_id'];?>" class="title"><?php echo $i['subject_title'];?></a>
					<?php
					if (!empty($i['notification'])) {
					?>
					<sup><?php $this->getLanguage('plugin_barnraiser_forum_notification_set');?></sup>
					<?php }?>
				</td>
				<td valign="top" align="right">
					<input type="checkbox" name="subject_ids[]" value="<?php echo $i['subject_id'];?>" />
				</td>
			</tr>
			<?php
			endforeach;
			?>
		</table>

		<p class="barnraiser_subject_email_hint">
			<?php $this->getLanguage('plugin_barnraiser_forum_notification_sent_to');?><?php echo $_SESSION['openid_email'];?>
		</p>
	</div>

	<div class="block_footer">
		<input type="submit" name="management_option_remove_subject_tracking" value="<?php $this->getLanguage('plugin_barnraiser_forum_remove_tracking');?>" />
		<input type="submit" name="management_option_remove_subject_tracking_notify" value="<?php $this->getLanguage('plugin_barnraiser_forum_remove_notifications');?>" />
	</div>
	
	<?php
	}
	else {
	?>
	<div class="block_body">
		<p>
			<?php $this->getLanguage('common_no_list_items');?>
		</p>
	</div>
	<?php }?>
</div>


<h3><?php $this->getLanguage('plugin_barnraiser_forum_recieve_digest');?></h3>


<div class="block">
	<?php
	if (!empty($_SESSION['openid_email'])) {
	?>
		<div class="block_body">
			<ul>
				<li><input type="radio" name="digest_frequency" value="0" checked="checked" /><?php $this->getLanguage('plugin_barnraiser_forum_never');?></li>
				<li><input type="radio" name="digest_frequency" value="1"<?php if(isset($plugin_barnraiser_forum_digest_frequency) && $plugin_barnraiser_forum_digest_frequency==1) { echo " checked=\"checked\"";}?> /><?php $this->getLanguage('plugin_barnraiser_forum_daily');?></li>
				<li><input type="radio" name="digest_frequency" value="7"<?php if(isset($plugin_barnraiser_forum_digest_frequency) && $plugin_barnraiser_forum_digest_frequency==7) { echo " checked=\"checked\"";}?> /><?php $this->getLanguage('plugin_barnraiser_forum_weekly');?></li>
				<li><input type="radio" name="digest_frequency" value="30"<?php if(isset($plugin_barnraiser_forum_digest_frequency) && $plugin_barnraiser_forum_digest_frequency==30) { echo " checked=\"checked\"";}?> /><?php $this->getLanguage('plugin_barnraiser_forum_monthly');?></li>
			</ul>
		</div>

		<div class="block_footer">
			<input type="submit" name="set_digest_frequency" value="<?php $this->getLanguage('plugin_barnraiser_forum_set_frequency');?>" />
		</div>
	<?php
	}
	else {
	?>
		<div class="block_body">
			<p>
				<?php $this->getLanguage('plugin_barnraiser_forum_cannot_recieve_digest');?>
			</p>
		</div>
	<?php }?>
</div>
</form>