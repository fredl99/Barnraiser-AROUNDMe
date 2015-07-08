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


class Webspace {

	// the constructor
	// Tom Calthrop, 26th March 2007
	//
	function Webspace($db) {
		$this->db = $db;
	} //EO Webpage



	// selWebSpace
	// Tom Calthrop, 26th March 2007
	// // selects the webspace and current page
	function selWebSpace () {

		$query = "
			SELECT ws.webspace_id, ws.language_code, ws.default_webpage_id, 
			ss.stylesheet_body, ss.stylesheet_id, 
			ws.webspace_create_datetime, ws.webspace_title, ws.webspace_locked,
			ws.owner_connection_id, ws.default_permission, ws.status_id
			FROM " . $this->db->prefix . "_webspace ws 
			LEFT JOIN " . $this->db->prefix . "_stylesheet ss ON ws.stylesheet_id=ss.stylesheet_id 
			WHERE 
			ws.webspace_unix_name=" . $this->db->qstr($this->webspace_unix_name)
		;
		
		$result = $this->db->Execute($query);
		
		if (isset($result[0])) {
			$this->webspace_id = $result[0]['webspace_id'];
			$_SESSION['webspace_id'] = $this->webspace_id;
			
			return $result[0];
		}
	} // EO selWebSpace

	
	// selWebPage
	// Tom Calthrop, 26th March 2007
	// // selects the webpage
	function selWebPage () {

		$query = "
			SELECT wp.webpage_id, wp.webpage_body, wp.webpage_name
			FROM " . $this->db->prefix . "_webspace ws, " . $this->db->prefix . "_webpage wp 
			WHERE
			ws.webspace_id=wp.webspace_id AND "
		;

		if (isset($this->webpage_name)) {
			$query .= "wp.webpage_name=" . $this->db->qstr($this->webpage_name) . " AND ws.webspace_id=" . $this->webspace_id . " AND ";
		}
		else {
			 $query .= "ws.webspace_id=" . $this->webspace_id . " AND ws.default_webpage_id=wp.webpage_id AND ";
		}

		$query .= "1=1";
		
		$result = $this->db->Execute($query);
		
		if (isset($result[0])) {
			return $result[0];
		}
	} // EO selWebPage


	// selWebPages
	// Tom Calthrop, 26th March 2007
	// // selects the webpages for a webspace
	function selWebPages ($webpage_name = null) {

		$query = "
			SELECT DISTINCT webpage_name
			FROM " . $this->db->prefix . "_webpage
			WHERE
			webspace_id=" . $this->webspace_id
		;

		if (isset($webpage_name)) {
			$query .= " AND webpage_name=" . $this->db->qstr($webpage_name);
		}

		$result = $this->db->Execute($query);
		
		if (!empty($result)) {
			$pages = array();

			foreach ($result as $key => $i):
				array_push($pages, $i['webpage_name']);
			endforeach;
				
			return $pages;
		}
	} // EO selWebPages
	
	// scan a director for directory names
	function amscandir($dir) {

		$dirnames = array();
		
		$entries = @scandir($dir);
		
		if (!empty($entries)) {
			foreach($entries as $i):
				if ($i != '.' && $i != '..' && $i != 'CVS' && $i != '.DS_Store') {
					array_push($dirnames, $i);
				}
			endforeach;
		}
	
		return $dirnames;
	}
	


	// selBlock
	// Tom Calthrop, 26th March 2007
	// // selects the webspace blocks
	function selBlock ($block_plugin, $block_name) {

		$query = "
			SELECT block_body 
			FROM " . $this->db->prefix . "_block
			WHERE
			webspace_id=" . $this->webspace_id . " AND 
			block_plugin=" . $this->db->qstr($block_plugin) . " AND
			block_name=" . $this->db->qstr($block_name)
		;
		
		$result = $this->db->Execute($query);
		
		if (isset($result[0])) {
			return $result[0];
		}
	}

	function insertBlock ($block_plugin, $block_name, $block_body) {
		
		if (get_magic_quotes_gpc()) {
			$block_body = addslashes($block_body);
		}
		
		$rec = array();
		$rec['block_plugin'] = $block_plugin;
		$rec['block_name'] = $block_name;
		$rec['block_body'] = $block_body;
		$rec['webspace_id'] = $this->webspace_id;
		
		$table = $this->db->prefix . "_block";
		
		$this->db->insertDb($rec, $table);
	}

	function appendLog ($log_entry) {
		
		$log_body = trim($log_entry['body']);
	
		if (!empty($log_body)) {
			
			$rec = array();
			$rec['webspace_id'] = $this->webspace_id;
			$rec['log_title'] = $log_entry['title'];
			$rec['log_body'] = $log_body;
			$rec['log_link'] = $log_entry['link'];
			$rec['log_create_datetime'] = time();
			
			$table = $this->db->prefix . "_log";
	
			$this->db->insertDb($rec, $table);
		}
	}


	function getWebspaceName($pattern) {
	
		preg_match ($pattern, $_SERVER['HTTP_HOST'], $matches);
		
		if (!empty($matches[1])) {
			return $matches[1];
		}
	}
}


?>