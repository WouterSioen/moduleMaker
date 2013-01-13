<?php

/**
 * Global configuration options and constants of the FORK CMS
 *
 * @package	Fork
 *
 * @author	Davy Hellemans <davy@netlash.com>
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Matthias Mullie <forkcms@mullie.eu>
 */

/**
 * Spoon configuration
 */
// message for the visitors when an exception occur
define('SPOON_DEBUG_MESSAGE', 'Internal error.');
// default charset used in spoon.
define('SPOON_CHARSET', 'utf-8');


/**
 * Fork configuration
 */
// version of Fork
define('FORK_VERSION', '3.4.4');

if(isStaged())
{
	require_once dirname(__FILE__) . '/globals_stage.php';
}
else
{
	define('SPOON_DEBUG', true);
	define('SPOON_DEBUG_EMAIL', '');
	define('DB_TYPE', 'mysql');
	define('DB_PORT', '3306');
	define('DB_DATABASE', 'fork_personal');
	define('DB_HOSTNAME', 'fork_personal');
	define('DB_USERNAME', 'root');
	define('DB_PASSWORD', '12345678');
}

/**
 * Site configuration
 */
// the protocol
define('SITE_PROTOCOL', isset($_SERVER['SERVER_PROTOCOL']) ? (strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? 'http' : 'https') : 'http');
// the domain (without http(s))
define('SITE_DOMAIN', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'fork.dev');
// the default title
define('SITE_DEFAULT_TITLE', 'Fork CMS');
// the url
define('SITE_URL', SITE_PROTOCOL . '://' . SITE_DOMAIN);
// is the site multilanguage?
define('SITE_MULTILANGUAGE', false);
// default action group tag
define('ACTION_GROUP_TAG', '@actiongroup');
// default action rights level
define('ACTION_RIGHTS_LEVEL', '7');

/**
 * @return bool Whether or not we're running the site in development.
 */
function isStaged()
{
	return file_exists(dirname(__FILE__) . '/globals_stage.php');
}

/**
 * Path configuration
 */
define('PATH_WWW', dirname(__FILE__) . '/..');
define('PATH_LIBRARY', dirname(__FILE__));
