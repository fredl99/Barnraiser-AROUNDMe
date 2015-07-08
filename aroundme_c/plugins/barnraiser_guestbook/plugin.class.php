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


class Plugin_barnraiser_guestbook {
	// storage and template instances should be passed by reference to this class
	
	var $level = 0; // the permission level requied to see an item
	var $attributes; // any block attributes passed to the class

	function block_list ($connection_id=null) {

		$query = "
			SELECT UNIX_TIMESTAMP(gb.guestbook_create_datetime) as guestbook_create_datetime,
			gb.guestbook_body, c.connection_nickname, c.connection_openid, c.connection_id, c.connection_avatar 
			FROM " . $this->am_storage->prefix . "_plugin_guestbook gb, " . $this->am_storage->prefix . "_connection c
			WHERE
			gb.connection_id=c.connection_id AND
			gb.webspace_id=" . AM_WEBSPACE_ID . " AND "
		;

		if (isset($connection_id)) {
			$query .= "gb.connection_id=" . $connection_id . " AND ";
		}
		
		$query .= "1=1 ORDER BY gb.guestbook_create_datetime desc";
		
		//if there is a limit we fill the rest of the array with empty fields
		if (isset($this->attributes['limit'])) {
			$result = $this->am_storage->Execute($query, (int) $this->attributes['limit']);
		}
		else {
			$result = $this->am_storage->Execute($query);
		}

		if (!empty($result)) {
			$this->am_template->set('barnraiser_guestbook_entries', $result);
		}
	}
}

$plugin_barnraiser_guestbook = new Plugin_barnraiser_guestbook();
$plugin_barnraiser_guestbook->am_storage = &$db;
$plugin_barnraiser_guestbook->am_template = &$body;
$plugin_barnraiser_guestbook->am_file = &$file;


// ASSIGN PERMISSIONS
$plugin_permissions['barnraiser_guestbook']['add_entry'] = $core_config['group']['contributor']+$core_config['group']['publisher']+$core_config['group']['editor'];
$plugin_permissions['barnraiser_guestbook']['maintain'] = $core_config['group']['editor'];
?>