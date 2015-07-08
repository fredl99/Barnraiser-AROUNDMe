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



// MAIN INCLUDES ---------------------------------------------------------
include_once ("core/config/core.config.php");
include_once ("core/inc/functions.inc.php");


// SESSION HANDLER -------------------------------------------------------
// sets up all session and global vars 
session_name($core_config['php']['session_name']);
session_start();


if (isset($_REQUEST['disconnect'])) {
	session_unset();
	session_destroy();
	session_write_close();
	header("Location: maintain.php");
	exit;
}


// ERROR HANDLING
// this is accessed and updated with all errors thoughtout this build
// processing regularly checks if empty before continuing
$GLOBALS['am_error_log'] = array();



// SETUP TEMPLATE -------------------------------------------
define("AM_TEMPLATE_PATH", "core/template/");
require_once('core/class/Template.class.php');
$tpl = new Template();


// SETUP LANGUAGE --------------------------------------------
if (!isset($core_config['language']['default'])) {
	die ('Default language pack not set correctly.');
}

define("AM_DEFAULT_LANGUAGE_CODE", $core_config['language']['default']);
setlocale(LC_ALL, $core_config['language']['pack'][AM_DEFAULT_LANGUAGE_CODE]);


$lang = array();

if (is_readable('core/language/' . AM_DEFAULT_LANGUAGE_CODE . '/common.lang.php')) {
	include_once('core/language/' . AM_DEFAULT_LANGUAGE_CODE . '/common.lang.php');
}
else {
	die ('Default language pack not set correctly or cannot be read..');
}

if (is_readable('core/language/' . AM_DEFAULT_LANGUAGE_CODE . '/installer.lang.php')) {
	include_once('core/language/' . AM_DEFAULT_LANGUAGE_CODE . '/installer.lang.php');
}


// SETUP OPENID -------------------------------------------
include 'core/class/OpenidConsumer.class.php';
$openid_consumer = new OpenidConsumer;

if (isset($_POST['start_install']) || isset($_POST['update_domain'])) {

	// set the session_id
	$php_session_name = 'PHPSESSIDAMC';

	for($i = 0; $i < 4; $i++) {
		$n = rand(0, 9);
		$php_session_name .= $n;
	}
		
	writeToInstConfig('$core_config[\'php\'][\'session_name\']', $php_session_name);
	
	if (!empty($_POST['new_domain'])) {
		$domain = substr($_POST['new_domain'], strpos($_POST['new_domain'], '.')+1);
	}
	else {
		$domain = $_SERVER['SERVER_NAME'];
	}
	
	// remove trailing slash
	if (substr($domain, -1) == "/") {
		$domain = substr($domain, 0, -1);
	}
	
	$tpl->set('display', 'setup_domain');
	$tpl->set('domain', $domain);
}
elseif (isset($_POST['create_domain'])) {

	$_POST['new_domain'] = substr($_POST['new_domain'], strpos($_POST['new_domain'], '.')+1);

	// setup for subdomain
	$pattern = "/(.*?)\." . $_POST['new_domain'] . "/";
	$url = "http://REPLACE." . $_POST['new_domain'];

	// add trailing slash
	if (substr($url, -1) == "/") {
		$url = substr($url, 0, -1);
	}
	
	writeToInstConfig('$core_config[\'am\'][\'domain_preg_pattern\']', $pattern);
	writeToInstConfig('$core_config[\'am\'][\'domain_replace_pattern\']', $url);

	$tpl->set('display', 'setup_database');
}
elseif (isset($_POST['create_database'])) {

	$core_config['db']['host'] = trim($_POST['database_host']);
	$core_config['db']['user'] = trim($_POST['database_user']);
	$core_config['db']['pass'] = trim($_POST['database_password']);
	$core_config['db']['db'] = trim($_POST['database_db']);
	
	if (empty($core_config['db']['host'])) {
		$GLOBALS['am_error_log'][] = array($lang['error']['installer_host_empty']);
	}
	
	if (empty($core_config['db']['user'])) {
		$GLOBALS['am_error_log'][] = array($lang['error']['installer_user_empty']);
	}
	
	if (empty($core_config['db']['db'])) {
		$GLOBALS['am_error_log'][] = array($lang['error']['installer_db_empty']);
	}
	
	$connection = @mysql_connect($core_config['db']['host'], $core_config['db']['user'] ,$core_config['db']['pass']);

	if (!is_resource($connection)) {
		$GLOBALS['am_error_log'][] = array(mysql_error());
		$tpl->set('display', 'setup_database');
	}
	elseif (empty($GLOBALS['am_error_log'])) {
		// We write the config
		writeToInstConfig('$core_config[\'db\'][\'host\']', $core_config['db']['host']);
		writeToInstConfig('$core_config[\'db\'][\'user\']', $core_config['db']['user']);
		writeToInstConfig('$core_config[\'db\'][\'pass\']', $core_config['db']['pass']);
		writeToInstConfig('$core_config[\'db\'][\'db\']', $core_config['db']['db']);
		
		// we create the database
		$query = "SET NAMES 'utf8'";

		mysql_query($query, $connection);

		$query = "SET CHARACTER SET 'utf8'";

		mysql_query($query, $connection);
		
		$query = "CREATE DATABASE " . $core_config['db']['db'] . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
		
		mysql_query($query, $connection);

		$db_selected = mysql_select_db($core_config['db']['db'], $connection);
		
		if (!$db_selected) {
			$GLOBALS['am_error_log'][] = array('db_select_error', mysql_error());
		}
		else {
			// we populate the database
			$queries = file_get_contents('installation/install.sql');
	
			$pattern = "/CREATE(.*?);/s";
			
			if (preg_match_all($pattern, $queries, $matches)) {
				
				if (isset($matches[0])) {
					foreach ($matches[0] as $key => $i):
						$query = str_replace(';', '', $i);
						
						mysql_query($query, $connection);
					endforeach;
				}
			}
		}

		$tpl->set('display', 'setup_maintainer');
	}
	else {
		$tpl->set('display', 'setup_database');
	}
}
elseif (isset($_POST['setup_webspace'])) {

	if ($_POST['webspace_creation_type'] == 2) {
		writeToInstConfig('$core_config[\'am\'][\'webspace_creation_type\']', 2);
	}
	elseif ($_POST['webspace_creation_type'] == 1) {
		writeToInstConfig('$core_config[\'am\'][\'webspace_creation_type\']', 1);
	}
	else {
		writeToInstConfig('$core_config[\'am\'][\'webspace_creation_type\']', 0);
	}

	// set the installation date MM-DD-YYYY
	$date = date("m-d-Y");
	writeToInstConfig('$core_config[\'release\'][\'install_date\']', $date);
	
	// set this file to not readable
	if(!chmod ('installer.php', 0000)) { // disable this installer
		exit($lang['installer_chmod_error']);
	}
	
	header("Location: maintain.php?installed=1");
	exit;
}
elseif (isset($_POST['connect'])) {
	
	// stage 2 - openid connect
	$_POST['openid_login'] = $openid_consumer->normalize($_POST['openid_login']);

	$openid_consumer->required_fields = array('nickname');

	if ($openid_consumer->discover($_POST['openid_login'])) { // we did discover a server
		if($openid_consumer->associate()) { // association is ok
			$openid_consumer->checkid_setup(); // do the setup
		}
		else {
			// error-log here
			$GLOBALS['am_error_log'][] = array($lang['error']['openid_associate']);
			$tpl->set('display', 'setup_maintainer');
		}
	}
	else {
		// error-log here
		$GLOBALS['am_error_log'][] = array($lang['error']['openid_discovery']);
		$tpl->set('display', 'setup_maintainer');
	}
}
elseif (isset($_GET['openid_mode']) && $_GET['openid_mode'] == 'id_res') { // we get data back from the server
	
	if ($openid_consumer->id_res()) { // was the result ok?

		$openid = $_GET['openid_identity'];

		if(substr($openid,-1,1) == '/'){
			$openid = substr($openid, 0, strlen($openid)-1);
		}

		writeToInstConfig('$core_config[\'am\'][\'maintainer_openids\'][]', $openid);
		
		$_SESSION['openid_identity'] = $openid;
		$_SESSION['openid_nickname'] = $_GET['openid_sreg_nickname'];
		$_SESSION['am_maintainer'] = 1;

		$tpl->set('display', 'setup_am');
		
	}
	else {
		// error-log here
		$GLOBALS['am_error_log'][] = array('openid_error'. 'id_res');
		$tpl->set('display', 'setup_maintainer');
	}
	
}
else { // pre-start checks and setup

	$am_sys_check = array();
	$is_error = 0;
	
	
	if (!function_exists('mysql_connect')) {
		$am_sys_check[6]['check'] = $lang['arr_am_sys_check']['php_mysql_exists']['name'];
		$am_sys_check[6]['is_valid'] = 0;
		$am_sys_check[6]['note'] = $lang['arr_am_sys_check']['php_mysql_exists']['error'];
		$is_error = 1;
	}
	else {
		$am_sys_check[6]['check'] = $lang['arr_am_sys_check']['php_mysql_exists']['name'];
		$am_sys_check[6]['is_valid'] = 1;
	}
	
	if ( (int) phpversion() < 5) {
		$am_sys_check[0]['check'] = $lang['arr_am_sys_check']['php_version']['name'];
		$am_sys_check[0]['is_valid'] = 0;
		$am_sys_check[0]['note'] = $lang['arr_am_sys_check']['php_version']['error'] . phpversion();
		$is_error = 1;
	}
	else {
		$am_sys_check[0]['check'] = $lang['arr_am_sys_check']['php_version']['name'];
		$am_sys_check[0]['is_valid'] = 1;
	}
	
	if (!function_exists('curl_init') || !function_exists('curl_setopt') || !function_exists('curl_exec')) {
		$am_sys_check[1]['check'] = $lang['arr_am_sys_check']['curl_exists']['name'];
		$am_sys_check[1]['is_valid'] = 0;
		$am_sys_check[1]['note'] = $lang['arr_am_sys_check']['curl_exists']['error'];
		$is_error = 1;
	}
	else {
		$am_sys_check[1]['check'] = $lang['arr_am_sys_check']['curl_exists']['name'];
		$am_sys_check[1]['is_valid'] = 1;
	}
	
	if (!extension_loaded ('bcmath')) {
		$am_sys_check[2]['check'] = $lang['arr_am_sys_check']['bcmath_exists']['name'];
		$am_sys_check[2]['is_valid'] = 0;
		$am_sys_check[2]['note'] = 'AROUNDMe collaboration server needs MySQL. Please add MySQL to PHP';
		$is_error = 1;
	}
	else {
		$am_sys_check[2]['check'] = $lang['arr_am_sys_check']['bcmath_exists']['name'];
		$am_sys_check[2]['is_valid'] = 1;
	}
	
	if (function_exists('gd_info')) {
		$gd_info = gd_info();
		
		if (!isset($gd_info['GD Version'])) {
			$am_sys_check[3]['check'] = $lang['arr_am_sys_check']['gd_version']['name'];
			$am_sys_check[3]['is_valid'] = 0;
			$am_sys_check[3]['note'] = $lang['arr_am_sys_check']['gd_version']['error'];
			$is_error = 1;
		}
		else {
			$am_sys_check[3]['check'] = $lang['arr_am_sys_check']['gd_version']['name'];
			$am_sys_check[3]['is_valid'] = 1;
		}
	}
	else {
		$am_sys_check[3]['check'] = $lang['arr_am_sys_check']['gd_version']['name'];
		$am_sys_check[3]['is_valid'] = 0;
		$am_sys_check[3]['note'] = $lang['arr_am_sys_check']['gd_version']['error'];
		$is_error = 1;
	}

	// check that we can write
	if (!is_writable("core/config/core.config.php")) {
		$am_sys_check[5]['check'] = $lang['arr_am_sys_check']['config_writable']['name'];
		$am_sys_check[5]['is_valid'] = 0;
		$am_sys_check[5]['note'] = $lang['arr_am_sys_check']['config_writable']['error'];
		$is_error = 1;
	}
	else {
		$am_sys_check[5]['check'] = $lang['arr_am_sys_check']['config_writable']['name'];
		$am_sys_check[5]['is_valid'] = 1;
	}
	$tpl->set('am_sys_check', $am_sys_check);
	$tpl->set('is_error', $is_error);
}


$tpl->lang = $lang;

$tpl->set('core_config', $core_config); // included in the installer as you have to see the username and type in the password anyway.

echo $tpl->fetch(AM_TEMPLATE_PATH . 'installer.tpl.php');


function writeToInstConfig($where, $what) {
	$config = file('core/config/core.config.php');
	foreach($config as $key => $val) {
		if (strstr($val, $where)) {
			$config[$key] = $where . ' = "' . $what . "\";\n";
			@file_put_contents('core/config/core.config.php', implode($config));
			break;
		}
	}
}

?>