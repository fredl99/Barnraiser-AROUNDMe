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

function gen_maptcha() {
	$numbers = array();
	$numbers['ascii'] = array(0,1,2,3,4,5,6,7,8,9,10);
	$numbers['words'] = array('zero','one','two','three','four','five','six','seven','eight','nine','ten');

	$operators = array();
	$operators['ascii'] = array('+','-','*');
	$operators['words'] = array('plus','minus','times');
	
	$_SESSION['maptcha'] = "";
	
	$m = 'ascii';
	if (rand(0,1)) {
		$m = 'words';
	}
	$n1 = rand(0, count($numbers[$m])-1);
	
	$x = $numbers[$m][$n1];
	
	$m = 'ascii';
	if (rand(0,1)) {
		$m = 'words';
	}
	$n2 = rand(0, count($numbers[$m])-1);
	
	$y = $numbers[$m][$n2];
	
	$m = 'ascii';
	if (rand(0,1)) {
		$m = 'words';
	}
	$n3 = rand(0, count($operators[$m])-1);
	
	$o = $operators[$m][$n3];
	eval('$_SESSION[\'maptcha\']=' . intval($numbers['ascii'][$n1]) . $operators['ascii'][$n3] . intval($numbers['ascii'][$n2]) . ';');
	
	if (rand(0,1)) {
		return 'Please give me the answer of ' . $x . ' ' . $o . ' ' .$y;
	}
	elseif (rand(0,1)) {
		return 'Try to solve this: ' . $x . ' ' . $o . ' ' .$y;
	}
	elseif (rand(0,1)) {
		return 'What\'s this: ' . $x . ' ' . $o . ' ' .$y;
	}
	else {
		return 'You have to calculate this small excercise ' . $x . ' ' . $o . ' ' .$y;
	}
}

function match_maptcha($answer) {
	return intval($answer) == intval($_SESSION['maptcha']);
}

?>