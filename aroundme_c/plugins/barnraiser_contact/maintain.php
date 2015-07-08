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

if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_contact']['manage_contact']) {
	
	if (is_readable('plugins/' . AM_PLUGIN_NAME . '/language/' . AM_DEFAULT_LANGUAGE_CODE . '/plugin_common.lang.php')) {
		include_once('plugins/' . AM_PLUGIN_NAME . '/language/' . AM_DEFAULT_LANGUAGE_CODE . '/plugin_common.lang.php');
	}
		
	if (is_readable('plugins/' . AM_PLUGIN_NAME . '/language/' . AM_DEFAULT_LANGUAGE_CODE . '/plugin_manage.lang.php')) {
		include_once('plugins/' . AM_PLUGIN_NAME . '/language/' . AM_DEFAULT_LANGUAGE_CODE . '/plugin_manage.lang.php');
	}

	// we overwrite any default array keys with the webspace language keys
	if (defined('AM_LANGUAGE_CODE')) {
		if (is_readable('plugins/' . AM_PLUGIN_NAME . '/language/' . AM_LANGUAGE_CODE . '/plugin_common.lang.php')) {
			include_once('plugins/' . AM_PLUGIN_NAME . '/language/' . AM_LANGUAGE_CODE . '/plugin_common.lang.php');
		}
		
		if (is_readable('plugins/' . AM_PLUGIN_NAME . '/language/' . AM_LANGUAGE_CODE . '/plugin_manage.lang.php')) {
			include_once('plugins/' . AM_PLUGIN_NAME . '/language/' . AM_LANGUAGE_CODE . '/plugin_manage.lang.php');
		}
	}

	if (isset($_POST['save_recipient_emails'])) {
		if (isset($_POST['recipient_emails'])) {
			$email_addresses = array();
			foreach($_POST['recipient_emails'] as $key => $i):
				if (!empty($i)) {
					array_push($email_addresses, $i);
				}
			endforeach;
		}

		if (!empty($email_addresses)) {
			$query = "DELETE FROM " . $db->prefix . "_plugin_contact_recipient WHERE webspace_id=" . AM_WEBSPACE_ID;

			$db->Execute($query);

			foreach($email_addresses as $key => $i):
				$rec = array();
				$rec['recipient_email'] = $i;
				$rec['webspace_id'] = AM_WEBSPACE_ID;

				$table = $db->prefix . "_plugin_contact_recipient";
				
				$db->insertDb($rec, $table);
			endforeach;
		}
		else {
			$GLOBALS['am_error_log'][] = array('barnraiser_contact_set_one_email');
		}
	}
	

	$query = "
		SELECT recipient_email
		FROM " . $db->prefix . "_plugin_contact_recipient
		WHERE
		webspace_id=" . AM_WEBSPACE_ID
	;

	$result = $db->Execute($query);

	if (!empty($result)) {
		$body->set('recipient_emails', $result);
	}
}
else {
	header("Location: index.php");
	exit;
}

?>