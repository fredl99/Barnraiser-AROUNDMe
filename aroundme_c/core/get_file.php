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

include "config/core.config.php";


session_name($core_config['php']['session_name']);
session_start();

if (!empty($_REQUEST['reloadsession'])) {
	// the wrapper.tpl.php refreshes the session before timeout...
	$_SESSION['last_session_access_time'] = time();
	session_write_close();

	header("Content-type: image/png");
	readfile('../' . $core_config['file']['dir'] . 'files/session-reload-image.png');
}
if (!empty($_REQUEST['graphic'])) {
	readfile('../' . $core_config['file']['dir'] . 'graphic/' . $_REQUEST['graphic']);
}
else {
	define("AM_WEBSPACE_ID", $_SESSION['webspace_id']);
	
	$tmp = strrpos($_REQUEST['file'], '.');
	if ($tmp) {
		$suffix = substr($_REQUEST['file'], $tmp+1);
		foreach($core_config['file']['mime'] as $key => $val):
			if (strstr($core_config['file']['mime'][$key]['mime'], $suffix)) {
				header("Content-type: " . $core_config['file']['mime'][$key]['mime']);
				break;
			}
		endforeach;
	}

	readfile('../' . $core_config['file']['dir'] . AM_WEBSPACE_ID . '/' . $_REQUEST['file']);
}
?>