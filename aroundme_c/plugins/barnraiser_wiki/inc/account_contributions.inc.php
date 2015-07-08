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

$query = "
	SELECT wip.wikipage_name, r.revision_id,
	UNIX_TIMESTAMP(r.revision_create_datetime) as revision_create_datetime 
	FROM " . $db->prefix . "_plugin_wiki_page wip, " . $db->prefix . "_plugin_wiki_revision r
	WHERE
	wip.current_revision_id=r.revision_id AND
	r.connection_id= " . $_REQUEST['connection_id'] . " AND
	wip.webspace_id=" . AM_WEBSPACE_ID . "
	ORDER BY r.revision_create_datetime desc"
;

$barnraiser_wiki_revision_contributions = $db->Execute($query, 6);

if (!empty($barnraiser_wiki_revision_contributions)) {
	$body->set('barnraiser_wiki_revision_contributions', $barnraiser_wiki_revision_contributions);
}


// GET THE DEFAULT WEBPAGE
$query = "
	SELECT wp.webpage_name
	FROM " . $db->prefix . "_plugin_wiki_preference p, " . $db->prefix . "_webpage wp
	WHERE
	p.default_webpage_id=wp.webpage_id AND
	p.webspace_id=" . AM_WEBSPACE_ID
;

$result = $db->Execute($query);

if (!empty($result[0]['webpage_name'])) {
	$body->set('plugin_barnraiser_wiki_default_webpage', $result[0]['webpage_name']);
}

?>