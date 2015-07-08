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


include ("config/core.config.php");
include ("inc/functions.inc.php");



// START SESSION -----------------------------------------------------------
session_name($core_config['php']['session_name']);
session_start();


if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $core_config['group']['designer']) {

	// SETUP STORAGE ---------------------------------------------------------------------------
	require_once('class/Db.class.php');
	$db = new Database($core_config['db']);


	// SETUP TEMPLATE -------------------------------------------
	define("AM_TEMPLATE_PATH", "template/");
	require_once('class/Template.class.php');
	$tpl = new Template();


	// SETUP WEBSPACE --------------------------------------------
	require_once('class/Webspace.class.php');
	$ws = new Webspace($db);
	
	$ws->webspace_unix_name = $ws->getWebspaceName($core_config['am']['domain_preg_pattern']);
	
	if (!empty($ws->webspace_unix_name)) {
		$output_webspace = $ws->selWebSpace();
	}
	
	
	// SETUP LANGUAGE ------------------------------------------------------
	if (!isset($core_config['language']['default'])) {
		die ('Default language pack not set correctly.');
	}
	
	define("AM_DEFAULT_LANGUAGE_CODE", $core_config['language']['default']);
	define("AM_DEFAULT_LANGUAGE_PATH", "language/" . AM_DEFAULT_LANGUAGE_CODE . "/");
	setlocale(LC_ALL, $core_config['language']['pack'][AM_DEFAULT_LANGUAGE_CODE]);
	
	$lang = array();

	if (is_readable(AM_DEFAULT_LANGUAGE_PATH . 'common.lang.php')) {
		include_once(AM_DEFAULT_LANGUAGE_PATH . 'common.lang.php');
	}
	else {
		die ('Default language pack not set correctly or cannot be read.');
	}
	
	if (is_readable(AM_DEFAULT_LANGUAGE_PATH . 'core.lang.php')) {
		include_once(AM_DEFAULT_LANGUAGE_PATH . 'core.lang.php');
	}

	if (isset($output_webspace['language_code']) && $output_webspace['language_code'] != AM_DEFAULT_LANGUAGE_CODE) {
		define("AM_LANGUAGE_CODE", $output_webspace['language_code']);
		define("AM_LANGUAGE_PATH", "core/language/" . AM_LANGUAGE_CODE . "/");
	}

	if (defined('AM_LANGUAGE_CODE')) {
		if (is_readable(AM_LANGUAGE_PATH . 'common.lang.php')) {
			include_once(AM_LANGUAGE_PATH . 'common.lang.php');
		}
		
		if (is_readable(AM_LANGUAGE_PATH . 'core.lang.php')) {
			include_once(AM_LANGUAGE_PATH . 'core.lang.php');
		}
	}
	

	// Selects and displays styles
	// If no styles are present then we skip to create a style
	// if no $_REQUEST['style'] we display default style
	if (isset($_POST['save_stylesheet'])) {
		
		if (!empty($_POST['stylesheet_id'])) {
			$query = "
				UPDATE " . $db->prefix . "_stylesheet
				SET 
				stylesheet_name=" . $db->qstr($_POST['stylesheet_name']) . ", 
				stylesheet_body=" . $db->qstr($_POST['stylesheet_body']) . "
				WHERE 
				stylesheet_id=" . $_POST['stylesheet_id']
			;
			
			$result = $db->Execute($query);
	
			$_REQUEST['stylesheet_id'] = $_POST['stylesheet_id'];

		}
		else { // we insert

			$rec = array();
			$rec['stylesheet_name'] = $_POST['stylesheet_name'];
			$rec['stylesheet_body'] = $_POST['stylesheet_body'];
			$rec['webspace_id'] = $_SESSION['webspace_id'];
	
			$table = $db->prefix . "_stylesheet";
		
			$db->insertDb($rec, $table);

			$_REQUEST['stylesheet_id'] = $db->insertID();;
		}

		$tpl->set('update_mother', 1);
	}
	elseif (isset($_POST['set_current_webspace_style'])) {

		$query = "
			UPDATE " . $db->prefix . "_webspace 
			SET
			stylesheet_id=" . $_POST['default_style_id'] . " 
			WHERE
			webspace_id=" . $_SESSION['webspace_id']
		;

		$result = $db->Execute($query);
		
		$tpl->set('update_mother', 1);
	}
	elseif (isset($_POST['delete_webspace_styles'])) {
		if (!empty($_POST['delete_style_ids'])) {
			$query = "
				DELETE FROM " . $db->prefix . "_stylesheet
				WHERE
				stylesheet_id in (" . implode(",", $_POST['delete_style_ids']) . ")"
			;
	
			$result = $db->Execute($query);
		}

		header("Location: stylesheet_editor.php");
		exit;
	}

	// SELECT CURRENT STYLESHEET
	$query = "
		SELECT ss.stylesheet_id, ss.stylesheet_body
		FROM " . $db->prefix . "_stylesheet ss, " . $db->prefix . "_webspace ws 
		WHERE
		ws.stylesheet_id=ss.stylesheet_id AND 
		ws. webspace_id=" . $_SESSION['webspace_id']
	;
	
	$result = $db->Execute($query);
	
	if (isset($result[0])) {
		$tpl->set('css', $result[0]);
	}
	
	
	// SELECT STYLESHEETS
	$query = "
		SELECT stylesheet_id, webspace_id, stylesheet_name, stylesheet_body 
		FROM " . $db->prefix . "_stylesheet
		WHERE
		webspace_id=" . $_SESSION['webspace_id']
	;
	
	$result = $db->Execute($query);
	
	if (isset($result)) {
		// find current CSS and apply style
		foreach ($result as $key => $i):
			if (isset($_REQUEST['stylesheet_id']) && $_REQUEST['stylesheet_id'] == $i['stylesheet_id']) {
				$tpl->set('style', $i);
			}
		endforeach;

		$tpl->set('styles', $result);
	}

	$tpl->lang = $lang;

	echo $tpl->fetch(AM_TEMPLATE_PATH . 'stylesheet_editor.tpl.php');
}
else {
	echo '<html><head><body onload="javascript:self.close();">closing...</body></head></html>';
	exit;
}

?>