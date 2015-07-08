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

<form action="index.php?p=barnraiser_contact&amp;t=maintain&amp;wp=<?php echo $_REQUEST['wp'];?>" method="POST">

<div class="box">
	<div class="box_header">
		<h1><?php $this->getLanguage('hdr_receiver_emails');?></h1>
	</div>

	<div class="box_body">
		<p>
			<?php $this->getLanguage('receiver_emails_intro');?>
		</p>

		<p>
			<label for="id_email1"><?php $this->getLanguage('label_email1');?></label>
			<input type="text" name="recipient_emails[0]" id="id_email1" value="<?php if(isset($recipient_emails[0]['recipient_email'])) { echo $recipient_emails[0]['recipient_email'];}?>" />
		</p>

		<p>
			<label for="id_email2"><?php $this->getLanguage('label_email2');?></label>
			<input type="text" name="recipient_emails[1]" id="id_email2" value="<?php if(isset($recipient_emails[1]['recipient_email'])) { echo $recipient_emails[1]['recipient_email'];}?>" />
		</p>

		<p>
			<label for="id_email3"><?php $this->getLanguage('label_email3');?></label>
			<input type="text" name="recipient_emails[2]" id="id_email3" value="<?php if(isset($recipient_emails[2]['recipient_email'])) { echo $recipient_emails[2]['recipient_email'];}?>" />
		</p>

		<p>
			<label for="id_email4"><?php $this->getLanguage('label_email4');?></label>
			<input type="text" name="recipient_emails[3]" id="id_email4" value="<?php if(isset($recipient_emails[3]['recipient_email'])) { echo $recipient_emails[3]['recipient_email'];}?>" />
		</p>

		<p>
			<label for="id_email5"><?php $this->getLanguage('label_email5');?></label>
			<input type="text" name="recipient_emails[4]" id="id_email5" value="<?php if(isset($recipient_emails[4]['recipient_email'])) { echo $recipient_emails[4]['recipient_email'];}?>" />
		</p>

		<p align="right">
			<input type="submit" name="save_recipient_emails" value="<?php $this->getLanguage('common_save');?>" />
		</p>
	</div>
</div>
</form>