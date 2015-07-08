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

if (isset($_POST['file'])) {
	
	$headers = @get_headers($_POST['file']);
	
	if (isset($headers) && !empty($headers)) {
		if ($headers[0] == 'HTTP/1.1 200 OK' && $headers[8] == 'Content-Type: application/xml') {
			$file = @file_get_contents($_POST['file']);
			if (isset($file) && !empty($file)) {
				header('Content-type: application/xml');
				echo "<?xml version=\"1.0\"?>\n";
				echo $file;
			}
			else {
				header('Content-type: application/xml');
				echo "<?xml version=\"1.0\"?>\n";
				echo '<failure>1</failure>'; // failure
			}
		}
		else {
			header('Content-type: application/xml');
			echo "<?xml version=\"1.0\"?>\n";
			echo '<failure>1</failure>'; // no file
		}
	}
	else {
		header('Content-type: application/xml');
		echo "<?xml version=\"1.0\"?>\n";
		echo '<failure>1</failure>'; // no file
	}
}

?>