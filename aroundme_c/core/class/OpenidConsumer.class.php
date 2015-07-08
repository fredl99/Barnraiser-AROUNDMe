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

include_once 'OpenidCommon.class.php';

class OpenidConsumer extends OpenidCommon {

	var $optional_fields = array(); // here you should put nickname, email, etc... (no prefix of openid)
	var $required_fields = array(); // here you should put nickname, email, etc... (no prefix of openid)
	
	function OpenidConsumer() {
		$this->_openid_dh_modulus = OPENID_DH_MODULUS;
		$this->_openid_dh_gen = OPENID_DH_GEN;
	}
	
	// this sends post to a server
	function associate() {
	
		$data_to_send = array();
	
		if (isset($this->openid_version) && $this->openid_version == 2) {
			$data_to_send['openid.ns'] = 'http://specs.openid.net/auth/2.0';
		}
		
		$data_to_send['openid.mode'] = 'associate';
		$data_to_send['openid.assoc_type'] = $this->association_type;
		$data_to_send['openid.session_type'] = $this->association_session_type;
		
		$_SESSION['association_type'] = $data_to_send['openid.assoc_type'];
		$_SESSION['association_session_type'] = $data_to_send['openid.session_type'];
		
		if ($this->association_session_type != 'no-encryption') {
			$data_to_send['openid.dh_modulus'] = base64_encode($this->btwocEncode($this->_openid_dh_modulus)); 
			$data_to_send['openid.dh_gen'] = base64_encode($this->btwocEncode($this->_openid_dh_gen));
			$data_to_send['openid.dh_consumer_public'] = $this->dh_public();
		}
		
		$result = $this->_send($data_to_send);
		
		if ($result) { 
			$data_to_return = array(); 
			foreach(explode("\n", trim($result)) as $key => $r) {
				$tmp = explode(':', $r);
				if (isset($tmp[0], $tmp[1])) {
					$data_to_return[$tmp[0]] = $tmp[1]; // we need to store this in a smart way later...
				}
				else {
					return 0;
				}
			}
			
			if (isset($data_to_return['assoc_handle'])) {
				$_SESSION['openid_assoc_handle'] = $data_to_return['assoc_handle'];
			}
			else { // no handle was sent, so something is wrong
				// new code (080205)
				if (isset($data_to_return['mode']) && $data_to_return['mode'] == 'error') {
					if (isset($data_to_return['error_code']) && $data_to_return['error_code'] == 'unsupported-type') {
						if (isset($data_to_return['assoc_type'])) {
							$this->association_type = $data_to_return['assoc_type'];
						}
						else {
							$this->association_type = 'HMAC-SHA1';
						}
			      
						if (isset($data_to_return['session_type'])) {
							$this->association_session_type = $data_to_return['session_type'];
						}
						else {
							$this->association_session_type = 'no-encryption';
						}
						return $this->associate();
					}
				}
				return 0; // failed to associate
			}
 
			if ($this->association_session_type != 'no-encryption') {
				$enc_mac_key = base64_decode($data_to_return['enc_mac_key']);
				$composite_key = bcpowmod($this->btwocDecode(base64_decode($data_to_return['dh_server_public'])), $_SESSION['openid_secret_key'], $this->_openid_dh_modulus);
			
			if ($data_to_send['openid.assoc_type'] == 'HMAC-SHA256') {
				$hash_function = 'sha256';
			}
			elseif ($data_to_send['openid.assoc_type'] == 'HMAC-SHA1') {
				$hash_function = 'sha1';
			}
			else {
				$hash_function = 'sha1';
			}
			
			  $sha_composite_key = hash($hash_function, $this->btwocEncode($composite_key), true);

			  $mac_key = '';
			
			  for ($i = 0; $i < strlen($enc_mac_key); $i++) {
				  $mac_key .= chr(ord($enc_mac_key[$i]) ^ ord($sha_composite_key[$i]));
			  }

			  $_SESSION['openid_mac_key'] = base64_encode($mac_key); // store the decrypted mac-key here
			  $_SESSION['openid_enc_mac_key'] = $enc_mac_key; // for debugging. Not really neccesary...?
			  return 1;
			}
			else {
			  /* DUMB MODE HERE */
			   $_SESSION['openid_mac_key'] = $data_to_return['mac_key'];
			   $_SESSION['openid_assoc_handle'] = $data_to_return['assoc_handle'];
			  return 1;
			}
		}
		return 0;
	}
	
	// this function is far away from done. Should be completly rewritten to meet 2.0 spec.
	function checkid_setup() {
		$data_to_send = array();
		
		if (isset($this->openid_version) && $this->openid_version == 2) {
		  $data_to_send['openid.ns'] = 'http://specs.openid.net/auth/2.0';
		}
		
		$data_to_send['openid.mode'] = 'checkid_setup';
		$data_to_send['openid.identity'] = $this->openid_url;
		
		if (isset($this->openid_version) && $this->openid_version == 2) {
		  $data_to_send['openid.claimed_id'] = $this->openid_url; // check this later
		}
		
		$data_to_send['openid.assoc_handle'] = $_SESSION['openid_assoc_handle'];
		
		if (isset($this->openid_return_to)) {
			$data_to_send['openid.return_to'] = $this->openid_return_to;
		}
		else {
			$data_to_send['openid.return_to'] = $this->openid_prefix . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
		}

		if (isset($this->openid_realm)) {
			$openid_realm = $this->openid_realm;
		}
		else {
			$openid_realm = $this->openid_prefix . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
		}

		if (isset($this->openid_version) && $this->openid_version == 2) {
			$data_to_send['openid.realm'] = $openid_realm;
		}
		else {
			$data_to_send['openid.trust_root'] = $openid_realm;
		}
		
		if (!empty($this->optional_fields)) {
			$data_to_send['openid.sreg.optional'] = implode(',', $this->optional_fields);
		}
		
		if (!empty($this->required_fields)) {
			$data_to_send['openid.sreg.required'] = implode(',', $this->required_fields);
		}
		
		// $this->openid_url_server points to 'the server' (which can be the same url as identity, but
		// it doesnt need to be that)
		// $this->openid_url_server probably needs to be normalized.
		
		$s = '?';
		if (strpos($this->openid_url_server, $s)) {
			$s = '&';
		}
		
		header('location: ' . $this->openid_url_server . $s . http_build_query($data_to_send));
		exit;
	}
	
	// function validates the decrypted mac-key with recevied signature.
	// this function is probably far from done yet.
	function id_res() {
	
		$this->association_type = $_SESSION['association_type'];
		$this->association_session_type = $_SESSION['association_session_type'];

		$tokens = '';
		$signed = explode (',', $_GET['openid_signed']);
		foreach($signed as $key => $v) {
			$tokens .=  $v . ':' . $_GET['openid_' . str_replace('.', '_', $v)] . "\n"; //do we need to rewrite this?
		}

		// with the hmac-function we check if there was a match using the mac-key+tokens (above) to the signature
		// we got from the server
		if (base64_encode($this->hmac(base64_decode($_SESSION['openid_mac_key']), $tokens)) == $_GET['openid_sig']) {
			// match ok. proceed from here
			return true;
		}
		else {
			// signature not met.
			return false;
		}
	}
	
	// This function should do lookup+validation and set some
	// private vars to this class. Lots of stuff to do here.
	function discover($openid_url) {
		
		$openid_headers = @get_headers($openid_url);
		if ($openid_headers[0] == 'HTTP/1.1 200 OK' || $openid_headers[0] == 'HTTP/1.0 200 OK') {
			$openid_content = file_get_contents($openid_url);
			
			$this->openid_url = $openid_url;
			
			// OpenID 2.0
			$pattern = "/<link rel=\"openid2.local_id\" href=\"(.*?)\"/";
			
			if (preg_match($pattern, $openid_content, $matches)) {
				// openid delegation
				if (!empty($matches[1]) && $matches[1] != $openid_url) {
					return $this->discover($matches[1]);
				}
			}
			
			// OpenID 1.1
			$pattern = "/<link rel=\"openid.delegate\" href=\"(.*?)\"/";
			
			if (preg_match($pattern, $openid_content, $matches)) {
				// openid delegation
				if (!empty($matches[1]) && $matches[1] != $openid_url) {
					return $this->discover($matches[1]);
				}
			}
			
			$pattern2 = "/<link rel=\"openid2.provider\" href=\"(.*?)\"/";
			$pattern1 = "/<link rel=\"openid.server\" href=\"(.*?)\"/";
			
			if (preg_match($pattern2, $openid_content, $matches)) {
				$this->openid_url_server = $matches[1];
				$this->openid_version = 2;
			}
			elseif (preg_match($pattern1, $openid_content, $matches)) {
				$this->openid_url_server = $matches[1];
			}
			else {
				$this->openid_url_server = $this->openid_url;
			}
			
			/* continue... we want to check it $openid_url indeed is an openid-url + some othe stuff */
			return 1;
		}
		else {
			$GLOBALS['am_error_log'][] = array('openid_discovery_failed');
			return 0;
		}
	}
}

?>
