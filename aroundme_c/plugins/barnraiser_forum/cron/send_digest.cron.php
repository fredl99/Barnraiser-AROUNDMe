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
This cron job sends out a daily, weekly or monthly digest newsletter.
It contains tracked items with reply count since you last logged in and
new subjects.

In the digest table we select the first send dates that are older
than now and send the digest. At each send we update the send date to
the next date in the future.

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


// WE SELECT 25 PEOPLE TO SEND A DIGEST TO
$query = "
	SELECT d.connection_id, d.webspace_id, d.digest_frequency,
	UNIX_TIMESTAMP(d.send_datetime) AS send_datetime, ws.webspace_unix_name, ws.webspace_title,
	c.connection_nickname, c.connection_email, wp.webpage_name,
	c.connection_last_datetime, ws.language_code, 
	UNIX_TIMESTAMP(c.connection_last_datetime) as u_connection_last_datetime,
	c.connection_create_datetime 
	FROM " . $db->prefix . "_plugin_forum_digest d, " . $db->prefix . "_webspace ws,
	" . $db->prefix . "_connection c, " . $db->prefix . "_plugin_forum_preference fp,
	" . $db->prefix . "_webpage wp 
	WHERE
	d.send_datetime<now() AND 
	d.connection_id=c.connection_id AND
	d.webspace_id=ws.webspace_id AND
	fp.webspace_id=d.webspace_id AND
	fp.default_webpage_id=wp.webpage_id AND
	c.connection_email IS NOT NULL"
;

$digest_recipients = $db->Execute($query, (int) $core_config['mail']['max_bulk_send']); // max_bulk_send is normally 25

if (!empty($digest_recipients)) {
	// SETUP MAIL CLASS
	require_once($path . '../../../core/class/Mail/class.phpmailer.php');
	$mail->From = $core_config['mail']['email_address'];
	
	$total_emails_sent = 0;
	$max_emails = $core_config['mail']['max_bulk_send'];


	// foreach digest recipient we select the subjects and create the email
	foreach ($digest_recipients as $key_dr => $dr):

		if (isset($dr['language_code']) && array_key_exists($dr['language_code'], $core_config['language']['pack'])) {
			$language_code = $dr['language_code'];
		}
		else {
			$language_code = $core_config['language']['default'];
		}
		
		// obtain email templates
		$email_content_html = file_get_contents($path . "../language/" . $language_code . "/email/send_digest.html.php");
		$email_content_txt = file_get_contents($path . "../language/" . $language_code . "/email/send_digest.txt.php");
	
		// pull out parts for email
		$email_content_html_parts = getEmailContentParts($email_content_html);
		$email_content_txt_parts = getEmailContentParts($email_content_txt);
		

		$mail->FromName = $dr['webspace_title'];
			
		$webspace_url = str_replace('REPLACE', $dr['webspace_unix_name'], $core_config['am']['domain_replace_pattern']);

		$email_content_html = str_replace('AM_KEYWORD_WEBSPACE_URL', $webspace_url, $email_content_html);
		$email_content_txt = str_replace('AM_KEYWORD_WEBSPACE_URL', $webspace_url, $email_content_txt);

		$email_content_html = str_replace('AM_KEYWORD_WEBSPACE_TITLE', $dr['webspace_title'], $email_content_html);
		$email_content_txt = str_replace('AM_KEYWORD_WEBSPACE_TITLE', $dr['webspace_title'], $email_content_txt);

		$email_title = "Digest: " . $dr['webspace_title'];

		$mail->Subject = $email_title;

		$email_content_html = str_replace('AM_KEYWORD_EMAIL_TITLE', $email_title, $email_content_html);
		$email_content_txt = str_replace('AM_KEYWORD_EMAIL_TITLE', $email_title, $email_content_txt);

		$email_content_html = str_replace('AM_SYS_KEYWORD_RECIPIENT_NICKNAME', $dr['connection_nickname'], $email_content_html);
		$email_content_txt = str_replace('AM_SYS_KEYWORD_RECIPIENT_NICKNAME', $dr['connection_nickname'], $email_content_txt);

		$email_content_html = str_replace('AM_KEYWORD_LAST_CONNECTION_DATETIME', strftime("%d %b %G %H:%M", $dr['u_connection_last_datetime']), $email_content_html);
		$email_content_txt = str_replace('AM_KEYWORD_LAST_CONNECTION_DATETIME', strftime("%d %b %G %H:%M", $dr['u_connection_last_datetime']), $email_content_txt);

		$remove_digest_code = $dr['webspace_id'] . "-" . $dr['connection_id'] . "-" . md5($dr['connection_create_datetime']);
		$remove_digest_url = $webspace_url . "/plugins/barnraiser_forum/set_tracking_notification.php?rm_digest_id=" . $remove_digest_code;

		$email_content_html = str_replace('AM_KEYWORD_REMOVE_DIGEST_URL', $remove_digest_url, $email_content_html);
		$email_content_txt = str_replace('AM_KEYWORD_REMOVE_DIGEST_URL', $remove_digest_url, $email_content_txt);
		
		
		// select tracked subjects
		$query ="
			SELECT st.subject_id, s.subject_title,
			UNIX_TIMESTAMP(s.subject_create_datetime) as subject_create_datetime,
			c.connection_nickname 
			FROM " . $db->prefix . "_plugin_forum_subject_track st, " . $db->prefix . "_plugin_forum_subject s, " . $db->prefix . "_connection c
			WHERE
			st.connection_id=c.connection_id AND 
			st.connection_id=" . $dr['connection_id'] . " AND
			st.webspace_id=" . $dr['webspace_id'] . " AND
			st.subject_id=s.subject_id"
		;
		
		$tracked_subjects = $db->Execute($query);
		
		if (!empty($tracked_subjects)) {
			
			$tracked_subjects_html = "";
			$tracked_subjects_txt = "";

			foreach ($tracked_subjects as $key_sbjt => $sbjt):
				$tracked_subject_html = $email_content_html_parts['tracked_subjects_loop'][1];
				$tracked_subject_txt = $email_content_txt_parts['tracked_subjects_loop'][1];
				
				$tracked_subject_html = str_replace('AM_KEYWORD_TRACKED_SUBJECT_TITLE', $sbjt['subject_title'], $tracked_subject_html);
				$tracked_subject_txt = str_replace('AM_KEYWORD_TRACKED_SUBJECT_TITLE', $sbjt['subject_title'], $tracked_subject_txt);

				$url = $webspace_url . "/index.php?wp=" . $dr['webpage_name'] . "&subject_id=" . $sbjt['subject_id'];

				$tracked_subject_html = str_replace('AM_KEYWORD_TRACKED_SUBJECT_URL', $url, $tracked_subject_html);
				$tracked_subject_txt = str_replace('AM_KEYWORD_TRACKED_SUBJECT_URL', $url, $tracked_subject_txt);
				
				$tracked_subject_html = str_replace('AM_KEYWORD_TRACKED_SUBJECT_AUTHOR', $sbjt['connection_nickname'], $tracked_subject_html);
				$tracked_subject_txt = str_replace('AM_KEYWORD_TRACKED_SUBJECT_AUTHOR', $sbjt['connection_nickname'], $tracked_subject_txt);

				$tracked_subject_html = str_replace('AM_KEYWORD_TRACKED_SUBJECT_CREATE_DATETIME', strftime("%d %b %G %H:%M", $sbjt['subject_create_datetime']), $tracked_subject_html);
				$tracked_subject_txt = str_replace('AM_KEYWORD_TRACKED_SUBJECT_CREATE_DATETIME', strftime("%d %b %G %H:%M", $sbjt['subject_create_datetime']), $tracked_subject_txt);
				
				
				// select the total number of replies
				$query = "
					SELECT count(reply_id) as total
					FROM " . $db->prefix . "_plugin_forum_reply
					WHERE
					subject_id=" . $sbjt['subject_id']
				;
				
				$total_replies = $db->Execute($query);

				if (!empty($total_replies[0]['total'])) {
					$total_replies = $total_replies[0]['total'];
				}
				else {
					$total_replies = 0;
				}
				
				$tracked_subject_html = str_replace('AM_KEYWORD_TRACKED_SUBJECT_REPLIES_TOTAL', $total_replies, $tracked_subject_html);
				$tracked_subject_txt = str_replace('AM_KEYWORD_TRACKED_SUBJECT_REPLIES_TOTAL', $total_replies, $tracked_subject_txt);


				// select the number of replies since last connection
				$query = "
					SELECT count(reply_id) as total
					FROM " . $db->prefix . "_plugin_forum_reply
					WHERE
					subject_id=" . $sbjt['subject_id'] . " AND
					reply_create_datetime>" . $db->qstr($dr['connection_last_datetime'])
				;
				
				$total_new_replies = $db->Execute($query);

				if (!empty($total_new_replies[0]['total'])) {
					$total_new_replies = $total_new_replies[0]['total'];
				}
				else {
					$total_new_replies = 0;
				}
				
				$tracked_subject_html = str_replace('AM_KEYWORD_TRACKED_SUBJECT_REPLIES', $total_new_replies, $tracked_subject_html);
				$tracked_subject_txt = str_replace('AM_KEYWORD_TRACKED_SUBJECT_REPLIES', $total_new_replies, $tracked_subject_txt);
				
				
				$tracked_subjects_html .= $tracked_subject_html;
				$tracked_subjects_txt .= $tracked_subject_txt;
			endforeach;

			$email_content_html = str_replace($email_content_html_parts['tracked_subjects'][0], $email_content_html_parts['tracked_subjects'][1], $email_content_html);
			$email_content_txt = str_replace($email_content_txt_parts['tracked_subjects'][0], $email_content_txt_parts['tracked_subjects'][1], $email_content_txt);
			
			$email_content_html = str_replace($email_content_html_parts['tracked_subjects_loop'][0], $tracked_subjects_html, $email_content_html);
			$email_content_txt = str_replace($email_content_txt_parts['tracked_subjects_loop'][0], $tracked_subjects_txt, $email_content_txt);
			
			
		}
		else {
			$email_content_html = str_replace($email_content_html_parts['tracked_subjects'][0], '', $email_content_html);
			$email_content_txt = str_replace($email_content_txt_parts['tracked_subjects'][0], '', $email_content_txt);
		}

		// WORK OUT THE PERIOD TO LOOK FROM
		$search_period_timestamp = $dr['send_datetime'] - ($dr['digest_frequency'] * 24 * 60 * 60);
		$search_period =  date("Y-m-d H:i:s", $search_period_timestamp);
		
		// GET NEW SUBJECTS SINCE LAST DIGEST
		$query ="
			SELECT s.subject_id, s.subject_title,
			UNIX_TIMESTAMP(s.subject_create_datetime) as subject_create_datetime,
			c.connection_nickname 
			FROM " . $db->prefix . "_plugin_forum_subject s, " . $db->prefix . "_connection c
			WHERE 
			s.connection_id=c.connection_id AND 
			s.webspace_id=" . $dr['webspace_id'] . " AND
			s.subject_create_datetime>" . $db->qstr($search_period) . "
			ORDER BY s.subject_create_datetime DESC"
		;
		
		$new_subjects = $db->Execute($query, 10);
		
		if (!empty($new_subjects)) {
			
			$new_subjects_html = "";
			$new_subjects_txt = "";

			foreach ($new_subjects as $key_sbjt => $sbjt):
				$new_subject_html = $email_content_html_parts['new_subjects_loop'][1];
				$new_subject_txt = $email_content_txt_parts['new_subjects_loop'][1];
				
				$new_subject_html = str_replace('AM_KEYWORD_NEW_SUBJECT_TITLE', $sbjt['subject_title'], $new_subject_html);
				$new_subject_txt = str_replace('AM_KEYWORD_NEW_SUBJECT_TITLE', $sbjt['subject_title'], $new_subject_txt);

				$url = $webspace_url . "/index.php?wp=" . $dr['webpage_name'] . "&subject_id=" . $sbjt['subject_id'];

				$new_subject_html = str_replace('AM_KEYWORD_NEW_SUBJECT_URL', $url, $new_subject_html);
				$new_subject_txt = str_replace('AM_KEYWORD_NEW_SUBJECT_URL', $url, $new_subject_txt);
				
				$new_subject_html = str_replace('AM_KEYWORD_NEW_SUBJECT_AUTHOR', $sbjt['connection_nickname'], $new_subject_html);
				$new_subject_txt = str_replace('AM_KEYWORD_NEW_SUBJECT_AUTHOR', $sbjt['connection_nickname'], $new_subject_txt);

				$new_subject_html = str_replace('AM_KEYWORD_NEW_SUBJECT_CREATE_DATETIME', strftime("%d %b %G %H:%M", $sbjt['subject_create_datetime']), $new_subject_html);
				$new_subject_txt = str_replace('AM_KEYWORD_NEW_SUBJECT_CREATE_DATETIME', strftime("%d %b %G %H:%M", $sbjt['subject_create_datetime']), $new_subject_txt);
				
				
				// select the total number of replies
				$query = "
					SELECT count(reply_id) as total
					FROM " . $db->prefix . "_plugin_forum_reply
					WHERE
					subject_id=" . $sbjt['subject_id']
				;
				
				$total_replies = $db->Execute($query);

				if (!empty($total_replies[0]['total'])) {
					$total_replies = $total_replies[0]['total'];
				}
				else {
					$total_replies = 0;
				}
				
				$new_subject_html = str_replace('AM_KEYWORD_NEW_SUBJECT_REPLIES_TOTAL', $total_replies, $new_subject_html);
				$new_subject_txt = str_replace('AM_KEYWORD_NEW_SUBJECT_REPLIES_TOTAL', $total_replies, $new_subject_txt);
				
				$new_subjects_html .= $new_subject_html;
				$new_subjects_txt .= $new_subject_txt;
				
			endforeach;
			$email_content_html = str_replace($email_content_html_parts['new_subjects'][0], $email_content_html_parts['new_subjects'][1], $email_content_html);
			$email_content_txt = str_replace($email_content_txt_parts['new_subjects'][0], $email_content_txt_parts['new_subjects'][1], $email_content_txt);
			
			$email_content_html = str_replace($email_content_html_parts['new_subjects_loop'][0], $new_subjects_html, $email_content_html);
			$email_content_txt = str_replace($email_content_txt_parts['new_subjects_loop'][0], $new_subjects_txt, $email_content_txt);
		}
		else {
			$email_content_html = str_replace($email_content_html_parts['new_subjects'][0], '', $email_content_html);
			$email_content_txt = str_replace($email_content_txt_parts['new_subjects'][0], '', $email_content_txt);
		}

		// GET LATEST REPLIES SINCE LAST DIGEST
		$query ="
			SELECT r.subject_id, r.reply_id, s.subject_title, r.reply_body, 
			UNIX_TIMESTAMP(r.reply_create_datetime) as reply_create_datetime,
			c.connection_nickname 
			FROM " . $db->prefix . "_plugin_forum_reply r, " . $db->prefix . "_plugin_forum_subject s, " . $db->prefix . "_connection c
			WHERE
			r.subject_id=s.subject_id AND 
			r.connection_id=c.connection_id AND
			r.webspace_id=" . $dr['webspace_id'] . " AND
			r.reply_archived IS NULL AND 
			r.reply_create_datetime>" . $db->qstr($search_period) . "
			ORDER BY r.reply_create_datetime DESC"
		;
		
		$new_posts = $db->Execute($query, 10);
		
		if (!empty($new_posts)) {
			$new_posts_html = "";
			$new_posts_txt = "";

			foreach ($new_posts as $key_pbjt => $pbjt):
				$new_post_html = $email_content_html_parts['new_posts_loop'][1];
				$new_post_txt = $email_content_txt_parts['new_posts_loop'][1];
				
				$new_post_html = str_replace('AM_KEYWORD_NEW_POST_TITLE', $pbjt['subject_title'], $new_post_html);
				$new_post_txt = str_replace('AM_KEYWORD_NEW_POST_TITLE', $pbjt['subject_title'], $new_post_txt);

				$url = $webspace_url . "/index.php?wp=" . $dr['webpage_name'] . "&subject_id=" . $pbjt['subject_id'] . "&reply_id=" . $pbjt['reply_id'];

				$new_post_html = str_replace('AM_KEYWORD_NEW_POST_URL', $url, $new_post_html);
				$new_post_txt = str_replace('AM_KEYWORD_NEW_POST_URL', $url, $new_post_txt);
				
				$new_post_html = str_replace('AM_KEYWORD_NEW_POST_AUTHOR', $pbjt['connection_nickname'], $new_post_html);
				$new_post_txt = str_replace('AM_KEYWORD_NEW_POST_AUTHOR', $pbjt['connection_nickname'], $new_post_txt);

				$new_post_html = str_replace('AM_KEYWORD_NEW_POST_CREATE_DATETIME', strftime("%d %b %G %H:%M", $pbjt['reply_create_datetime']), $new_post_html);
				$new_post_txt = str_replace('AM_KEYWORD_NEW_POST_CREATE_DATETIME', strftime("%d %b %G %H:%M", $pbjt['reply_create_datetime']), $new_post_txt);

				$reply_body = strip_tags($pbjt['reply_body']);

				if (strlen($reply_body) > 150) {
					$reply_body = mb_substr($reply_body, 0, 150, 'UTF-8') . '...';
				}
				
				$new_post_html = str_replace('AM_KEYWORD_NEW_POST_BODY', $reply_body, $new_post_html);
				$new_post_txt = str_replace('AM_KEYWORD_NEW_POST_BODY', $reply_body, $new_post_txt);
				
				$new_posts_html .= $new_post_html;
				$new_posts_txt .= $new_post_txt;
				
			endforeach;
			$email_content_html = str_replace($email_content_html_parts['new_posts'][0], $email_content_html_parts['new_posts'][1], $email_content_html);
			$email_content_txt = str_replace($email_content_txt_parts['new_posts'][0], $email_content_txt_parts['new_posts'][1], $email_content_txt);
			
			$email_content_html = str_replace($email_content_html_parts['new_posts_loop'][0], $new_posts_html, $email_content_html);
			$email_content_txt = str_replace($email_content_txt_parts['new_posts_loop'][0], $new_posts_txt, $email_content_txt);
		}
		else {
			$email_content_html = str_replace($email_content_html_parts['new_posts'][0], '', $email_content_html);
			$email_content_txt = str_replace($email_content_txt_parts['new_posts'][0], '', $email_content_txt);
		}


		// GET CONNECTION STATISTICS
		$query ="
			SELECT count(connection_id) as total_connections 
			FROM " . $db->prefix . "_connection c
			WHERE
			webspace_id=" . $dr['webspace_id']
		;

		$results = $db->Execute($query);
		
		if (empty($results[0]['total_connections'])) {
			$results[0]['total_connections'] = "0";
		}
		
		$email_content_html = str_replace('AM_SYS_KEYWORD_CONNECTION_TOTAL', $results[0]['total_connections'], $email_content_html);
		$email_content_txt = str_replace('AM_SYS_KEYWORD_CONNECTION_TOTAL', $results[0]['total_connections'], $email_content_txt);

		$query ="
			SELECT count(connection_id) as period_connections
			FROM " . $db->prefix . "_connection c
			WHERE
			webspace_id=" . $dr['webspace_id'] . " AND
			connection_create_datetime>" . $db->qstr($search_period)
		;
		
		$results = $db->Execute($query);
		
		if (empty($results[0]['period_connections'])) {
			$results[0]['period_connections'] = "0";
		}
		
		$email_content_html = str_replace('AM_SYS_KEYWORD_CONNECTION_PERIOD_TOTAL', $results[0]['period_connections'], $email_content_html);
		$email_content_txt = str_replace('AM_SYS_KEYWORD_CONNECTION_PERIOD_TOTAL', $results[0]['period_connections'], $email_content_txt);
		
		

		$email_content_html = utf8_decode ($email_content_html);
		$email_content_txt = utf8_decode ($email_content_txt);
		
		// SEND
		$mail->Body = $email_content_html;
		// non - HTML-version of the email
		$mail->AltBody   = $email_content_txt;

		// add new email-address to mailer-object
		$mail->ClearAddresses();
		$mail->AddAddress($dr['connection_email'], $dr['connection_nickname']);

		// Send email
		if(!$mail->Send()) {
			$log = "There has been a mail error sending newsletter (subject:" . $mail->Subject . ") to " . $dr['connection_email'] . "," . $dr['connection_nickname'] . ".";
		}
		
		// reset next send datetime
		$next_send_timestamp = $dr['send_datetime'] + ($dr['digest_frequency'] * 24 * 60 * 60);
		$next_send = date('Y-m-d H:i:s', $next_send_timestamp);
		
		$query = "
			UPDATE " . $db->prefix . "_plugin_forum_digest
			SET
			send_datetime=" . $db->qstr($next_send) . "
			WHERE
			connection_id=" . $dr['connection_id'] . " AND 
			webspace_id=" . $dr['webspace_id']
		;
		
		$db->Execute($query);
		
	endforeach;
}

function getEmailContentParts($str) {

	$parts = array();

	$pattern = "/<tracked_subjects>(.*?)?<\/tracked_subjects>/s";

	if (preg_match_all($pattern, $str, $part)) {
		
		if (!empty($part)) {
		
			$parts['tracked_subjects'][0] = $part[0][0];
			$parts['tracked_subjects'][1] = $part[1][0];
			
			$pattern = "/<tracked_subjects_loop>(.*?)?<\/tracked_subjects_loop>/s";

			if (preg_match_all($pattern, $parts['tracked_subjects'][0], $part)) {
		
				if (!empty($part)) {
					$parts['tracked_subjects_loop'][0] = $part[0][0];
					$parts['tracked_subjects_loop'][1] = $part[1][0];
				}
			}
		}
	}


	$pattern = "/<new_subjects>(.*?)?<\/new_subjects>/s";

	if (preg_match_all($pattern, $str, $part)) {

		if (!empty($part)) {
			$parts['new_subjects'][0] = $part[0][0];
			$parts['new_subjects'][1] = $part[1][0];

			$pattern = "/<new_subjects_loop>(.*?)?<\/new_subjects_loop>/s";

			if (preg_match_all($pattern, $parts['new_subjects'][0], $part)) {
		
				if (!empty($part)) {
					$parts['new_subjects_loop'][0] = $part[0][0];
					$parts['new_subjects_loop'][1] = $part[1][0];
				}
			}
		}
	}


	$pattern = "/<new_posts>(.*?)?<\/new_posts>/s";

	if (preg_match_all($pattern, $str, $part)) {

		if (!empty($part)) {
			$parts['new_posts'][0] = $part[0][0];
			$parts['new_posts'][1] = $part[1][0];

			$pattern = "/<new_posts_loop>(.*?)?<\/new_posts_loop>/s";

			if (preg_match_all($pattern, $parts['new_posts'][0], $part)) {
		
				if (!empty($part)) {
					$parts['new_posts_loop'][0] = $part[0][0];
					$parts['new_posts_loop'][1] = $part[1][0];
				}
			}
		}
	}
	
	return $parts;
}
?>