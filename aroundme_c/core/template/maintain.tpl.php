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

<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title><?php $this->getLanguage('common_html_title');?></title>
	
	<style type="text/css">
	<!--
	@import url(<?php echo AM_TEMPLATE_PATH;?>css/aroundme.css);
	-->
	</style>
	
	<!--[if IE]>
	<style type="text/css">
	@import url(<?php echo AM_TEMPLATE_PATH;?>css/aroundme-IE.css);
	</style>
	<![endif]-->
</head>

<body id="am_admin">
	<?php
	if (isset($_SESSION['am_maintainer'])) {
	?>
	<div id="am_menu_container">
		<ul>
			<li><a href="maintain.php"><?php $this->getLanguage('maintain_am_menu_home');?></a></li>
			<li><a href="maintain.php?v=list"><?php $this->getLanguage('maintain_am_menu_list');?></a></li>
			<li><a href="maintain.php?v=config"><?php $this->getLanguage('maintain_am_menu_configure');?></a></li>
			<li><a href="maintain.php?disconnect=1"><?php $this->getLanguage('maintain_am_menu_disconnect');?></a></li>
		</ul>
	</div>
	<?php }?>
	
	<?php
	if (!empty($GLOBALS['am_error_log'])) {
	?>
	<div id="error_container">
		<?php
		foreach($GLOBALS['am_error_log'] as $key => $i):
		?>
			<?php
			if (isset($this->lang['arr_am_error'][$i[0]])) {
				echo $this->lang['arr_am_error'][$i[0]];
			}
			else {
				echo $i[0];
			}
	
			if (!empty($i[1])) {
				echo ": " . $i[1];
			}?>
			<br />
		<?php
		endforeach;
		?>
	</div>
	<?php }?>
	
	<div id="body_container">
		<form action="maintain.php" method="POST">
		<input type="hidden" name="webspace_id" value="<?php if (isset($webspace['webspace_id'])) { echo $webspace['webspace_id'];}?>" />

		<?php
		if (isset($_SESSION['am_maintainer']) && isset($_REQUEST['v']) && $_REQUEST['v'] == "list") {
		?>

			<?php
			if (isset($webspace)) {
			$url = str_replace('REPLACE', $webspace['webspace_unix_name'], $domain_replace_pattern);
			?>
			<h1><?php $this->getLanguage('maintain_manage_webspace');?></h1>
			
			<p>
				<b><?php $this->getLanguage('maintain_name');?></b><br />
				<a href="<?php echo $url;?>"><?php echo $webspace['webspace_unix_name'];?></a>
			</p>

			<p>
				<b><?php $this->getLanguage('common_nickname');?></b><br />
				<?php echo $webspace['connection_nickname'];?>
				<?php
				if (!empty($webspace['connection_fullname'])) {
				?>
				(<?php echo $webspace['connection_fullname'];?>)
				<?php }?>
				<br />
			</p>

			<p>
				<b><?php $this->getLanguage('common_openid');?></b><br />
				<a href="<?php echo $webspace['connection_openid'];?>"><?php echo $webspace['connection_openid'];?></a>
			</p>

			<p>
				<b><?php $this->getLanguage('common_email');?></b><br />
				<a href="mailto:<?php echo $webspace['connection_email'];?>"><?php echo $webspace['connection_email'];?></a>
			</p>

			<p>
				<b><?php $this->getLanguage('maintain_created');?></b><br />
				<?php echo strftime("%d %b %G", $webspace['webspace_create_datetime']);?>
			</p>

			<p>
				<b><?php $this->getLanguage('common_language');?></b><br />
				<?php echo $webspace['language_code'];?>
			</p>

			<p>
				<b><?php $this->getLanguage('maintain_locked');?></b><br />
				<?php echo $webspace['webspace_locked'];?>
			</p>

			<p>
				<label for="id_webspace_allocation"><?php $this->getLanguage('common_status');?></label><br />
				<select name="status_id">
					<?php
					$selected = "";

					if ($webspace['status_id'] == 1) {
						$selected = " selected=\"selected\"";
					}
					?>
					<option value="1"<?php echo $selected ;?>><?php echo $this->lang['arr_webspace_status'][1];?></option>
					<?php
					$selected = "";

					if ($webspace['status_id'] == 2) {
						$selected = " selected=\"selected\"";
					}
					?>
					<option value="2"<?php echo $selected ;?>><?php echo $this->lang['arr_webspace_status'][2];?></option>
					<?php
					$selected = "";

					if ($webspace['status_id'] == 3) {
						$selected = " selected=\"selected\"";
					}
					?>
					<option value="3"<?php echo $selected ;?>><?php echo $this->lang['arr_webspace_status'][3];?></option>
				</select>
			</p>

			<p>
				<label for="id_webspace_allocation"><?php $this->getLanguage('maintain_allocation');?></label><br />
				<input id="id_webspace_allocation" size="5" name="webspace_allocation" value="<?php echo $webspace['webspace_allocation'];?>" />&nbsp;<?php $this->getLanguage('maintain_kilobyte');?>
			</p>

			<p align="right">
				<input type="submit" name="update_webspace" value="<?php $this->getLanguage('common_save');?>" />
			</p>

			<?php
			}
			elseif (!empty($webspaces)) {
			?>
			<h1><?php $this->getLanguage('maintain_manage_webspace');?></h1>
			
			<table cellspacing="2" cellpadding="2" border="0" width="100%">
				<tr>
					<td valign="top">
						<b><?php $this->getLanguage('maintain_name');?></b>
					</td>
					<td valign="top">
						<b><?php $this->getLanguage('common_status');?></b>
					</td>
					<td valign="top">
						<b><?php $this->getLanguage('maintain_created');?></b>
					</td>
					<td valign="top">
						<b><?php $this->getLanguage('maintain_allocation');?></b>
					</td>
					<td valign="top">
						<b><?php $this->getLanguage('common_openid');?></b>
					</td>
					<td valign="top">
						<b><?php $this->getLanguage('common_language');?></b>
					</td>
					<td valign="top" align="right">
						<b><?php $this->getLanguage('common_email');?></b>
					</td>
				</tr>
				<?php
				foreach ($webspaces as $key => $i):
				?>
				<tr>
					<td valign="top">
						<a href="maintain.php?webspace_id=<?php echo $i['webspace_id'];?>"><?php echo $i['webspace_unix_name'];?></a>
					</td>
					<td valign="top">
						<?php
						if (isset($this->lang['arr_webspace_status'][$i['status_id']])) {
							echo $this->lang['arr_webspace_status'][$i['status_id']];
						}
						else {
							echo $i['status_id'];
						}
						?>
					</td>
					<td valign="top">
						<?php echo strftime("%d %b %G", $i['webspace_create_datetime']);?>
					</td>
					<td valign="top">
						<?php echo $i['webspace_allocation'];?>&nbsp;<?php $this->getLanguage('maintain_kilobyte');?>
					</td>
					<td valign="top">
						<a href="<?php echo $i['connection_openid'];?>"><?php $this->getLanguage('maintain_visit');?></a>
					</td>
					<td valign="top">
						<?php echo $i['language_code'];?>
					</td>
					<td valign="top" align="right">
						<?php
						if (!empty($i['connection_email'])) {
						?>
						<a href="mailto:<?php echo $i['connection_email'];?>"><?php $this->getLanguage('common_email');?></a>
						<?php }?>
					</td>
				</tr>
				<?php
				endforeach;
				?>
			</table>
			
			<p class="buttons">
				<a href="create/create.php"><?php $this->getLanguage('maintain_add_webspace');?></a>
			</p>
			<?php
			}
			else {
			?>
			<h1><?php $this->getLanguage('maintain_manage_webspace');?></h1>

			<p>
				<?php echo $lang['common_no_list_items'];?>
			</p>

			<p class="buttons">
				<a href="create/create.php"><?php $this->getLanguage('maintain_add_webspace');?></a>
			</p>
			<?php }?>
			
		<?php
		}
		elseif (isset($_SESSION['am_maintainer']) && isset($_REQUEST['v']) && $_REQUEST['v'] == "config") {
		?>

			<div id="col_left_50">
				<div class="box">
					<div class="box_header">
						<h1><?php $this->getLanguage('maintain_configure_webspace');?></h1>
					</div>
	
					<div class="box_body">
						<p>
							<label for="id_file_default_allocation"><?php $this->getLanguage('maintain_allocation');?></label>
							<input type="text" name="file_default_allocation" id="id_file_default_allocation" value="<?php echo $default_allocation;?>" />
						</p>

						<p class="note">
							The default allocation is the amount of disk space (measured in Mb) you allow a webspace to use by default. You can over-ride this by editing a webspace.
						</p>
						
						<p>
							<label for="id_display_max_list_rows"><?php $this->getLanguage('maintain_list_length');?></label>
							<input type="text" name="display_max_list_rows" id="id_display_max_list_rows" value="<?php echo $max_list_rows;?>" />
						</p>

						<p class="note">
							The list length defines the number of items you want listed before the "next/previous" buttons are displayed.
						</p>
						
						<p>
							<label for="id_openid0"><?php $this->getLanguage('maintain_create_type');?></label><br />
							<input type="radio" name="webspace_creation_type" value="0" checked="checked" /><label for="id_openid0" class="radio_label"><?php $this->getLanguage('maintain_create_type_none');?></label><br />
							<input type="radio" name="webspace_creation_type" value="1"<?php if ($webspace_creation_type == 1) { echo " checked=\"checked\"";}?> /><label for="id_openid1" class="radio_label"><?php $this->getLanguage('maintain_create_type_approve');?></label><br />
							<input type="radio" name="webspace_creation_type" value="2"<?php if ($webspace_creation_type == 2) { echo " checked=\"checked\"";}?> /><label for="id_openid2" class="radio_label"><?php $this->getLanguage('maintain_create_type_auto');?></label>
						</p>

						<p>
							<label for="id_reserved_webspace_names"><?php $this->getLanguage('maintain_reserved_names');?></label>
							<input type="text" name="reserved_webspace_names" id="id_reserved_webspace_names" value="<?php echo $excluded_webspace_names;?>" />
						</p>

						<p class="note">
							Use a comma to separate each name.
						</p>

						<p class="buttons">
							<input type="submit" name="save_config" value="<?php $this->getLanguage('common_save');?>" />
						</p>
					</div>
				</div>
			</div>
					
			<div id="col_right_50">
				<div class="box">
					<div class="box_header">
						<h1><?php $this->getLanguage('maintain_configure_email');?></h1>
					</div>
	
					<div class="box_body">
						<p>
							<label for="id_email_address"><?php $this->getLanguage('maintain_email_default');?></label>
							<input type="text" name="email_address" id="id_email_address" value="<?php echo $arr_mail['email_address'];?>" />
						</p>

						<p class="note">
							This is the default address from which emails are sent.
						</p>
					
						<p>
							<label for="id_email_host"><?php $this->getLanguage('maintain_email_host');?></label>
							<input type="text" name="email_host" id="id_email_host" value="<?php echo $arr_mail['host']?>" />
						</p>

						<p class="note">
							<?php $this->getLanguage('maintain_email_host_help');?>
						</p>
					
						<p>
							<label for="id_email_smtp_user"><?php $this->getLanguage('maintain_email_username');?></label>
							<input type="text" name="smtp_user" id="id_email_smtp_user" value="<?php if (isset($arr_mail['smtp']['username'])) { echo $arr_mail['smtp']['username'];}?>" />
						</p>
					
						<p>
							<label for="id_email_smtp_password"><?php $this->getLanguage('maintain_email_password');?></label>
							<input type="text" name="smtp_password" id="id_email_smtp_password" value="<?php if (isset($arr_mail['smtp']['password'])) { echo $arr_mail['smtp']['password'];}?>" />
						</p>

						<p class="note">
							<?php $this->getLanguage('maintain_email_smtp_user_pass');?>
						</p>

						<p class="buttons">
							<input type="submit" name="save_email" value="<?php $this->getLanguage('common_save');?>" />
						</p>
					</div>
				</div>
				
				<div class="box">
					<div class="box_header">
						<h1><?php $this->getLanguage('maintain_configure_domain');?></h1>
					</div>
	
					<div class="box_body">
						<p>
							<?php $this->getLanguage('maintain_configure_domain_intro');?>
						</p>

						<p>
							<label for="id_domain_preg_pattern"><?php $this->getLanguage('maintain_pattern_parse');?></label>
							<input type="text" name="domain_preg_pattern" id="domain_preg_pattern" value="<?php echo $domain_preg_pattern;?>" />
						</p>
		
						<p>
							<label for="id_domain_replace_pattern"><?php $this->getLanguage('maintain_pattern_render');?></label>
							<input type="text" name="domain_replace_pattern" id="id_domain_replace_pattern" value="<?php echo $domain_replace_pattern;?>" />
						</p>

						<p class="buttons">
							<input type="submit" name="save_patterns" value="<?php $this->getLanguage('common_save');?>" />
						</p>
					</div>
				</div>
			</div>
		<?php
		}
		elseif (isset($_SESSION['am_maintainer'])) {
		?>

			<div id="col_left_50">
				<?php
				if (isset($_REQUEST['installed'])) {
				?>
				<div class="box">
					<div class="box_header">
						<h1><?php $this->getLanguage('maintain_installed');?></h1>
					</div>
	
					<div class="box_body">
						<p>
							<?php $this->getLanguage('maintain_maintain_intro');?>
						</p>
					</div>
				</div>
				<?php }?>
						

				<div class="box">
					<div class="box_header">
						<h1><?php $this->getLanguage('maintain_create_webspace');?></h1>
					</div>
	
					<div class="box_body">
						<p>
							<?php
							if ($webspace_creation_type == 2) {
							?>
							<?php $this->getLanguage('maintain_create_webspace_none');?>
							<?php
							}
							elseif ($webspace_creation_type == 1) {
							?>
							<?php $this->getLanguage('maintain_create_webspace_approve');?>
							<?php
							}
							else {
							?>
							<?php $this->getLanguage('maintain_create_webspace_auto');?>
							<?php }?>
						</p>

						<ul>
							<li><a href="create/create.php"><?php $this->getLanguage('maintain_add_webspace');?></a></li>
						</ul>
					</div>
				</div>
			</div>

			<div id="col_right_50">
				<div class="box">
					<div class="box_header">
						<h1><?php $this->getLanguage('maintain_maintainers');?></h1>
					</div>
	
					<div class="box_body">
						<ul>
							<?php
							foreach ($maintainer_openids as $key):
							?>
							<li><?php echo $key;?></li>
							<?php
							endforeach;
							?>
						</ul>
					</div>
				</div>
			</div
		<?php
		}
		else {
		?>
			<div class="box" style="text-align:left;margin-left:auto;margin-right:auto; width:380px;">
				<h1><?php $this->getLanguage('maintain_maintainer_access');?></h1>
			
				<p>
					<input type="text" id="openid_login" name="openid_login" value="" />
					<input name="connect" type="submit" value="<?php $this->getLanguage('common_connect');?>" />
					<input name="connect" type="hidden" value="1"/>
				</p>
			</div>
		<?php }?>
	</form>
	</body>
</html>