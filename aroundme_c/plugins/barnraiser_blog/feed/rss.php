<?php

include ("../../../core/config/core.config.php");
include ("../../../core/inc/functions.inc.php");


// SESSION HANDLER ----------------------------------------------------------------------------
// sets up all session and global vars 
session_name($core_config['php']['session_name']);
session_start();


// SETUP AROUNDMe CORE -----------------------------------------------------------------------
require('../../../core/class/Db.class.php');
$db = new Database($core_config['db']);


if(isset($_REQUEST['ws'])) {
	// SETUP WEBSPACE --------------------------------------------
	require_once('../../../core/class/Webspace.class.php');
	$ws = new Webspace($db);
	$ws->webspace_unix_name = $_REQUEST['ws'];
	$output_webspace = $ws->selWebSpace();
	
	if (!empty($output_webspace['webspace_id'])) {
		// the key operates to help stop people viewing the RSS feed
		// when they have just tried to guess the feed name
		// used for locked webspaces 
		$key = md5($output_webspace['webspace_create_datetime']);


		if (isset($_REQUEST['k']) && $_REQUEST['k'] == $key) {
			define("AM_WEBSPACE_ID", $output_webspace['webspace_id']);
		}

	}
}

if(defined('AM_WEBSPACE_ID')) {

	if(isset($_REQUEST['v']) && $_REQUEST['v'] == "comments") {

		$query = "
			SELECT 
			bc.comment_id, bc.comment_body as body, be.blog_title as title, 
			bc.comment_create_datetime as datetime, be.blog_id, 
			c.connection_nickname as author 
			FROM " . $db->prefix . "_plugin_blog_comment bc, " . $db->prefix . "_connection c, " . $db->prefix . "_plugin_blog_entry be
			WHERE
			bc.blog_id=be.blog_id AND 
			bc.connection_id=c.connection_id AND
			bc.webspace_id=" . AM_WEBSPACE_ID . " AND 
			bc.comment_hidden IS NULL
			ORDER BY bc.comment_create_datetime DESC"
		;
		
		$result = $db->Execute($query);

		if (!empty($result)) {
			$ws_url = str_replace('REPLACE', $ws->webspace_unix_name, $core_config['am']['domain_replace_pattern']);

			$feed_items = $result;
			
			foreach($feed_items as $key => $i):

				$feed_items[$key]['link'] = $ws_url . "index.php?wp=" . $_REQUEST['wp'] . "&amp;blog_id=" . $i['blog_id'] . "#comment_id" . $i['comment_id'];
				$feed_items[$key]['body'] = trim(strip_tags($i['body']));
				$feed_items[$key]['body'] = mb_substr($feed_items[$key]['body'], 0, 200, 'UTF-8');
				$feed_items[$key]['body'] = htmlspecialchars($feed_items[$key]['body']);

				$feed_items[$key]['title'] = $i['datetime'] . " (" . $i['publisher'] . ") " . $i['title'];

			endforeach;
		}
	}
	else {
		$query = "
			SELECT b.blog_id, b.blog_title as title, blog_body as body,
			b.blog_create_datetime as datetime, c.connection_nickname as author
			FROM " . $db->prefix . "_plugin_blog_entry b, " . $db->prefix . "_connection c
			WHERE
			b.connection_id=c.connection_id AND
			b.webspace_id=" . AM_WEBSPACE_ID . " AND
			b.blog_archived IS NULL
			ORDER BY b.blog_create_datetime DESC"
		;
			
		$result = $db->Execute($query);
	
		if (!empty($result)) {

			$ws_url = str_replace('REPLACE', $ws->webspace_unix_name, $core_config['am']['domain_replace_pattern']);

			$feed_items = $result;
			
			foreach($feed_items as $key => $i):

				$feed_items[$key]['link'] = $ws_url . "/index.php?wp=" . $_REQUEST['wp'] . "&amp;blog_id=" . $i['blog_id'];
				$feed_items[$key]['body'] = trim(strip_tags($i['body']));
				$feed_items[$key]['body'] = mb_substr($feed_items[$key]['body'], 0, 200, 'UTF-8');
				$feed_items[$key]['body'] = htmlspecialchars($feed_items[$key]['body']);

			endforeach;
		}
	
	}

	// SELECT PREFERENCES
	$query = "
		SELECT rss_title, rss_title_comment, rss_description
		FROM " . $db->prefix . "_plugin_blog_preference
		WHERE
		webspace_id=" . AM_WEBSPACE_ID
	;

	$result = $db->Execute($query);

	if (!empty($result[0])) {
		$preferences = $result[0];
	}
	
	if (empty($preferences['rss_title'])) {
		$preferences['rss_title'] = "title";
	}

	if (empty($preferences['rss_title_comment'])) {
		$preferences['rss_title_comment'] = "title comments";
	}

	if (empty($preferences['rss_description'])) {
		$preferences['rss_description'] = "description";
	}

	
	$url = "http://" . $_SERVER['HTTP_HOST'];
	$url .= $_SERVER['PHP_SELF'];
	$url .= "?ws=" . $_REQUEST['ws'] . "&amp;wp=" . $_REQUEST['wp'] . "&amp;k=" . $_REQUEST['k'];

	if (isset($_REQUEST['v']) && $_REQUEST['v'] == "comments") {
		$url .= "&amp;v=comments";

		$preferences['rss_title'] = $preferences['rss_title_comment'];
	}
	
	header("Content-Type: application/xml; charset=ISO-8859-1");
	
	echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>\n";
	echo "<?xml-stylesheet title=\"XSL_formatting\" type=\"text/xsl\" href=\"nolsol.xsl\"?>\n";
	echo "<rss version=\"2.0\">\n";
	echo "<channel>\n";
	echo "<title>" . utf8_decode($preferences['rss_title']) . "</title>\n";
 	echo "<link>" . $url . "</link>\n";
 	echo "<description>" . utf8_decode($preferences['rss_description']) . "</description>\n";
 	echo "<language>" . $output_webspace['language_code'] . "</language>\n";
 	echo "<lastBuildDate>" . date("r") . "</lastBuildDate>\n";
	
	if (!empty($feed_items)) {
		foreach ($feed_items as $key => $i):
			echo "<item>\n";
			echo "<title>" . utf8_decode($i['title']) . "</title>\n";
			echo "<description>" . utf8_decode($i['body']) . "</description>\n";
			echo "<link>" . $i['link'] . "</link>\n";
			echo "<author>" . utf8_decode($i['author']) . "</author>\n";
			echo "<pubDate>" . date("r", strtotime($i['datetime'])) . "</pubDate>\n";
			echo "</item>";
		endforeach;
	}
	
	echo "</channel>\n";
	echo "</rss>";
}

?>