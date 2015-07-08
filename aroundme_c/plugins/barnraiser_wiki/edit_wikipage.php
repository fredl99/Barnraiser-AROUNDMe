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

if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_wiki']['edit_page']) {
	
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
	
	if (isset($_POST['set_as_current_revision'])) {
		
		$query = "
			UPDATE " . $db->prefix . "_plugin_wiki_page
			SET
			current_revision_id=" . $_POST['revision_id'] . " 
			WHERE
			wikipage_id=" . $_POST['wikipage_id']
		;
			
		$db->Execute($query);
		
		$_REQUEST['v'] = "revisions";
		$_REQUEST['wikipage'] = $_POST['wikipage_name'];
	}
	elseif (isset($_POST['insert_revision']) || isset($_POST['insert_go_revision'])) {

		$_POST['revision_body'] = trim($_POST['revision_body']);

		if (empty($_POST['revision_body'])) {
			$GLOBALS['am_error_log'][] = array('wikipage_body_empty');
		}
		
		// we look for incorrectly formatted wikipage links
		$pattern = "/<wikilink name=\"(.*?)\">(.*?)<\/wikilink>/";

		$revision_body = $_POST['revision_body'];

		if (get_magic_quotes_gpc()) {
			$revision_body = stripslashes($revision_body);
		}
		
		if(preg_match_all($pattern, $revision_body, $wikilinks, PREG_PATTERN_ORDER)) {
			if (!empty($wikilinks[1])) {
				foreach ($wikilinks[1] as $key => $i):
					// strip off any anchors
					$anchor_position = strrpos($i, "#");

					if ($anchor_position > 0 ) {
						$i = substr($i, 0, $anchor_position);
					}
					
					$pattern = "/^[a-zA-Z0-9]*$/";
					
					if (!preg_match($pattern, $i)) {
						$GLOBALS['am_error_log'][] = array('plugin_barnraiser_wiki_plugin_wikilink_bad_chars', $i);
					}
					
					
					if (strlen($i) > 30) { // link too long
						$GLOBALS['am_error_log'][] = array('plugin_barnraiser_wiki_plugin_wikilink_too_long', $i);
					}

					$anchor_position = strrpos($i, "#");
				endforeach;
			}
		}



		if (empty($GLOBALS['am_error_log'])) {
			// If it is a first revision we will not have a wiki_page
			if (empty($_POST['wikipage_id'])) {
				// we want to double check this against the name
				$query = "
					SELECT wikipage_id 
					FROM " . $db->prefix . "_plugin_wiki_page 
					WHERE
					webspace_id=" . $output_webspace['webspace_id'] . " AND
					wikipage_name=" . $db->qstr($_POST['wikipage_name'])
				;
				
				$result = $db->Execute($query);
				
				if (isset($result[0])) {
					$_POST['wikipage_id'] = $result[0]['wikipage_id'];
				}
				else {
					// we insert the wiki_page
					$rec = array();
					$rec['webspace_id'] = $_SESSION['webspace_id'];
					$rec['wikipage_name'] = $_POST['wikipage_name'];
			
					$table = $db->prefix . "_plugin_wiki_page";
			
					$db->insertDb($rec, $table);
			
					$_POST['wikipage_id'] = $db->insertID();
				}
			}
	
	
			// We insert the revision
			$_POST['revision_body'] = $db->am_parse($_POST['revision_body']);
			
			$rec = array();
			$rec['revision_body'] = $_POST['revision_body'];
			$rec['connection_id'] = $_SESSION['connection_id'];
			$rec['revision_create_datetime'] = time();
			$rec['wikipage_id'] = $_POST['wikipage_id'];
	
			$table = $db->prefix . "_plugin_wiki_revision";
	
			$db->insertDb($rec, $table);
			
			$revision_id = $db->insertID();

			// Append log
			$log_entry = array();
			$log_entry['title'] = $lang['arr_log']['title']['wiki_page_revised'];
			$log_entry['body'] = '<a href="index.php?t=network&amp;connection_id=' . $_SESSION['connection_id'] . '">' . $_SESSION['openid_nickname'] . '</a> ' . str_replace("SYS_KEYWORD_WIKI_REVISION_URL", 'index.php?wp=' . $_REQUEST['wp'] . '&amp;revision_id=' . $revision_id, $lang['arr_log']['body']['wiki_page_revised']);
			$log_entry['link'] = "index.php?wp=" . $_REQUEST['wp'] . "&amp;revision_id=" . $revision_id;
			$ws->appendLog($log_entry);
			
			
	
			// we update the wiki_page with the current_revision_id
			if (!empty($revision_id)) {

				if (empty($_POST['wikipage_allow_note'])) {
					$_POST['wikipage_allow_note'] = "null";
				}
			
				$query = "
					UPDATE " . $db->prefix . "_plugin_wiki_page
					SET
					current_revision_id=" . $revision_id . ",
					wikipage_allow_note=" . $_POST['wikipage_allow_note']. " 
					WHERE
					wikipage_id=" . $_POST['wikipage_id']
				;
	
				$db->Execute($query);
				
				$_REQUEST['revision_id'] = $revision_id;
			}
			
			$_REQUEST['wikipage'] = $_POST['wikipage_name'];

			if (isset($_POST['insert_go_revision'])) {
				header("Location: index.php?wp=" . $_REQUEST['wp'] . "&wikipage=" . $_REQUEST['wikipage']);
				exit;
			}
		}
		else {
			if (get_magic_quotes_gpc()) {
				$_POST['revision_body'] = stripslashes($_POST['revision_body']);
			}
			
			$body->set('revision', $_POST);
			unset($_REQUEST['wikipage_id']);
		}
	}
	
	if (!empty($_REQUEST['v']) && $_REQUEST['v'] = "revisions" && !empty($_REQUEST['wikipage'])) {
		// we list all revisions of a wikipage
		$query = "
			SELECT wip.current_revision_id, r.revision_id,
			UNIX_TIMESTAMP(r.revision_create_datetime) as revision_create_datetime,
			c.connection_nickname, c.connection_openid, c.connection_id 
			FROM " . $db->prefix . "_plugin_wiki_page wip, " . $db->prefix . "_plugin_wiki_revision r, " . $db->prefix . "_connection c
			WHERE 
			wip.wikipage_id=r.wikipage_id AND 
			wip.wikipage_name= " . $db->qstr($_REQUEST['wikipage']) . " AND
			r.connection_id=c.connection_id AND 
			wip.webspace_id=" . AM_WEBSPACE_ID . " 
			ORDER BY r.revision_create_datetime desc"
		;
		
		$result = $db->Execute($query);
	
		if (isset($result)) {
			$body->set('revisions', $result);
		}	
	}	
	
	
	if (!empty($_REQUEST['wikipage'])) { // we are editing a page
		$query = "
			SELECT wip.wikipage_id, wip.wikipage_name, r.revision_body, 
			wip.current_revision_id, wip.wikipage_allow_note, r.revision_id 
			FROM " . $db->prefix . "_plugin_wiki_page wip, " . $db->prefix . "_plugin_wiki_revision r
			WHERE 
			wip.webspace_id=" . AM_WEBSPACE_ID . " AND
			wip.wikipage_name=" . $db->qstr($_REQUEST['wikipage']) . " AND "
		;
		
		if (isset($_REQUEST['revision_id'])) {
			$query .= "r.revision_id=" . $_REQUEST['revision_id'];
		}
		else {
			$query .= "wip.current_revision_id=r.revision_id";
		}
		
		$result = $db->Execute($query);
		
		if (isset($result[0])) {
			$output_revision = $result[0];
			$output_revision['revision_body'] = $body->am_render($output_revision['revision_body']);
		}
	}
	
	if (!isset($output_revision) && !empty($_REQUEST['wikipage']) && empty($_POST)) {
		
		$output_revision['wikipage_name'] = $_REQUEST['wikipage'];
	}
	
	if (!empty($output_revision)) {
		$body->set('revision', $output_revision);
		
		// get webpages
		$output_webpages = $ws->selWebPages();
	
		if (!empty($output_webpages)) {
			$body->set('webpages', $output_webpages);
		}
		
		// get wikipages
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

		// GET FILES ----------------------------------
		$output_files = $file->selFiles();
		
		if (!empty($output_files)) {
			$body->set('pictures', $output_files);
		}
	}
	else { // we list and manage wikipages
		
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
	}
	

}
else { // no permission to be here
	header("Location: index.php");
	exit;
}
?>