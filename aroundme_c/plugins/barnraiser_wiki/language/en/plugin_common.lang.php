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


// AM ERRORS (business logic layer)
$lang['arr_am_error']['plugin_barnraiser_wiki_plugin_wikipage_attribute'] =				"The wiki plugin named 'page' required an attribute called 'wikipage'";
$lang['arr_am_error']['plugin_barnraiser_wiki_plugin_wikilink_bad_chars'] =				"You can only use a-z, A-Z and 0-9 in a wiki link";
$lang['arr_am_error']['plugin_barnraiser_wiki_plugin_wikilink_too_long'] =				"A wiki link can only be less that 30 characters long";


// PLUGIN
$lang['plugin_barnraiser_wiki_plugin_title'] = 					"Wiki";
$lang['plugin_barnraiser_wiki_plugin_description'] = 			"A plugin that lets your connections collectively build a repository of information.";


// BLOCK TITLES
$lang['plugin_barnraiser_wiki_block_history'] = 				"Wiki revisions history";
$lang['plugin_barnraiser_wiki_block_page'] = 					"Wiki page";


// RESOURCES
$lang['plugin_barnraiser_wiki']['resource']['edit_page'] = 		"Edit wikipage";
$lang['plugin_barnraiser_wiki']['resource']['add_note'] = 		"Add note";
$lang['plugin_barnraiser_wiki']['resource']['manage_wiki'] = 	"Manage";


// ARRAY PERMISSIONS
$lang['arr_permissions_desc']['edit_wikipage'] = 				"Allow the editing of wiki pages";


// ACTIVITY LOG ITEMS
$lang['arr_log']['title']['wiki_page_note_added'] =					"wiki page note added";
$lang['arr_log']['body']['wiki_page_note_added'] =					"added a <a href=\"SYS_KEYWORD_WIKI_NOTE_URL\">wiki page note</a>.";
$lang['arr_log']['title']['wiki_page_revised'] =					"wiki page revised";
$lang['arr_log']['body']['wiki_page_revised'] =					"revised a <a href=\"SYS_KEYWORD_WIKI_REVISION_URL\">wiki page</a>.";


// ACCOUNT CONTRIBUTIONS ADDITION TO NETWORK PAGE
$lang['plugin_barnraiser_wiki_latest_contributions'] =			"Latest wiki contributions";

?>