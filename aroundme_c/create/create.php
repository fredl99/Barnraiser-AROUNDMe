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


/*
note

// 0=deny, 1=approval_required, 2=automatic
$core_config['am']['webspace_creation_type'] = 			2;

// list reserved webspace names
$core_config['am']['excluded_webspace_names'] = 	array('www', 'ftp', 'mail');

Webspace status's
1=pending, 2=barred, 3=active
*/

// MAIN INCLUDES ---------------------------------------------------------
include ("../core/config/core.config.php");
include ("../core/inc/functions.inc.php");

session_name($core_config['php']['session_name']);
session_start();


// SECURITY CHECK --------------------------------------------------------
if (!isset($_SESSION['am_maintainer']) && $core_config['am']['webspace_creation_type'] != 1 && $core_config['am']['webspace_creation_type'] != 2) {
	
	exit;
}


// SETUP DATABASE ------------------------------------------------------
include ('../core/class/Db.class.php');
$db = new Database($core_config['db']);

// SETUP TEMPLATE -------------------------------------------
define("AM_TEMPLATE_PATH", "core/template/");
include ('../core/class/Template.class.php');
$tpl = new Template();


// SETUP OPENID -------------------------------------------
include '../core/class/OpenidConsumer.class.php';
$openid_consumer = new OpenidConsumer;



// SETUP LANGUAGE --------------------------------------------
if (!isset($core_config['language']['default'])) {
	die ('Default language pack not set correctly.');
}

define("AM_DEFAULT_LANGUAGE_CODE", $core_config['language']['default']);
setlocale(LC_ALL, $core_config['language']['pack'][AM_DEFAULT_LANGUAGE_CODE]);


$lang = array();

if (is_readable('../core/language/' . AM_DEFAULT_LANGUAGE_CODE . '/common.lang.php')) {
	include_once('../core/language/' . AM_DEFAULT_LANGUAGE_CODE . '/common.lang.php');
}
else {
	die ('Default language pack not set correctly or cannot be read.');
}

if (is_readable('language/' . AM_DEFAULT_LANGUAGE_CODE . '/create.lang.php')) {
	include_once('language/' . AM_DEFAULT_LANGUAGE_CODE . '/create.lang.php');
}



if (isset($_POST['accept_terms']) || isset($_POST['reject_terms'])) { // stage 1 action
	
	if (isset($_POST['accept_terms'])) {
		$_SESSION['terms_agreed'] = 1;
		
		$tpl->set('stage', 2);
	}
	else {
		header("location: ../index.php?t=overview");
		exit;
	}
	
}
elseif (isset($_POST['test_webspace_name']) || isset($_POST['reject_webspace_name'])) {
	// stage 3 - select webspace name
	if (empty($_POST['webspace_name'])) {
		$GLOBALS['am_error_log'][] = array('webspace name empty');
	}
	else {
		formatWebspaceName();
	}

	if (empty($GLOBALS['am_error_log'])) {
		
		if (testWebspaceName($db, $_POST['webspace_name'], $core_config['am']['excluded_webspace_names'])) {

			$url = str_replace('REPLACE', $_POST['webspace_name'], $core_config['am']['domain_replace_pattern']);
			
			$headers = @get_headers($url);
		
			if (isset($headers[0]) && ($headers[0] == 'HTTP/1.1 200 OK' || $headers[0] == 'HTTP/1.0 200 OK')) {
				if (isset($_POST['test_webspace_name'])) {
					$tpl->set('webspace_name', $_POST['webspace_name']);
				}
			}
			else {
				$tpl->set('confirm_webspace_name', 1);
			}
		}
		else {
			$tpl->set('forbidden_webspace_name', $_POST['webspace_name']);
		}
	}
	$tpl->set('stage', 5);
}
elseif (isset($_POST['apply_design'])) {
	
	$_SESSION['webspace_theme'] = $_POST['theme_name'];
	$_SESSION['webspace_css'] = $_POST['theme_css'];
	$tpl->set('stage', 4);
}
elseif (isset($_POST['configure'])) {

	$tpl->set('stage', 5);
	
	if (isset($_POST['webspace_locked'])) {
		$_SESSION['webspace_locked'] = 1;
	}
	else {
		unset($_SESSION['webspace_locked']);
	}

	if (isset($_POST['language_code'])) {
		$_SESSION['language_code'] = $_POST['language_code'];
	}
	else {
		$_SESSION['language_code'] = AM_DEFAULT_LANGUAGE_CODE;
	}

	if (empty($_POST['webspace_title'])) {
		$GLOBALS['am_error_log'][] = array('no_title');
		unset($_SESSION['webspace_title']);
		$tpl->set('stage', 4);
	}
	else {
		$_SESSION['webspace_title'] = $_POST['webspace_title'];
	}
	
	
}
elseif (isset($_POST['complete'])) {

	insertWebspace($db, $core_config);

	$tpl->set('stage', 6);
	
}
elseif (isset($_POST['connect'])) {
	
	// stage 2 - openid connect
	$_POST['openid_login'] = $openid_consumer->normalize($_POST['openid_login']);

	$openid_consumer->required_fields = array('nickname', 'email');

	if ($openid_consumer->discover($_POST['openid_login'])) { // we did discover a server
		if($openid_consumer->associate()) { // association is ok
			$openid_consumer->checkid_setup(); // do the setup
		}
		else {
			// error-log here
			$tpl->set('stage', 2);
		}
	}
	else {
			$tpl->set('stage', 2);
	}
}
elseif (isset($_GET['openid_mode']) && $_GET['openid_mode'] == 'id_res') { // we get data back from the server
	
	if ($openid_consumer->id_res()) { // was the result ok?

		$openid = $_GET['openid_identity'];

		if(substr($openid,-1,1) == '/'){
			$openid = substr($openid, 0, strlen($openid)-1);
		}

		$_SESSION['openid_identity'] = $openid;
		$_SESSION['openid_nickname'] = $_GET['openid_sreg_nickname'];
		$_SESSION['openid_email'] = $_GET['openid_sreg_email'];

		$tpl->set('stage', 3);
	}
	else {
		// error-log here
		$tpl->set('stage', 2);
	}
	
}
else {
	unset($_SESSION['terms_agreed']);
	unset($_SESSION['openid_identity'], $_SESSION['openid_nickname'], $_SESSION['openid_email']);
	unset($_SESSION['webspace_theme'], $_SESSION['webspace_css'], $_SESSION['webspace_locked']);
	$tpl->set('stage', 1);
}



// THEME SETUP -------------------------------------------------
$themes = selThemes();


// TEMPLATE SEUP -----------------------------------------------
$tpl->set('themes', $themes);
$tpl->set('config_url', $core_config['am']['domain_replace_pattern']);
$tpl->set('webspace_creation_type', $core_config['am']['webspace_creation_type']);
$tpl->set('arr_language', $core_config['language']);
$tpl->set('lang', $lang);
$tpl->lang = $lang;

echo $tpl->fetch('template/create.tpl.php');


// FUNCTIONS ---------------------------------------------------
function selThemes () {

	global $lang;
	
	$thumbs = glob('themes/*/thumb/*.png');
	$css = glob('themes/*/css/*.css');
	$webpages = glob('themes/*/webpage/*.php');
	$themes = array();
	
	foreach($thumbs as $key => $t):
		$tmp = explode('/', $t);
		$themes[$tmp[1]]['thumb'][] = $t;
	endforeach;
	
	foreach($css as $key => $t):
		$tmp = explode('/', $t);
		$themes[$tmp[1]]['css'][] = $t;
	endforeach;
	
	foreach($webpages as $key => $t):
		$tmp = explode('/', $t);
		$themes[$tmp[1]]['webpage'][] = $t;
	endforeach;
	
	// LOAD UP THE LANGUAGE FILES
	foreach($themes as $key => $t):
		if (is_readable('themes/' . $key . '/language/en/theme.lang.php')) {
			include('themes/' . $key . '/language/en/theme.lang.php');
		}
	endforeach;
	
	return $themes;
}


function formatWebspaceName() {
	$_POST['webspace_name'] = strtolower($_POST['webspace_name']);

	$pattern = "/^[a-zA-Z0-9]*$/";
	
	if (!preg_match($pattern, $_POST['webspace_name'])) {
		$GLOBALS['am_error_log'][] = array('only_characters_allowed');
	}

	if (strlen($_POST['webspace_name']) > 30) { // link too long
		$GLOBALS['am_error_log'][] = array('name_too_long');
	}

	return $_POST['webspace_name'];
}


function testWebspaceName ($db, $name, $excluded) {
	// create excluded array
	$excluded = explode(',',$excluded);

	foreach($excluded as $key => $i):
		$excluded[$key] = trim($i);
	endforeach;
	
	// Test that name is not in excluded list
	if (in_array($name, $excluded)) {
		$GLOBALS['am_error_log'][] = array('reserved_name');
		return 0;
		
	}
	
	// Test the name is not already in use
	$query = "
		SELECT *
		FROM " . $db->prefix . "_webspace
		WHERE webspace_unix_name LIKE " . $db->qstr($name)
	;

	$result = $db->Execute($query);
	if (!empty($result)) {
		$GLOBALS['am_error_log'][] = array('name_taken');
		return 0;
	}
	
	return 1;
}


function insertWebspace ($db, $core_config) {

	// Test the name is not already in use
	$query = "
		SELECT *
		FROM " . $db->prefix . "_webspace
		WHERE webspace_unix_name LIKE " . $db->qstr($_POST['webspace_name'])
	;

	$result = $db->Execute($query);
	
	if (empty($result)) {
		
		// INSERT THE WEBSPACE ---------------------
		$rec = array();
		$rec['webspace_unix_name'] = $_POST['webspace_name'];
		$rec['webspace_title'] = $_SESSION['webspace_title'];
		$rec['language_code'] = $_SESSION['language_code'];
		$rec['default_permission'] = $core_config['am']['webspace_default_permission'];
		$rec['webspace_allocation'] = $core_config['file']['default_allocation'];
		$rec['webspace_create_datetime'] = time();

		if ($core_config['am']['webspace_creation_type'] == 2) { // automatic
			$rec['status_id'] = 3; // active
		}
		else {
			$rec['status_id'] = 1; // pending
		}

		if (isset($_SESSION['webspace_locked'])) {
			$rec['webspace_locked'] = 1;
		}
		
		$table = $db->prefix . '_webspace';
		
		$db->insertDb($rec, $table);
	
		$webspace_id = $db->insertID();
		
		$_SESSION['webspace_create_datetime'] = time();
		$_SESSION['webspace_name'] = $_POST['webspace_name'];
		
	
		
		// INSERT THE CONNECTION --------------------
		$permissions = 0;
	
		foreach ($core_config['group'] as $key => $i):
			$permissions = $permissions+$i;
		endforeach;
	
		
		$rec = array();
		$rec['webspace_id'] = $webspace_id;
		$rec['connection_create_datetime'] = time();
		$rec['status_id'] = 2; // 1=barred,2=active
		$rec['connection_openid'] = $_SESSION['openid_identity'];
		$rec['connection_nickname'] = $_SESSION['openid_nickname'];
		$rec['connection_email'] = $_SESSION['openid_email'];
		$rec['connection_total'] = 1;
		$rec['connection_permission'] = $permissions;
		
		$table = $db->prefix . "_connection";
	
		$db->insertDB($rec, $table);
	
		$connection_id = $db->insertID();
		
		
		// INSERT PAGES
		$webpages = glob('themes/' . $_SESSION['webspace_theme'] . '/webpage/*.php');
	
		$rec = array();
		$rec['webspace_id'] = $webspace_id;
		$rec['webpage_create_datetime'] = time();
	
		$table = $db->prefix . '_webpage';
	
		foreach($webpages as $w) {
			$tmp = explode('/', $w);
			$tmp = explode('.', $tmp[count($tmp)-1]);
	
			$webpage_contents = "";
			$webpage_contents .= @file_get_contents($w);

			if (get_magic_quotes_gpc()) {
				$webpage_contents = addslashes($webpage_contents);
			}
			
			if (substr($tmp[0], -8) == '_default') {
				$rec['webpage_name'] = str_replace('_default', '', $tmp[0]);
			}
			else {
				$rec['webpage_name'] = $tmp[0];
			}

			$rec['webpage_body'] = $webpage_contents;
	
			$db->insertDb($rec, $table);
	
			// if the filename ends with _default then we record this as the default page
			if (substr($tmp[0], -8) == '_default') {
				$webpage_id = $db->insertID();
			}
		}
	
		if (!isset($webpage_id)) {
			$webpage_id = $db->insertID();
		}
			
	
		// INSERT BLOCKS
		$blocks = glob('themes/' . $_SESSION['webspace_theme'] . '/block/*.php');
	
		$rec = array();
		$rec['webspace_id'] = $webspace_id;
		$rec['block_plugin'] = 'custom';
		
		$table = $db->prefix . '_block';
	
		foreach($blocks as $b) {
			$tmp = explode('/', $b);
			$tmp = explode('.', $tmp[count($tmp)-1]);
			
			$block_contents = "";
			$block_contents .= @file_get_contents($b);

			if (get_magic_quotes_gpc()) {
				$block_contents = addslashes($block_contents);
			}
			
			$rec['block_name'] = $tmp[0];
			$rec['block_body'] = $block_contents;
	
			$db->insertDb($rec, $table);
		}


		// INSERT CSS'S
		$stylesheets = glob('themes/' . $_SESSION['webspace_theme'] . '/css/*.css');
	
		$rec = array();
		$rec['webspace_id'] = $webspace_id;
		
		$table = $db->prefix . '_stylesheet';
	
		foreach($stylesheets as $s) {
			$tmp = explode('/', $s);
			$tmp = explode('.', $tmp[count($tmp)-1]);
			
			$stylesheet_contents = "";
			$stylesheet_contents .= @file_get_contents($s);

			if (get_magic_quotes_gpc()) {
				$stylesheet_contents = addslashes($stylesheet_contents);
			}
			
			$rec['stylesheet_name'] = $tmp[0];
			$rec['stylesheet_body'] = $stylesheet_contents;
	
			$db->insertDb($rec, $table);

			if ($tmp[0] == $_SESSION['webspace_css']) {
				$stylesheet_id = $db->insertID();
			}
		}

		if (!isset($stylesheet_id)) {
			$stylesheet_id = $db->insertID();
		}
		
		
		// UPDATE WEBSPACE
		$query = "
			UPDATE " . $db->prefix . "_webspace
			SET
			owner_connection_id=" . $connection_id . ", 
			stylesheet_id=" . $stylesheet_id . ",
			default_webpage_id=" . $webpage_id . "
			WHERE webspace_id=" . $webspace_id
		;
		
		$db->Execute($query);
	}
}

?>