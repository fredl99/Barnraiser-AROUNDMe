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


if (!empty($_REQUEST['search'])) {

	$query = "
		SELECT COUNT(*) AS total
		FROM " . $db->prefix . "_webspace
		WHERE 
		MATCH(webspace_title) 
		AGAINST (" . $db->qstr($_REQUEST['search']) . ") AND
		status_id=3 AND webspace_locked is NULL"
	;
	$result = $db->Execute($query, 1);
	
	if ($result) {
		$total = $result[0]['total'];
		$body->set('total_nr_of_rows', $total);
	}
	
	$query = "
		SELECT MATCH(webspace_title) 
		AGAINST (" . $db->qstr($_REQUEST['search']) . ") AS score
		FROM " . $db->prefix . "_webspace
		WHERE 
		MATCH(webspace_title) 
		AGAINST (" . $db->qstr($_REQUEST['search']) . ") AND
		status_id=3 AND webspace_locked is NULL  
		ORDER BY score DESC"
	;
	$result = $db->Execute($query);

	if ($result) {
		$max = $result[0]['score'];
	}

	$query = "
		SELECT webspace_id, webspace_unix_name, 
 		webspace_title, webspace_create_datetime,
		MATCH(webspace_title) 
		AGAINST (" . $db->qstr($_REQUEST['search']) . ") AS score
		FROM " . $db->prefix . "_webspace
		WHERE
		MATCH(webspace_title)
		AGAINST (" . $db->qstr($_REQUEST['search']) . ") AND
		status_id=3 AND webspace_locked is NULL 
		ORDER BY score DESC"
	;

	$from = isset($_GET['_frm']) ? (int) $_GET['_frm'] : 0;
	$output_webspaces = $db->Execute($query, $core_config['display']['max_list_rows'], $from);

	if (!empty($output_webspaces)) {
		foreach($output_webspaces as $key => $g):
			$output_webspaces[$key]['percentage'] = round(($g['score'] / $max) * 100);
		
			$webspace_url = 'index.php?ws=' . $g['webspace_id'];
			
			$output_webspaces[$key]['webspace_url'] = $webspace_url;
		endforeach;
		$body->set('search_webspaces', $output_webspaces);
	}
}
else {

	$query = "
		SELECT COUNT(*) AS total
		FROM " . $db->prefix . "_webspace
		WHERE status_id=3 AND webspace_locked is NULL"
	;
	$result = $db->Execute($query);
	
	if (isset($result[0]['total'])) {
		$total = $result[0]['total'];
		$body->set('total_nr_of_rows', $total);
	}

	$query = "
		SELECT w.webspace_id, w.webspace_unix_name, 
		w.webspace_title, w.webspace_create_datetime
		FROM " . $db->prefix . "_webspace w
		WHERE w.status_id=3 AND webspace_locked is NULL 
		ORDER BY w.webspace_create_datetime DESC"
	;

	$from = isset($_GET['_frm']) ? (int) $_GET['_frm'] : 0;
	$output_webspaces = $db->Execute($query, $core_config['display']['max_list_rows'], $from);
	
	if (!empty($output_webspaces)) {
		foreach($output_webspaces as $key => $g):
		
			$webspace_url = str_replace('REPLACE', $g['webspace_unix_name'], $core_config['am']['domain_replace_pattern']);
			
			$output_webspaces[$key]['webspace_url'] = $webspace_url;
		endforeach;
		
		$body->set('webspaces', $output_webspaces);
	}
}

?>