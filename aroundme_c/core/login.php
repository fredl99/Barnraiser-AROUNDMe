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


// If we are connected to check all required fields; if empty we prompt to fill
// If not connected we present login box

if (is_readable(AM_DEFAULT_LANGUAGE_PATH . 'core.lang.php')) {
	include_once(AM_DEFAULT_LANGUAGE_PATH . 'core.lang.php');
}

if (defined('AM_LANGUAGE_CODE') && is_readable(AM_LANGUAGE_PATH . 'core.lang.php')) {
	include_once(AM_LANGUAGE_PATH . 'core.lang.php');
}


if (isset($_POST['update_connection'])) {
	
	$query = "UPDATE " . $db->prefix . "_connection SET ";

	if (!empty($_POST['connection_required_fields'])) {
		foreach ($_POST['connection_required_fields'] as $key => $i):
			$i = trim($i);
			
			if (empty($i)) {
				$GLOBALS['am_error_log'][] = array('login_account_information', $key);
			}
			else {
				$query .= "connection_" . $key . "=" . $db->qstr($i) . ", ";

				$_SESSION['openid_' . $key] = $i;
			}
		endforeach;
	}

	if (!empty($_POST['connection_optional_fields'])) {
		foreach ($_POST['connection_optional_fields'] as $key => $i):
			$i = trim($i);

			if (!empty($i)) {
				$query .= "connection_" . $key . "=" . $db->qstr($i) . ", ";

				$_SESSION['openid_' . $key] = $i;
			}
		endforeach;
	}
		
	if (empty($GLOBALS['am_error_log'])) {
		$query = substr($query, 0, -2);

		$query .= " WHERE connection_id=" . $_SESSION['connection_id'];
		
		$db->Execute($query);
		
		// append log
		$log_entry = array();
		$log_entry['title'] = $lang['arr_log']['title']['someone_connected'];
		$log_entry['body'] = '<a href="index.php?t=network&amp;connection_id=' . $_SESSION['connection_id'] . '">' . $_SESSION['openid_nickname'] . '</a> ' . $lang['arr_log']['body']['someone_connected'];
		$log_entry['link'] = $_SESSION['openid_identity'];
		$ws->appendLog($log_entry);


		if (!empty($_POST['return_to'])) {
			header("Location: " . $_POST['return_to']);
			exit;
		}
		else {
			header("Location: index.php");
			exit;
		}
	}

	if (!empty($GLOBALS['am_error_log'])) {
		$body->set('display', 'append_connection');
	}
}
elseif (isset($_SESSION['connection_id']) && isset($_REQUEST['no_sreg'])) {
	$query = "
		SELECT *
		FROM " . $db->prefix . "_connection
		WHERE
		connection_id=" . $_SESSION['connection_id'] . " AND
		webspace_id=" . AM_WEBSPACE_ID
	;

	$result = $db->Execute($query, 1);

	if (isset($result[0])) {
		$body->set('connection', $result[0]);
	}

	$body->set('display', 'append_connection');
}
elseif (isset($_SESSION['connection_id'])) {
	header("Location: index.php");
	exit;
}

if(!empty($core_config['openid_account_registration'])) {
	$body->set('openid_account_registration_url', $core_config['openid_account_registration']);
}

?>