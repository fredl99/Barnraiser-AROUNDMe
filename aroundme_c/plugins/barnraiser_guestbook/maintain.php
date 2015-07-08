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

if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $core_config['group']['editor']) {
	
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
	
	if (isset($_POST['delete_guestbook_entries'])) {
		if (!empty($_POST['delete_guestbook_entry_id'])) {
			foreach($_POST['delete_guestbook_entry_id'] as $key => $i):
			
				$query = "DELETE FROM " . $db->prefix . "_plugin_guestbook WHERE guestbook_id=" . $i;
				
				$db->Execute($query);
				
			endforeach;
		}
	}
	
	// we get the guestbook entries
	$query = "
		SELECT UNIX_TIMESTAMP(gb.guestbook_create_datetime) as guestbook_create_datetime,
		gb.guestbook_body, c.connection_nickname, c.connection_id, gb.guestbook_id 
		FROM " . $db->prefix . "_plugin_guestbook gb, " . $db->prefix . "_connection c
		WHERE
		gb.connection_id=c.connection_id AND
		gb.webspace_id=" . AM_WEBSPACE_ID . "
		ORDER BY gb.guestbook_create_datetime DESC"
	;

	$result = $db->Execute($query);
	
	if (!empty($result)) {
		$body->set('guestbook_entries', $result);
	}
}
else {
	header("Location: index.php");
	exit;
}

?>