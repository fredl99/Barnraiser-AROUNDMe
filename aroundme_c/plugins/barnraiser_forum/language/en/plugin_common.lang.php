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


// PLUGIN
$lang['plugin_barnraiser_forum_plugin_title'] = 					"Forum";
$lang['plugin_barnraiser_forum_plugin_description']= 				"A plugin that lets your connections leave comments against your webspace.";


// BLOCK TITLES
$lang['plugin_barnraiser_forum_block_subject'] = 					"Discussion item";
$lang['plugin_barnraiser_forum_block_subject_list'] = 				"Discussions listing";
$lang['plugin_barnraiser_forum_block_tagcloud'] = 					"Tagcloud";
$lang['plugin_barnraiser_forum_block_search'] = 					"Search";


// RESOURCES
$lang['plugin_barnraiser_forum']['resource']['add_subject'] = 		"Add a discussion";
$lang['plugin_barnraiser_forum']['resource']['add_reply'] = 		"Add a reply";
$lang['plugin_barnraiser_forum']['resource']['reply_recommend'] = 	"Recommend a reply";
$lang['plugin_barnraiser_forum']['resource']['reply_filter'] = 		"Reject a reply";
$lang['plugin_barnraiser_forum']['resource']['manage_tags'] = 		"Manage tags";
$lang['plugin_barnraiser_forum']['resource']['manage_forum'] = 		"Manage forum";


// ARRAY PERMISSIONS
$lang['arr_permissions_desc']['add_forum_subject'] = 				"Allow the adding of discussions to the forum";
$lang['arr_permissions_desc']['forum_maintain'] = 					"Allow access to maintain the forum";


// ACTIVITY LOG ITEMS
// All lowercase unless specific to brand [example: OpenID]
$lang['arr_log']['title']['forum_reply_added'] =					"discussion reply added";
$lang['arr_log']['body']['forum_reply_added'] =						"added a <a href=\"SYS_KEYWORD_REPLY_URL\">discussion reply</a>.";
$lang['arr_log']['title']['forum_subject_added'] =					"forum discussion added";
$lang['arr_log']['body']['forum_subject_added'] =					"added a <a href=\"SYS_KEYWORD_DISCUSSION_URL\">discussion</a>.";
$lang['arr_log']['title']['forum_recommend_reply'] =				"discussion reply recommended";
$lang['arr_log']['body']['forum_recommend_reply'] =					"recommended a discussion <a href=\"SYS_KEYWORD_RECOMMEND_URL\">reply</a>.";



// ACCOUNT MANAGE ADDITION TO NETWORK PAGE
$lang['plugin_barnraiser_forum_forum_options'] =					"Discussion forum options";
$lang['plugin_barnraiser_forum_notification_set'] =					"notification set";
$lang['plugin_barnraiser_forum_notification_sent_to'] =				"Notifications are sent to ";
$lang['plugin_barnraiser_forum_remove_tracking'] = 					"remove";
$lang['plugin_barnraiser_forum_remove_notifications'] = 			"remove email notifications";
$lang['plugin_barnraiser_forum_recieve_digest'] =					"Receive digest";
$lang['plugin_barnraiser_forum_never'] = 							"never";
$lang['plugin_barnraiser_forum_daily'] = 							"daily";
$lang['plugin_barnraiser_forum_weekly'] = 							"weekly";
$lang['plugin_barnraiser_forum_monthly'] = 							"monthly";
$lang['plugin_barnraiser_forum_set_frequency'] =					"set frequency";
$lang['plugin_barnraiser_forum_cannot_recieve_digest'] =			"You cannot receive tracked items because you have not provided an email address. Disconnect, return to your OpenID account, remove trust and re-connect providing a valid email address to receive email notifications.";


// ACCOUNT CONTRIBUTIONS ADDITION TO NETWORK PAGE
$lang['plugin_barnraiser_forum_subject_contributions'] =			"Latest discussion contributions";

?>