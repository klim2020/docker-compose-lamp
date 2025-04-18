<?php
// HTTP
define('HTTP_SERVER', 'http://localhost:8071/oc38/admin/');
define('HTTP_CATALOG', 'http://localhost:8071/oc38/');
 //HTTPS
define('HTTPS_SERVER', 'http://localhost:8071/oc38/admin/');
define('HTTPS_CATALOG', 'http://localhost:8071/oc38/');

//define('HTTP_SERVER', 'http://right-comic-starling.ngrok-free.app/oc38/admin/');
//define('HTTP_CATALOG', 'http://right-comic-starling.ngrok-free.app/oc38/');

// HTTPS
//define('HTTPS_SERVER', 'https://right-comic-starling.ngrok-free.app/oc38/admin/');
//define('HTTPS_CATALOG', 'https://right-comic-starling.ngrok-free.app/oc38/');

// DIR
define('DIR_APPLICATION', '/var/www/html/oc38/admin/');
define('DIR_SYSTEM', '/var/www/html/oc38/system/');
define('DIR_IMAGE', '/var/www/html/oc38/image/');
define('DIR_STORAGE', DIR_SYSTEM . 'storage/');
define('DIR_CATALOG', '/var/www/html/oc38/catalog/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'database');
define('DB_USERNAME', 'docker');
define('DB_PASSWORD', 'docker');
define('DB_DATABASE', 'oc38');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');

// OpenCart API
define('OPENCART_SERVER', 'https://www.opencart.com/');
