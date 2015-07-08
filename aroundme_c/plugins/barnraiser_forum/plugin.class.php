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


class Plugin_barnraiser_forum {
	// storage and template instances should be passed by reference to this class
	
	var $level = 0; // the permission level requied to see an item
	var $attributes; // any block attributes passed to the class


	function block_subject () {
		// details a subject and associated replies
		// will list either requested subject or latest subject (under a particular webpage if requested)
		
		if (isset($_REQUEST['subject_id'])) {
			$query = "
				SELECT s.subject_id, s.subject_title, s.subject_body,
				UNIX_TIMESTAMP(s.subject_create_datetime) as subject_create_datetime,
				UNIX_TIMESTAMP(s.subject_edit_datetime) as subject_edit_datetime,
				s.subject_locked, s.subject_sticky,
				c.connection_nickname, c.connection_openid, c.connection_id
				FROM " . $this->am_storage->prefix . "_plugin_forum_subject s, " . $this->am_storage->prefix . "_connection c
				WHERE
				s.connection_id=c.connection_id AND
				s.webspace_id=" . AM_WEBSPACE_ID . " AND 
				s.subject_id=" . $_REQUEST['subject_id']
			;
			
			$result = $this->am_storage->Execute($query);
			
			if (isset($result[0])) {
				if (isset($_SESSION['connection_id'])) {
					$result[0]['tracking'] = $this->_selTracking($result[0]['subject_id']);
				}
				
				$this->am_template->set('barnraiser_forum_subject', $result[0]);

				$_REQUEST['subject_id'] = $result[0]['subject_id'];

				$this->selReplies($result[0]['subject_id']);
			}
		}
		else {
			// paging...
			if (isset($_GET['tag'])) {
				$query = "
					SELECT COUNT(s.subject_id) AS total
					FROM " . $this->am_storage->prefix . "_plugin_forum_subject s
					INNER JOIN " . $this->am_storage->prefix . "_plugin_forum_tag t
					ON s.subject_id=t.subject_id
					WHERE (s.webspace_id=" . AM_WEBSPACE_ID . " AND
					s.subject_archived IS NULL AND
					t.tag_name=" . $this->am_storage->qstr($_GET['tag']) . ")"
				;
			}
			elseif (isset($_GET['barnraiser_forum_subject_search_text'])) {
				$query = "
					SELECT COUNT(s.subject_id) AS total
					FROM " . $this->am_storage->prefix . "_plugin_forum_subject s
					WHERE s.webspace_id=" . AM_WEBSPACE_ID . " AND
					s.subject_archived IS NULL AND
					(s.subject_body LIKE " . $this->am_storage->qstr("%" . $_GET['barnraiser_forum_subject_search_text'] . "%") . "
					OR s.subject_title LIKE " . $this->am_storage->qstr("%" . $_GET['barnraiser_forum_subject_search_text'] . "%") . ")"
				;
			}
			else {
				$query = "
					SELECT COUNT(s.subject_id) AS total
					FROM " . $this->am_storage->prefix . "_plugin_forum_subject s 
					WHERE s.webspace_id=" . AM_WEBSPACE_ID . " AND
					s.subject_archived IS NULL"
				;
			}
		
			$result = $this->am_storage->Execute($query);
		
			if (isset($result[0]['total'])) {
				$total = $result[0]['total'];
				$this->am_template->set('total_nr_of_rows_subjects', $total);
			}
			else {
				$this->am_template->set('total_nr_of_rows_subjects', 0);
			}
			$from = isset($_GET['_frmsubjects']) ? (int) $_GET['_frmsubjects'] : 0;
			// eo paging... 
			
			$query = "
				SELECT s.subject_title, s.subject_sticky, s.subject_locked, c.connection_avatar, 
				c.connection_openid, c.connection_id, c.connection_nickname, 
				UNIX_TIMESTAMP(s.subject_create_datetime) as subject_create_datetime, 
				COUNT(r.reply_id) AS tot_replies, s.subject_id
				FROM " . $this->am_storage->prefix . "_plugin_forum_subject s
				INNER JOIN " . $this->am_storage->prefix . "_connection c
				ON s.connection_id=c.connection_id
				LEFT JOIN " . $this->am_storage->prefix . "_plugin_forum_reply r
				ON s.subject_id=r.subject_id "
			;
			
			if (isset($_GET['tag'])) {
				$query .= "INNER JOIN " . $this->am_storage->prefix . "_plugin_forum_tag t
				ON s.subject_id=t.subject_id ";
			}
			
			if (isset($_GET['tag'])) {
				$query .= " WHERE s.webspace_id=" . AM_WEBSPACE_ID . " AND t.tag_name=" . $this->am_storage->qstr($_GET['tag']);
			}
			
			if (isset($_GET['barnraiser_forum_subject_search_text'])) {
				$query .= " WHERE (s.subject_body LIKE " . $this->am_storage->qstr("%" . $_GET['barnraiser_forum_subject_search_text'] . "%") . "
				OR s.subject_title LIKE " . $this->am_storage->qstr("%" . $_GET['barnraiser_forum_subject_search_text'] . "%") . ") AND s.webspace_id=" . AM_WEBSPACE_ID;
			}
			
			if (!isset($_GET['barnraiser_forum_subject_search_text']) && !isset($_GET['tag'])) {
				$query .= "WHERE s.webspace_id=" . AM_WEBSPACE_ID;
			}
			
			$query .= "
				AND s.subject_archived IS NULL 
				GROUP BY s.subject_id
				ORDER BY s.subject_sticky DESC, s.subject_create_datetime DESC"
			;

			$result = $this->am_storage->Execute($query, AM_MAX_LIST_ROWS, $from);
			
			if (!empty($result)) {
				foreach($result as $key => $r) {
					$query = "
						SELECT DISTINCT tag_name
						FROM " . $this->am_storage->prefix . "_plugin_forum_tag
						WHERE subject_id=" . $r['subject_id'] . "
						ORDER BY tag_name"
					;
					$result[$key]['tags'] = $this->am_storage->Execute($query);
					
					$query = "
						SELECT r.reply_id,
						UNIX_TIMESTAMP(r.reply_create_datetime) as reply_create_datetime, c.connection_nickname,
						c.connection_openid, c.connection_id
						FROM " . $this->am_storage->prefix . "_plugin_forum_reply r
						INNER JOIN " . $this->am_storage->prefix . "_connection c
						ON r.connection_id=c.connection_id
						WHERE r.subject_id=" . $r['subject_id'] . "
						ORDER BY r.reply_create_datetime DESC
						LIMIT 1"
					;
					$result[$key]['latest_comment'] = $this->am_storage->Execute($query);
				}

				$this->am_template->set('barnraiser_forum_subjects_list', $result);
			}
		}
	}
	

	function block_subject_list ($connection_id=null) {
		// list of latest subjects, reply total, last reply datetime
		$query = "
			SELECT s.subject_id, s.subject_title, s.subject_body,
			UNIX_TIMESTAMP(s.subject_create_datetime) as subject_create_datetime,
			c.connection_avatar, c.connection_id, c.connection_openid,
			c.connection_nickname 
			FROM " . $this->am_storage->prefix . "_plugin_forum_subject s,
			" . $this->am_storage->prefix . "_connection c "
		;
		
		if (isset($_GET['tag'])) {
			$query .= ", " . $this->am_storage->prefix. "_plugin_forum_tag t ";
		}
		
		$query .= "
			WHERE
			s.connection_id=c.connection_id AND 
			s.webspace_id=" . AM_WEBSPACE_ID . " AND
			s.subject_archived IS NULL AND ";
		
		if (isset($_GET['tag'])) {
			$query .= "t.tag_name='" . $_GET['tag'] . "' 
			AND s.subject_id=t.subject_id AND ";
		}

		if (isset($connection_id)) {
			$query .= "s.connection_id=" . $connection_id . " AND ";
		}
		
		$query .= "1=1 ORDER BY s.subject_sticky desc, s.subject_create_datetime desc";

		if (isset($this->attributes['limit'])) {
			$result = $this->am_storage->Execute($query, (int) $this->attributes['limit']);
		}
		else {
			$result = $this->am_storage->Execute($query);
		}
 	
		if (!empty($result)) {
			foreach($result as $key => $i):
				$result[$key]['subject_body'] = strip_tags($result[$key]['subject_body']);

				if (isset($this->attributes['trim'])) {
					if (strlen($result[$key]['subject_title']) > $this->attributes['trim']) {
						$result[$key]['subject_title'] = mb_substr($result[$key]['subject_title'], 0, $this->attributes['trim'], 'UTF-8') . '...';
					}
				}
				
				if (isset($this->attributes['trim'])) {
					if (strlen($result[$key]['subject_body']) > $this->attributes['trim']) {
						$result[$key]['subject_body'] = mb_substr($result[$key]['subject_body'], 0, $this->attributes['trim'], 'UTF-8') . '...';
					}
				}
				
				if (isset($this->attributes['webpage'])) {
					$result[$key]['webpage'] = $this->attributes['webpage'];
				}
				elseif (defined('AM_WEBPAGE_NAME')) {
					$result[$key]['webpage'] = AM_WEBPAGE_NAME;
				}
			
			endforeach;
			
			$this->am_template->set('barnraiser_forum_subjects', $result);
		}

		if (isset($this->attributes['webpage'])) {
			$barnraiser_forum_subjects_wp = $this->attributes['webpage'];
		}
		elseif (defined('AM_WEBPAGE_NAME')) {
			$barnraiser_forum_subjects_wp = AM_WEBPAGE_NAME;
		}

		if (isset($barnraiser_forum_subjects_wp)) {
			$this->am_template->set('barnraiser_forum_subjects_wp', $barnraiser_forum_subjects_wp);
		}
	}


	function block_subject_combo_list () {
		// list of latest subjects and replies
		$query = "
			SELECT s.subject_id, s.subject_title, s.subject_body as body, 0 as reply_id, 
			UNIX_TIMESTAMP(s.subject_create_datetime) AS create_datetime, c.*
			FROM " . $this->am_storage->prefix . "_plugin_forum_subject s
			INNER JOIN " . $this->am_storage->prefix . "_connection c
			ON s.connection_id=c.connection_id
			WHERE
			s.subject_archived IS NULL
			UNION
			SELECT s.subject_id, s.subject_title, r.reply_body as body, r.reply_id,
			UNIX_TIMESTAMP(r.reply_create_datetime) AS create_datetime, c.* 
			FROM " . $this->am_storage->prefix . "_plugin_forum_subject s
			INNER JOIN " . $this->am_storage->prefix . "_plugin_forum_reply r
			ON r.subject_id=s.subject_id
			INNER JOIN " . $this->am_storage->prefix . "_connection c
			ON r.connection_id=c.connection_id
			WHERE
			s.subject_archived IS NULL
			ORDER BY create_datetime DESC"
		;
		
		
		if (isset($this->attributes['limit'])) {
			$result = $this->am_storage->Execute($query, (int) $this->attributes['limit']);
		}
		else {
			$result = $this->am_storage->Execute($query);
		}
 	
		if (!empty($result)) {
			foreach($result as $key => $i):
				$result[$key]['body'] = strip_tags($result[$key]['body']);

				if (isset($this->attributes['trim'])) {
					if (strlen($result[$key]['subject_title']) > $this->attributes['trim']) {
						$result[$key]['subject_title'] = mb_substr($result[$key]['subject_title'], 0, $this->attributes['trim'], 'UTF-8') . '...';
					}
				}
				
				if (isset($result[$key]['body']) && isset($this->attributes['trim'])) {
					if (strlen($result[$key]['body']) > $this->attributes['trim']) {
						$result[$key]['body'] = mb_substr($result[$key]['body'], 0, $this->attributes['trim'], 'UTF-8') . '...';
					}
				}
				
				if (isset($this->attributes['webpage'])) {
					$result[$key]['webpage'] = $this->attributes['webpage'];
				}
				elseif (defined('AM_WEBPAGE_NAME')) {
					$result[$key]['webpage'] = AM_WEBPAGE_NAME;
				}
			
			endforeach;
			
			$this->am_template->set('barnraiser_forum_subjects_combo_list', $result);
		}

		if (isset($this->attributes['webpage'])) {
			$barnraiser_forum_subjects_wp = $this->attributes['webpage'];
		}
		elseif (defined('AM_WEBPAGE_NAME')) {
			$barnraiser_forum_subjects_wp = AM_WEBPAGE_NAME;
		}

		if (isset($barnraiser_forum_subjects_wp)) {
			$this->am_template->set('barnraiser_forum_subjects_wp', $barnraiser_forum_subjects_wp);
		}
	}
	

	function block_reply_list () {
		// list of latest replies, subject, connection who published reply
		// option = order="recommended" gets by highest recommended comment
		$query = "
			SELECT r.reply_id, r.reply_body, r.subject_id, r.reply_archived,
			UNIX_TIMESTAMP(r.reply_create_datetime) as reply_create_datetime,
			c.connection_nickname, c.connection_openid, c.connection_id 
			FROM " . $this->am_storage->prefix . "_plugin_forum_reply r, " . $this->am_storage->prefix . "_plugin_forum_subject s,
			" . $this->am_storage->prefix . "_connection c
			WHERE
			r.connection_id=c.connection_id AND 
			r.webspace_id=" . AM_WEBSPACE_ID . " AND 
			s.subject_id=r.subject_id AND "
		;
		
		if (isset($_REQUEST['subject_id'])) {
			$query .= "s.subject_id=" . $_REQUEST['subject_id'] . " AND ";
		}
		else { // if we do not get a subject_id we select the latest
			$query .= "
				s.subject_archived IS NULL AND "
			;
		}

		$query .= "1=1 ORDER BY r.reply_create_datetime desc";
			
		if (isset($attributes['limit'])) {
			$result = $this->am_storage->Execute($query, (int) $attributes['limit']);
		}
		else {
			$result = $this->am_storage->Execute($query);
		}
	
		if (!empty($result)) {
			foreach($result as $key => $i):
				$result[$key]['reply_body'] = strip_tags($result[$key]['reply_body']);
				
				if (isset($attributes['trim'])) {
					if (strlen($result[$key]['reply_body']) > $attributes['trim']) {
						$result[$key]['reply_body'] = mb_substr($result[$key]['reply_body'], 0, $this->attributes['trim'], 'UTF-8') . '...';
					}
				}
				
				if (isset($this->attributes['webpage'])) {
					$result[$key]['webpage'] = $this->attributes['webpage'];
				}
				else {
					$result[$key]['webpage'] = AM_WEBPAGE_NAME;
				}
			endforeach;
			
			$this->am_template->set('barnraiser_forum_replies', $result);
		}
	}
	

	function block_tagcloud () {
		// list of latest replies, subject, connection who published reply
		$query = "
			SELECT t.tag_name, COUNT(t.tag_name) AS tag_total
			FROM " . $this->am_storage->prefix . "_plugin_forum_tag t, " . $this->am_storage->prefix . "_plugin_forum_subject s,
			" . $this->am_storage->prefix . "_connection c 
			WHERE
			s.webspace_id=" . AM_WEBSPACE_ID . " AND
			s.connection_id=c.connection_id AND 
			s.subject_id=t.subject_id AND 
			s.subject_archived IS NULL 
			GROUP BY t.tag_name
			ORDER BY t.tag_name"
		;
		
		$result = $this->am_storage->Execute($query);
		
		if (!empty($result)) {
			$this->am_template->set('barnraiser_forum_tags', $result);
		}

		if (isset($this->attributes['webpage'])) {
			$this->am_template->set('barnraiser_forum_tagcloud_wp', $this->attributes['webpage']);
		}
		elseif (defined('AM_WEBPAGE_NAME')) {
			$this->am_template->set('barnraiser_forum_tagcloud_wp', AM_WEBPAGE_NAME);
		}
	}
	
	function block_subject_search () {
		
	}
	
	function block_digest_manager () {
		if (isset($_SESSION['connection_id'])) {
			// GET DIGEST NOTIFICATION
			$query = "
				SELECT digest_frequency
				FROM " . $this->am_storage->prefix . "_plugin_forum_digest
				WHERE
				connection_id=" . $_SESSION['connection_id'] . " AND
				webspace_id=" . AM_WEBSPACE_ID
			;
			
			$result = $this->am_storage->Execute($query);
			
			if (!empty($result[0]['digest_frequency'])) {
				$this->am_template->set('barnraiser_forum_digest_frequency', $result[0]['digest_frequency']);
			}
		}
	}

	
	function selReplies($subject_id) {

		if (!empty($_SESSION['connection_id'])) {
			$connection_id = $_SESSION['connection_id'];
		}
		else { // not logged in
			$connection_id = 0;
		}

		// paging...
	
		$query = "
			SELECT COUNT(r.reply_id) AS total
			FROM " . $this->am_storage->prefix . "_plugin_forum_reply r 
			WHERE r.subject_id=" . $subject_id . " AND"
		;
		
		if (isset($_REQUEST['all'])) {
			$query .= " (r.reply_archived=1 OR r.reply_archived IS null)";
		}
		else {
			$query .= " r.reply_archived IS null";
		}
		
		$result = $this->am_storage->Execute($query);
		
		if (isset($result[0]['total'])) {
			$total = $result[0]['total'];
			$this->am_template->set('total_nr_of_rows_replies', $total);
		}
		else {
			$this->am_template->set('total_nr_of_rows_replies', 0);
		}
		$from = isset($_GET['_frmreplies']) ? (int) $_GET['_frmreplies'] : 0;
		// eo paging... 

		// GET ASSOCIATED REPLIES
		$query = "
			SELECT r.reply_id, r.reply_body, r.reply_archived, 
			UNIX_TIMESTAMP(r.reply_create_datetime) as reply_create_datetime,
			c.connection_nickname, c.connection_openid, c.connection_id,
			COUNT(rr.connection_id) AS total_recommendations, rr2.connection_id as recommendation_connection_id
			FROM " . $this->am_storage->prefix . "_plugin_forum_reply r
			INNER JOIN " . $this->am_storage->prefix . "_connection c
			ON r.connection_id=c.connection_id
			LEFT JOIN " . $this->am_storage->prefix . "_plugin_forum_reply_recommendation rr
			ON r.reply_id=rr.reply_id
			LEFT JOIN " . $this->am_storage->prefix . "_plugin_forum_reply_recommendation rr2
			ON (rr2.connection_id=" . $connection_id . "
			AND r.reply_id=rr2.reply_id)
			WHERE"
		;
			
		if (isset($_REQUEST['all'])) {
			$query .= " (r.reply_archived=1 OR r.reply_archived IS null) AND";
		}
		else {
			$query .= " r.reply_archived IS null AND";
		}
			
		$query .= " r.subject_id=" . $subject_id . " GROUP BY r.reply_id";

		if (isset($_REQUEST['recommended'])) {
			$query .= " ORDER BY total_recommendations DESC";
		}
		else {
			$query .= " ORDER BY r.reply_create_datetime";
		}

		$result = $this->am_storage->Execute($query, AM_MAX_LIST_ROWS, $from);

		if (isset($result)) {
			// Get total recommendataions

			$this->am_template->set('barnraiser_forum_subject_replies', $result);
		}
	}


	function block_recommended_reply_list () {
		
		$query = "
			SELECT r.reply_id, r.reply_body, r.subject_id, 
			UNIX_TIMESTAMP(r.reply_create_datetime) as reply_create_datetime,
			c.connection_nickname, c.connection_openid, c.connection_id,
			UNIX_TIMESTAMP(rr.recommendation_datetime) as recommendation_datetime, 
			COUNT(rr.connection_id) AS total_recommendations
			FROM " . $this->am_storage->prefix . "_plugin_forum_reply r, " . $this->am_storage->prefix . "_plugin_forum_reply_recommendation rr, " . $this->am_storage->prefix . "_connection c
			WHERE
			r.webspace_id=" . AM_WEBSPACE_ID . " AND 
			r.reply_id=rr.reply_id AND
			rr.connection_id=c.connection_id AND 
			r.reply_archived IS null
			GROUP BY r.reply_id
			ORDER BY rr.recommendation_datetime"
		;

		if (isset($attributes['limit'])) {
			$result = $this->am_storage->Execute($query, (int) $attributes['limit']);
		}
		else {
			$result = $this->am_storage->Execute($query);
		}

		if (!empty($result)) {
			foreach($result as $key => $i):
				$result[$key]['reply_body'] = strip_tags($result[$key]['reply_body']);

				if (isset($this->attributes['trim'])) {
					if (strlen($result[$key]['reply_body']) > $this->attributes['trim']) {
						$result[$key]['reply_body'] = mb_substr($result[$key]['reply_body'], 0, $this->attributes['trim'], 'UTF-8') . '...';
					}
				}
				
				if (isset($this->attributes['webpage'])) {
					$result[$key]['webpage'] = $this->attributes['webpage'];
				}
				elseif (defined('AM_WEBPAGE_NAME')) {
					$result[$key]['webpage'] = AM_WEBPAGE_NAME;
				}
			
			endforeach;
			
			$this->am_template->set('barnraiser_forum_recommended_reply_list', $result);
		}
	}

	function _selTracking ($subject_id) {

		$query = "
			SELECT subject_id, notification 
			FROM " . $this->am_storage->prefix . "_plugin_forum_subject_track
			WHERE
			webspace_id=" . AM_WEBSPACE_ID . " AND
			connection_id=" . $_SESSION['connection_id'] . " AND
			subject_id=" . $subject_id
		;
		
		$result = $this->am_storage->Execute($query);
		
		if (!empty($result[0])) {
			return $result[0];
		}
	}
}


$plugin_barnraiser_forum = new Plugin_barnraiser_forum();
$plugin_barnraiser_forum->am_storage = &$db;
$plugin_barnraiser_forum->am_template = &$body;
$plugin_barnraiser_forum->am_file = &$file;


// ASSIGN PERMISSIONS
$plugin_permissions['barnraiser_forum']['add_subject'] = $core_config['group']['contributor']+$core_config['group']['publisher']+$core_config['group']['editor'];
$plugin_permissions['barnraiser_forum']['add_reply'] = $core_config['group']['contributor']+$core_config['group']['publisher']+$core_config['group']['editor'];
$plugin_permissions['barnraiser_forum']['reply_recommend'] = $core_config['group']['contributor']+$core_config['group']['publisher']+$core_config['group']['editor'];
$plugin_permissions['barnraiser_forum']['reply_filter'] = $core_config['group']['editor'];
$plugin_permissions['barnraiser_forum']['manage_tags'] = $core_config['group']['editor'];
$plugin_permissions['barnraiser_forum']['manage_forum'] = $core_config['group']['editor'];

?>