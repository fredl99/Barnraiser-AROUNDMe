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


class Plugin_barnraiser_wiki {
	// storage and template instances should be passed by reference to this class
	
	var $level = 0; // the permission level requied to see an item
	var $attributes; // any block attributes passed to the class


	function block_history () {
		// select history of a page
		if (isset($_REQUEST['revision_id'])) {
			$query = "
				SELECT wip.wikipage_name
				FROM " . $this->am_storage->prefix . "_plugin_wiki_page wip, " . $this->am_storage->prefix . "_plugin_wiki_revision r
				WHERE
				wip.wikipage_id=r.wikipage_id AND
				r.revision_id=" . $_REQUEST['revision_id']
			;
			
			$result = $this->am_storage->Execute($query);

			if (!empty($result[0]['wikipage_name'])) {
				$wikipage_name = $result[0]['wikipage_name'];
			}
		}

		if (!isset($wikipage_name)) {
			if (isset($_REQUEST['wikipage'])) {
				$wikipage_name = $_REQUEST['wikipage'];
			}
			elseif (isset($this->attributes['wikipage'])) {
				$wikipage_name = $this->attributes['wikipage'];
			}
		}
		
		if (isset($wikipage_name)) {
			
			$query = "
				SELECT wip.current_revision_id, r.revision_id,
				UNIX_TIMESTAMP(r.revision_create_datetime) as revision_create_datetime,
				c.connection_nickname, c.connection_openid, c.connection_id 
				FROM " . $this->am_storage->prefix . "_plugin_wiki_page wip, " . $this->am_storage->prefix . "_plugin_wiki_revision r, " . $this->am_storage->prefix . "_connection c
				WHERE
				wip.wikipage_id=r.wikipage_id AND 
				wip.wikipage_name= " . $this->am_storage->qstr($wikipage_name) . " AND
				r.connection_id=c.connection_id AND 
				wip.webspace_id=" . AM_WEBSPACE_ID . " 
				ORDER BY r.revision_create_datetime desc"
			;
	
			if (isset($this->attributes['limit']) && is_numeric($this->attributes['limit'])) {
				$result = $this->am_storage->Execute($query, (int) $this->attributes['limit']);
			}
			else {
				$result = $this->am_storage->Execute($query);
			}
			
			if (!empty($result)) {
				$this->am_template->set('barnraiser_wiki_history', $result);
			}
		}
	}
	
	function block_page () {
		// We look for a $_REQUEST['wikipage']. If we don't have one then we load the
		//default page (from $this->attribute['wikipage_name'])
		
		if (empty($this->attributes['wikipage'])) {
			$GLOBALS['am_error_log'][] = array('plugin_barnraiser_wiki_plugin_wikipage_attribute');
		}
		else {
		
			if(!empty($_REQUEST['wikipage'])) {
				$output_wikipage = $this->selWikiPage($_REQUEST['wikipage']);
			}
		
			if (!isset($output_wikipage['wikipage_id'])) {
				$output_wikipage = $this->selWikiPage($this->attributes['wikipage']);
				
				if (!isset($output_wikipage['wikipage_id'])) {
					// this must be a new page so we need to check the formatting
					$pattern = "/^[a-zA-Z0-9]*$/";
					
					if (!preg_match($pattern, $this->attributes['wikipage'])) {
						$GLOBALS['am_error_log'][] = array('plugin_barnraiser_wiki_plugin_wikilink_bad_chars', $this->attributes['wikipage']);
					}
					
					if (strlen($this->attributes['wikipage']) > 30) { // link too long
						$GLOBALS['am_error_log'][] = array('plugin_barnraiser_wiki_plugin_wikilink_too_long', $this->attributes['wikipage']);
					}
				}
			}
		}
		
		if (isset($output_wikipage['wikipage_id'])) {
		
			$output_wikipage['revision_body'] = $this->applyWikiLinking($output_wikipage['revision_body']);
			$output_wikipage['revision_body'] = $this->applyToc($output_wikipage['revision_body']);
			$this->selNotes ($output_wikipage['wikipage_id']);
		}
		else {
			$output_wikipage['wikipage_name'] = $this->attributes['wikipage'];
		}
		
		if (isset($this->attributes['wikipage'])) {
			$this->am_template->set('barnraiser_wiki_wikipage', $this->attributes['wikipage']);
		}
		
		$this->am_template->set('barnraiser_wiki_page', $output_wikipage);
	}
	
	function selWikiPage ($wikipage_name) {
		$query = "
			SELECT 
			wip.wikipage_id, r.revision_id, r.revision_body, r.connection_id,
			UNIX_TIMESTAMP(r.revision_create_datetime) as revision_create_datetime,
			wip.current_revision_id, wip.wikipage_name, wip.wikipage_allow_note, 
			c.connection_nickname, c.connection_openid, c.connection_id 
			FROM " . $this->am_storage->prefix . "_plugin_wiki_page wip, " . $this->am_storage->prefix . "_plugin_wiki_revision r, " . $this->am_storage->prefix . "_connection c
			WHERE 
			r.connection_id=c.connection_id AND "
		;

		if (isset($_REQUEST['revision_id'])) {
			$query .= " r.revision_id=" . $_REQUEST['revision_id'] . " AND r.wikipage_id=wip.wikipage_id AND ";
		}
		else {
			$query .= "
				wip.wikipage_name=" . $this->am_storage->qstr($wikipage_name) . " AND
				wip.current_revision_id=r.revision_id AND "
			;
		}

		$query .= "wip.webspace_id=" . AM_WEBSPACE_ID;

		$result = $this->am_storage->Execute($query);

		if (isset($result[0])) {
			$_REQUEST['revision_id'] = $result[0]['revision_id'];
			return $result[0];
		}
	}
	
	function applyWikiLinking($body) {
		
		// WIKILINKING ------------------------------------------------
		$query = "
			SELECT DISTINCT wikipage_name
			FROM " . $this->am_storage->prefix . "_plugin_wiki_page
			WHERE
			webspace_id=" . AM_WEBSPACE_ID
		;

		$result = $this->am_storage->Execute($query);

		if (isset($result)) {
			$pages = array();

			foreach ($result as $key => $i):
				array_push($pages, $i['wikipage_name']);
			endforeach;
		}

		$wikilink_path = "index.php?wp=" . AM_WEBPAGE_NAME . "&wikipage=";
		$wikilink_new_path = "index.php?p=barnraiser_wiki&t=edit_wikipage&wp=" . AM_WEBPAGE_NAME . "&wikipage=";

		// we run through the page body replacing any pages
		if (!empty($pages)) {
			foreach ($pages as $keyp => $p):
				// with anchors
				$pattern = "/<wikilink name=\"" . $p . "#(.*?)\">(.*?)<\/wikilink>/";

				$replacement = "<a href=\"" . $wikilink_path . $p . "#$1\">$2</a>";

				$body = preg_replace($pattern, $replacement, $body);

				// without anchors
				$pattern = "/<wikilink name=\"" . $p . "\">(.*?)<\/wikilink>/";

				$replacement = "<a href=\"" . $wikilink_path . $p . "\">$1</a>";

				$body = preg_replace($pattern, $replacement, $body);
			endforeach;
		}

		// now we look for new pages
		$pattern = "/<wikilink name=\"(.*?)\">(.*?)<\/wikilink>/";

		$replacement = "$2<a href=\"" . $wikilink_new_path . "$1\"><sup>?</sup></a>";

		$body = preg_replace($pattern, $replacement, $body);
		
		return $body;
	}
	
	function applyToc ($body) {
		
		$pattern = "/<toc>(.*?)<\/toc>/";

		if (preg_match($pattern, $body, $matches)) {
			$toc = "<div id=\"toc\">" . $matches[0] . "<br />";
			$toc .= "<ul>";
	
			// find h tags and order
			$h_pattern = "/<h(.*?)>(.*?)<\/h/";

			if (preg_match_all($h_pattern, $body, $matches)) {
				if (!empty($matches[2])) {
					foreach ($matches[2] as $key => $i):
			
						$toc .= "<li><a href=\"#atoc" . $key . "\">" . $i . "</a></li>";

						// workaround for problem with using forward slash - tom
						$i = str_replace("/", "&#47;", $i);
						
						// get the H and apply anchor
						$body = str_replace("<h" . $matches[1][$key] . ">" . $i . "</h" . $matches[1][$key] . ">", "<a name=\"atoc" . $key ."\"></a>\n<h" . $matches[1][$key] . ">" . $i . "</h" . $matches[1][$key] . ">", $body);
					endforeach;
				}
			}

	   		$toc .= "</ul>";
	   		$toc .= "</div>";
       
	   		$body = preg_replace($pattern, $toc, $body);
		}
		
		return $body;
	}
	
	function selRevisionNotes ($revision_id) {
		$query = "
			SELECT 
			wip.wikipage_id, r.revision_id, r.revision_body, r.connection_id,
			UNIX_TIMESTAMP(r.revision_create_datetime) as revision_create_datetime,
			wip.current_revision_id, wip.wikipage_name, 
			c.connection_nickname, c.connection_openid, c.connection_id 
			FROM " . $this->am_storage->prefix . "_plugin_wiki_page wip, " . $this->am_storage->prefix . "_plugin_wiki_revision r, " . $this->am_storage->prefix . "_connection c
			WHERE 
			r.connection_id=c.connection_id AND "
		;

		if (isset($_REQUEST['revision_id'])) {
			$query .= " r.revision_id=" . $_REQUEST['revision_id'] . " AND r.wikipage_id=wip.wikipage_id AND ";
		}
		else {
			$query .= "
				wip.wikipage_name=" . $this->am_storage->qstr($wikipage_name) . " AND
				wip.current_revision_id=r.revision_id AND "
			;
		}

		$query .= "wip.webspace_id=" . AM_WEBSPACE_ID;

		$result = $this->am_storage->Execute($query);

		if (isset($result[0])) {
			return $result[0];
		}
	}
	
	
	function selNotes ($wikipage_id) {
		
		if (!empty($_SESSION['connection_id'])) {
			$connection_id = $_SESSION['connection_id'];
		}
		else { // not logged in
			$connection_id = 0;
		}

		// GET ASSOCIATED REPLIES
		$query = "
			SELECT n.note_id, n.note_body,
			UNIX_TIMESTAMP(n.note_create_datetime) as note_create_datetime,
			c.connection_nickname, c.connection_openid, c.connection_id 
			FROM " . $this->am_storage->prefix . "_plugin_wiki_note n
			INNER JOIN " . $this->am_storage->prefix . "_connection c
			ON n.connection_id=c.connection_id 
			WHERE
			n.wikipage_id=" . $wikipage_id . "
			ORDER BY n.note_create_datetime"
		;

		$result = $this->am_storage->Execute($query);

		if (isset($result)) {
			$this->am_template->set('barnraiser_wiki_notes', $result);
		}
	}
}

$plugin_barnraiser_wiki = new Plugin_barnraiser_wiki();
$plugin_barnraiser_wiki->am_storage = &$db;
$plugin_barnraiser_wiki->am_template = &$body;
$plugin_barnraiser_wiki->am_file = &$file;


// ASSIGN PERMISSIONS
$plugin_permissions['barnraiser_wiki']['edit_page'] = $core_config['group']['editor'];
$plugin_permissions['barnraiser_wiki']['add_note'] = $core_config['group']['contributor']+$core_config['group']['publisher']+$core_config['group']['editor'];
$plugin_permissions['barnraiser_wiki']['manage_wiki'] = $core_config['group']['editor'];
?>