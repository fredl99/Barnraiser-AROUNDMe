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

if (isset($_REQUEST['rm_notify_id'])) {
	// rm_notify_id = webspace_id-subject_id-md5(connection_create_datetime)
	$items = explode("-", $_REQUEST['rm_notify_id']);
	
	if (count($items) == 3) {
		$query = "
			SELECT tr.webspace_id, tr.subject_id, tr.connection_id 
			FROM " . $db->prefix . "_plugin_forum_subject_track tr,  " . $db->prefix . "_connection c 
			WHERE 
			tr.connection_id=c.connection_id AND 
			tr.webspace_id=" . $items[0] . " AND 
			tr.subject_id=" . $items[1] . " AND
			md5(c.connection_create_datetime)=" . $db->qstr($items[2])
		;
		
		$item = $db->Execute($query);

		if (!empty($item[0])) {
			$query = "
				DELETE FROM " . $db->prefix . "_plugin_forum_subject_track
				WHERE
				connection_id=" . $item[0]['connection_id'] . " AND
				webspace_id=" . $item[0]['webspace_id'] . " AND
				subject_id=" . $item[0]['subject_id']
			;

			$db->Execute($query);
		}

		$query = "
			SELECT webspace_unix_name
			FROM " . $db->prefix . "_webspace ws
			WHERE 
			webspace_id=" . $items[0]
		;

		$ws = $db->Execute($query);

		if (!empty($ws[0])) {
			$url = str_replace('REPLACE', $ws[0]['webspace_unix_name'], $core_config['am']['domain_replace_pattern']);

			header("Location: " . $url);
		}
	}

	exit;
}
elseif (isset($_REQUEST['rm_digest_id'])) {
	// rm_notify_id = webspace_id-connection_id-md5(connection_create_datetime)
	$items = explode("-", $_REQUEST['rm_digest_id']);
	
	if (count($items) == 3) {
		$query = "
			SELECT d.webspace_id, d.connection_id 
			FROM " . $db->prefix . "_plugin_forum_digest d,  " . $db->prefix . "_connection c
			WHERE 
			d.connection_id=c.connection_id AND
			d.webspace_id=" . $items[0] . " AND 
			md5(c.connection_create_datetime)=" . $db->qstr($items[2])
		;
		
		$item = $db->Execute($query);

		if (!empty($item[0])) {
			$query = "
				DELETE FROM " . $db->prefix . "_plugin_forum_digest 
				WHERE
				connection_id=" . $item[0]['connection_id'] . " AND
				webspace_id=" . $item[0]['webspace_id']
			;

			$db->Execute($query);
		}

		$query = "
			SELECT webspace_unix_name
			FROM " . $db->prefix . "_webspace ws
			WHERE 
			webspace_id=" . $items[0]
		;

		$ws = $db->Execute($query);

		if (!empty($ws[0])) {
			$url = str_replace('REPLACE', $ws[0]['webspace_unix_name'], $core_config['am']['domain_replace_pattern']);

			header("Location: " . $url);
		}
	}

	exit;
}
elseif (!empty($_SESSION['webspace_id']) && !empty($_SESSION['connection_id'])) {
	if (isset($_POST['set_digest_frequency'])) {
	
		$query = "
			DELETE FROM " . $db->prefix . "_plugin_forum_digest
			WHERE
			webspace_id=" . $_SESSION['webspace_id'] . " AND 
			connection_id=" . $_SESSION['connection_id']
		;
	
		$db->Execute($query);
	
		if (!empty($_POST['digest_frequency'])) {
	
			$next_send_time = time() + ($_POST['digest_frequency'] * 24 * 60 * 60);
			
			$rec = array();
			$rec['webspace_id'] = $_SESSION['webspace_id'];
			$rec['connection_id'] = $_SESSION['connection_id'];
			$rec['digest_frequency'] = $_POST['digest_frequency'];
			$rec['send_datetime'] = $next_send_time;
			
			$table = $db->prefix . "_plugin_forum_digest";
	
			$db->insertDb($rec, $table);
		}
	}
	elseif (isset($_POST['set_subject_tracking'])) {

		$query = "
			DELETE FROM " . $db->prefix . "_plugin_forum_subject_track
			WHERE
			webspace_id=" . $_SESSION['webspace_id'] . " AND 
			connection_id=" . $_SESSION['connection_id'] . " AND
			subject_id=" . $_POST['subject_id']
		;

		$db->Execute($query);
		
		$rec = array();
		$rec['webspace_id'] = $_SESSION['webspace_id'];
		$rec['connection_id'] = $_SESSION['connection_id'];
		$rec['subject_id'] = $_POST['subject_id'];
			
		$table = $db->prefix . "_plugin_forum_subject_track";
	
		$db->insertDb($rec, $table);
		
	}
	elseif (isset($_POST['set_subject_notify'])) {

		$query = "
			DELETE FROM " . $db->prefix . "_plugin_forum_subject_track
			WHERE
			webspace_id=" . $_SESSION['webspace_id'] . " AND 
			connection_id=" . $_SESSION['connection_id'] . " AND
			subject_id=" . $_POST['subject_id']
		;

		$db->Execute($query);
		
		$rec = array();
		$rec['webspace_id'] = $_SESSION['webspace_id'];
		$rec['connection_id'] = $_SESSION['connection_id'];
		$rec['subject_id'] = $_POST['subject_id'];
		$rec['notification'] = 1;
			
		$table = $db->prefix . "_plugin_forum_subject_track";
	
		$db->insertDb($rec, $table);
		
	}
	elseif (isset($_POST['remove_subject_tracking'])) {

		$query = "
			DELETE FROM " . $db->prefix . "_plugin_forum_subject_track
			WHERE
			webspace_id=" . $_SESSION['webspace_id'] . " AND 
			connection_id=" . $_SESSION['connection_id'] . " AND
			subject_id=" . $_POST['subject_id']
		;
		
		$db->Execute($query);
		
	}
	elseif (isset($_POST['management_option_remove_subject_tracking']) && !empty($_POST['subject_ids'])) {
		
		$ids = implode(',', $_POST['subject_ids']);
		
		$query = "
			DELETE FROM " . $db->prefix . "_plugin_forum_subject_track
			WHERE
			webspace_id=" . $_SESSION['webspace_id'] . " AND 
			connection_id=" . $_SESSION['connection_id'] . " AND
			subject_id in (" . $ids . ")"
		;
		
		$db->Execute($query);
		
	}
	elseif (isset($_POST['management_option_remove_subject_tracking_notify']) && !empty($_POST['subject_ids'])) {
		
		foreach($_POST['subject_ids'] as $key => $i):
		
			$query = "
				UPDATE " . $db->prefix . "_plugin_forum_subject_track
				SET notification=null 
				WHERE
				webspace_id=" . $_SESSION['webspace_id'] . " AND 
				connection_id=" . $_SESSION['connection_id'] . " AND
				subject_id=" . $i
			;
		
			$db->Execute($query);
		endforeach;
	}
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;

?>