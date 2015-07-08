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

<!-- Made with AROUNDMe Collaboration Server - http://www.barnraiser.org/ - Enjoy free software -->

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<?php
	if (isset($webspace['language_code'])) {
	?>
	<meta http-equiv="Content-Language" content="<?php echo $webspace['language_code'];?>" />
	<?php }?>

	<?php
	if (isset($webspace['webspace_title'])) {
	?>
	<title><?php echo $webspace['webspace_title'];?></title>
	<?php
	}
	else {
	?>
	<title><?php $this->getLanguage('common_html_title');?></title>
	<?php }?>
	
	
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

	<style type="text/css" id="css">
	<!--
	<?php
	if (isset($webspace['stylesheet_body'])) {
		echo $webspace['stylesheet_body'];
	}
	?>
	-->
	</style>
	

	<?php
	//we reload an image in this template just before the session times out to
	//make sure that the session does not time out
	$session_maxlifetime = ini_get('session.gc_maxlifetime'); // in seconds
	
	// we need to warn 2 minutes before
	$session_warning_time = 120; // seconds
	if ($session_maxlifetime > $session_warning_time) {
		$session_maxlifetime = $session_maxlifetime-$session_warning_time;
	}
	$session_maxlifetime_ms = $session_maxlifetime*1000; // in milliseconds
	?>

	<script type="text/javascript" src="<?php echo AM_TEMPLATE_PATH;?>js/functions.js"></script>

	<script type="text/javascript">
	//<![CDATA[
		var session_maxlifetime_ms = <?php echo $session_maxlifetime_ms;?>;

		function ShowTimeoutWarning () {
			// we append the time to the string to avoid caching
			var urldate = new Date()
			var urltime = urldate.getTime()
			document.session_reload_image.src = 'core/get_file.php?reloadsession=start&now=' + urltime;
			setTimeout( 'ShowTimeoutWarning();', session_maxlifetime_ms );
		}
	//]]>
	</script>

	<?php
	if (!empty($this->header_link_tag_arr)) {
	foreach ($this->header_link_tag_arr as $key => $i):
	?>
	<link rel="<?php echo $i[0];?>" type="<?php echo $i[1];?>" title="<?php echo $i[2];?>" href="<?php echo $i[3];?>" />
	<?php
	endforeach;
	}
	?>

	<link rel="icon" href="core/template/img/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="core/template/img/favicon.ico" type="image/x-icon">
</head>

<?php
if (!defined('AM_SCRIPT_NAME') && defined('AM_WEBPAGE_NAME')) {
?>	
<body id="am_webpage" onload="setTimeout( 'ShowTimeoutWarning();', session_maxlifetime_ms ); checkImages();">
<?php
}
else {
?>
<body id="am_admin" onload="setTimeout( 'ShowTimeoutWarning();', session_maxlifetime_ms ); checkImages();">
<?php }?>



<?php
if (defined('AM_WEBSPACE_ID')) {
?>	
<div id="am_menu_container">
	<ul>
		<?php
		$link_css = "";
		if (!defined('AM_SCRIPT_NAME') && isset($webspace['webpage_id']) && $webspace['default_webpage_id'] == $webspace['webpage_id']) {
			$link_css = " class=\"highlight\"";
		}
		?>
		<li class="am_menu_home"><a href="index.php"<?php echo $link_css;?>><?php $this->getLanguage('common_am_menu_home');?></a></li>

		
	
		<?php
		if (isset($_SESSION['connection_id'])) {
		$link_css = "";
		if (defined('AM_SCRIPT_NAME') && AM_SCRIPT_NAME == "network" && (isset($_REQUEST['connection_id']) && $_SESSION['connection_id'] == $_REQUEST['connection_id'])) {
			$link_css = " class=\"highlight\"";
		}
		?>
		<li class="am_menu_account"><a href="index.php?t=network&amp;connection_id=<?php echo $_SESSION['connection_id'];?>"<?php echo $link_css;?>><?php $this->getLanguage('common_am_menu_account');?></a></li>
		<?php
		}
		else {
		?>
		<li class="am_menu_connect"><a href="index.php?t=login"><?php $this->getLanguage('common_am_menu_connect');?></a></li>
		<?php }?>
	
	
		<?php
		if (isset($_SESSION['connection_id'])) {
		$link_css = "";
		if (defined('AM_SCRIPT_NAME') && AM_SCRIPT_NAME == "network" && (!isset($_REQUEST['connection_id']) || $_SESSION['connection_id'] != $_REQUEST['connection_id'])) {
			$link_css = " class=\"highlight\"";
		}
	
		if (isset($webspace_applicants)) {
		?>
		<li class="am_menu_applicants"><a href="index.php?t=network&amp;v=applicants"<?php echo $link_css;?>><?php $this->getLanguage('common_am_menu_network');?></a> (<?php echo $webspace_applicants;?>)</li>
		<?php
		}
		else {
		?>
		<li class="am_menu_network"><a href="index.php?t=network"<?php echo $link_css;?>><?php $this->getLanguage('common_am_menu_network');?></a></li>
		<?php }?>
		<?php }?>
	
	
	
		<?php
		if(isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $arr_group['designer']) {
		$link_css = "";
		if (defined('AM_SCRIPT_NAME') && AM_SCRIPT_NAME == "setup") {
			$link_css = " class=\"highlight\"";
		}
		?>
		<li class="am_menu_setup"><a href="index.php?t=setup"<?php echo $link_css;?>><?php $this->getLanguage('common_am_menu_setup');?></a></li>
		<?php }?>
	
	
	
		<?php
		if(isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $arr_group['designer']) {
		$link_css = "";
		if (defined('AM_SCRIPT_NAME') && AM_SCRIPT_NAME == "file") {
			$link_css = " class=\"highlight\"";
		}
		?>
		<li class="am_menu_file"><a href="index.php?t=file"<?php echo $link_css;?>><?php $this->getLanguage('common_am_menu_file');?></a></li>
		<?php }?>
	
	
	
		<?php
		if(!defined('AM_SCRIPT_NAME') && isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $arr_group['designer']) {
		?>
		<li class="am_menu_edit"><a href="index.php?wp=<?php echo AM_WEBPAGE_NAME;?>&amp;t=webpage"><?php $this->getLanguage('common_am_menu_edit');?></a></li>
		<?php }?>
	
	
	
		<?php
		if(!defined('AM_SCRIPT_NAME') && isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $arr_group['designer']) {
		?>
		<li class="am_menu_style"><a href="#" onclick="javascript:launchPopupWindow('core/stylesheet_editor.php', 'stylesheet editor');"><?php $this->getLanguage('common_am_menu_style');?></a></li>
		<?php }?>
		
		<?php
		if (isset($_SESSION['connection_id'])) {
		?>
		<li class="am_menu_disconnect"><a href="index.php?disconnect=1"><?php $this->getLanguage('common_am_menu_disconnect');?></a></li>
		<?php }?>
	</ul>
</div>
<?php }?>


<?php
if (!empty($GLOBALS['am_error_log'])) {
?>
<div id="error_container">
	<div class="content">
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
</div>
<?php }?>
	
<div id="body_container">
	<?php echo $content;?>
</div>

<div id="id_session_reload_image">
	<img name="session_reload_image" src="core/get_file.php?reloadsession=1" alt="" />
</div>

<div id="interface_system_message" style="display:none; z-index:500;">
	<div id="interface_system_message_header"></div>
	<div id="interface_system_message_body"></div>
	<div id="interface_system_message_footer" onclick="javascript:hideInterfaceSystemMessage();"><?php $this->getLanguage('common_close');?></div>
</div>

<!-- AROUNDMe Collaboration Server version <?php echo $am_release['version'];?> - Installed <?php echo $am_release['install_date'];?> -->
</body>
</html>