<?php

// ---------------------------------------------------------------------------------------------
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
// ---------------------------------------------------------------------------------------------

class OpenidStorageMysql {
	
	function OpenidStorageMysql($db_core_config = null) {
		if (isset($db_core_config)) {
			$this->db_config = $db_core_config;
			$this->prefix = $db_core_config['prefix'];
		}
		
		$this->newConnection();
	}
	
	function newConnection() {
		//we connect to the database
		$this->connection = @mysql_connect($this->db_config['host'], $this->db_config['user'] , $this->db_config['pass']);

		if (!is_resource($this->connection)) {
			$GLOBALS['am_error_log'][] = array('db_error', mysql_error());
		}
		else {
			//we select the database
			$db_selected = mysql_select_db($this->db_config['db'], $this->connection);
			if (!$db_selected) {
				$GLOBALS['am_error_log'][] = array('db_select_error', mysql_error());
			}
			else {
				$db->prefix = $this->db_config['prefix'];

				// set up database collation
				$query = "SET NAMES 'utf8'";
				$this->Execute($query);
				$query = "SET CHARACTER SET 'utf8'";
				$this->Execute($query);
			}
		}
	}
	
	function Execute($query, $rows=null, $offset=null) {
		
		$query = trim($query);

		if (!isset($this->connection)) {
			$this->newConnection();
		}
		
		if (isset($rows) && is_int($rows) && $rows > 0) { // is_numeric
			
			if (isset($offset) && is_int($offset) && $offset > 0) { // is_numeric
				$query .= " LIMIT " . $offset . ", " . $rows;
			}
			else {
				$query .= " LIMIT " . $rows;
			}
		}

		$this->resource = mysql_query($query, $this->connection);
		
		if (!$this->resource) {
			if ($this->db_config['am']['debug_level'] == 1) {
				$error = mysql_error().  "\n\n" . $query;
			}
			else {
				$error = mysql_error();
			}
			$GLOBALS['am_error_log'][] = array('db_error', $error);
		}
		else {

			if (is_resource($this->resource)) { // SELECT, SHOW, DESCRIBE or EXPLAIN
				
				if (mysql_num_rows($this->resource) > 0) {
					$result = array();
					while($row = mysql_fetch_array($this->resource)) {
						$result[] = $row;
					}
					//mysql_free_result($resource);
					return $result;
				}
				else {
					return array(); // empty result
				}
			}
			return 1; // It's ok if we reach here!
		}
		return 0; // Not OK
	}
	
	// if magic quotes disabled, use stripslashes()
	function qstr($s) {
		
		if (!get_magic_quotes_gpc()) {
 			$s = addslashes($s);
		}
		return "'" . $s . "'";
	}

	function insertID() {
		if (isset($this->connection)) {
			if (is_resource($this->connection)) {
				return mysql_insert_id ($this->connection);
			}
		}
		return 0;
	}

	function insertDb($data, $table) {
	
		$query = "
			DESCRIBE " . $table
		;
		
		$result = $this->Execute($query);
		
		$query = "INSERT INTO " . $table . "(";
		
		foreach($data as $key => $d):
			$query .= $key . ", ";
		endforeach;
		
		$query = substr($query, 0, strlen($query) - 2);
		$query .= ") VALUES (";
		
		foreach($data as $key => $d):
			
			$data_type = "";
			for ($i = 0; $i < count($result); $i++) {
				if ($key == $result[$i]['Field']) {
					$data_type = $result[$i]['Type'];
				}
			}
			
			if ($data_type == 'datetime') {
				$query .= $this->qstr(date('Y-m-d H:i:s', $d)) . ", ";
			}
			elseif (is_string($d)) {
				$query .= $this->qstr($d, get_magic_quotes_gpc()) . ", ";
			}
			else {
				$query .= $d . ", ";
			}
		endforeach;
		
		$query = substr($query, 0, strlen($query) - 2);
		$query .= ")";

		return $this->Execute($query);
	}
	
	function selDataToSend($openid_trusted_root) {

		$query = "
			SELECT site_data_sent
			FROM " . $this->prefix . "_site
			WHERE identity_id=" . $_SESSION['identity_id'] . " AND
			site_realm=" . $this->qstr($openid_trusted_root)
		;
		
		$result = $this->Execute($query);
		
		if (isset($result[0]['site_data_sent'])) {
			$data_to_send = array();
			$data_to_send = unserialize($result[0]['site_data_sent']);
		}
		else {
			$data_to_send = array();
		}
		
		return $data_to_send;
	}
	
	function selIsTrusted($openid_trusted_root) {
	
		$query = "
			SELECT site_trusted
			FROM " . $this->prefix . "_site
			WHERE identity_id=" . $_SESSION['identity_id'] . " AND 
			site_realm=" . $this->qstr($openid_trusted_root)
		;
		
		$result = $this->Execute($query);
		
		if (isset($result[0]['site_trusted'])) {
			return (int) $result[0]['site_trusted'];
		}
		else {
			return 0;
		}
	}
	
	function selLogin($username, $password) {
		
		$query = "
			SELECT identity_id
			FROM " . $this->prefix . "_identity
			WHERE identity_name=" . $this->qstr($username) . " AND
			identity_password=" . $this->qstr(md5($password))
		;

		$result = $this->Execute($query);
		
		if (isset($result[0]['identity_id']) && !empty($result[0]['identity_id'])) {
			return 1;
		}
		else {
			return 0;
		}
	}
	
	function saveTrust($data) {
		$rec = array();
		$rec['site_trusted'] = $data['always'];
		$rec['site_title'] = $data['openid_trusted_root_title'];
		$rec['site_realm'] = $data['openid_trusted_root'];
		$rec['site_datetime_first_visit'] = time();
		$rec['site_datetime_last_visit'] = time();
		$rec['site_connections'] = 1;
		$rec['identity_id'] = $_SESSION['identity_id'];
		
		if (isset($data['trusted_data_arr']) && !empty($data['trusted_data_arr'])) {
			$rec['site_data_sent'] = serialize($data['trusted_data_arr']);
		}
		
		$query = "
			SELECT site_id, site_connections
			FROM " . $this->prefix . "_site
			WHERE identity_id=" . $_SESSION['identity_id'] . " AND 
			site_realm=" . $this->qstr($rec['site_realm'])
		;
		
		$result = $this->Execute($query);
		
		if (empty($result)) {
			$table = $this->prefix . '_site';
			$this->insertDb($rec, $table);
		}
		else {
			$connections = $result[0]['site_connections'] + 1;
			
			$query = "
				UPDATE " . $this->prefix . "_site
				SET site_trusted=" . $rec['site_trusted'] . ",
				site_title=" . $this->qstr($rec['site_title']) . ",
				site_datetime_last_visit=" . $rec['site_datetime_last_visit'] . ",
				site_connections=" . $connections . ", 
				site_data_sent=" . $this->qstr($rec['site_data_sent']) . "
				WHERE identity_id=" . $_SESSION['identity_id'] . " AND
				site_realm=" . $this->qstr($rec['site_realm'])
			;

			$this->Execute($query);
		}
		
	}
	
	function saveLogEntry($entry_string) {
		
	}
	
	function writeSession($data) {
	
		$rec = array();
		
		if (isset($data['assoc_handle'])) {
			$rec['assoc_handle'] = $data['assoc_handle'];
		}
		
		if (isset($data['assoc_type'])) {
			$rec['assoc_type'] = $data['assoc_type'];
		}
		
		if (isset($data['enc_mac_key'])) {
			$rec['enc_mac_key'] = $data['enc_mac_key'];
		}
		
		if (isset($data['expires_in'])) {
			$rec['expires_in'] = $data['expires_in'];
		}
		
		if (isset($data['session_type'])) {
			$rec['session_type'] = $data['session_type'];
		}
		
		if (isset($data['mac_key'])) {
			$rec['mac_key'] = $data['mac_key'];
		}
		
		$table = $this->prefix . '_openid_session';
		$this->insertDb($rec, $table);
	}
	
	function selSession($str) {
		$query = "
			SELECT *
			FROM " . $this->prefix . "_openid_session
			WHERE assoc_handle='" . $str . "'"
		;
		
		$result = $this->Execute($query);
		
		if (isset($result[0]['assoc_handle'])) {
			return $result[0];
		}
		else {
			return array();
		}
	}
}

?>