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



include ("../../core/config/core.config.php");
include ("../../core/inc/functions.inc.php");

// START SESSION
session_name($core_config['php']['session_name']);
session_start();


// SETUP DATABASE ------------------------------------------------------
require_once('../../core/class/Db.class.php');
$db = new Database($core_config['db']);


if (!empty($_POST['guestbook_body']) && !empty($_SESSION['webspace_id'])) {


	// INSERT GUESTBOOK ENTRY
	$rec = array();
	$rec['connection_id'] = $_SESSION['connection_id'];
	$rec['guestbook_body'] = $db->am_parse($_POST['guestbook_body']);
	$rec['webspace_id'] = $_SESSION['webspace_id'];
	$rec['guestbook_create_datetime'] = time();
	
	$table = $db->prefix . "_plugin_guestbook";
		
	$db->insertDb($rec, $table);

	
	// SETUP WEBSPACE
	require_once('../../core/class/Webspace.class.php');
	$ws = new Webspace($db);
	$ws->webspace_unix_name = $ws->getWebspaceName($core_config['am']['domain_preg_pattern']);

	if (!empty($ws->webspace_unix_name)) {
		$output_webspace = $ws->selWebSpace();
	}

	// SET LANGUAGE CODE
	if (isset($output_webspace['language_code'])) {
		define("AM_LANGUAGE_CODE", $output_webspace['language_code']);
	}
	else {
		define("AM_LANGUAGE_CODE", $core_config['language']['default']);
	}


	include_once('language/' . AM_LANGUAGE_CODE . '/plugin_common.lang.php');
	// Append log
	$log_entry = array();
	$log_entry['title'] = $lang['arr_log']['title']['guestbook_entry_added'];
	$log_entry['body'] = '<a href="index.php?t=network&amp;connection_id=' . $_SESSION['connection_id'] . '">' . $_SESSION['openid_nickname'] . '</a> ' . str_replace("SYS_KEYWORD_GUESTBOOK_URL", 'index.php?wp=' . $_REQUEST['wp'] . '&amp;recommended=1', $lang['arr_log']['body']['guestbook_entry_added']);
	$log_entry['link'] = "index.php?wp=" . $_REQUEST['wp'];
	$ws->appendLog($log_entry);
	
	session_write_close();
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;

?>