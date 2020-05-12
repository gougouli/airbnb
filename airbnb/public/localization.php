<?php
error_reporting(E_ALL | E_STRICT);

// define constants
define('PROJECT_DIR', realpath('./'));
define('LOCALE_DIR', PROJECT_DIR .'/public/locale');
define('DEFAULT_LOCALE', 'fr_FR');

require_once('lib/gettext/gettext.inc');

$supported_locales = array('en_US', 'fr_FR', 'it_IT', 'es_ES', 'de_DE');
$encoding = 'UTF-8';

/*if(isset($_SESSION['lang'])){$locale = $_SESSION['lang'];
}elseif(isset($_GET['lang'])){$locale = $_GET['lang'];
}else{$locale = DEFAULT_LOCALE;}*/

$locale = (isset($_GET['lang']))? $_GET['lang'] : DEFAULT_LOCALE;


// gettext setup
T_setlocale(LC_MESSAGES, $locale);
// Set the text domain as 'messages'
$domain = 'trad';
bindtextdomain($domain, LOCALE_DIR);
// bind_textdomain_codeset is supported only in PHP 4.2.0+
if (function_exists('bind_textdomain_codeset'))
  bind_textdomain_codeset($domain, $encoding);
textdomain($domain);
header("Content-type: text/html; charset=$encoding");
