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

if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $core_config['group']['designer']) {

	if (is_readable(AM_DEFAULT_LANGUAGE_PATH . 'core.lang.php')) {
		include_once(AM_DEFAULT_LANGUAGE_PATH . 'core.lang.php');
	}
	
	if (defined('AM_LANGUAGE_CODE') && is_readable(AM_LANGUAGE_PATH . 'core.lang.php')) {
		include_once(AM_LANGUAGE_PATH . 'core.lang.php');
	}


	// check that our webpage name is valid ------------------------------
	if (isset($_REQUEST['wp'])) {
	
		$pattern = "/^[a-zA-Z0-9]*$/";
	
		if (!preg_match($pattern, $_REQUEST['wp'])) {
			header("Location: index.php");
			exit;
		}
	
		if (strlen($_REQUEST['wp']) > 30) { // link too long
			header("Location: index.php");
			exit;
		}
	
		define("AM_WEBPAGE_NAME", $_REQUEST['wp']);
	}
	else {
		// no webpage - we error
		header("Location: index.php");
		exit;
	}
	
	
	
	if (isset($_POST['save_webpage']) || isset($_POST['save_go_webpage'])) {
	
		if (preg_match_all("/\<\?(.*)\?\>/", $_POST['webpage_body'], $matches)) { 
			foreach($matches[1] as $m) {
				if (!$db->check_tokens($m, $core_config['invalid_tokens'])) {
					$GLOBALS['am_error_log'][] = array('forbidden_php_tokens', htmlentities($m));
					break;
				}
			}
		}
		
		if (empty($GLOBALS['am_error_log'])) {
			if (!empty($_POST['webpage_id'])) { // we update the page
				
				$query = "
					UPDATE " . $db->prefix . "_webpage
					SET  
					webpage_body=" . $db->qstr($_POST['webpage_body']) . " 
					WHERE
					webpage_id=" . $_POST['webpage_id'] . " AND 
					webspace_id=" . AM_WEBSPACE_ID
				;
			
				$result = $db->Execute($query);
				
			}
			else { // we insert a new page
				
				$rec = array();
				$rec['webpage_body'] = $_POST['webpage_body'];
				$rec['webpage_name'] = AM_WEBPAGE_NAME;
				$rec['webspace_id'] = AM_WEBSPACE_ID;
				$rec['webpage_create_datetime'] = time();
	
				$table = $db->prefix . "_webpage";
			
				$db->insertDb($rec, $table);
			}

			if (isset($_POST['save_go_webpage'])) {
				header("Location: index.php?wp=" . AM_WEBPAGE_NAME);
				exit;
			}
		}
	}
	
	
	
	if (defined('AM_WEBPAGE_NAME')) { // we are editing a page
		$query = "
			SELECT *
			FROM " . $db->prefix . "_webpage
			WHERE webpage_name=" . $db->qstr(AM_WEBPAGE_NAME) . " AND 
			webspace_id=" . AM_WEBSPACE_ID
		;
		
		$result = $db->Execute($query);
		
		if (isset($result[0])) {
			$output_webpage = $result[0];

			$body->set('webpage', $output_webpage);
		}
	}
	
	
	// BUILD EDITOR HELPERS
	$plugins = $ws->amscandir('plugins');
	
	if (!empty($plugins)) {
		$body->set('plugins', $plugins);
		
		foreach ($plugins as $key => $i):
			if (is_readable('plugins/' . $i . '/language/' . AM_DEFAULT_LANGUAGE_CODE . '/plugin_common.lang.php')) {
				include_once('plugins/' . $i . '/language/' . AM_DEFAULT_LANGUAGE_CODE . '/plugin_common.lang.php');
			}
			
			if (defined('AM_LANGUAGE_CODE') && is_readable('plugins/' . $i . '/language/' . AM_LANGUAGE_CODE . '/plugin_common.lang.php')) {
				include_once('plugins/' . $i . '/language/' . AM_LANGUAGE_CODE . '/plugin_common.lang.php');
			}
		endforeach;
	}
	
	// get webpages
	$output_webpages = $ws->selWebPages();
	
	if (!empty($output_webpages)) {
		$body->set('webpages', $output_webpages);
	}
	
	
	// GET FILES ----------------------------------
	$output_files = $file->selFiles();
	
	if (!empty($output_files)) {
		$body->set('pictures', $output_files);
	}
	

	// GET PAGE LAYOUTS ---------------------------
	$webpage_layouts = $ws->amscandir('create/layout');
	
	if (!empty($webpage_layouts)) {
		$body->set('webpage_layouts', $webpage_layouts);
	}
	
	// GET CUSTOM BLOCKS
	$query = "
		SELECT block_name 
		FROM " . $db->prefix . "_block
		WHERE
		webspace_id=" . AM_WEBSPACE_ID . " AND 
		block_plugin='custom' 
		ORDER BY block_name"
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