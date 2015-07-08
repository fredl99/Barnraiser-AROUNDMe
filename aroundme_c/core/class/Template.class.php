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



class Template {
	var $vars; // contains all template variables
	

	// the constructor
	// Tom Calthrop, 26th March 2007
	//
	function Template() {
	
	} // EO Constructor


	// Set
	// Tom Calthrop, 26th March 2007
	// sets template variable
	//
	function set($key, $value) {
		$this->vars[$key] = $value;
	} // EO set


	// Set
	// Tom Calthrop, 26th March 2007
	// output buffers file
	//
	function fetch($file) {
		if (!empty($this->vars)) {
			extract($this->vars);
		}
		ob_start();
		include($file);
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	} // EO fetch


	// Parse
	// Tom Calthrop, 26th March 2007
	// output buffers file
	//
	function parse($content) {
		if (!empty($this->vars)) {
			extract($this->vars);
		}
		ob_start();
		eval("?>".$content);
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	} // EO fetch


	// render any database information for screen display
	function am_render($str) {

		$str = str_replace("<p>", "", $str);
		$str = str_replace("</p>", "", $str);
		$str = str_replace("<br />", "\n", $str);
		
		return trim($str);
	}
	
	// sebastian öblom, 9th october 2007
	// a simple paging method. use extension for different paging
	// on the same page.
	function paging($total, $display, $url_base, $extension='') {

		if ($total <= $display) {
			return "";
		}

		if (isset($_GET['_frm' . $extension]) && is_numeric($_GET['_frm' . $extension])) {
			$from = $_GET['_frm' . $extension];
		}
		else {
			$from = 0;
		}
	
		$to = $from + $display;
	
		$listnav = "<div class=\"listnav\">";

		if (substr($url_base, -3, 3) == "php") {
			$url_base = $url_base . "?";
		}
		else {
			$url_base = $url_base . "&";
		}
	
		if ($from == 0) {
			$listnav .= "<a href=\"". $url_base . "_frm" . $extension . "=0\" class=\"active\">&laquo;&laquo;</a> ";
		}
		else {
			$listnav .= "<a href=\"". $url_base . "_frm" . $extension . "=0\">&laquo;&laquo;</a> ";
		}
	
		if ($from == 0) {
			$listnav .= "<a href=\"". $url_base . "_frm" . $extension . "=0\" class=\"active\">&laquo;</a> ";
		}
		else {
			$listnav .= "<a href=\"". $url_base . "_frm" . $extension . "=" . ($from - $display) ."\">&laquo;</a> ";
		}
	
		for ($i = 0; $i <= ceil($total/($display))-1; $i++) {
			if ($display*$i >= $from && $display*$i < $to) {
				$listnav .= "<a href=\"" . $url_base . "_frm" . $extension . "=" . $i*$display . "\" class=\"active\">" . (int) ($i+1) . "</a> ";
			}
			else {
				$listnav .= "<a href=\"" . $url_base . "_frm" . $extension . "=" . $i*$display . "\">" . (int) ($i+1) . "</a> ";
			}
		}
	
		if ($from <= $total && $to >= $total) {
			$listnav .= "<a href=\"". $url_base . "_frm" . $extension . "=" . $from . "\" class=\"active\">&raquo;</a> ";
		}
		else {
			$listnav .= "<a href=\"". $url_base . "_frm" . $extension . "=" . $to ."\">&raquo;</a> ";
		}
	
		if ($from <= $total && $to >= $total) {
			$listnav .= "<a href=\"". $url_base . "_frm" . $extension . "=" . ($i-1)*$display ."\" class=\"active\">&raquo;&raquo;</a> ";
		}
		else {
			$listnav .= "<a href=\"". $url_base . "_frm" . $extension . "=" . ($i-1)*$display ."\">&raquo;&raquo;</a> ";
		}
	
		$listnav .= "</div>";
	
		return $listnav;
	}

	function getLanguage($key) {
		if (@array_key_exists($key, $this->lang)) {
			echo $this->lang[$key];
		}
		else {
			echo "MISSING LANGUAGE PACK ITEM";
			$GLOBALS['am_error_log'][] = array ('language_item_missing', $key);
		}
	}
}
?>
