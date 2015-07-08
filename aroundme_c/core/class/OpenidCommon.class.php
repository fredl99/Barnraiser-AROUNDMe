<?php

// ---------------------------------------------------------------------------------------------
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
// ---------------------------------------------------------------------------------------------


// some default values
define("OPENID_DH_MODULUS", '155172898181473697471232257763715539915724801966915404479707795314057629378541917580651227423698188993727816152646631438561595825688188889951272158842675419950341258706556549803580104870537681476726513255747040765857479291291572334510643245094715007229621094194349783925984760375594985848253359305585439638443');
define("OPENID_DH_GEN", 2);
define("OPENID_EXPIRES_IN", 10000000);
define("DEBUG", 0);

include 'OpenidStorageMysql.class.php';

class OpenidCommon {

	// see section 8.3 of specification
	var $association_type = 'HMAC-SHA1';
	//var $association_type = 'HMAC-SHA256';
	
	// see section 8.4 of specification
	var $association_session_type = 'DH-SHA1';
	//var $association_session_type = 'DH-SHA256';
	
	// nr of bytes in the hmac-function
	var $blocksize = 64;

	// All references to specification numbers refer to version 2.0 of the 
	// OpenID authentication specification unless otherwise stated.
	
	
	// constructor
	function OpenidCommon($core_config = null) {
		$this->storage = new OpenidStorageMysql($core_config['db']);
	}
	
	// Spec 4.2: Integer representations - Converts $n into a twos
	// compliment of a binary number (encoding it)
	function btwocEncode($long) {
		$cmp = bccomp($long, 0);

		if ($cmp == 0) {
			return "\x00";
		}

		$bytes = array();

		while (bccomp($long, 0) > 0) {
			array_unshift($bytes, bcmod($long, 256));
			$long = bcdiv($long, pow(2, 8));
		}

		if ($bytes && ($bytes[0] > 127)) {
			array_unshift($bytes, 0);
		}

		$string = '';
		foreach ($bytes as $byte) {
			$string .= pack('C', $byte);
		}

		return $string;
	}
	
	// Spec 4.2: Integer representations - Converts $n into a binary
	// from a twos compliment (decoding it)
	function btwocDecode($str) {
		$bytes = array_merge(unpack('C*', $str));
		$n = 0;

		foreach ($bytes as $byte) {
			$n = bcmul($n, pow(2, 8));
			$n = bcadd($n, $byte);
		}
		return $n;
	}
	
	// bitwise exclusive or function - either / or
	// takes 1100 and compares to 1001 to get 1010 
	function _xor($x, $y) {
		$a = '';
		for($i=0; $i < strlen($y); $i++) { 
			$a .= $x[$i] ^ $y[$i];
		}
		return $a;
	}
	
	// encryption-function... for more info read http://en.wikipedia.org/wiki/HMAC
	// is used when creating the signature where $key is assoc_handle/mac-key and $data is key-values (tokens)
	// (see 4.1.1 of specification)
	function hmac($key, $data) {
	
		switch($this->association_type) {
			case 'HMAC-SHA256':
				$hash_function = 'sha256';
			break;
			case 'HMAC-SHA1':
				$hash_function = 'sha1';
			break;
			default:
				$hash_function = 'sha1';
		}
		return hash_hmac($hash_function, $data, $key, true);
	}
	
	// calculates g^x mod p (x=secret number at server) and returns it encoded binary
	function dh_public() {
		$secret_key = '';
		for($i = 0; $i < rand(1, strlen($this->_openid_dh_modulus)-1); $i++) {
			if ($i == 0) {
				$secret_key .= rand(1, 9);
			}
			else {
				$secret_key .= rand(0, 9);
			}
		}
		$_SESSION['openid_secret_key'] = $secret_key;
		
		return base64_encode($this->btwocEncode(bcpowmod($this->_openid_dh_gen, $secret_key, $this->_openid_dh_modulus)));
	}
	
	// do we need this method?
	function destroy() {
		unset($_SESSION);
		session_destroy();
	}
	
	function server_url() {
		
		$prefix = 'http://';
		if (isset($_SERVER['HTTPS'])) {
			if (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == 1) {
				$prefix = 'https://';
			}
		}

		return $prefix . $_SERVER['SERVER_NAME'] . '/op.php';
	}
	
	// a simple debug-function.
	// remove this later.
	function _debug($arr=null) {
		if (!empty($arr)) {
			$f = 'debug_array.txt';
		}
		elseif (isset($_POST['openid_mode'])) {
			$f = 'debug_' . $_POST['openid_mode'] . '.txt';
		}
		elseif (isset($_GET['openid_mode'])) {
			$f = 'debug_' . $_GET['openid_mode'] . '.txt';
		}
		
		if (empty($arr)) {
			file_put_contents($f, microtime() . "\n\nGET\n\n" . implode("\n", explode('&', http_build_query($_GET))) . "\n\n\n\nPOST\n\n" . implode("\n", explode('&', http_build_query($_POST))));
		}
		else {
			$str = "";
			foreach($arr as $key => $v) {
				$str .= $key . ':' . $v . "\n";
			}
			file_put_contents($f, $str);
		}
	}
	
	// normalizes an url
	function normalize($url) {
	
		if (substr($url, 0, strlen('https://')) != 'https://') {
			if (substr($url, 0, strlen('http://')) != 'http://') {
				$url = 'http://' . $url;
			}
			$this->openid_prefix = 'http://';
		}
		else {
			$this->openid_prefix = 'https://';
		}
		
		if (substr($url, -9) == 'index.php') {
			$url = substr($url, 0, -9);
		}

		if (substr($url, -1) == '#') {
			$url = substr($url, 0, strlen($url) - 1);
		}
		
		if (strpos(substr($url, strlen($this->openid_prefix), strlen($url)), '/')) {
			// do nothing
		}
		elseif (strpos(substr($url, strlen($this->openid_prefix), strlen($url)), ':')) {
			// do nothing
		}
		else {
			$url .= '/';
		}

		return $url;
	}
	
	
	// curl-function that senda data to an openid-server
	function _send($data, $method = 'POST', $url=null) {
		
		if (!isset($url)) {
			$url = $this->openid_url_server;
		}
		
		$s = '?';
		if (strpos($url, $s)) {
			$s = '&';
		}
		
		if ($method == 'GET') {
			$url .= $s . http_build_query($data);
		}
	
		$curl = curl_init($url);
		
		if (!ini_get("safe_mode")) {
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		}
		
		if ($method == 'POST') {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		}
		else {
			curl_setopt($curl, CURLOPT_HTTPGET, true);
		}
		
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); // this solves the issues with the chunked encoding
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);
		
		if (curl_errno($curl) == 0){
			return $response;
		}
		else {
			return 0;
		}
	}
}

?>
