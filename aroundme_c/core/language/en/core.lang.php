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


// CORE LANG; LANG VARS NOT USED WITHIN COMMON


// ARRAY PERMISSIONS
// All lowercase unless specific to brand [example: OpenID]
$lang['arr_permissions_desc']['maintain_connections'] = 	"allow the maintainance of connected people connected to this webspace";
$lang['arr_permissions_desc']['invite_connections'] = 		"allow the sending of invitations to other people into this webspace";
$lang['arr_permissions_desc']['edit_stylesheet'] = 			"allow access to edit webspace style";
$lang['arr_permissions_desc']['edit_webpages'] = 			"allow access to edit webpages";
$lang['arr_permissions_desc']['add_tags'] = 				"allow the tagging of information within this webspace";
$lang['arr_permissions_desc']['view_files'] = 				"allow the viewing of files within this webspace";
$lang['arr_permissions_desc']['upload_files'] = 			"allow the uploading of files to this webspace";


// ARRAY GROUPS
// All lowercase unless specific to brand [example: OpenID]
$lang['arr_group_name']['contributor'] = 					"contributor";
$lang['arr_group_name']['publisher'] = 						"publisher";
$lang['arr_group_name']['editor'] = 						"editor";
$lang['arr_group_name']['designer'] = 						"designer";
$lang['arr_group_name']['maintainer'] = 					"maintainer";


// AM ERRORS (business logic layer)
$lang['arr_am_error']['file_not_set'] =						"There is no file defined to upload. Please choose a file";
$lang['arr_am_error']['not_valid_mime'] =					"The filetype you tried to upload is forbidden.";
$lang['arr_am_error']['file_not_uploaded'] =				"The file could not be uploaded because an undefined error occured.";
//$lang['arr_am_error']['not_enough_space'] =				"You don't have enough space to upload this file";
//$lang['arr_am_error']['width_not_numeric'] =				"You defined a new width to the image, but it is not numeric. Please give a numeric width";
$lang['arr_am_error']['webspace_access_denied'] =			"Sorry, you are not allowed access to this webspace";
$lang['arr_am_error']['webspace_prior_access'] =			"You are already in this webspace. Please connect with your OpenID account to enter";
$lang['arr_am_error']['webspace_reapplication'] =			"You have already applied. Your application will be processed and an email sent to you";
$lang['arr_am_error']['no_openid'] =						"Please fill in your OpenID";
$lang['arr_am_error']['no_name'] =							"Please fill in your name";
$lang['arr_am_error']['no_email'] =							"Please fill in your email";
$lang['arr_am_error']['login_account_information'] =		"Please fill in your account information";
$lang['arr_am_error']['forbidden_php_tokens'] = 			"The webpage body contains forbidden PHP tokens. Please remove them";



// BLOCK EDITOR
// All capitalized unless specific to brand [example: OpenID] or label
$lang['core_edit_plugin_block'] = 							"Edit your plugin block";
$lang['core_block'] = 										"block";
$lang['core_name'] = 										"name";
$lang['core_webpage_helper'] = 								"Add links to webpages";
$lang['core_file_helper'] = 								"Add a picture";
$lang['core_custom'] = 										"Custom";


// FILE
$lang['core_upload_file'] = 								"Upload a file";
$lang['core_selected_file'] = 								"Your selected file";
$lang['core_file'] = 										"file";
$lang['core_files'] = 										"files";
$lang['core_width_intro'] = 								"If you are uploading a picture you can set the width to be the width in pixels that you want the image to be.";
$lang['core_width'] = 										"width";
$lang['core_uploaded'] = 									"uploaded";
$lang['core_size'] = 										"size";
$lang['core_type'] = 										"type";
$lang['core_upload_datetime'] = 							"date uploaded";
$lang['core_pixels'] = 										"pixels";
$lang['core_file_in_use'] = 								"You cannot delete this file because it is in use.";
$lang['core_file_tag'] =									"tag";
$lang['core_note_max_file_size'] =							"The maximum size of a file that you can upload is SYS_KEYWORD_MAX_FILE_SIZE";

// FILE ERRORS
$lang['error']['not_enough_space'] =						"You don't have enough space to upload this file";
$lang['error']['width_not_numeric'] =						"You defined a new width to the image, but it is not numeric. Please give a numeric width.";
$lang['error']['file_not_set'] =							"There is no file defined to upload. Please choose a file";
$lang['error']['not_valid_mime'] =							"The filetype you tried to upload is forbidden.";
$lang['error']['file_not_uploaded'] =						"The file could not be uploaded because an undefined error occured.";
$lang['error']['file_not_deleted'] =						"The file could not be deleted because it does not exists at the correct place.";
$lang['error']['file_not_deleted_database'] =				"The file could not be deleted because it does not exists in the database";
$lang['error']['files_not_deleted'] =						"The files could not be deleted";


// LOCK
$lang['core_webspace_pending'] = 							"Webspace pending";
$lang['core_webspace_locked'] = 							"This webspace is by invitation only";
$lang['core_webspace_apply'] = 								"Apply to join";
$lang['core_apply'] = 										"apply";
$lang['core_list_webspaces'] = 								"List other local webspaces";
$lang['core_webspace_locked_intro'] = 						"You can connect to this webspace if you have been invited in only. If you have been then enter your OpenID account to come in.";
$lang['core_webspace_barred_intro'] = 						"Warning: This webspace has been barred for access by the site maintainers. If you are the webspace owner please read the section of the policy entitled 'Barring access to a webspace', which will give you guidance on how you should proceed.";
$lang['core_webspace_pending_intro'] = 						"Note: This webspace has not yet been approved by the site maintainers. If you are the webspace owner please read the section of the policy entitled 'Webspace application approvals process', which will give you guidance on how you should proceed.";
$lang['core_webspace_applied_intro'] = 						"Your application has been received. You will recieve an email shortly.";
$lang['core_webspace_apply_intro'] = 						"The following information is used once to process your application. If accepted you will receive an email from a webspace maintainer. Add further information to the 'note' to support your application.";


// LOGIN
$lang['core_append_connection'] = 							"Additional information required";
$lang['core_connect_intro'] = 								"Connect to contribute! To connect enter your OpenID.";
$lang['core_append_connection_intro'] = 					"Your OpenID provider could not give us some information we require. Please fill in the form below to proceed. Items marked with '*' are required and everything else is optional.";
$lang['core_no_openid_account_intro'] =						"No OpenID account yet? Register here and get one.";
$lang['core_register_intro'] = 								"Need an OpenID service? Click the 'register' link below to register with Barnraiser and get a free OpenID account.";
$lang['core_register_intro'] = 								"By filling in this registration form you will get a free OpenID account from Barnraiser. With it you get your own web site, OpenID account and decentralized social network!";
$lang['core_get_openid_intro'] = 							"You can get an OpenID account from any OpeniD service provider or you can get your own <a href=\"http://www.barnraiser.org/\">free OpenID software</a> to host your own from Barnraiser.";
$lang['core_get_openid'] = 									"Get an OpenID";


// NETWORK
$lang['core_log'] = 										"Activity log";
$lang['core_options'] = 									"options";
$lang['core_group_allocation'] = 							"groups";
$lang['core_set_as_banned'] = 								"Deny access";
$lang['core_contributions'] = 								"Webspace contributions";
$lang['core_identity'] = 									"identity";
$lang['code_plugin_permissions'] =							"Plugin permissions";
$lang['core_default_permissions'] = 						"Default permissions";
$lang['core_applicants'] =									"applicants";
$lang['core_applicant_details'] =							"Applicant details";
$lang['core_connect_to_view'] =								"Connect to see profiles";
$lang['core_connections'] = 								"connections";
$lang['core_management_options'] = 							"Management options";
$lang['core_manage_permissions'] = 							"Manage permissions";
$lang['core_deny_access'] = 								"deny access";
$lang['core_allow_access'] = 								"accept access and send email";
$lang['core_response'] = 									"response";
$lang['core_barred'] = 										"banned";
$lang['core_filter'] = 										"filter";
$lang['core_connect_to_view_intro'] =						"Please connect to see contributor profiles.";
$lang['core_permissions_allow_levels'] = 					"By being a member of the above groups this person gets the following permissions within this webspace:";
$lang['core_permissions_notes'] = 							"Only people in the 'maintainer' group can adust your group settings. To find out who they are 'list' members and filter 'maintainers'.";
$lang['core_group_membership_intro'] = 						"This person is a member of the following groups:";
$lang['core_response_message'] = 							"Hi SYS_KEYWORD_NICKNAME,\n\nYour application to SYS_KEYWORD_URL is approved. Welcome to this webspace! Click the link and use your OpenID (SYS_KEYWORD_OPENID) to come on in.\n\nKind regards,\n\nSYS_KEYWORD_OPENID_NICKNAME";
$lang['core_applications_intro'] = 							"Click on the applicants name to approve or deny them access to your webspace.";
$lang['core_permissions_allowed'] = 						"can";
$lang['core_permissions_denied'] = 							"cannot";
$lang['core_request_error'] = 								"There was a problem with the request.";
$lang['core_no_network'] = 									"This person has no network.";
$lang['core_no_entries'] = 									"No entries";
// NETWORK ERRORS
$lang['error']['no_connections'] = 							"No connections found.";
$lang['error']['no_applications'] = 						"No applications outstanding.";


// OVERVIEW
$lang['core_create_webspace'] = 							"create";
$lang['core_search_results'] = 								"Search results";
$lang['core_latest_webspaces'] = 							"Latest webspaces";
$lang['core_webspaces'] = 									"List latest webspaces";
$lang['core_create_webspace'] = 							"create your webspace now!";
$lang['core_create_webspace_intro'] = 						"You can create your own webspace right now. Just go and get an OpenID, then come back here. It's free to start your webspace and it's free to obtain an OpenID!";
$lang['core_relevance'] = 									"relevance";


// SETUP
$lang['core_webspace_intro'] = 								"Webspace information";
$lang['core_webpages'] = 									"Web pages";
$lang['core_plugins'] = 									"Plugin blocks";
$lang['core_delete_selected'] = 							"delete selected";
$lang['core_set_start'] = 									"set start page";
$lang['core_add_block'] = 									"Add custom block";
$lang['core_locked'] = 										"Private";
$lang['core_webspace_name'] = 								"Name";
$lang['core_tag'] = 										"Tag";
$lang['core_start'] = 										"Start";
$lang['core_custom_block'] = 								"Custom";
$lang['core_language_intro'] = 								"Select the language you would prefer your webspace navigation to be in.";
$lang['core_locked_intro'] = 								"Check the checkbox to make this webspace private so that only authorized connections in enter.";


// STYLES POPUP
$lang['core_styles'] = 										"styles";
$lang['core_style_html_title'] = 							"Stylesheet editor";
$lang['core_delete_selected'] = 							"delete checked styles";
$lang['core_set_default'] = 								"set current style";
$lang['core_stylesheet'] = 									"stylesheet";
$lang['core_current'] = 									"Current";
$lang['core_add_stylesheet'] = 								"Add a stylesheet";
$lang['core_simple_editor_intro'] = 						"Listed below are your stylesheets. You can set one of them to be your current webspace style.";


// WEBPAGE
$lang['core_edit_webpage'] = 								"Edit your web page";
$lang['core_plugin_helper'] = 								"Plugin block creator";
$lang['core_layouts'] = 									"Webpage layouts";
$lang['core_layout_helper'] = 								"Add a webpage layout";
$lang['core_plugin_helper'] = 								"Add a plugin block";
$lang['core_layout_helper_intro'] = 						"Select the layout that you want to use in your webpage.";
$lang['core_layout_helper_instruction'] = 					"Copy and paste the code below into your webpage.";
$lang['core_plugin_helper_intro'] = 						"Copy the above tag into either a block or a webpage to activate the plugin.";

?>