<?php

// ---------------------------------------------------------------------
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
// --------------------------------------------------------------------

$lang['installer_start'] = 									"AROUNDMe collaboration server installer";
$lang['installer_start_intro'] = 							"This installer will install version 'AM_SYS_KEYWORD_VERSION' of Barnraisers AROUNDMe collaboration server. The installation is in 4 simple steps; setup your domain, configure your MySQL database, add a maintainer account and setup. After setup you will be taken to the maintainers area where you can proceed to create your first webspace.";
$lang['installer_start_installation'] = 					"start installation";
$lang['installer_configure_database'] = 					"Configure database";
$lang['installer_database_host'] = 							"Host";
$lang['installer_database_host_example'] = 					"Example: localhost";
$lang['installer_database_user'] = 							"Username";
$lang['installer_database_password'] = 						"Password";
$lang['installer_database_name'] = 							"Database name";
$lang['installer_database_name_example'] = 					"Example: aroundme_collaboration";
$lang['installer_database_create'] = 						"create database";
$lang['installer_maintainer'] = 							"Maintainer";
$lang['installer_openid_require'] = 						"Need an OpenID account?";
$lang['installer_openid_require_intro'] = 					"OpenID is like a digital identity card with which you can show to enter a web site. You get to keep your information in one place and you get to choose which information you wish to present to each web site. If you have your own domain name you can host your own OpenID account using <a href=\"http://www.barnraiser.org/\">AROUNDMe Personal identity</a> which is free. Alternatively you can obtain an OpenID account from a service provider such as <a href=\"http://www.barnraiser.info/\">Barnraiser</a>.";
$lang['installer_setup'] = 									"Setup";
$lang['installer_setup_create'] = 							"Create";
$lang['installer_setup_create_intro'] = 					"Who can create a webspace?";
$lang['installer_setup_create_maintainer'] = 				"Only maintainers";
$lang['installer_setup_create_approve'] = 					"Anyone, but they need maintainer approval";
$lang['installer_setup_create_auto'] = 						"Anyone, automatic creation";
$lang['installer_setup_domain'] =							"Setup domain";
$lang['installer_setup_domain_intro'] =						"The domain you try to install at is ";
$lang['installer_setup_domain_webspace_intro'] =			"This implies your webspace urls will look something like this";
$lang['installer_setup_domain_correct'] =					"Is this correct?";
$lang['installer_setup_domain_yes'] =						"yes";
$lang['installer_setup_domain_no'] =						"no";
$lang['installer_setup_domain_example'] =					"Give an example of how a webspace should look like.";
$lang['installer_chmod_error'] =							"The installer did not manage to change permission to this file. You need to manually change permissons to unreadable to the file <b>installer.php</b>. After that you will be able to access the maintainarea by following this link <a href=\"maintain.php?install=complete\">maintain.php?install=complete</a>";

$lang['error']['username_not_set'] = 						"Maintainer username not set";
$lang['error']['password_not_set'] = 						"Maintainer password not set";
$lang['error']['password_not_verified'] =					"Maintainer password not verified";
$lang['error']['installer_host_empty'] =					"Host is not set";
$lang['error']['installer_user_empty'] =					"Username is not set";
$lang['error']['installer_db_empty'] =						"Database is not set";
$lang['error']['openid_associate'] =						"OpenID error: failed to associate with server";
$lang['error']['openid_discovery'] =						"OpenID error: failed to discover server";



// AM SYSTEM CHECKS
$lang['arr_am_sys_check']['php_mysql_exists']['name'] =		"PHP MySQL exists";
$lang['arr_am_sys_check']['php_mysql_exists']['error'] =	"AROUNDMe collaboration server needs MySQL. Please add curl to PHP";
$lang['arr_am_sys_check']['php_version']['name'] =			"PHP version > 5.0";
$lang['arr_am_sys_check']['php_version']['error'] =			"AROUNDMe collaboration server needs PHP 5.0 or greater. Your PHP version is ";
$lang['arr_am_sys_check']['curl_exists']['name'] =			"CURL exists";
$lang['arr_am_sys_check']['curl_exists']['error'] =			"AROUNDMe collaboration server needs curl. Please add CURL to PHP";
$lang['arr_am_sys_check']['bcmath_exists']['name'] =		"BCMath exists";
$lang['arr_am_sys_check']['bcmath_exists']['error'] =		"AROUNDMe collaboration server needs MySQL. Please add MySQL to PHP";
$lang['arr_am_sys_check']['gd_version']['name'] =			"GD library version > 2.0";
$lang['arr_am_sys_check']['gd_version']['error'] =			"AROUNDMe collaboration server needs GD library. Please add GD library to PHP";
//$lang['arr_am_sys_check']['directory_structure']['name'] =	"Directory structure";
//$lang['arr_am_sys_check']['directory_structure']['error'] =	"Directory structure not intact. You need to upload the entire release directory structure";

$lang['arr_am_sys_check']['config_writable']['name'] =		"Config file writable";
$lang['arr_am_sys_check']['config_writable']['error'] =		"AROUNDMe collaboration server cannot write to its config file. Please check your permissions";



?>