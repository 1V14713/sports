<?php
/**
* @version $Id: rss_feed.php 434 2012-06-28 15:47:22Z eska $
* @package phpmygpx
* @copyright Copyright (C) 2012 Sebastian Klemm.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/**
* Example code taken from http://www.php-astux.info/flux-rss-site-web.php
* Thanks to Luc (http://www.lesrandoactifs.org)
*/

define( '_VALID_OSM', TRUE );
define( '_PATH', './' );
$DEBUG = FALSE;
if($DEBUG) error_reporting(E_ALL);
else       error_reporting(E_NONE);
include("./config.inc.php");
include("./libraries/functions.inc.php");
include("./languages/".get_lang($cfg['config_language']).".php");

/* **********************************
 * PART 1 : DATABASE MYSQL
 * **********************************
 */

	// Connect to database
	$link = db_connect_h($cfg['db_host'], $cfg['db_name'], $cfg['db_user'], $cfg['db_password']);

	// If we have "view=XXX in URL, we fetch the number, else we select 10 by default
	$view = (isset($_GET['view'])) ? abs(intval($_GET['view'])) : 10;

	// Prepare the database request
	// Remark: length and timezone could be remove
	$query = "SELECT id, name, timestamp, length, size, description 
		FROM `${cfg['db_table_prefix']}gpx_files` 
		ORDER BY `id` DESC LIMIT 0,".$view.";";

	// Execute the request
	$newtracks = db_query($query);

	// Put the request answer in a php table:
	$array_tracks = array();

	if (mysql_num_rows($newtracks) > 0) // It is existing at least one track
	{
		while ($f = mysql_fetch_array($newtracks))
		{
			$array_tracks[$f['id']]['name'] = $f['name'];
			$array_tracks[$f['id']]['timestamp'] = $f['timestamp'];
			$array_tracks[$f['id']]['length'] = $f['length'];
			$array_tracks[$f['id']]['size'] = intval($f['size'] / 1024);
			$array_tracks[$f['id']]['description'] = $f['description'];
		};
	};

/* **********************************
 * PART 2 : FORMAT THE DOCUMENT
 * **********************************
 */

	$self = $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/') +1);

	// Feed type detection
	$feed = (isset($_GET['feed'])) ? strtolower($_GET['feed']) : '';
	// and set default to rss, just to be sure
	if (($feed != 'rss') && ($feed != 'atom')) { $feed = 'rss'; }

	// now, regarding the requested feed, we prepare the format:
	$rss = ''; // variable init

	if ($feed == 'rss')
	{
		// RSS 2.0 prologue:
		$rss .= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$rss .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'."\n";

		// The channel is the Head of the feed:
		$rss .= '	<channel>'."\n";
		// Title of the Website
		$rss .= '		<title>'._APP_NAME.'</title>'."\n";
		// WebSite URL
		$rss .= '		<link>http://'.$_SERVER['HTTP_HOST'].'</link>'."\n";
		// WebSite Description
		$rss .= '		<description>'._APP_TITLE.'</description>'."\n";
		// Feed language
		$rss .= '		<language>'._LANGUAGE.'</language>'."\n";
		// Publication date
		$rss .= '		<pubDate>'.date('D, d M Y H:i:s O').'</pubDate>'."\n";
		// Feed built date
		$rss .= '		<lastBuildDate>'.gmdate('D, d M Y H:i:s').' GMT</lastBuildDate>'."\n";
		// Feed generator, the program used to generate the channel
		$rss .= '		<generator>'._APP_NAME.' '._APP_VERSION.'</generator>'."\n";
		// Time To Live, number of minutes for caching the channel
		$rss .= '		<ttl>1440</ttl>'."\n";
		// Important line the link should be the feed page URL
		$rss .= '		<atom:link href="http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?feed=rss" rel="self" type="application/rss+xml" />'."\n";

		// Now we write the datas fetched
		foreach($array_tracks as $track_id => $track)
		{
			// For each track
			$rss .= '		<item>'."\n";
			// Each track name will be [track name]
			$rss .= '			<title>['.htmlspecialchars(html_entity_decode($track['name'])).']</title>'."\n";
			// Block Link
			$rss .= '			<link>http://'.$self.'map.php?id='.$track_id.'</link>'."\n";
			// Track description and file size
			$rss .= '			<description>'.htmlspecialchars($track['description'], ENT_NOQUOTES).' &lt;br /&gt;'.htmlspecialchars(_CMN_FILE_SIZE).': '.htmlspecialchars($track['size'], ENT_NOQUOTES).' kB</description>'."\n";
			// Timestamp
			$rss .= '			<pubDate>'.date('D, d M Y H:i:s O', strtotime($track['timestamp'])).'</pubDate>'."\n";
			// GPX file link
			$rss .= '			<guid>http://'.$self.'map.php?id='.$track_id.'</guid>'."\n";
			$rss .= '		</item>'."\n";
		};

		// Ending of RSS feed ...
		$rss .= '	</channel>'."\n";
		$rss .= '</rss>'."\n";
	};

	if ($feed == 'atom')
	{
		// ATOM 1.0 prologue:
		$rss .= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$rss .= '<feed xmlns="http://www.w3.org/2005/Atom" xml:lang="en">'."\n";

		// No channel for ATOM feed...

		// Title of the Website
		$rss .= '	<title>'._APP_NAME.'</title>'."\n";
		// WebSite Description
		$rss .= '	<subtitle>'._APP_TITLE.'</subtitle>'."\n";
		// Important line the link should be the feed page URL
		$rss .= '	<link rel="self" href="http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?feed=atom" type="application/atom+xml" />'."\n";

		// Publication date
		$rss .= '	<updated>'.date('Y-m-d\TH:i:s\Z').'</updated>'."\n";
		// Feed generator, the program used to generate the channel
		$rss .= '	<generator version="'._APP_VERSION.'">'._APP_NAME.'</generator>'."\n";
		// Lines for the document redactor
		$rss .= '	<author>'."\n";
		$rss .= '		<name>A proud phpMyGPX user</name>'."\n";
		$rss .= '		<email>no-reply@'.$_SERVER['HTTP_HOST'].'</email>'."\n";
		$rss .= '	</author>'."\n";
		// WebSite URL
		$rss .= '	<id>http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'</id>'."\n";


		// Now we write the datas fetched
		foreach($array_tracks as $track_id => $track)
		{
			// For each track
			$rss .= '		<entry>'."\n";
			// Each track name will be [track name]
			$rss .= '			<title>['.html_entity_decode($track['name']).']</title>'."\n";
			// Block Link
			$rss .= '			<link href="http://'.$self.'map.php?id='.$track_id.'" />'."\n";
			// ID should be unique
			$rss .= '			<id>http://'.$self.'map.php?id='.$track_id.'</id>'."\n";
			// Timestamp
			$rss .= '			<updated>'.date('Y-m-d\TH:i:s\Z', strtotime($track['timestamp'])).'</updated>'."\n";
			// Block summary
			//$rss .= '			<summary type="html">'.htmlspecialchars($track['description'], ENT_NOQUOTES).'&lt;br /&gt;'.htmlspecialchars(_CMN_FILE_SIZE).': '.htmlspecialchars($track['size'], ENT_NOQUOTES).' kB</summary>'."\n";
			// Feed Block Format(XHTML Strict)
			$rss .= '			<content type="html">'."\n";
			$rss .= '				'.htmlspecialchars($track['description'], ENT_NOQUOTES).'&lt;br /&gt;'.htmlspecialchars(_CMN_FILE_SIZE).': '.htmlspecialchars($track['size'], ENT_NOQUOTES).' kB'."\n";
			$rss .= '			</content>'."\n";
			$rss .= '		</entry>'."\n";
		};
		// Ending of RSS feed ...
		$rss .= '</feed>'."\n";
	};
	
/* **************************************
 * PART 3 : RSS FEED DISPLAY
 * **************************************
 */

	// Send XML Headers / no cache
	header('Content-Type: text/xml');
	header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	// Now the feed is sent
	echo $rss;

// End part 3

// Close database connection
db_close($newtracks, $link);
?>
