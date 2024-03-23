<?php
// HTTP
define('HTTP_SERVER', 'http://localhost:8071/ocs23/');

// HTTPS
define('HTTPS_SERVER', 'http://localhost:8071/ocs23/');

// DIR
define('DIR_APPLICATION', '/var/www/html/ocs23/catalog/');
define('DIR_SYSTEM', '/var/www/html/ocs23/system/');
define('DIR_IMAGE', '/var/www/html/ocs23/image/');
define('DIR_LANGUAGE', '/var/www/html/ocs23/catalog/language/');
define('DIR_TEMPLATE', '/var/www/html/ocs23/catalog/view/theme/');
define('DIR_CONFIG', '/var/www/html/ocs23/system/config/');
define('DIR_CACHE', '/var/www/html/ocs23/system/storage/cache/');
define('DIR_DOWNLOAD', '/var/www/html/ocs23/system/storage/download/');
define('DIR_LOGS', '/var/www/html/ocs23/system/storage/logs/');
define('DIR_MODIFICATION', '/var/www/html/ocs23/system/storage/modification/');
define('DIR_UPLOAD', '/var/www/html/ocs23/system/storage/upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'database');
define('DB_USERNAME', 'docker');
define('DB_PASSWORD', 'docker');
define('DB_DATABASE', 'ocs23');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');

// Redis
// define('CACHE_HOSTNAME', 'localhost');
// define('CACHE_PORT', '6379');
// define('CACHE_PREFIX', 'oc_');
