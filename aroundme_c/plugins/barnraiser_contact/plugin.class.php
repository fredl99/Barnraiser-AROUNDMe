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


class Plugin_barnraiser_contact {
	// storage and template instances should be passed by reference to this class
	
	var $level = 0; // the permission level requied to see an item
	var $attributes; // any block attributes passed to the class


	function block_email () {
		if (isset($_POST['barnraiser_contact_send_email'])) {
			// compose email
			if (empty($_POST['barnraiser_contact_subject'])) {
				$GLOBALS['am_error_log'][] = array('barnraiser_contact_no_subject');
			}
	
			if (empty($_POST['barnraiser_contact_message'])) {
				$GLOBALS['am_error_log'][] = array('barnraiser_contact_no_message');
			}

			//if (!isset($_SESSION['connection_id']) && isset($_SESSION['hash']) && $_SESSION['hash'] !== md5(strtoupper($_POST['captcha_text']))) {
			if (!isset($_SESSION['connection_id']) && !match_maptcha($_POST['maptcha_text'])) {
				$GLOBALS['am_error_log'][] = array("barnraiser_contact_captcha_mismatch");
			}
		
			if (empty($GLOBALS['am_error_log'])) {
		
				$this->am_mail->Subject = stripslashes(htmlspecialchars($_POST['barnraiser_contact_subject']));
		
				// COMPOSE MESSAGE
				$email_message = stripslashes(htmlspecialchars($_POST['barnraiser_contact_message']));
		
				if (!empty($_POST['barnraiser_contact_email'])) {
					$email_message .= "\n\nYou can reply to " . $_POST['barnraiser_contact_email'];
				}
		
				if (!empty($_SESSION['openid_identity'])) {
					$email_message .= "\n\nFrom OpenID " . $_SESSION['openid_identity'];
				}
		
				if (!empty($_SESSION['connection_id'])) {
					$email_message .= "\n\nProfile " . $this->url . "/index.php?t=network&conection_id=" . $_SESSION['connection_id'];
				}
	
				$email_message .= "\n\nThis mail was sent from " . $this->url;
		
				// HTML-version of the mail
				$html  = "<HTML><HEAD><TITLE></TITLE></HEAD>";
				$html .= "<BODY>";
				$html .= utf8_decode(nl2br($email_message));
				$html .= "</BODY></HTML>";
			
				$this->am_mail->Body = $html;
				// non - HTML-version of the email
				$this->am_mail->AltBody   = utf8_decode($email_message);

				
				if (!empty($_POST['barnraiser_contact_nickname'])) {
					$nickname = $_POST['barnraiser_contact_nickname'];
					$this->am_mail->FromName = $nickname;
				}
		
				if (!empty($_POST['barnraiser_contact_email'])) {
					$this->am_mail->From = $_POST['barnraiser_contact_email'];
					$this->am_mail->AddReplyTo($_POST['barnraiser_contact_email'], '');
				}
	
				// WE SEND TO THE RECIPIENTS
				$query = "
					SELECT recipient_email
					FROM " . $this->am_storage->prefix . "_plugin_contact_recipient
					WHERE
					webspace_id=" . AM_WEBSPACE_ID
				;
	
				$result = $this->am_storage->Execute($query);
	
				if (!empty($result[0])) {
					$this->am_mail->ClearAddresses();
		
					foreach($result as $key => $i):
						$this->am_mail->AddAddress($i['recipient_email'], '');
					endforeach;
		
					if($this->am_mail->Send()) {
						// sent
						$this->am_template->set('barnraiser_contact_email_sent', 1);
		
						if (!empty($_POST['barnraiser_contact_copy']) && isset($_POST['barnraiser_contact_email'])) {
							$this->am_mail->ClearAddresses();
							$this->am_mail->AddAddress($_POST['barnraiser_contact_email'], '');
		
							if($this->am_mail->Send()) {}
						}
					}
					else {
						$GLOBALS['am_error_log'][] = array('barnraiser_contact_email_not_sent', $this->am_mail->ErrorInfo);
					}
				}
			}
		}

		// get email recipients
		$query = "
			SELECT recipient_email 
			FROM " . $this->am_storage->prefix . "_plugin_contact_recipient
			WHERE
			webspace_id=" . AM_WEBSPACE_ID
		;
	
		$result = $this->am_storage->Execute($query);
	
		if (!empty($result[0])) {
			$this->am_template->set('barnraiser_contact_email_ok', 1);
		}
		
		$maptcha = gen_maptcha();
		$this->am_template->set ('maptcha', $maptcha);
	}
}


$plugin_barnraiser_contact = new Plugin_barnraiser_contact();
$plugin_barnraiser_contact->am_storage = &$db;
$plugin_barnraiser_contact->am_template = &$body;


// setup mail
require_once('core/class/Mail/class.phpmailer.php');
$plugin_barnraiser_contact->am_mail = &$mail;

$plugin_barnraiser_contact->url = str_replace('REPLACE', $ws->webspace_unix_name, $core_config['am']['domain_replace_pattern']);

// ASSIGN PERMISSIONS
$plugin_permissions['barnraiser_contact']['manage_contact'] = $core_config['group']['maintainer'];

?>