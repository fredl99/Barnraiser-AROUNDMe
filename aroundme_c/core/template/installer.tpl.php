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
	@import url(<?php echo AM_TEMPLATE_PATH;?>css/aroundme.css);
	</style>
	
	<!--[if IE]>
	<style type="text/css">
	@import url(<?php echo AM_TEMPLATE_PATH;?>css/aroundme-IE.css);
	</style>
	<![endif]-->
</head>

<body id="am_admin">

	<?php
	if (!empty($GLOBALS['am_error_log'])) {
	?>
	<div id="error_container">
		<?php
		foreach($GLOBALS['am_error_log'] as $key => $i):
		?>
			<?php
			if (isset($lang['arr_am_error'][$i[0]])) {
				echo $lang['arr_am_error'][$i[0]];
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
		<form method="POST">

		<?php
		if (!isset($display)) {
		?>
		
			<div class="box">
				<h1><?php $this->getLanguage('installer_start');?></h1>

				<p>
					<?php
					echo str_replace ('AM_SYS_KEYWORD_VERSION', $core_config['release']['version'], $this->lang['installer_start_intro']);
					?>
				</p>
				
				<?php
				if (isset($am_sys_check)) {
				?>
					<table width="100%" cellspacing="4" cellpadding="0">
						<tr>
							<th align="left" colspan="2">system check</th>
						</tr>
						<?php
						foreach($am_sys_check as $key => $value):
						?>
							<tr>
								<td style="border: 1px solid #CCC;"><?php echo $value['check']; ?></td>
								<td style="border: 1px solid #CCC;"><?php
									if (empty($value['is_valid'])) { 
									?>
									FAILED
									<?php
									}
									else {
									?>
									PASSED
									<?php
									}
									
									if (isset($value['note'])) {
									?>
										<i><?php echo $value['note'];?></i>
									<?php }?>
								</td>
							</tr>
						<?php
						endforeach;
						?>
					</table>
				<?php }?>
				
				<p>
					<input type="submit" name="start_install" value="<?php $this->getLanguage('installer_start_installation');?>" <?php if ($is_error) { echo " disabled"; }?>/>
				</p>
			</div>
		<?php
		}
		elseif (isset($display) && $display == "setup_domain") {
		?>
			<div class="box">
				<h1><?php $this->getLanguage('installer_setup_domain');?></h1>
				<p>
					<?php $this->getLanguage('installer_setup_domain_intro');?><b>http://<?php echo $domain; ?></b>.<br />
					<?php $this->getLanguage('installer_setup_domain_webspace_intro');?>
					<ul>
						<li><b>http://plumber.<?php echo $domain; ?></b></li>
						<li><b>http://carpenter.<?php echo $domain; ?></b></li>
						<li><b>http://electrician.<?php echo $domain; ?></b></li>
					</ul>
					<?php $this->getLanguage('installer_setup_domain_correct');?>
					<input type="button" onclick="document.getElementById('id_new_domain').style.display='block';" value="<?php $this->getLanguage('installer_setup_domain_no');?>"/>
					<input type="submit" name="create_domain" value="<?php $this->getLanguage('installer_setup_domain_yes');?>"/>
				</p>
				<p id="id_new_domain" style="display: none;">
					<?php $this->getLanguage('installer_setup_domain_example');?><br />
					http://<input type="text" name="new_domain" value="example.<?php echo $domain; ?>" /><br />
					<input type="submit" name="update_domain" value="update"/>
				</p>
			</div>
		<?php
		}
		elseif (isset($display) && $display == "setup_database") {
		?>
			<div class="box">
				<h1><?php $this->getLanguage('installer_configure_database');?></h1>

				<p>
					<label for="id_database_host"><?php $this->getLanguage('installer_database_host');?></label>
					<input type="text" name="database_host" id="id_database_host" value="<?php if (isset($core_config['db']['host'])) { echo $core_config['db']['host'];}?>" />
					<i><?php $this->getLanguage('installer_database_host_example');?></i>
				</p>

				<p>
					<label for="id_database_user"><?php $this->getLanguage('installer_database_user');?></label>
					<input type="text" name="database_user" id="id_database_user" value="<?php if (isset($core_config['db']['user'])) { echo $core_config['db']['user'];}?>" />
				</p>

				<p>
					<label for="id_database_password"><?php $this->getLanguage('installer_database_password');?></label>
					<input type="text" name="database_password" id="id_database_password" value="<?php if (isset($core_config['db']['password'])) { echo $core_config['db']['password'];}?>" />
				</p>

				<p>
					<label for="id_database_db"><?php $this->getLanguage('installer_database_name');?></label>
					<input type="text" name="database_db" id="id_database_db" value="<?php if (isset($core_config['db']['db'])) { echo $core_config['db']['db'];} else { echo "aroundme_collaboration";}?>" />
					<i><?php $this->getLanguage('installer_database_name_example');?></i>
				</p>

				<p>
					<input type="submit" name="create_database" value="<?php $this->getLanguage('installer_database_create');?>" />
				</p>
			</div>
		
		<?php
		}
		elseif (isset($display) && $display == "setup_maintainer") {
		?>

			<div class="box">
				<h1><?php $this->getLanguage('installer_maintainer');?></h1>
				
				<p>
					<label for="id_openid"><?php $this->getLanguage('common_openid');?></label><br />
					<input type="text" id="openid_login" name="openid_login" value="http://example.domain.org" onFocus="this.value=''; return false;" />
				</p>
	
				<p align="right">
					<input type="submit" name="connect" value="<?php $this->getLanguage('common_connect');?>" />
				</p>
	
				<h3><?php $this->getLanguage('installer_openid_require');?></h3>
	
				<p>
					<?php $this->getLanguage('installer_openid_require_intro');?>
				</p>
			</div>
		
		<?php
		}
		elseif (isset($display) && $display == "setup_am") {
		?>

			<div class="box">
				<h1><?php $this->getLanguage('installer_setup');?></h1>

				<p>
					<label for="id_openid"><?php $this->getLanguage('installer_setup_create');?></label><br />
					<?php $this->getLanguage('installer_setup_create_intro');?><br />
					<input type="radio" name="webspace_creation_type" value="0" checked="checked" /><?php $this->getLanguage('installer_setup_create_maintainer');?><br />
					<input type="radio" name="webspace_creation_type" value="1" /><?php $this->getLanguage('installer_setup_create_approve');?><br />
					<input type="radio" name="webspace_creation_type" value="2" /><?php $this->getLanguage('installer_setup_create_auto');?>
				</p>
	
				<p align="right">
					<input type="submit" name="setup_webspace" value="<?php $this->getLanguage('common_save');?>" />
				</p>
			</div>
		<?php }?>
		</form>
	</div>
</body>
</html>