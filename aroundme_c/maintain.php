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


// CHECK INSTALLED
if (is_readable("installer.php")) {
	header("Location: installer.php");
	exit;
}


// MAIN INCLUDES
include ("core/config/core.config.php");
include ("core/inc/functions.inc.php");


// SESSION HANDLER -------------------------------------------------------
// sets up all session and global vars 
session_name($core_config['php']['session_name']);
session_start();


// ERROR HANDLING
// this is accessed and updated with all errors thoughtout this build
// processing regularly checks if empty before continuing
$GLOBALS['am_error_log'] = array();


if (isset($_REQUEST['disconnect'])) {
	session_unset();
	session_destroy();
	session_write_close();
	header("Location: index.php?t=overview");
	exit;
}


// SETUP DATABASE ------------------------------------------------------
require_once('core/class/Db.class.php');
$db = new Database($core_config['db']);


// SETUP TEMPLATE -------------------------------------------
define("AM_TEMPLATE_PATH", "core/template/");
require_once('core/class/Template.class.php');
$tpl = new Template();


// SETUP LANGUAGE --------------------------------------------
if (!isset($core_config['language']['default'])) {
	die ('Default language pack not set correctly or cannot be read.');
}

define("AM_DEFAULT_LANGUAGE_CODE", $core_config['language']['default']);
define("AM_DEFAULT_LANGUAGE_PATH", "core/language/" . AM_DEFAULT_LANGUAGE_CODE . "/");
setlocale(LC_ALL, $core_config['language']['pack'][AM_DEFAULT_LANGUAGE_CODE]);


$lang = array();

if (is_readable(AM_DEFAULT_LANGUAGE_PATH . 'common.lang.php')) {
	include_once(AM_DEFAULT_LANGUAGE_PATH . 'common.lang.php');
}
else {
	die ('Default language pack not set correctly.');
}

if (is_readable(AM_DEFAULT_LANGUAGE_PATH . 'maintain.lang.php')) {
	include_once(AM_DEFAULT_LANGUAGE_PATH . 'maintain.lang.php');
}


// INCLUDE OPENID CONSUMER ----------------------------------------------
require_once ('core/class/OpenidConsumer.class.php');

$openid_consumer = new OpenidConsumer;

if (isset($_POST['connect'])) { // we connect

	$_POST['openid_login'] = $openid_consumer->normalize($_POST['openid_login']);

	if ($openid_consumer->discover($_POST['openid_login'])) { // we did discover a server
		if($openid_consumer->associate()) { // association is ok
			$openid_consumer->checkid_setup(); // do the setup
		}
		else {
			// error-log here
		}
	}
}
elseif (isset($_GET['openid_mode']) && $_GET['openid_mode'] == 'id_res') { // we get data back from the server
	if ($openid_consumer->id_res()) { // was the result ok?

		$openid = $_GET['openid_identity'];
			
		if(substr($openid,-1,1) == '/'){
			$openid = substr($openid, 0, strlen($openid)-1);
		}
			
		$_SESSION['openid_identity'] = $openid;
		$_SESSION['am_maintainer'] = 1;
	}
	else {
		// error-log here
	}
}


// SETUP WEBSPACE --------------------------------------------
require_once('core/class/Webspace.class.php');
$ws = new Webspace($db);


if (isset($_POST['save_patterns'])) {

	backupConfig();
	
	$core_config['am']['domain_preg_pattern'] = stripslashes($_POST['domain_preg_pattern']);
	$core_config['am']['domain_replace_pattern'] = stripslashes($_POST['domain_replace_pattern']);

	writeToConfig('$core_config[\'am\'][\'domain_preg_pattern\']', $core_config['am']['domain_preg_pattern']);
	writeToConfig('$core_config[\'am\'][\'domain_replace_pattern\']', $core_config['am']['domain_replace_pattern']);
	
	$_REQUEST['v'] = "config";
}
elseif (isset($_POST['save_email'])) {

	backupConfig();

	$core_config['mail']['host'] = 	$_POST['email_host'];
	$core_config['mail']['email_address'] = $_POST['email_address'];

	writeToConfig('$core_config[\'mail\'][\'host\']', $core_config['mail']['host']);
	writeToConfig('$core_config[\'mail\'][\'email_address\']', $core_config['mail']['email_address']);

	if (!empty($_POST['smtp_user'])) {
		$core_config['mail']['smtp']['username'] = $_POST['smtp_user'];
		$core_config['mail']['smtp']['password'] = $_POST['smtp_password'];

		writeToConfig('$core_config[\'mail\'][\'smtp\'][\'username\']', $core_config['mail']['smtp']['username']);
		writeToConfig('$core_config[\'mail\'][\'smtp\'][\'password\']', $core_config['mail']['smtp']['password']);
	}


	$_REQUEST['v'] = "config";
}
elseif (isset($_POST['save_config'])) {

	backupConfig();

	$core_config['file']['default_allocation'] = $_POST['file_default_allocation'];

	writeToConfig('$core_config[\'file\'][\'default_allocation\']', $core_config['file']['default_allocation']);

	$core_config['display']['max_list_rows'] = $_POST['display_max_list_rows'];

	writeToConfig('$core_config[\'display\'][\'max_list_rows\']', $core_config['display']['max_list_rows']);

	if ($_POST['webspace_creation_type'] == 2) {
		writeToConfig('$core_config[\'am\'][\'webspace_creation_type\']', 2);
	}
	elseif ($_POST['webspace_creation_type'] == 1) {
		writeToConfig('$core_config[\'am\'][\'webspace_creation_type\']', 1);
	}
	else {
		writeToConfig('$core_config[\'am\'][\'webspace_creation_type\']', 0);
	}

	$core_config['am']['excluded_webspace_names'] = $_POST['reserved_webspace_names'];

	writeToConfig('$core_config[\'am\'][\'excluded_webspace_names\']', $core_config['am']['excluded_webspace_names']);

	$_REQUEST['v'] = "config";
}
elseif (isset($_POST['update_webspace'])) {

	if (!is_numeric($_POST['webspace_allocation'])) {
		$GLOBALS['am_error_log'][] = array('webspace_allocation_error');
	}
	
	$query = "
		UPDATE " . $db->prefix . "_webspace
		SET
		webspace_allocation=" . $_POST['webspace_allocation'] . ",
		status_id=" . $_POST['status_id']
	;

	$query .= " WHERE webspace_id=" . $_POST['webspace_id'];
	
	
	$result = $db->Execute($query);

	$_REQUEST['webspace_id'] = $_POST['webspace_id'];

	$_REQUEST['v'] = "list";
}


if (isset($_SESSION['openid_identity']) && in_array($_SESSION['openid_identity'], $core_config['am']['maintainer_openids'])) {
	if (!empty($_REQUEST['webspace_id'])) {
		$query = "
			SELECT ws.webspace_id, c.connection_openid, c.connection_email, ws.webspace_unix_name,
			ws.language_code, UNIX_TIMESTAMP(ws.webspace_create_datetime) as webspace_create_datetime,
			ws.webspace_allocation, ws.status_id,
			c.connection_fullname, c.connection_nickname, ws.webspace_locked 
			FROM " . $db->prefix . "_webspace ws, " . $db->prefix . "_connection c
			WHERE
			ws.owner_connection_id=c.connection_id AND 
			ws.webspace_id=" . $_REQUEST['webspace_id']
		;
	
		$result = $db->Execute($query);
	
		if (isset($result[0])) {
			$tpl->set('webspace', $result[0]);
		}

		$_REQUEST['v'] = "list";
	}
	elseif (isset($_REQUEST['v']) && $_REQUEST['v'] == "config") {

	}
	elseif (isset($_REQUEST['v']) && $_REQUEST['v'] == "list") {
		
		$query = "
			SELECT ws.webspace_id, c.connection_openid, c.connection_email, ws.webspace_unix_name,
			ws.language_code, UNIX_TIMESTAMP(ws.webspace_create_datetime) as webspace_create_datetime,
			ws.webspace_allocation, ws.status_id 
			FROM " . $db->prefix . "_webspace ws, " . $db->prefix . "_connection c
			WHERE
			ws.owner_connection_id=c.connection_id
			ORDER BY ws.webspace_unix_name"
		;
		
		$result = $db->Execute($query);
		
		if (!empty($result)) {
			$tpl->set('webspaces', $result);
		}
	}
}
else {
	session_unset();
	session_destroy();
	session_write_close();
}

$tpl->lang = $lang;

$tpl->set('domain_replace_pattern', $core_config['am']['domain_replace_pattern']);
$tpl->set('domain_preg_pattern', $core_config['am']['domain_preg_pattern']);
$tpl->set('default_allocation', $core_config['file']['default_allocation']);
$tpl->set('max_list_rows', $core_config['display']['max_list_rows']);
$tpl->set('webspace_creation_type', $core_config['am']['webspace_creation_type']);
$tpl->set('excluded_webspace_names', $core_config['am']['excluded_webspace_names']);
$tpl->set('arr_mail', $core_config['mail']);
$tpl->set('maintainer_openids', $core_config['am']['maintainer_openids']);


echo $tpl->fetch(AM_TEMPLATE_PATH . 'maintain.tpl.php');


function writeToConfig($where, $what) {
	$config = file('core/config/core.config.php');
	foreach($config as $key => $val) {
		if (strstr($val, $where)) {
			$config[$key] = $where . ' = "' . $what . "\";\n";
			@file_put_contents('core/config/core.config.php', implode($config));
			break;
		}
	}
}

function backupConfig() {

	$name = "~core.config_" . time() . ".php";

	$config = file_get_contents('core/config/core.config.php');

	file_put_contents('core/config/' . $name , $config);
}

?>