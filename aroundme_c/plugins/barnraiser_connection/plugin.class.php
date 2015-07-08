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


class Plugin_barnraiser_connection {
	// storage and template instances should be passed by reference to this class
	
	var $level = 0; // the permission level requied to see an item
	var $attributes; // any block attributes passed to the class


	function block_connect () {
	
	}


	function block_gallery () {
		// creates avatar gallery with links directly to persons site
		$query = "
			SELECT
			connection_id, connection_openid, connection_nickname, connection_avatar 
			FROM " . $this->am_storage->prefix . "_connection
			WHERE 
			webspace_id=" . AM_WEBSPACE_ID . " AND
			status_id=2 AND "
		;

		if (isset($this->attributes['avatar'])) {
			$query .= "connection_avatar IS NOT NULL AND ";
		}
		
		$query .= " 1=1 ORDER BY connection_last_datetime desc, connection_create_datetime desc";

		//if there is a limit we fill the rest of the array with empty fields
		if (isset($this->attributes['limit'])) {
			$result = $this->am_storage->Execute($query, (int) $this->attributes['limit']);
		}
		else {
			$result = $this->am_storage->Execute($query);
		}

		
		if (isset($result)) {
			$this->am_template->set('barnraiser_connection_inbound_connections', $result);
		}
		
	}


	function block_log () {
		
		$query = "
			SELECT
			log_body, UNIX_TIMESTAMP(log_create_datetime) as log_create_datetime
			FROM " . $this->am_storage->prefix . "_log
			WHERE 
			webspace_id=" . AM_WEBSPACE_ID . "
			ORDER BY log_create_datetime desc"
		;

		//if there is a limit we fill the rest of the array with empty fields
		if (isset($this->attributes['limit'])) {
			$result = $this->am_storage->Execute($query, (int) $this->attributes['limit']);
		}
		else {
			$result = $this->am_storage->Execute($query);
		}
		
		if (!empty($result)) {
			$this->am_template->set('barnraiser_connection_log', $result);
		}
		
	}
}

if (!empty($core_config['openid_account_registration'])) {
	$body->set('account_registration_url', $core_config['openid_account_registration']);
}

$plugin_barnraiser_connection = new Plugin_barnraiser_connection();
$plugin_barnraiser_connection->am_storage = &$db;
$plugin_barnraiser_connection->am_template = &$body;
$plugin_barnraiser_connection->am_file = &$file;



?>