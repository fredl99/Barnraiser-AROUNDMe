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


// PHP ERROR REPORTING -----------------------------------------------
//error_reporting(E_ALL); // error handling in development environment.
error_reporting(0);	// error handling in production environment


// RELEASE NOTES ---------------------------------------------------------
$core_config['release']['version'] = 					"1.6.2";
$core_config['release']['release_date'] = 				"03-27-2008"; // MM-DD-YYYY
$core_config['release']['install_date'] = 				"";


//DATABASE CONFIGURATION -------------------------------------------------
$core_config['db']['host'] = "localhost";
$core_config['db']['user'] = "root";
$core_config['db']['pass'] = "";
$core_config['db']['db'] = "";
$core_config['db']['prefix'] =	 						"am";
$core_config['db']['collate'] =	 						""; // utf8_swedish_ci



// LANGUAGE CONFIGURATION -----------------------------------------------
// debian note: go to aptitude and install -language-pack-*-base, the restart webserver
// locale -a to display list of installed packs. Key entries must be lowercase
$core_config['language']['pack']['en'] = 			"en_US";
// default language key
$core_config['language']['default'] = 				"en";



// AROUNDMe CONFIGURATION ---------------------------------------------
$core_config['am']['domain_preg_pattern'] = "/(.*?)\.example.org/"; // add trailing slash
$core_config['am']['domain_replace_pattern'] = "http://REPLACE.example.org"; // remove trailing slash

// we can access the maintainer.php page?
$core_config['am']['maintainer_openids'][] = "";

// allow people to create webspaces (maintainers always have ability to create)
// 0=deny, 1=approval_required, 2=automatic
$core_config['am']['webspace_creation_type'] = "2";
// list reserved webspace names
$core_config['am']['excluded_webspace_names'] = 		"www, ftp, mail";
// default permission setting for new webspace
$core_config['am']['webspace_default_permission'] = 	1;



// OpenID CONFIGURATION ----------------------------------------------------
$core_config['openid_extension']['sreg']['required_fields'] = 	array('nickname');
$core_config['openid_extension']['sreg']['optional_fields'] = 	array('fullname', 'email', 'dob', 'postcode', 'gender', 'country', 'timezone', 'language'); // add to optional fields and required fields


// Remote openid account registration
// You can include a link to a remote registration script for people to
// obtain an OpenID account without having to leave your web site.
// You are welcome to use Barnraisers OpenID server, but if comes with
// no uptime garantee - http://barnraiser.info/register.php?remote=1
// Leaving this field empty hides registration.
$core_config['openid_account_registration'] =			"";


// EMAIL CONFIGURATION -----------------------------------------------------
$core_config['mail']['host'] = 							"your_mail_server.org";
$core_config['mail']['port'] = 							"25";
$core_config['mail']['email_address'] = 				"you@your_mail.org";
$core_config['mail']['mailer'] = 						"smtp";
$core_config['mail']['wordwrap'] = 						"80";
//if you need a username and password to access SMTP then uncomment these
// and add your username and password
//$core_config['mail']['smtp']['username'] = 			"your_mailserver_username";
//$core_config['mail']['smtp']['password'] = 			"your_mailserver_password";
$core_config['mail']['max_bulk_send'] = 					"25";



// FILE CONFIGURATION ----------------------------------------------------
$core_config['file']['mime'][1]['mime'] = 				"image/jpeg";
$core_config['file']['mime'][2]['mime'] = 				"image/png";
$core_config['file']['mime'][3]['mime'] = 				"image/gif";
$core_config['file']['mime'][4]['mime'] = 				"application/pdf";
$core_config['file']['mime'][5]['mime'] = 				"text/plain";
$core_config['file']['type']['application/pdf']['image'][1] = 	"img/pdf.png";
$core_config['file']['type']['text/plain']['image'][1] = 		"img/txt.png";
$core_config['file']['type']['application/pdf']['image'][2] = 	"img/pdf_35.png";
$core_config['file']['type']['text/plain']['image'][2] = 		"img/txt_35.png";
// We use this to map IE-mimetype to standard mimetype
$core_config['file']['browser_path'] =					array(array("from" => "image/pjpeg", "to" => "image/jpeg"));
//image and thumbs
$core_config['file']['dir'] =							"../asset/";
$core_config['file']['thumbnail']['width'][1] =			35;
$core_config['file']['thumbnail']['height'][1] =		35;
$core_config['file']['thumbnail']['width'][2] =			90;
$core_config['file']['thumbnail']['height'][2] =		90;
$core_config['file']['default_allocation'] =			200; // in KB



// CONNECTED PERMISSIONS ----------------------------------------------------------
// connections get permissions to use the system (bitflags!! x2)
$core_config['group']['contributor'] = 					1; // can comment
$core_config['group']['publisher'] = 					2; // can publish things like blogs
$core_config['group']['editor'] = 						4; // can manage tags and posts
$core_config['group']['designer'] = 					8; // can edit webpages / stylesheets
$core_config['group']['maintainer'] = 					16; // manages contributors



// DISPLAY CONFIGURATION ---------------------------------------------------
$core_config['display']['max_list_rows'] = 				50;



// PHP CONFIGURATION -----------------------------------------------
// PHP keeps data in a session. The session is called "PHPSESSID" as standard. If you
// have more than one instance of this software you should create a unique session name.
// recomended is characters A-Z (uppercase),0-9 with no spaces. DO NOT use a dot (.).
$core_config['php']['session_name'] = "PHPSESSIDAMC";
// Set to 0 if you do not want cron jobs running, 1 if you do
$core_config['php']['cron_active'] = 0;


// tokens that are not accepted ------------------------------------------------
$core_config['invalid_tokens'][] = 						'exec';
$core_config['invalid_tokens'][] = 						'passthru';
$core_config['invalid_tokens'][] = 						'shell_exec';
$core_config['invalid_tokens'][] = 						'system';
$core_config['invalid_tokens'][] = 						'proc_terminate';
$core_config['invalid_tokens'][] = 						'proc_open';

// END OF CONFIG FILE ----------------------------------------------------

?>