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

if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_blog']['add_blog_entry']) {

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
	
	if (isset($_POST['save_blog']) || isset($_POST['save_go_blog'])) {
		if (empty($_POST['blog_title'])) {
			$GLOBALS['am_error_log'][] = array('blog_title_empty');
		}

		if (empty($_POST['blog_body'])) {
			$GLOBALS['am_error_log'][] = array('blog_body_empty');
		}

		if (empty($GLOBALS['am_error_log'])) {
			$_POST['blog_title'] = strip_tags($_POST['blog_title']);

			$_POST['blog_body'] = $db->am_parse($_POST['blog_body']);
		
			if (!empty($_POST['blog_id'])) { // we update the page
				
				if (!isset($_POST['blog_allow_comment'])) {
					$allow_comment = "null";
				}
				else {
					$allow_comment = 1;
				}
				
				$query = "
					UPDATE " . $db->prefix . "_plugin_blog_entry 
					SET
					blog_title=" . $db->qstr($_POST['blog_title']) . ",
					blog_body=" . $db->qstr($_POST['blog_body']) . ",
					blog_allow_comment=" . $allow_comment . ",
					blog_edit_datetime=" . $db->qstr(date('Y-m-d H:i:s')) . "
					WHERE
					blog_id=" . $_POST['blog_id']
				;
				
				$result = $db->Execute($query);
			}
			else { // we insert
		
				$rec = array();
				$rec['webspace_id'] = $_SESSION['webspace_id'];
				$rec['blog_title'] = $_POST['blog_title'];
				$rec['blog_body'] = $_POST['blog_body'];
				$rec['connection_id'] = $_SESSION['connection_id'];
				$rec['blog_create_datetime'] = time();
				
				if (isset($_POST['blog_allow_comment'])) {
					$rec['blog_allow_comment'] = 1;
				}
				else {
					$rec['blog_allow_comment'] = "null";
				}
				
				$table = $db->prefix . "_plugin_blog_entry";
				
				$db->insertDb($rec, $table);
		
				$_REQUEST['blog_id'] = $db->insertID();


				// Append log
				$log_entry = array();
				$log_entry['title'] = $lang['arr_log']['title']['blog_entry_added'];
				$log_entry['body'] = '<a href="index.php?t=network&amp;connection_id=' . $_SESSION['connection_id'] . '">' . $_SESSION['openid_nickname'] . '</a> ' . str_replace("SYS_KEYWORD_BLOG_ENTRY_URL", 'index.php?wp=' . $_REQUEST['wp'] . '&amp;blog_id=' . $_REQUEST['blog_id'], $lang['arr_log']['body']['blog_entry_added']);
				$log_entry['link'] = "index.php?wp=" . $_REQUEST['wp'] . "&amp;blog_id=" . $_REQUEST['blog_id'];
				$ws->appendLog($log_entry);
			}

			if (isset($_POST['save_go_blog'])) {
				header("Location: index.php?wp=" . $_REQUEST['wp'] . "&blog_id=" . $_REQUEST['blog_id']);
				exit;
			}
		}
		else {
			if (!get_magic_quotes_gpc()) {
				$_POST['blog_body'] = stripslashes($_POST['blog_body']);
				$_POST['blog_title'] = stripslashes($_POST['blog_title']);
			}
			
			$_POST['blog_title'] = htmlspecialchars($_POST['blog_title']);
			
			$body->set('blog', $_POST);
			unset($_REQUEST['blog_id']);
		}
	}
	
	
	if (!empty($_REQUEST['blog_id'])) { // we are editing a page
		$query = "
			SELECT blog_id, blog_title, blog_body, blog_allow_comment
			FROM " . $db->prefix . "_plugin_blog_entry 
			WHERE blog_id=" . $_REQUEST['blog_id'] . " AND
			webspace_id=" . AM_WEBSPACE_ID
		;
		
		$result = $db->Execute($query);
		
		if (isset($result[0])) {
			$output_blog = $result[0];
			
			$output_blog['blog_body'] = $body->am_render($output_blog['blog_body']);
			
			$body->set('blog', $output_blog);
		}
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
else { // no permission to be here
	header("Location: index.php");
	exit;
}
?>