<?php
/**
* @version $Id: index.php 438 2012-07-09 21:24:25Z eska $
* @package phpmygpx
* @copyright Copyright (C) 2009-2012 Sebastian Klemm.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

define( '_VALID_OSM', TRUE );
define( '_PATH', './' );
$DEBUG = FALSE;
if($DEBUG) error_reporting(E_ALL);

session_start();

include("./check_db.php");
#include("./config.inc.php");
#include("./libraries/functions.inc.php");
#include("./libraries/classes.php");
include("./libraries/html.classes.php");

setlocale (LC_TIME, $cfg['config_locale']);
include("./languages/".get_lang($cfg['config_language']).".php");
include("./head.html.php");

if($cfg['show_exec_time'])
    $startTime = microtime_float();

if($DEBUG) {
    foreach($_POST as $akey => $val)
        out("<b>$akey</b> = $val", "OUT_DEBUG");
}

$task = getUrlParam('HTTP_GET', 'STRING', 'task');
$referrer = getUrlParam('HTTP_POST', 'STRING', 'referrer');

// connect to database
$link = db_connect_h($cfg['db_host'], $cfg['db_name'], $cfg['db_user'], $cfg['db_password']);

if(!$cfg['embedded_mode'] || !$cfg['public_host'] || check_password($cfg['admin_password'])) {
	HTML::heading(_APP_NAME, 2);
	HTML::main_menu();
}

switch ($task) {
	case 'login':
		login($referrer);
		break;
	case 'logout':
		logout();
		break;
	default:
        start();
		break;
}


function login($ref) {
	global $cfg;
	if($_POST['pwd']) {
		$_SESSION['pwd_hash'] = md5(strip_tags($_POST['pwd']));
		if(check_password($cfg['admin_password'])) {
			HTML::message(_LOGIN_SUCCESS);
			// Redirect to previous page after login
			if($ref) {
				echo "<script type='text/javascript'>\n";
				echo "window.setTimeout(\"window.location.href='$ref'\", 1500);\n";
				echo "</script>\n";
			}
		}else {
			$_SESSION['pwd_hash'] = '';
			HTML::message(_LOGIN_FAILED);
			HTML::message(_LOGIN_DESCRIPTION);
			HTML_index::loginForm($ref);
		}
	}else {
		// use current referrer for page redirection after login...
		if(strpos($_SERVER['HTTP_REFERER'], 'task=login') === FALSE)
			$ref = $_SERVER['HTTP_REFERER'];
		// .. or fake it IF it already was the login page OR referrer was disabled by browser  
		elseif(!$ref || !$_SERVER['HTTP_REFERER'])
			$ref = 'index.php';
		// ... ELSE use untouched referrer ($ref) sent by empty login form without password
		  
		HTML::message(_LOGIN_DESCRIPTION);
		HTML_index::loginForm($ref);
	}
}

function logout() {
	$_SESSION['pwd_hash'] = '';
	HTML::message(_LOGOUT_SUCCESS);
	// Redirect to previous page after logout
	echo "<script type='text/javascript'>\n";
	echo "window.setTimeout(\"window.location.href='index.php'\", 1500);\n";
	echo "</script>\n";
}

function start() {
	global $cfg;
	HTML::heading(_HOME_WELCOME_TO . _APP_NAME, 3);
	#HTML::message(_HOME_INTRO);
	HTML::message('<a target="_blank" href="http://www.openstreetmap.org/">OpenStreetMap</a>');
	HTML::message('<a target="_blank" href="http://www.osmtools.de/osmlinks/?lang='.
		_LANGUAGE .'&lat='. $cfg['home_latitude'] .'&lon='. $cfg['home_longitude']
		.'&zoom='. $cfg['home_zoom'] .'">OpenStreetMap Links</a>');
	if($cfg['pma_app_show_link']) {
		HTML::message('<a target="_blank" href="'.$cfg['pma_app_path'].'">phpMyAdmin</a>');
	}
}

class HTML_index {
	function loginForm($login_referrer) {
		echo "<form method='post'>\n";
		echo "<input type='password' name='pwd' size=12 />\n";
		echo "<input type='hidden' name='referrer' value='$login_referrer' />\n";
		echo "<input type='submit' value='Login' />\n";
		echo "</form>\n";
	}
}
    

if($cfg['show_exec_time']) {
    $endTime = microtime_float();
    $exectime = round($endTime - $startTime, 4);
}
include("./foot.html.php");
?>