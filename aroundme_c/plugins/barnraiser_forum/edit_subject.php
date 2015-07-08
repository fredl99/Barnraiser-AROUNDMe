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

if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_forum']['add_subject']) {
	
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
		
	if (isset($_POST['continue_create_discussion'])) {
		$body->set('new_subject', 1);
		$body->set('subject', $_POST);
	}
	
	if (isset($_POST['create_discussion'])) {
		
			$query = "
			SELECT subject_title, subject_id, " . $db->qstr($_REQUEST['wp']) . " AS wp, subject_body 
			FROM " . $db->prefix . "_plugin_forum_subject
			WHERE MATCH(subject_title, subject_body) AGAINST (" . $db->qstr($_POST['subject_title']) . ")
			AND webspace_id=" . AM_WEBSPACE_ID . " 
 			ORDER BY MATCH(subject_title, subject_body) AGAINST (" . $db->qstr($_POST['subject_title']) . ")
			LIMIT 10"
		;
		
		$result = $db->Execute($query);
		
		if (!empty($result)) {
			foreach($result as $key => $i):
				$result[$key]['subject_body'] = strip_tags($result[$key]['subject_body']);
				
				if (strlen($result[$key]['subject_body']) > 300) {
					$result[$key]['subject_body'] = mb_substr($result[$key]['subject_body'], 0, 300, 'UTF-8') . '...';
				}
			endforeach;
				
			$body->set('subjects', $result);
		}
		else {
			$body->set('new_subject', 1);
		}

		if (get_magic_quotes_gpc()) {
			$_POST['subject_title'] = stripslashes($_POST['subject_title']);
		}
		
		$_POST['subject_title'] = htmlentities($_POST['subject_title']);
		
		$body->set('subject', $_POST);
	}
	
	
	if (isset($_POST['save_subject']) || isset($_POST['save_go_subject'])) {
		$body->set('new_subject', 1);
		if (empty($_POST['subject_title'])) {
			$GLOBALS['am_error_log'][] = array('subject_title_empty');
		}

		if (empty($_POST['subject_body'])) {
			$GLOBALS['am_error_log'][] = array('subject_body_empty');
		}
		
		if (empty($_POST['tags'])) {
			$GLOBALS['am_error_log'][] = array('subject_tags_empty');
		}

		if (empty($GLOBALS['am_error_log'])) {
			$_POST['subject_title'] = strip_tags($_POST['subject_title']);

			$_POST['subject_body'] = $db->am_parse($_POST['subject_body']);
		
			if (!empty($_POST['subject_id'])) { // we update the page
				
				$query = "
					UPDATE " . $db->prefix . "_plugin_forum_subject 
					SET
					subject_title=" . $db->qstr($_POST['subject_title']) . ",
					subject_body=" . $db->qstr($_POST['subject_body']) . ",
					subject_edit_datetime=" . $db->qstr(date('Y-m-d H:i:s')) . "
					WHERE
					subject_id=" . $_POST['subject_id']
				;
				
				$result = $db->Execute($query);
				
				$query = "
					DELETE
					FROM " . $db->prefix . "_plugin_forum_tag
					WHERE subject_id=" . $_POST['subject_id'] . "
					AND webspace_id=" . AM_WEBSPACE_ID
				;
				
				$db->Execute($query);
				
				$rec = array();
				$rec['webspace_id'] = $_SESSION['webspace_id'];
				$rec['connection_id'] = $_SESSION['connection_id'];
				$rec['subject_id'] = $_REQUEST['subject_id'];
				
				$table = $db->prefix . '_plugin_forum_tag';
				
				foreach(explode(',', $_POST['tags']) as $t) {
					$t = trim($t);
					if (!empty($t)) {
						$rec['tag_name'] = $t;
						$db->insertDb($rec, $table);
					}
				}
			}
			else { // we insert
		
				$rec = array();
				$rec['webspace_id'] = $_SESSION['webspace_id'];
				$rec['subject_title'] = $_POST['subject_title'];
				$rec['subject_body'] = $_POST['subject_body'];
				$rec['connection_id'] = $_SESSION['connection_id'];
				$rec['subject_create_datetime'] = time();
				
				$table = $db->prefix . "_plugin_forum_subject";
				
				$db->insertDb($rec, $table);
		
				$_REQUEST['subject_id'] = $db->insertID();

		
				// Append log
				$log_entry = array();
				$log_entry['title'] = $lang['arr_log']['title']['forum_subject_added'];
				$log_entry['body'] = '<a href="index.php?t=network&amp;connection_id=' . $_SESSION['connection_id'] . '">' . $_SESSION['openid_nickname'] . '</a> ' . str_replace("SYS_KEYWORD_DISCUSSION_URL", 'index.php?wp=' . $_REQUEST['wp'] . '&amp;subject_id=' . $_REQUEST['subject_id'], $lang['arr_log']['body']['forum_subject_added']);
				$log_entry['link'] = "index.php?wp=" . $_REQUEST['wp'] . "&amp;subject_id=" . $_REQUEST['subject_id'];
				$ws->appendLog($log_entry);

				
				$rec = array();
				$rec['webspace_id'] = $_SESSION['webspace_id'];
				$rec['connection_id'] = $_SESSION['connection_id'];
				$rec['subject_id'] = $_REQUEST['subject_id'];
				
				$table = $db->prefix . '_plugin_forum_tag';
				
				foreach(explode(',', $_POST['tags']) as $t) {
					$t = trim($t);
					if (!empty($t)) {
						$rec['tag_name'] = $t;
						$db->insertDb($rec, $table);
					}
				}
			}

			if (isset($_POST['save_go_subject'])) {
				header("Location: index.php?wp=" . $_REQUEST['wp'] . "&subject_id=" . $_REQUEST['subject_id']);
				exit;
			}
		}
		else {
			if (!get_magic_quotes_gpc()) {
				$_POST['subject_title'] = stripslashes($_POST['subject_title']);
				$_POST['subject_body'] = stripslashes($_POST['subject_body']);
			}
			
			$_POST['subject_title'] = htmlspecialchars($_POST['subject_title']);
			
			$body->set('subject', $_POST);
			unset($_REQUEST['subject_id']);
		}
	}
	
	
	if (!empty($_REQUEST['subject_id'])) { // we are editing a page
		$query = "
			SELECT subject_id, subject_title, subject_body 
			FROM " . $db->prefix . "_plugin_forum_subject 
			WHERE subject_id=" . $_REQUEST['subject_id']
		;
		
		$result = $db->Execute($query);
		
		if (isset($result[0])) {
			$output_subject = $result[0];
			
			$output_subject['subject_body'] = $body->am_render($output_subject['subject_body']);
			
			$query = "
				SELECT DISTINCT tag_name
				FROM " . $db->prefix . "_plugin_forum_tag
				WHERE
				subject_id=" . $_REQUEST['subject_id'] . " AND
				webspace_id=" . AM_WEBSPACE_ID
			;
			
			$result = $db->Execute($query);
			if (!empty($result)) {
				$tags = "";
				foreach($result as $t) {
					$tags .= $t['tag_name'] . ',';
				}
				$output_subject['tags'] = rtrim($tags, ',');
			}
			
			$body->set('subject', $output_subject);
			$body->set('new_subject', 1);
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
	
	
	// get top 10 tags in this webspace and present them 
	$query = "
		SELECT tag_name, COUNT(tag_name)
		FROM " . $db->prefix . "_plugin_forum_tag
		WHERE webspace_id=" . AM_WEBSPACE_ID . "
		GROUP BY tag_name
		LIMIT 10"
	;
	
	$result = $db->Execute($query);
	
	if (!empty($result)) {
		$body->set('popular_tags', $result);
	}
	
}
else { // no permission to be here
	header("Location: index.php");
	exit;
}
?>