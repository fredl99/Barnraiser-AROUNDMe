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


// The lock page handles all access based security.
// People are sent here if the webspace is locked. People are sent here if:
// the webspace is barred (the maintainer closed the webspace)
// the webspace is pending (the maintainer has not yet approved the webspace)
// the webspace is locked and they have not yet logged in

if (is_readable(AM_DEFAULT_LANGUAGE_PATH . 'core.lang.php')) {
	include_once(AM_DEFAULT_LANGUAGE_PATH . 'core.lang.php');
}

if (defined('AM_LANGUAGE_CODE') && is_readable(AM_LANGUAGE_PATH . 'core.lang.php')) {
	include_once(AM_LANGUAGE_PATH . 'core.lang.php');
}


if (isset($_POST['submit_application'])) {

	$_POST['applicant_openid'] = $openid_consumer->normalize($_POST['applicant_openid']);

	// check that they are not already in the webspace
	$query = "
		SELECT
		connection_id, status_id
		FROM " . $db->prefix . "_connection
		WHERE
		connection_openid=" . $db->qstr($_POST['applicant_openid']) . " AND
		webspace_id=" . AM_WEBSPACE_ID
	;
	
	$result = $db->Execute($query, 1);

	if (isset($result[0]['connection_id'])) {
		if ($result[0]['status_id'] == 1) { // 1=barred,2=active
			$GLOBALS['am_error_log'][] = array('webspace_access_denied');
		}
		else {
			$GLOBALS['am_error_log'][] = array('webspace_prior_access');
		}
	}

	if (empty($GLOBALS['am_error_log'])) {
		// check that we are not already an applicant
		$query = "
			SELECT
			applicant_id 
			FROM " . $db->prefix . "_applicant 
			WHERE
			applicant_openid=" . $db->qstr($_POST['applicant_openid']) . " AND
			webspace_id=" . AM_WEBSPACE_ID
		;
		
		$result = $db->Execute($query, 1);
	
		if (isset($result[0]['applicant_id'])) {
			$GLOBALS['am_error_log'][] = array('webspace_reapplication');
		}
	}

	if (empty($GLOBALS['am_error_log'])) {
		if (empty($_POST['applicant_openid'])) {
			$GLOBALS['am_error_log'][] = array('no_openid');
		}

		if (empty($_POST['applicant_nickname'])) {
			$GLOBALS['am_error_log'][] = array('no_name');
		}

		if (empty($_POST['applicant_email'])) {
			$GLOBALS['am_error_log'][] = array('no_email');
		}

		if (empty($GLOBALS['am_error_log'])) {
			$rec = array();
			$rec['webspace_id'] = AM_WEBSPACE_ID;
			$rec['applicant_openid'] = $_POST['applicant_openid'];
			$rec['applicant_nickname'] = $_POST['applicant_nickname'];
			$rec['applicant_email'] = $_POST['applicant_email'];
			$rec['applicant_note'] = $_POST['applicant_note'];
	
			$table = $db->prefix . "_applicant";
	
			$db->insertDB($rec, $table);
	
			$body->set('display', 'applied');
		}

	}
}


?>