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

if (is_readable(AM_DEFAULT_LANGUAGE_PATH . 'core.lang.php')) {
	include_once(AM_DEFAULT_LANGUAGE_PATH . 'core.lang.php');
}

if (defined('AM_LANGUAGE_CODE') && is_readable(AM_LANGUAGE_PATH . 'core.lang.php')) {
	include_once(AM_LANGUAGE_PATH . 'core.lang.php');
}


if (isset($_SESSION['connection_id'])) {
	if (isset($_POST['update_plugin_permissions'])) {
		
		// we clear all permissions from DB
		$query = "DELETE FROM " . $db->prefix . "_permission WHERE webspace_id=" . $_SESSION['webspace_id'];
	
		$db->Execute($query);
		
		if (!empty($_POST['plugin_permissions'])) {
			foreach ($_POST['plugin_permissions'] as $key_plugin => $pl):
				if (!empty($pl)) {
					foreach ($pl as $key_resource => $rs):
						if (!empty($rs)) {
							$resource_value = 0;
						
							foreach ($rs as $key_permission => $perm):
								$resource_value = $resource_value+$perm;
							endforeach;
	
							// we check that an adjustment has happened
							if ($plugin_permissions[$key_plugin][$key_resource] != $resource_value) {
								// we insert into DB
								$rec = array();
								$rec['webspace_id'] = $_SESSION['webspace_id'];
								$rec['plugin_name'] = $key_plugin;
								$rec['resource_name'] = $key_resource;
								$rec['bitwise_operator'] = $resource_value;
	
								$table = $db->prefix . "_permission";
	
								$db->insertDB($rec, $table);
								
								//echo "plugin: ".$key_plugin." resource: " . $key_resource . "= ".$resource_value . "<br />";
								$plugin_permissions[$key_plugin][$key_resource] = $resource_value;
							}
						}
					endforeach;
				}
			endforeach;
		}
	
		$_REQUEST['v'] = "permissions";
	}
	elseif (isset($_POST['update_default_permission'])) {
		
		$default_permission = 0;
	
		if (!empty($_POST['bitwise_operators'])) {
			foreach($_POST['bitwise_operators'] as $p):
				$default_permission += $p;
			endforeach;
		}
	
		$query = "
			UPDATE
			" . $db->prefix . "_webspace
			SET default_permission=" . $default_permission . "
			WHERE webspace_id=" . $_SESSION['webspace_id'] . ""
		;
	
		$db->Execute($query);
	}
	elseif (!empty($_POST['update_connection'])) {
	
		if (!empty($_POST['status_id'])) {
			$status_id = 1; // 2= active, 1=barred
		}
		else {
			$status_id = 2; // 2= active, 1=barred
		}
	
		// update permissions
		$permission_value = 0;
	
		if (!empty($_POST['bitwise_operators'])) {
			foreach ($_POST['bitwise_operators'] as $key => $i):
				$permission_value = $permission_value+$i;
			endforeach;
		}
	
		$query = "
			UPDATE " . $db->prefix . "_connection
			SET
			status_id=" . $status_id . ",
			connection_permission=" . $permission_value . "
			WHERE
			connection_id=" . $_POST['connection_id']
		;
	
		$result = $db->Execute($query);
	
		if ($_POST['connection_id'] == $_SESSION['connection_id']) {
			$_SESSION['connection_permission'] = $permission_value;
		}
	
		$_REQUEST['connection_id'] = $_POST['connection_id'];
	}
	
	
	if (isset($_REQUEST['connection_id'])) {
		$query = "
			SELECT connection_id, connection_openid, connection_nickname, connection_email,
			connection_fullname, connection_country, connection_language, connection_avatar, 
			UNIX_TIMESTAMP(connection_create_datetime) as connection_create_datetime,
			UNIX_TIMESTAMP(connection_last_datetime) as connection_last_datetime,
			status_id, connection_permission 
			FROM " . $db->prefix . "_connection
			WHERE
			webspace_id=" . $_SESSION['webspace_id'] . " AND
			connection_id=" . $_REQUEST['connection_id']
		;
	
		$result = $db->Execute($query);
	
		if (isset($result[0])) {
			$connection = $result[0];
			$body->set('connection', $connection);
	
			$output_plugins = $ws->amscandir('plugins');
		
			$output_contributions = array();
			$output_management = array();
			
			if (!empty($output_plugins)) {
				foreach ($output_plugins as $key => $i):
	
					if (is_readable('plugins/' . $i . '/language/' . AM_DEFAULT_LANGUAGE_CODE . '/plugin_common.lang.php')) {
						include_once('plugins/' . $i . '/language/' . AM_DEFAULT_LANGUAGE_CODE . '/plugin_common.lang.php');
					}
		
					if (defined('AM_LANGUAGE_CODE') && is_readable('plugins/' . $i . '/language/' . AM_LANGUAGE_CODE . '/plugin_common.lang.php')) {
						include_once('plugins/' . $i . '/language/' . AM_LANGUAGE_CODE . '/plugin_common.lang.php');
					}
		
					// gather contributions
					if (is_readable('plugins/' . $i . '/inc/account_contributions.inc.php')) {
						require_once('plugins/' . $i . '/inc/account_contributions.inc.php');
		
						array_push($output_contributions, $i);
					}
		
					// gather management options
					if (isset($_SESSION['connection_id']) && $connection['connection_id'] == $_SESSION['connection_id'] && is_readable('plugins/' . $i . '/inc/account_manage.inc.php')) {
						require_once('plugins/' . $i . '/inc/account_manage.inc.php');
		
						array_push($output_management, $i);
					}
		
				endforeach;
		
				if (!empty($output_contributions)) {
					$body->set('contribution_includes', $output_contributions);
				}
		
				if (!empty($output_management)) {
					$body->set('account_management_includes', $output_management);
				}
			}
		}
		else {
			// no connection was available
			header("Location: index.php?t=network");
			exit;
		}
	}
	elseif(isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $core_config['group']['maintainer'] && isset($_REQUEST['v']) && $_REQUEST['v'] == "permissions") {
	
		if (isset($plugin_permissions)) {
			$body->set('plugin_permissions', $plugin_permissions);
		}
	
		$output_plugins = $ws->amscandir('plugins');
	
		if (!empty($output_plugins)) {
			foreach ($output_plugins as $key => $i):
				if (is_readable('plugins/' . $i . '/language/' . AM_DEFAULT_LANGUAGE_CODE . '/plugin_common.lang.php')) {
					include_once('plugins/' . $i . '/language/' . AM_DEFAULT_LANGUAGE_CODE . '/plugin_common.lang.php');
				}
	
				if (defined('AM_LANGUAGE_CODE') && is_readable('plugins/' . $i . '/language/' . AM_LANGUAGE_CODE . '/plugin_common.lang.php')) {
					include_once('plugins/' . $i . '/language/' . AM_LANGUAGE_CODE . '/plugin_common.lang.php');
				}
			endforeach;
		}
	
		$body->set('display', 'permissions');
	}
	elseif(isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $core_config['group']['maintainer'] && (isset($_REQUEST['v']) && $_REQUEST['v'] == "applicants" || isset($_REQUEST['applicant_id']))) {
	
		if (isset($_POST['deny_applicant'])) {
			$query = "
				DELETE FROM " . $db->prefix . "_applicant
				WHERE
				webspace_id=" . AM_WEBSPACE_ID . " AND
				applicant_id=" . $_POST['applicant_id']
			;
	
			$db->Execute($query);
		}
		elseif (isset($_POST['accept_applicant'])) {
			// We add a connection, send an email and remove application
			$query = "
				SELECT
				applicant_openid, applicant_nickname, applicant_email
				FROM " . $db->prefix . "_applicant
				WHERE 
				webspace_id=" . AM_WEBSPACE_ID . " AND
				applicant_id=" . $_POST['applicant_id']
			;
			
			$result = $db->Execute($query, 1);
		
			if (!empty($result[0])) {
				// INSERT CONNECTION
				$rec = array();
				$rec['webspace_id'] = AM_WEBSPACE_ID;
				$rec['connection_create_datetime'] = time();
				$rec['status_id'] = 2; // 1= barred, 2=active
				$rec['connection_openid'] = $result[0]['applicant_openid'];
				$rec['connection_nickname'] = $result[0]['applicant_nickname'];
				$rec['connection_permission'] = $output_webspace['default_permission'];
				$rec['connection_email'] = $result[0]['applicant_email'];
	
				$table = $db->prefix . "_connection";
	
				$db->insertDB($rec, $table);
	
				// DELETE APPLICANT
				$query = "
					DELETE FROM " . $db->prefix . "_applicant
					WHERE
					webspace_id=" . AM_WEBSPACE_ID . " AND
					applicant_id=" . $_POST['applicant_id']
				;
		
				$db->Execute($query);
	
				
				
				// SEND EMAIL
				if (!empty($_POST['response_email'])) {
					
					require_once('core/class/Mail/class.phpmailer.php');
					
					if (!empty($_SESSION['openid_email'])) {
						$mail->From = $_SESSION['openid_email'];
					}
					
					$mail->FromName = 	$_SESSION['openid_nickname'];
					$mail->Subject = 'Application to our webspace';
					
					$email_message = stripslashes(htmlspecialchars($_POST['response_email']));
					
				
					// HTML-version of the mail
					$html  = "<HTML><HEAD><TITLE></TITLE></HEAD>";
					$html .= "<BODY>";
					$html .= utf8_decode(nl2br($email_message));
					$html .= "</BODY></HTML>";
					
					$mail->Body = $html;
					// non - HTML-version of the email
					$mail->AltBody   = utf8_decode($email_message);
					
					$mail->ClearAddresses();
					$mail->AddAddress($result[0]['applicant_email'], $result[0]['applicant_nickname']);
				
					if(!$mail->Send()) {
						$GLOBALS['am_error_log'][] = array('mail_send_error', $mail->ErrorInfo);
					}
				}
			}
		}
		elseif (isset($_REQUEST['applicant_id'])) {
			$query = "
				SELECT
				applicant_id, applicant_openid, applicant_nickname, applicant_email, applicant_note
				FROM " . $db->prefix . "_applicant
				WHERE 
				webspace_id=" . AM_WEBSPACE_ID . " AND
				applicant_id=" . $_REQUEST['applicant_id']
			;
		
			$result = $db->Execute($query, 1);
		
			if (!empty($result[0])) {
				$body->set('webspace_applicant', $result[0]);
			}
		}
		
		$query = "
			SELECT
			applicant_id, applicant_openid, applicant_nickname, applicant_email, applicant_note 
			FROM " . $db->prefix . "_applicant
			WHERE 
			webspace_id=" . AM_WEBSPACE_ID
		;
	
		$result = $db->Execute($query);
	
		if (!empty($result)) {
			$body->set('webspace_applicants', $result);
		}	
		
		$body->set('display', 'applicants');
	}
	else {
		//SELECT connection_nickname FROM `am_connection` WHERE connection_permission & 2
		// display owner
		// search on nic, full name
		$query = "
			SELECT COUNT(connection_id) AS total
			FROM " . $db->prefix . "_connection
			WHERE 
			webspace_id=" . $output_webspace['webspace_id']
		;
		
		if (!empty($_POST['search_text'])) {
			$query .= " 
				AND (connection_nickname like '%" . $_POST['search_text'] . "%' OR 
				connection_fullname like '%" . $_POST['search_text'] . "%')"
			;
		}
		
		if (isset($_POST['filter']) && $_POST['filter'] > 0) {
			$query .= " AND connection_permission & " . $_POST['filter'];
		}

		$result = $db->Execute($query);
		
		if (isset($result[0]['total'])) {
			$total = $result[0]['total'];
			$body->set('total_nr_of_rows', $total);
		}
		else {
			$body->set('total_nr_of_rows', 0);
		}
		
		$from = isset($_GET['_frmconnections']) ? (int) $_GET['_frmconnections'] : 0;
		// eo paging...

		$query = "
			SELECT connection_id, connection_openid, connection_nickname, connection_email,
			connection_fullname, connection_country, connection_language,
			UNIX_TIMESTAMP(connection_create_datetime) as connection_create_datetime,
			UNIX_TIMESTAMP(connection_last_datetime) as connection_last_datetime,
			status_id, connection_permission, connection_avatar 
			FROM " . $db->prefix . "_connection
			WHERE 
			webspace_id=" . $output_webspace['webspace_id']
		;
		
		if (!empty($_POST['search_text'])) {
			$query .= " 
				AND (connection_nickname like '%" . $_POST['search_text'] . "%' OR 
				connection_fullname like '%" . $_POST['search_text'] . "%')"
			;
		}
		
		if (isset($_POST['filter']) && $_POST['filter'] > 0) {
			$query .= " AND connection_permission & " . $_POST['filter'];
		}

		
		$result = $db->Execute($query, $core_config['display']['max_list_rows'], $from);
	
		if (isset($result)) {
			$output_connections = $result;
	
			if (!empty($output_connections)) {
				$body->set('connections', $output_connections);
			}
		}
	}
}

?>