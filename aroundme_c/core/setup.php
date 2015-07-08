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

if (is_readable(AM_DEFAULT_LANGUAGE_PATH . 'core.lang.php')) {
	include_once(AM_DEFAULT_LANGUAGE_PATH . 'core.lang.php');
}

if (defined('AM_LANGUAGE_CODE') && is_readable(AM_LANGUAGE_PATH . 'core.lang.php')) {
	include_once(AM_LANGUAGE_PATH . 'core.lang.php');
}


if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $core_config['group']['designer']) {

	if (isset($_POST['save_webspace'])) {
		if (empty($_POST['webspace_title'])) {
			$GLOBALS['am_error_log'][] = array('title_empty');
		}
	
		if (empty($GLOBALS['am_error_log'])) {
			$_POST['webspace_title'] = strip_tags($_POST['webspace_title']);
		
			
			if (!empty($_POST['webspace_locked'])) {
				$webspace_locked = 1;
			}
			else {
				$webspace_locked = "null";
			}
			
			$query = "
				UPDATE " . $db->prefix . "_webspace
				SET 
				webspace_title=" . $db->qstr($_POST['webspace_title']) . ",
				webspace_locked=" . $webspace_locked;
			;
		
			if (isset($_POST['language_code'])) {
				$query .= ", language_code=" . $db->qstr($_POST['language_code']);
			}
			
			$query .= " WHERE webspace_id=" . AM_WEBSPACE_ID;
			//echo $query; exit;
			$result = $db->Execute($query);
	
			if (isset($_POST['language_code'])) {
				$_SESSION['language_code'] = $_POST['language_code'];
			}

			header("Location: index.php?t=setup");
			exit;
		}
	}
	elseif (isset($_POST['set_default_webpage'])) { // set the default page id
	
		$query = "
			UPDATE " . $db->prefix . "_webspace
			SET
			default_webpage_id=" . $_POST['default_webpage_id'] . " 
			WHERE
			webspace_id=" . $_SESSION['webspace_id']
		;
			
		$result = $db->Execute($query);
	
		$output_webspace['default_webpage_id'] = $_POST['default_webpage_id'];
		
	}
	elseif (isset($_POST['delete_webpages'])) { // delete selected pages
		if (!empty($_POST['delete_webpage_ids'])) {
			$query = "
				DELETE FROM " . $db->prefix . "_webpage
				WHERE
				webpage_id in (" . implode(",", $_POST['delete_webpage_ids']) . ")"
			;
			
			$result = $db->Execute($query);
		}
	}
	
	$output_plugins = $ws->amscandir('plugins');

	if (!empty($output_plugins)) {
		foreach ($output_plugins as $key => $i):

			if (is_readable('plugins/' . $i . '/language/' . AM_DEFAULT_LANGUAGE_CODE . '/plugin_common.lang.php')) {
				include_once('plugins/' . $i . '/language/' . AM_DEFAULT_LANGUAGE_CODE . '/plugin_common.lang.php');
			}

			if (defined('AM_LANGUAGE_CODE') && is_readable('plugins/' . $i . '/language/' . AM_LANGUAGE_CODE . '/plugin_common.lang.php')) {
				include_once('plugins/' . $i . '/language/' . AM_LANGUAGE_CODE . '/plugin_common.lang.php');
			}
		endforeach;
	}
	
	
	// get webpages
	$query = "
		SELECT DISTINCT webpage_name, webpage_id 
		FROM " . $db->prefix . "_webpage
		WHERE
		webspace_id=" . AM_WEBSPACE_ID
	;
	
	$result = $db->Execute($query);
	
	if (!empty($result)) {
		$body->set('webpages', $result);
	}
	
	// get blocks
	$query = "
		SELECT block_id, block_plugin, block_name, block_body 
		FROM " . $db->prefix . "_block
		WHERE
		webspace_id=" . AM_WEBSPACE_ID . " 
		ORDER BY block_plugin, block_name"
	;
	
	$result = $db->Execute($query);
	
	if (!empty($result)) {
		$body->set('blocks', $result);
	}
}
else { // no permission to be here
	header("Location: index.php");
	exit;
}

?>