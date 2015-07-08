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

// START SESSION
include ("../../core/config/core.config.php");
include ("../../core/inc/functions.inc.php");

session_name($core_config['php']['session_name']);
session_start();



// SETUP DATABASE ------------------------------------------------------
require_once('../../core/class/Db.class.php');
$db = new Database($core_config['db']);


if (isset($_POST['insert_note']) && !empty($_SESSION['webspace_id'])) {

	$_POST['note_body'] = trim($_POST['note_body']);
	
	if (!empty($_POST['note_body'])) {
		
		$rec = array();
		$rec['webspace_id'] = $_SESSION['webspace_id'];
		$rec['wikipage_id'] = $_POST['wikipage_id'];
		$rec['connection_id'] = $_SESSION['connection_id'];
		$rec['note_body'] = $db->am_parse($_POST['note_body']);
		$rec['note_create_datetime'] = time();
		
		$table = $db->prefix . "_plugin_wiki_note";

		$db->insertDb($rec, $table);
		
		$note_id = $db->insertID();

	
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

		// Append log
		include_once('language/' . AM_LANGUAGE_CODE . '/plugin_common.lang.php');
		$log_entry = array();
		$log_entry['title'] = $lang['arr_log']['title']['wiki_page_note_added'];
		$log_entry['body'] = '<a href="index.php?t=network&amp;connection_id=' . $_SESSION['connection_id'] . '">' . $_SESSION['openid_nickname'] . '</a> ' . str_replace("SYS_KEYWORD_WIKI_NOTE_URL", 'index.php?wp=' . $_REQUEST['wp'] . '&amp;wikipage=' . $_POST['wikipage_name'] . '#note_id' . $note_id, $lang['arr_log']['body']['wiki_page_note_added']);
		$log_entry['link'] = "index.php?wp=" . $_REQUEST['wp'] . "&amp;wikipage=" . $_POST['wikipage_name'] . "#note_id" . $note_id;
		$ws->appendLog($log_entry);
	}
}
elseif (isset($_POST['delete_note']) && !empty($_SESSION['webspace_id'])) {

	$query = "
		DELETE FROM " . $db->prefix . "_plugin_wiki_note 
		WHERE
		note_id=" . $_POST['note_id'] . " AND 
		webspace_id=" . $_SESSION['webspace_id']
	;
		
	$db->Execute($query);
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;

?>