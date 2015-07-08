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

$plugin_barnraiser_forum->attributes['limit'] = 6;
$plugin_barnraiser_forum->block_subject_list($_REQUEST['connection_id']);


// GET THE DEFAULT WEBPAGE
$query = "
	SELECT wp.webpage_name
	FROM " . $db->prefix . "_plugin_forum_preference p, " . $db->prefix . "_webpage wp
	WHERE
	p.default_webpage_id=wp.webpage_id AND
	p.webspace_id=" . AM_WEBSPACE_ID
;

$result = $db->Execute($query);

if (!empty($result[0]['webpage_name'])) {
	$body->set('plugin_barnraiser_forum_default_webpage', $result[0]['webpage_name']);
}

?>