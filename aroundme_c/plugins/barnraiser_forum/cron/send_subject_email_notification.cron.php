<?php

// ---------------------------------------------------------------------
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
// --------------------------------------------------------------------

/*
This cron job selects replies that have been added to the forum of which
notification has been requested.

If the notify table is not empty it selects 25 people to send to. If there
were more than 25 people it leaves a flag in the notify table so that
25-50 can be selected next time.

Note: the webspace owner MUST set the default webpage (in forum-maintain)
for this script to work.
*/

$path = dirname(__FILE__) . "/";

include ($path . "../../../core/config/core.config.php");

session_name($core_config['php']['session_name']);
session_start();


// SETUP DATABASE ------------------------------------------------------
require($path . '../../../core/class/Db.class.php');
$db = new Database($core_config['db']);


// WE SELECT QUEUED NOTIFICATION ITEMS
$query = "
	SELECT sn.reply_id, sn.webspace_id, sn.subject_id,
	sn.last_connection_id, sn.notification_create_datetime,
	ws.webspace_unix_name, ws.webspace_title, s.subject_title,
	r.reply_body, c.connection_nickname, ws.language_code, 
	UNIX_TIMESTAMP(r.reply_create_datetime) as reply_create_datetime,
	wp.webpage_name, styl.stylesheet_body 
	FROM
	" . $db->prefix . "_plugin_forum_subject_notify sn, " . $db->prefix . "_webspace ws,
	" . $db->prefix . "_plugin_forum_subject s, " . $db->prefix . "_plugin_forum_reply r,
	" . $db->prefix . "_connection c, " . $db->prefix . "_plugin_forum_preference fp,
	" . $db->prefix . "_webpage wp, " . $db->prefix . "_stylesheet styl 
	WHERE
	ws.stylesheet_id=styl.stylesheet_id AND
	sn.reply_id=r.reply_id AND
	sn.subject_id=s.subject_id AND
	r.connection_id=c.connection_id AND 
	sn.webspace_id=ws.webspace_id AND
	fp.webspace_id=sn.webspace_id AND
	fp.default_webpage_id=wp.webpage_id"
;

$notification_items = $db->Execute($query);

if (!empty($notification_items)) {
	// SETUP MAIL CLASS
	require_once($path . '../../../core/class/Mail/class.phpmailer.php');
	$mail->From = $core_config['mail']['email_address'];
	
	$total_emails_sent = 0;
	$max_emails = $core_config['mail']['max_bulk_send']; // normally 25


	// foreach notification item we select the recipients and create the email
	foreach ($notification_items as $key_ni => $ni):
		// select the recipients
		$query = "
			SELECT c.connection_id, c.connection_nickname, c.connection_email, c.connection_create_datetime
			FROM " . $db->prefix . "_connection c, " . $db->prefix . "_plugin_forum_subject_track st 
			WHERE 
			c.connection_id=st.connection_id AND 
			c.webspace_id=st.webspace_id AND
			st.notification=1 AND
			st.subject_id=" . $ni['subject_id'] . " AND
			st.connection_id>" . $ni['last_connection_id'] . "
			ORDER BY st.connection_id"
		;

		$recipients = $db->Execute($query);

		if (!empty($recipients)) {
			// select the reply, format the email and send
			if (isset($ni['language_code']) && array_key_exists($ni['language_code'], $core_config['language']['pack'])) {
				$language_code = $ni['language_code'];
			}
			else {
				$language_code = $core_config['language']['default'];
			}
			
			$email_content_html = file_get_contents($path . "../language/" . $language_code . "/email/send_subject_notification.html.php");
			$email_content_txt = file_get_contents($path . "../language/" . $language_code . "/email/send_subject_notification.txt.php");

			
			$mail->FromName = $ni['webspace_title'];
			
			$webspace_url = str_replace('REPLACE', $ni['webspace_unix_name'], $core_config['am']['domain_replace_pattern']);
			
			$email_content_html = str_replace('AM_KEYWORD_WEBSPACE_URL', $webspace_url, $email_content_html);
			$email_content_txt = str_replace('AM_KEYWORD_WEBSPACE_URL', $webspace_url, $email_content_txt);

			$email_content_html = str_replace('AM_KEYWORD_WEBSPACE_TITLE', $ni['webspace_title'], $email_content_html);
			$email_content_txt = str_replace('AM_KEYWORD_WEBSPACE_TITLE', $ni['webspace_title'], $email_content_txt);

			// append CSS in html version only
			$email_content_html = str_replace('AM_KEYWORD_CSS', $ni['stylesheet_body'], $email_content_html);
			
			$email_title = "Notification: " . $ni['subject_title'];

			$mail->Subject = $email_title;

			$email_content_html = str_replace('AM_KEYWORD_EMAIL_TITLE', $email_title, $email_content_html);
			$email_content_txt = str_replace('AM_KEYWORD_EMAIL_TITLE', $email_title, $email_content_txt);

			$email_content_html = str_replace('AM_KEYWORD_SUBJECT_TITLE', $ni['subject_title'], $email_content_html);
			$email_content_txt = str_replace('AM_KEYWORD_SUBJECT_TITLE', $ni['subject_title'], $email_content_txt);
			
			$email_content_html = str_replace('AM_KEYWORD_SUBJECT_REPLY_NICKNAME', $ni['connection_nickname'], $email_content_html);
			$email_content_txt = str_replace('AM_KEYWORD_SUBJECT_REPLY_NICKNAME', $ni['connection_nickname'], $email_content_txt);

			$email_content_html = str_replace('AM_KEYWORD_SUBJECT_REPLY_DATETIME', strftime("%d %b %G %H:%M", $ni['reply_create_datetime']), $email_content_html);
			$email_content_txt = str_replace('AM_KEYWORD_SUBJECT_REPLY_DATETIME', strftime("%d %b %G %H:%M", $ni['reply_create_datetime']), $email_content_txt);

			$email_content_html = str_replace('AM_KEYWORD_SUBJECT_REPLY_BODY', $ni['reply_body'], $email_content_html);
			$email_content_txt = str_replace('AM_KEYWORD_SUBJECT_REPLY_BODY', stripslashes(strip_tags($ni['reply_body'])), $email_content_txt);

			$subject_url = $webspace_url . "index.php?wp=" . $ni['webpage_name'] . "&subject_id=" . $ni['subject_id'] . "&reply_id=" . $ni['reply_id'];

			$email_content_html = str_replace('AM_KEYWORD_SUBJECT_URL', $subject_url, $email_content_html);
			$email_content_txt = str_replace('AM_KEYWORD_SUBJECT_URL', $subject_url, $email_content_txt);

			// append any content based local links
			$email_content_html = str_replace('href="index.php', 'href="' . $webspace_url . 'index.php', $email_content_html);
			$email_content_txt = str_replace('href="index.php', 'href="' . $webspace_url . 'index.php', $email_content_txt);

			
			foreach ($recipients as $keyr => $r):
				// we complete email formatting, send and update total sent
				$remove_notification_code = $ni['webspace_id'] . "-" . $ni['subject_id'] . "-" . md5($r['connection_create_datetime']);
				$remove_notification_url = $webspace_url . "plugins/barnraiser_forum/set_tracking_notification.php?rm_notify_id=" . $remove_notification_code;

				$recipient_email_content_html = str_replace('AM_KEYWORD_REMOVE_NOTIFICATION_URL', $remove_notification_url, $email_content_html);
				$recipient_email_content_txt = str_replace('AM_KEYWORD_REMOVE_NOTIFICATION_URL', $remove_notification_url, $email_content_txt);

				$recipient_email_content_html = str_replace('AM_SYS_KEYWORD_RECIPIENT_NICKNAME', $r['connection_nickname'], $recipient_email_content_html);
				$recipient_email_content_txt = str_replace('AM_SYS_KEYWORD_RECIPIENT_NICKNAME', $r['connection_nickname'], $recipient_email_content_txt);
				
				$recipient_email_content_html = utf8_decode($recipient_email_content_html);
				$recipient_email_content_txt = utf8_decode($recipient_email_content_txt);
				
				// SEND
				$mail->Body = $recipient_email_content_html;
				// non - HTML-version of the email
				$mail->AltBody   = $recipient_email_content_txt;
				
				// add new email-address to mailer-object
				$mail->ClearAddresses();
				$mail->AddAddress($r['connection_email'], $r['connection_nickname']);
				
				// Send email
				if(!$mail->Send()) {
					$log = "There has been a mail error sending newsletter (subject:" . $mail->Subject . ")to " . $r['connection_email'] . "," . $r['connection_nickname'] . ".";
				}
				
				// UPDATE TOTALS AND EXIT IF ON MAX
				$total_emails_sent++;

				if ($total_emails_sent >= $max_emails) {
					// we update and exit
					$query = "
						UPDATE " . $db->prefix . "_plugin_forum_subject_notify
						SET
						last_connection_id=" . $r['connection_id'] . " 
						WHERE 
						subject_id=" . $ni['subject_id'] . " AND 
						reply_id=" . $ni['reply_id'] . " AND 
						webspace_id=" . $ni['webspace_id']
					;
					
					$db->Execute($query);

					exit;
				}
				
			
			endforeach;

			// send for this notification was complete, hence we remove it
			$query = "
				DELETE FROM " . $db->prefix . "_plugin_forum_subject_notify
				WHERE
				subject_id=" . $ni['subject_id'] . " AND
				reply_id=" . $ni['reply_id'] . " AND
				webspace_id=" . $ni['webspace_id']
			;
			
			$db->Execute($query);
		}
	endforeach;

}

?>