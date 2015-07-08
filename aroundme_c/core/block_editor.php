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
	
	// update block ----------------------------------------------------
	if (isset($_POST['save_block']) || isset($_POST['save_go_block'])) {
		// We save the block. If plugin="custom" we check and save the name
		
		if (preg_match_all("/\<\?(.*)\?\>/", $_POST['block_body'], $matches)) { 
			foreach($matches[1] as $m) {
				if (!$db->check_tokens($m, $core_config['invalid_tokens'])) {
					$GLOBALS['am_error_log'][] = array('forbidden_php_tokens', htmlentities($m));
					break;
				}
			}
		}
		
		if ($_POST['block_plugin'] == "custom") {
			
			$pattern = "/^[a-zA-Z0-9_]*$/";
	
			if (!preg_match($pattern, $_POST['block_name'])) {
				$GLOBALS['am_error_log'][] = array('only_characters_allowed');
				
				$body->set('block', $_POST);
			}

			if (empty($GLOBALS['am_error_log'])) {
				
				if(!empty($_POST['block_id'])) {
					$query = "
						UPDATE " . $db->prefix . "_block
						SET 
						block_name=" . $db->qstr($_POST['block_name']) . ",
						block_body=" . $db->qstr($_POST['block_body']) . "
						WHERE
						block_id=" . $_POST['block_id'] . " AND
						webspace_id=" . AM_WEBSPACE_ID
					;
					
					$result = $db->Execute($query);
			
					$_REQUEST['block_id'] = $_POST['block_id'];
				}
				else { // we insert
					$rec = array();
					$rec['block_plugin'] = 'custom';
					$rec['block_name'] = $_POST['block_name'];
					$rec['block_body'] = $_POST['block_body'];
					$rec['webspace_id'] = AM_WEBSPACE_ID;
			
					$table = $db->prefix . "_block";
					
					$db->insertDb($rec, $table);
			
					$_REQUEST['block_id'] = $db->insertID();
				}
			}
		}
		else {
			// plugin blocks can only be updated
			if(!empty($_POST['block_id'])) {
				$query = "
					UPDATE " . $db->prefix . "_block
					SET
					block_body=" . $db->qstr($_POST['block_body']) . "
					WHERE
					block_id=" . $_POST['block_id'] . " AND
					webspace_id=" . AM_WEBSPACE_ID
				;
				
				$result = $db->Execute($query);

				$_REQUEST['block_id'] = $_POST['block_id'];
			}
		}

		if (isset($_POST['save_go_block']) && empty($GLOBALS['am_error_log'])) {
			header("Location: index.php?t=setup");
			exit;
		}
	}
	elseif (isset($_POST['reset_block']) && $_POST['block_plugin'] != "custom") {

		if (isset($_POST['block_id']) && isset($_POST['block_plugin']) && isset($_POST['block_name'])) {
			
			$block_name = $_POST['block_plugin'] . '_' . $_POST['block_name'] . '.block.php';

			$block_html = @file_get_contents('plugins/' . $_POST['block_plugin'] . '/source_blocks/'. $block_name);
			
			if (get_magic_quotes_gpc()) {
				$block_html = addslashes($block_html);
			}

			// compile language into block
			$block_lang = array();

			if (is_readable('plugins/' . $_POST['block_plugin'] . '/language/'. AM_DEFAULT_LANGUAGE_CODE . '/block.lang.php')) {
				include('plugins/' . $_POST['block_plugin'] . '/language/'. AM_DEFAULT_LANGUAGE_CODE . '/block.lang.php');
			}

			if (defined('AM_LANGUAGE_CODE')) {
				if (is_file('plugins/' . $_POST['block_plugin'] . '/language/'. AM_LANGUAGE_CODE . '/block.lang.php')) {
					include('plugins/' . $_POST['block_plugin'] . '/language/'. AM_LANGUAGE_CODE . '/block.lang.php');
				}
			}

			foreach($block_lang as $lang_key => $lang_val):
				$block_key = "AM_BLOCK_LANGUAGE_" . strtoupper($lang_key);
				$block_html = str_replace($block_key, $lang_val, $block_html);
			endforeach;
			
			
			if (isset($block_html)) {
				$query = "
					UPDATE " . $db->prefix . "_block
					SET
					block_body=" . $db->qstr($block_html) . "
					WHERE
					block_id=" . $_POST['block_id'] . " AND
					webspace_id=" . AM_WEBSPACE_ID
				;
				
				$result = $db->Execute($query);

				$_REQUEST['block_id'] = $_POST['block_id'];
			}
		}
	}
	elseif (isset($_POST['delete_block'])) {
		if (!empty($_POST['block_id'])) {
			$query = "
				DELETE FROM " . $db->prefix . "_block
				WHERE
				block_id=" . $_POST['block_id'] . " AND
				webspace_id=" . AM_WEBSPACE_ID
			;

			$result = $db->Execute($query);
		}

		header("Location: index.php?t=setup");
		exit;
	}
	
	
	if(!empty($_REQUEST['block_id'])) {
		$query = "
			SELECT block_plugin, block_id, block_name, block_body 
			FROM " . $db->prefix . "_block
			WHERE
			webspace_id=" . AM_WEBSPACE_ID . " AND 
			block_id=" . $_REQUEST['block_id']
		;
		
		$result = $db->Execute($query);
		
		if (!empty($result[0])) {
			$result[0]['block_body'] = htmlspecialchars($result[0]['block_body']);
			
			$body->set('block', $result[0]);
		}
	}
	elseif (isset($_REQUEST['add_block'])) { // add a custom block
		$block = array();
		$block['block_plugin'] = "custom";

		$body->set('block', $block);
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
}
else {
	header("Location: index.php");
	exit;
}

?>