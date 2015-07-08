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

<form action="index.php?t=lock" method="post">

<?php
if ($webspace['status_id'] == 2) { //1=pending, 2=barred, 3=active
?>
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('hdr_webspace_barred');?></h1>
		</div>
	
		<div class="box_body">
			<p>
				<?php $this->getLanguage('core_webspace_barred_intro');?>
			</p>
		
			<ul>
				<li><a href="index.php?t=overview"><?php $this->getLanguage('core_list_webspaces');?></a></li>
			</ul>
		</div>
	</div>
<?php
}
elseif ($webspace['status_id'] == 1) { //1=pending, 2=barred, 3=active
?>
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('core_webspace_pending');?></h1>
		</div>
	
		<div class="box_body">
			<p>
				<?php $this->getLanguage('core_webspace_pending_intro');?>
			</p>

			<ul>
				<li><a href="index.php?t=overview"><?php $this->getLanguage('core_list_webspaces');?></a></li>
			</ul>
		</div>
	</div>
<?php
}
else {
?>
	<div id="col_left_50">
		<div class="box">
			<div class="box_header">
				<h1><?php $this->getLanguage('core_webspace_locked');?></h1>
			</div>
		
			<div class="box_body">
				<p>
					<?php $this->getLanguage('core_webspace_locked_intro');?>
				</p>

				<p>
					<label for="openid_login"><?php $this->getLanguage('common_openid');?></label>
					<input type="text" id="openid_login" name="openid_login" value="http://example.domain.org" onFocus="this.value=''; return false;" />
					<input type="submit" name="connect"  value="<?php $this->getLanguage('common_connect');?>" />
				</p>
			</div>
		</div>
	</div>

	<div id="col_left_50">
		<div class="box">
			<div class="box_header">
				<h1><?php $this->getLanguage('core_webspace_apply');?></h1>
			</div>
		
			<div class="box_body">
				<?php
				if (isset($display) && $display == "applied") {
				?>
					<p>
						<?php $this->getLanguage('core_webspace_applied_intro');?>
					</p>
				
				<?php
				}
				else {
				?>
					<p>
						<?php $this->getLanguage('core_webspace_apply_intro');?>
					</p>
			
					<p>
						<label for="id_openid"><?php $this->getLanguage('common_openid');?></label>
						<input type="text" id="id_openid" name="applicant_openid" value="<?php if (isset($_POST['applicant_openid'])) { echo $_POST['applicant_openid'];}?>" />
					</p>

					<p>
						<label for="id_applicant_nickname"><?php $this->getLanguage('common_nickname');?></label>
						<input type="text" id="id_applicant_nickname" name="applicant_nickname" value="<?php if (isset($_POST['applicant_nickname'])) { echo $_POST['applicant_nickname'];}?>" />
					</p>
					
					<p>
						<label for="id_applicant_email"><?php $this->getLanguage('common_email');?></label>
						<input type="text" id="id_applicant_email" name="applicant_email" value="<?php if (isset($_POST['applicant_email'])) { echo $_POST['applicant_email'];}?>" />
					</p>

					<p>
						<label for="id_applicant_note"><?php $this->getLanguage('common_note');?></label>
						<textarea id="id_applicant_note" name="applicant_note"><?php if (isset($_POST['applicant_note'])) { echo $_POST['applicant_note'];}?></textarea>
					</p>

					<p align="right">
						<input name="submit_application" type="submit" value="<?php $this->getLanguage('core_apply');?>" />
					</p>
				<?php }?>
			</div>
		</div>
	</div>
<?php }?>
</form>