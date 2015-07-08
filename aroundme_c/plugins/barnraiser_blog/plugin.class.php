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


class Plugin_barnraiser_blog {
	// storage and template instances should be passed by reference to this class
	
	var $level = 0; // the permission level requied to see an item
	var $attributes; // any block attributes passed to the class


	function block_entry () {
		$query = "
			SELECT b.blog_id, b.blog_title, b.blog_body, b.blog_allow_comment,
			UNIX_TIMESTAMP(b.blog_create_datetime) as blog_create_datetime,
			UNIX_TIMESTAMP(b.blog_edit_datetime) as blog_edit_datetime, 
			c.connection_nickname, c.connection_openid, c.connection_id 
			FROM " . $this->am_storage->prefix . "_plugin_blog_entry b, " . $this->am_storage->prefix . "_connection c
			WHERE
			b.connection_id=c.connection_id AND
			b.webspace_id=" . AM_WEBSPACE_ID . " AND "
		;

		if (isset($_REQUEST['blog_id'])) {
			$query .= "b.blog_id=" . $_REQUEST['blog_id'];
		}
		else { // if we do not get a blog_id we select the latest
			$query .= "b.blog_archived IS NULL ORDER BY b.blog_create_datetime desc";
		}

		if (isset($this->attributes['limit'])) {
			$result = $this->am_storage->Execute($query, (int) $this->attributes['limit']);
		}
		else {
			$result = $this->am_storage->Execute($query);
		}

		if (isset($result[0])) {
			$this->am_template->set('barnraiser_blog_entry', $result[0]);
			
			$_REQUEST['blog_id'] = $result[0]['blog_id'];

			$this->selComments($result[0]['blog_id']);

			$this->_setupRSS(AM_WEBPAGE_NAME);
		}
	}

	function block_list ($connection_id=null) {
		$query = "
			SELECT b.blog_id, b.blog_title, b.blog_body,
			UNIX_TIMESTAMP(b.blog_create_datetime) as blog_create_datetime,
			c.connection_avatar, c.connection_id, c.connection_openid,
			c.connection_nickname 
			FROM " . $this->am_storage->prefix . "_plugin_blog_entry b,
			" . $this->am_storage->prefix . "_connection c 
			WHERE 
			b.connection_id=c.connection_id AND
			b.webspace_id=" . AM_WEBSPACE_ID . " AND
			b.blog_archived IS NULL AND "
		;
		
		if (isset($connection_id)) {
			$query .= "b.connection_id=" . $connection_id . " AND ";
		}
		
		$query .= "1=1 ORDER BY b.blog_create_datetime desc";


		if (isset($this->attributes['limit'])) {
			$result = $this->am_storage->Execute($query, (int) $this->attributes['limit']);
		}
		else {
			$result = $this->am_storage->Execute($query);
		}

		if (!empty($result)) {
			foreach($result as $key => $i):
				if (isset($this->attributes['trim'])) {
					if (strlen($result[$key]['blog_title']) > $this->attributes['trim']) {
						$result[$key]['blog_title'] = mb_substr($result[$key]['blog_title'], 0, $this->attributes['trim'], 'UTF-8') . '...';
					}
				}

				$result[$key]['blog_body'] = strip_tags($result[$key]['blog_body']);
				
				if (isset($this->attributes['trim'])) {
					if (strlen($result[$key]['blog_body']) > $this->attributes['trim']) {
						$result[$key]['blog_body'] = mb_substr($result[$key]['blog_body'], 0, $this->attributes['trim'], 'UTF-8') . '...';
					}
				}

				if (isset($this->attributes['webpage'])) {
					$result[$key]['webpage'] = $this->attributes['webpage'];
				}
				elseif (defined('AM_WEBPAGE_NAME')) {
					$result[$key]['webpage'] = AM_WEBPAGE_NAME;
				}
			
			endforeach;
			
			$this->am_template->set('barnraiser_blog_list', $result);
		}

		if (isset($this->attributes['webpage'])) {
			$barnraiser_blog_list_wp = $this->attributes['webpage'];
		}
		elseif (defined('AM_WEBPAGE_NAME')) {
			$barnraiser_blog_list_wp = AM_WEBPAGE_NAME;
		}

		if (isset($barnraiser_blog_list_wp)) {
			$this->am_template->set('barnraiser_blog_list_wp', $barnraiser_blog_list_wp);

			$this->_setupRSS($barnraiser_blog_list_wp);
		}
	}

	function block_tagcloud () {

	}

	function selComments ($blog_id) {

		$query = "
			SELECT 
			bc.comment_id, bc.comment_body, bc.comment_hidden,
			UNIX_TIMESTAMP(bc.comment_create_datetime) as comment_create_datetime,
			c.connection_nickname, c.connection_openid, c.connection_id 
			FROM " . $this->am_storage->prefix . "_plugin_blog_comment bc, " . $this->am_storage->prefix . "_connection c
			WHERE 
			bc.connection_id=c.connection_id AND
			bc.webspace_id=" . AM_WEBSPACE_ID . " AND
			bc.blog_id=" . $blog_id
		;
		
		if (!isset($_SESSION['connection_permission']) || !($_SESSION['connection_permission'] & $this->filter_permission)) {
			$query .= " AND bc.comment_hidden IS NULL";
		}

		$query .= " ORDER BY bc.comment_create_datetime";
		
		$result = $this->am_storage->Execute($query);

		if (isset($result)) {
			$this->am_template->set('barnraiser_blog_comments', $result);
		}
	}

	function _setupRSS ($webpage_name) {
		// ADD RSS FEED TO HEADER ------------------------
		$query = "
			SELECT default_webpage_id, rss_title, rss_title_comment
			FROM " . $this->am_storage->prefix . "_plugin_blog_preference
			WHERE
			webspace_id=" . AM_WEBSPACE_ID
		;
		
		$result = $this->am_storage->Execute($query);
		
		if (!empty($result[0]['rss_title'])) {
			$rss_title = $result[0]['rss_title'];
		}
		else {
			$rss_title = 'blog feed';
		}
		
		$rss_link = $this->ws_url . "/plugins/barnraiser_blog/feed/rss.php?ws=" . AM_WEBSPACE_NAME . "&wp=" . $webpage_name . "&k=" . md5(AM_WEBSPACE_CREATE_DATETIME);
		
		$template_link_attributes = array('alternate', 'application/rss+xml', $rss_title, $rss_link);

		$this->am_template->header_link_tag_arr['blog'] = $template_link_attributes;

		// comments
		if (!empty($result[0]['rss_title_comment'])) {
			$rss_title = $result[0]['rss_title_comment'];
		}
		else {
			$rss_title = 'blog comments feed';
		}
		
		$rss_link = $rss_link . "&v=comments";
		
		$template_link_attributes = array('alternate', 'application/rss+xml', $rss_title, $rss_link);

		$this->am_template->header_link_tag_arr['blog_comment'] = $template_link_attributes;
	}
}


$plugin_barnraiser_blog = new Plugin_barnraiser_blog();
$plugin_barnraiser_blog->am_storage = &$db;
$plugin_barnraiser_blog->am_template = &$body;
$plugin_barnraiser_blog->am_file = &$file;
$plugin_barnraiser_blog->ws_url = str_replace('REPLACE', $ws->webspace_unix_name, $core_config['am']['domain_replace_pattern']);


// ASSIGN PERMISSIONS
$plugin_permissions['barnraiser_blog']['add_blog_entry'] = $core_config['group']['publisher']+$core_config['group']['editor'];
$plugin_permissions['barnraiser_blog']['add_comment'] = $core_config['group']['contributor']+$core_config['group']['publisher']+$core_config['group']['editor'];
$plugin_permissions['barnraiser_blog']['comment_filter'] = $core_config['group']['editor'];
$plugin_permissions['barnraiser_blog']['manage_tags'] = $core_config['group']['editor'];
$plugin_permissions['barnraiser_blog']['manage_blog'] = $core_config['group']['editor'];

$plugin_barnraiser_blog->filter_permission = $plugin_permissions['barnraiser_blog']['comment_filter'];

?>