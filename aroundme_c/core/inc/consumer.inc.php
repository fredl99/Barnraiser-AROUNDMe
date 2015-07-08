<?php

// ---------------------------------------------------------------------
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
// --------------------------------------------------------------------

require_once ('core/class/OpenidConsumer.class.php');

$openid_consumer = new OpenidConsumer;

if (isset($_POST['connect'])) { // we connect
	
	$_POST['openid_login'] = $openid_consumer->normalize($_POST['openid_login']);

	unset($_SESSION['openid_login']);
	$_SESSION['openid_login'] = $_POST['openid_login'];
	
	if (!empty($_POST['return_to'])) {
		$openid_consumer->openid_return_to = $_POST['return_to'];
	}
	
	$openid_consumer->openid_realm = str_replace('REPLACE', AM_WEBSPACE_NAME, $core_config['am']['domain_replace_pattern']);

	$openid_consumer->required_fields = $core_config['openid_extension']['sreg']['required_fields'];
	$openid_consumer->optional_fields = $core_config['openid_extension']['sreg']['optional_fields'];
	$openid_consumer->optional_fields[] = 'avatar'; // hack - we need an openid avatar extension - Tom
	

	if ($openid_consumer->discover($_POST['openid_login'])) { // we did discover a server
		if($openid_consumer->associate()) { // association is ok
			
			$openid_consumer->checkid_setup(); // do the setup
		}
		else {
			// error-log here
		}
	}
}
elseif (isset($_GET['openid_mode']) && $_GET['openid_mode'] == 'id_res') { // we get data back from the server
	if ($openid_consumer->id_res()) { // was the result ok?

		// SET CONNECTION
		$openid = $_SESSION['openid_login'];
			
		if(substr($openid,-1,1) == '/'){
			$openid = substr($openid, 0, strlen($openid)-1);
		}
			
		// $_SESSION['openid_identity'] = $openid;

		$_SESSION['openid_identity'] = $openid;
		
		// get connection ------------------------------------------

		// We look to see if we have a connection_ID
		// If yes, we look to see if they are the owner
		// If yes, we update the record
		// if no, we create a record
		$query = "
			SELECT
			connection_id, connection_permission, connection_openid,
			connection_total, status_id
			FROM " . $db->prefix . "_connection
			WHERE
			connection_openid=" . $db->qstr($_SESSION['openid_identity']) . " AND
			webspace_id=" . AM_WEBSPACE_ID
		;
		
		$result = $db->Execute($query, 1);
		
		if (isset($result[0])) { // I have previously connected

			if ($result[0]['status_id'] != 2) {  // 1=barred,2=active
				header("Location: index.php?t=lock");
				exit;
			}
				
			$connection = $result[0];

			$_SESSION['connection_id'] =  $connection['connection_id'];
			$_SESSION['connection_permission'] =  $connection['connection_permission'];
			$_SESSION['connection_total'] =  $connection['connection_total']+1;
		}
		elseif (!empty($output_webspace['webspace_locked'])) {
			// We are not in a locked webspace
			header('location: index.php?t=lock');
			exit;
		}
		else {
			// we insert a virgin connection
			$rec = array();
			$rec['webspace_id'] = $output_webspace['webspace_id'];
			$rec['connection_create_datetime'] = time();
			$rec['status_id'] = 2; // 1=barred,2=active
			$rec['connection_openid'] = $_SESSION['openid_identity'];
			$rec['connection_permission'] = $output_webspace['default_permission'];
			$rec['connection_total'] = 1;
			
			$table = $db->prefix . "_connection";

			$db->insertDB($rec, $table);

			$_SESSION['connection_id'] = $db->insertID();
			$_SESSION['connection_permission'] = $output_webspace['default_permission'];
			$_SESSION['connection_total'] =  1;
		}

		if (!empty($_SESSION['connection_id'])) {
			
			// check if required fields are set ------------------------------------------
			// if not we request them from the login form --------------------------------
			if (!empty($core_config['openid_extension']['sreg']['required_fields'])) {
				foreach ($core_config['openid_extension']['sreg']['required_fields'] as $key => $i):
					$val = "";

					if (!empty($_GET['openid_sreg_' . $i])) {
						$val = $_GET['openid_sreg_' . $i];
						$val = trim($val);
					}
					
					if (empty($val)) {
						if (!empty($_GET['openid_return_to'])) {
							header("Location: index.php?t=login&no_sreg=1&return_to=" . urlencode($_GET['openid_return_to']));
						}
						else {
							header("Location: index.php?t=login&no_sreg=1");
						}
						exit;
					}
				endforeach;
			}


			// APPLY OPENID SIMPLE REGISTRATION EXTENSION INFORMATION --------------
		
			if (!empty($_GET['openid_sreg_nickname'])) {
				$_SESSION['openid_nickname'] = trim($_GET['openid_sreg_nickname']);
			}
			
			if (!empty($_GET['openid_sreg_email'])) {
				$_SESSION['openid_email'] = trim($_GET['openid_sreg_email']);
			}
	
			if (!empty($_GET['openid_sreg_fullname'])) {
				$_SESSION['openid_fullname'] = trim($_GET['openid_sreg_fullname']);
			}
	
			if (!empty($_GET['openid_sreg_country'])) {
				$_SESSION['openid_country'] = trim($_GET['openid_sreg_country']);
			}
	
			if (!empty($_GET['openid_sreg_language'])) {
				$_GET['openid_sreg_language'] = strtolower(trim($_GET['openid_sreg_language']));
				
				if (in_array($_GET['openid_sreg_language'], $core_config['language']['pack'])) {
					$_SESSION['openid_language_code'] = $_GET['openid_sreg_language'];
				}
			}
			
			if (!empty($_GET['openid_sreg_avatar'])) {
				if (substr($_GET['openid_sreg_avatar'], 0,4) != "http") {
					$_GET['openid_sreg_avatar'] = $_SESSION['openid_identity'] . "/" . trim($_GET['openid_sreg_avatar']);
				}
				
				$_SESSION['openid_avatar'] = $_GET['openid_sreg_avatar'];
			}

			// UPDATE CONNECTION
			$query = "UPDATE " . $db->prefix . "_connection SET ";

			if (!empty($_SESSION['openid_nickname'])) {
				$query .= "connection_nickname=" . $db->qstr($_SESSION['openid_nickname']);
			}
			else {
				$query .= "connection_nickname=NULL";
			}
			
			if (!empty($_GET['openid_sreg_email'])) {
				$query .= ", connection_email=" . $db->qstr($_GET['openid_sreg_email']);
			}
			else {
				$query .= ", connection_email=NULL";
			}

			if (!empty($_GET['openid_sreg_fullname'])) {
				$query .= ", connection_fullname=" . $db->qstr($_GET['openid_sreg_fullname']);
			}
			else {
				$query .= ", connection_fullname=NULL";
			}

			if (!empty($_GET['openid_sreg_country'])) {
				$query .= ", connection_country=" . $db->qstr($_GET['openid_sreg_country']);
			}
			else {
				$query .= ", connection_country=NULL";
			}

			if (!empty($_GET['openid_sreg_language'])) {
				$query .= ", connection_language=" . $db->qstr($_GET['openid_sreg_language']);
			}
			else {
				$query .= ", connection_language=NULL";
			}
			
			if (!empty($_GET['openid_sreg_avatar'])) {
				$query .= ", connection_avatar=" . $db->qstr($_GET['openid_sreg_avatar']);
			}
			else {
				$query .= ", connection_avatar=NULL";
			}
			
			$query .= ", connection_total=" . $_SESSION['connection_total'];
			
			$query .= " WHERE connection_id=" . $_SESSION['connection_id'];
			
			$db->Execute($query);
			
			
			// append log --------------------------------------------
			$log_entry = array();
			$log_entry['title'] = $lang['arr_log']['title']['someone_connected'];
			$log_entry['body'] = '<a href="index.php?t=network&amp;connection_id=' . $_SESSION['connection_id'] . '">' . $_SESSION['openid_nickname'] . '</a> ' . $lang['arr_log']['body']['someone_connected'];
			$log_entry['link'] = $_SESSION['openid_identity'];
			$ws->appendLog($log_entry);
		

			if (!empty($_GET['openid_return_to'])) {
				header("Location: " . $_GET['openid_return_to']);
				exit;
			}
		}
	}
	else {
		// error-log here
	}

	// clean up
	unset($_SESSION['openid_login']);
}

// we can have an instance of someone not filling in the additional information and then going to the main domain as logged in
if (!empty($_SESSION['connection_id']) && !isset($_REQUEST['t']) || (isset($_REQUEST['t']) && $_REQUEST['t'] != "login")) {
	if (!empty($core_config['openid_extension']['sreg']['required_fields'])) {
		foreach ($core_config['openid_extension']['sreg']['required_fields'] as $key => $i):
			if (empty($_SESSION['openid_' . $i])) {
				header("Location: index.php?t=login&no_sreg=1");
				exit;
			}
		endforeach;
	}
}

?>