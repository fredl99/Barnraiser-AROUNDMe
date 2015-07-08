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


if (isset($_POST['submit_file_upload'])) {

	if (!empty($_POST['file_width'])) {
		$file->width = $_POST['file_width'];
	}
	
	if (!empty($_POST['frm_title'])) {
		$file->title = $_POST['frm_title'];
	}
	
	$file->uploadFile();
}
elseif (isset($_POST['delete_file']) && !empty($_POST['file_to_delete'])) {
	$file->deleteFile($_POST['file_to_delete']);
}

$output_files = $file->selFiles();

if (!empty($output_files)) {
	$body->set('files', $output_files);
}

if (isset($_REQUEST['file_name'])) {
	$output_file = $file->selFiles($_REQUEST['file_name']);
	
	$pattern = "%core/get_file.php?file=" . $_REQUEST['file_name'] . "%";
	
	$query = "
		SELECT webpage_name AS name, concat('index.php?wp=', webpage_name) AS link, 'webpage' AS type
		FROM " . $db->prefix . "_webpage
		WHERE webpage_body LIKE " . $db->qstr($pattern) . "
		UNION 
		SELECT stylesheet_name AS name, concat('stylesheet_editor.php?stylesheet_id=', stylesheet_id) AS link, 'stylesheet' AS type
		FROM " . $db->prefix . "_stylesheet
		WHERE stylesheet_body LIKE " . $db->qstr($pattern) . "
		UNION
		SELECT block_name AS name, concat('index.php?t=block_editor&block_id=', block_id) AS link, 'block' AS type
		FROM " . $db->prefix . "_block
		WHERE block_body LIKE " . $db->qstr($pattern) . "
		UNION
		SELECT subject_title AS name, concat('index.php?wp=', subject_title, '&subject_id=', subject_id) AS link, 'forum subject' as type
		FROM " . $db->prefix . "_plugin_forum_subject
		WHERE subject_body LIKE " . $db->qstr($pattern) . "
		UNION
		SELECT 'subject reply' AS name, concat('index.php?wp=') AS link, 'forum reply' AS type
		FROM " . $db->prefix . "_plugin_forum_reply
		WHERE reply_body LIKE " . $db->qstr($pattern) . "";

	$result = $db->Execute($query);
	
	if (!empty($output_file)) {
		$body->set('file', $output_file[0]);
		
		if (!empty($result)) {
			$body->set('in_use', $result);
		}
	}
}

?>