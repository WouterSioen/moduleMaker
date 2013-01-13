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
// should the debug information be shown
define('SPOON_DEBUG', true);
// mailaddress where the exceptions will be mailed to (<tag>-bugs@fork-cms.be)
define('SPOON_DEBUG_EMAIL', 'wouter.sioen@wijs.be');
// message for the visitors when an exception occur
define('SPOON_DEBUG_MESSAGE', 'Internal error.');
// default charset used in spoon.
define('SPOON_CHARSET', 'utf-8');


/**
 * Fork configuration
 */
// version of Fork
define('FORK_VERSION', '3.4.4');


/**
 * Database configuration
 */
// type of connection
define('DB_TYPE', 'mysql');
// database name
define('DB_DATABASE', 'fork_personal');
// database host
define('DB_HOSTNAME', '127.0.0.1');
// database port
define('DB_PORT', '3306');
// database username
define('DB_USERNAME', 'root');
// datebase password
define('DB_PASSWORD', '12345678');


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
 * Path configuration
 *
 * Depends on the server layout
 */
// path to the website itself
define('PATH_WWW', '/Users/woutersioen/Sites/fork.personal');
// path to the library
define('PATH_LIBRARY', '/Users/woutersioen/Sites/fork.personal/library');
