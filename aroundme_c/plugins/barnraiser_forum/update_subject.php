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


include_once ("../../core/config/core.config.php");


// SETUP DATABASE ------------------------------------------------------
require_once('../../core/class/Db.class.php');
$db = new Database($core_config['db']);


if (isset($_POST['update_subject_locked'])) {

	$query = "UPDATE " . $db->prefix . "_plugin_forum_subject SET subject_locked";

	if (!empty($_POST['subject_locked'])) {
		$query .= "=NULL ";
	}
	else {
		$query .= "=1 ";
	}

	$query .= "WHERE subject_id=" . $_POST['subject_id'];

	$db->Execute($query);
}
elseif (isset($_POST['update_subject_sticky'])) {

	$query = "UPDATE " . $db->prefix . "_plugin_forum_subject SET subject_sticky";

	if (!empty($_POST['subject_sticky'])) {
		$query .= "=NULL ";
	}
	else {
		$query .= "=1 ";
	}

	$query .= "WHERE subject_id=" . $_POST['subject_id'];

	$db->Execute($query);
}
elseif (!empty($_POST['reject'])) {
	$query = "
		UPDATE " . $db->prefix . "_plugin_forum_reply
		SET reply_archived=1
		WHERE reply_id=" . key($_POST['reject'])
	;
	
	$db->Execute($query);
}
elseif (!empty($_POST['unreject'])) {
	$query = "
		UPDATE " . $db->prefix . "_plugin_forum_reply
		SET reply_archived=null
		WHERE reply_id=" . key($_POST['unreject'])
	;
	
	$db->Execute($query);
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;

?>