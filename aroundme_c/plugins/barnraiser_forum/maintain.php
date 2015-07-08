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

if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_forum']['manage_forum']) {
	
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
				UPDATE " . $db->prefix . "_plugin_forum_preference
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
			
			$table = $db->prefix . "_plugin_forum_preference";
				
			$db->insertDb($rec, $table);
		}
	}
	elseif (isset($_POST['save_tag'])) {
		if (isset($_POST['sticky_tag']) && $_POST['sticky_tag'] == 1) {
			$sticky_tag = 1;
		}
		else {
			$sticky_tag = 0;
		}
		
		$query = "UPDATE " . $db->prefix . "_plugin_forum_tag SET ";

		if (!empty($_POST['selected_tag_2'])) {
			$query .= "tag_name=" . $db->qstr($_POST['selected_tag_2']) . ",";
		}

		$query .= "
			sticky=" . $sticky_tag . "
			WHERE
			tag_name=" . $db->qstr($_POST['selected_tag_1']) . " AND
			webspace_id=" . $_SESSION['webspace_id']
		;
		
		$db->Execute($query);
	}
	elseif (isset($_POST['delete_tag'])) {
		$query = "
			DELETE FROM " . $db->prefix . "_plugin_forum_tag
			WHERE
			tag_name=" . $db->qstr($_POST['selected_tag_2']) . " AND
			webspace_id=" . $_SESSION['webspace_id']
		;
		
		$db->Execute($query);

		header("Location: index.php?p=barnraiser_forum&t=maintain&wp=" . $_REQUEST['wp']);
		exit;
	}
	
	if (isset($_POST['update_subjects'])) {
		if (!empty($_POST['subject_ids'])) {
			foreach ($_POST['subject_ids'] as $key => $i):
				
				$query = "
					UPDATE " . $db->prefix . "_plugin_forum_subject
					SET"
				;
				
				if (!empty($_POST['subject_archived'][$i])) {
					$query .= " subject_archived=1";
				}
				else {
					$query .= " subject_archived=NULL";
				}

				$query .= " WHERE subject_id=" . $i;

				$result = $db->Execute($query);
			endforeach;
		}
	}
	
	$query = "
		SELECT s.subject_id, s.subject_title, s.subject_archived,
		UNIX_TIMESTAMP(s.subject_create_datetime) as subject_create_datetime 
		FROM " . $db->prefix . "_plugin_forum_subject s
		WHERE
		s.webspace_id=" . AM_WEBSPACE_ID . " AND "
	;

	$query .= "1=1 ORDER BY s.subject_create_datetime";

	if (isset($attributes['limit'])) {
		$result = $db->Execute($query, (int) $attributes['limit']);
	}
	else {
		$result = $db->Execute($query);
	}

	if (!empty($result)) {
		foreach($result as $key => $i):
			$result[$key]['wp'] = $_REQUEST['wp'];
		endforeach;

		$body->set('subjects', $result);
	}
	
	$query = "
		SELECT tag_name, COUNT(tag_name) AS tag_total, sticky
		FROM " . $db->prefix . "_plugin_forum_tag
		WHERE webspace_id=" . AM_WEBSPACE_ID . "
		GROUP BY tag_name
		ORDER BY tag_name"
	;
	
	$result = $db->Execute($query);
	
	if (!empty($result)) {
		$body->set('output_tags', $result);
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
		FROM " . $db->prefix . "_plugin_forum_preference 
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