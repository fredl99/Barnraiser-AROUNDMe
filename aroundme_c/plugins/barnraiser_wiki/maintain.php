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

if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_wiki']['manage_wiki']) {

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
	
	if (isset($_POST['save_preferences'])) {
		if (!empty($_POST['preference_id'])) {
			$query = "
				UPDATE " . $db->prefix . "_plugin_wiki_preference
				SET
				default_webpage_id=" . $_POST['default_webpage_id'] . " 
				WHERE
				preference_id=" . $_POST['preference_id']
			;
				
			$result = $db->Execute($query);
		}
		else {
			$rec = array();
			$rec['webspace_id'] = $_SESSION['webspace_id'];
			$rec['default_webpage_id'] = $_POST['default_webpage_id'];
			
			$table = $db->prefix . "_plugin_wiki_preference";
				
			$db->insertDb($rec, $table);
		}
	}

	
	// get wikipages, revision count, usage
	$query = "
		SELECT
		wikipage_name
		FROM " . $db->prefix . "_plugin_wiki_page
		WHERE
		webspace_id=" . AM_WEBSPACE_ID
	;

	$result = $db->Execute($query);

	if (isset($result)) {
		$body->set('wikipages', $result);
	}
	
	// SELECT WEBPAGES
	$query = "
		SELECT webpage_id, webpage_name
		FROM " . $db->prefix . "_webpage
		WHERE
		webspace_id=" . AM_WEBSPACE_ID
	;

	$result = $db->Execute($query);

	if (!empty($result)) {
		$body->set('webpages', $result);
	}

	// SELECT PREFERENCES
	$query = "
		SELECT preference_id, default_webpage_id 
		FROM " . $db->prefix . "_plugin_wiki_preference
		WHERE
		webspace_id=" . AM_WEBSPACE_ID
	;

	$result = $db->Execute($query);

	if (!empty($result[0])) {
		$preferences = $result[0];
	}
	
	if (empty($preferences['default_webpage_id']) && isset($_REQUEST['wp'])) {
		$preferences['default_webpage_id'] = $_REQUEST['wp'];
	}
		
	$body->set('preferences', $preferences);
	

}
else { // no permission to be here
	header("Location: index.php");
	exit;
}
?>