<?php

session_start();

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'guestbook_bd');
define('DB_CHARSET', 'utf8');
define('DB_USER', 'root');
define('DB_PASSWORD', 'Root');
define('DB_DSN_MYSQL', 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET);

define ('MESSAGES_PER_PAGE',2);
define ('PAGINATION_INDENT',5);

require_once('Controller.php');
require_once('Model.php');
require_once('View.php');

$controller = new Controller();
$controller->run();

?>