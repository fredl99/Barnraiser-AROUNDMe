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


session_name($core_config['php']['session_name']);
session_start();


// SETUP DATABASE ------------------------------------------------------
require('../../core/class/Db.class.php');
$db = new Database($core_config['db']);


if (isset($_POST['insert_reply']) && !empty($_SESSION['webspace_id'])) {

	$_POST['comment_body'] = trim($_POST['reply_body']);
	
	if (!empty($_POST['reply_body'])) {
		
		$rec = array();
		$rec['webspace_id'] = $_SESSION['webspace_id'];
		$rec['subject_id'] = $_POST['subject_id'];
		$rec['connection_id'] = $_SESSION['connection_id'];
		$rec['reply_body'] = $db->am_parse($_POST['reply_body']);
		$rec['reply_create_datetime'] = time();
		
		$table = $db->prefix . "_plugin_forum_reply";

		$db->insertDb($rec, $table);

		$reply_id = $db->insertID();
		
		$query = "
			SELECT COUNT(r.reply_id) AS total_replies
			FROM " . $db->prefix . "_plugin_forum_reply r
			WHERE r.subject_id=" . $_POST['subject_id']
		;
		
		$result = $db->Execute($query);
		
		$frm = '';
		if (isset($result[0]['total_replies'])) {
			if ($result[0]['total_replies'] >= $core_config['display']['max_list_rows']) {
				$tmp = (int) floor($result[0]['total_replies'] / $core_config['display']['max_list_rows']);
				$frm = '&_frmreplies=' . $tmp * $core_config['display']['max_list_rows'];
			}
		}

			
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

		$sub_url = 'index.php?wp=' . $_REQUEST['wp'] . '&amp;subject_id=' . $_POST['subject_id'] . $frm . '#reply_id' . $reply_id;

		// Append log
		$log_entry = array();
		$log_entry['title'] = $lang['arr_log']['title']['forum_reply_added'];
		$log_entry['body'] = '<a href="index.php?t=network&amp;connection_id=' . $_SESSION['connection_id'] . '">' . $_SESSION['openid_nickname'] . '</a> ';
		$log_entry['body'] .= str_replace('SYS_KEYWORD_REPLY_URL', $sub_url, $lang['arr_log']['body']['forum_reply_added']);
		$log_entry['link'] = $sub_url;
		$ws->appendLog($log_entry);

		// Apply notification
		$query = "
			SELECT count(connection_id) as total
			FROM " . $db->prefix . "_plugin_forum_subject_track
			WHERE
			webspace_id=" . $_SESSION['webspace_id'] . " AND 
			subject_id=" . $_POST['subject_id']
		;

		$result = $db->Execute($query);
		
		if (!empty($result[0]['total'])) {
			$rec = array();
			$rec['subject_id'] = $_POST['subject_id'];
			$rec['reply_id'] = $reply_id;
			$rec['webspace_id'] = $_SESSION['webspace_id'];
			$rec['last_connection_id'] = 0;
			$rec['notification_create_datetime'] = time();

			$table = $db->prefix . "_plugin_forum_subject_notify";

			$db->insertDb($rec, $table);
		}
	}
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;

?>