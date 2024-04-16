<?php
if (strpos($_SERVER['HTTP_HOST'],'right-comic-starling.ngrok-free.app') !== false){
    $url='https://right-comic-starling.ngrok-free.app';
}elseif (strpos($_SERVER['HTTP_HOST'],'localhost:8071')!== false){
    $url='http://localhost:8071';
}
// HTTP
define('HTTP_SERVER', $url.'/ocs23/admin/');
define('HTTP_CATALOG', $url.'/ocs23/');

// HTTPS
define('HTTPS_SERVER', $url.'/ocs23/admin/');
define('HTTPS_CATALOG', $url.'/ocs23/');

// DIR
define('DIR_APPLICATION', '/var/www/html/ocs23/admin/');
define('DIR_SYSTEM', '/var/www/html/ocs23/system/');
define('DIR_IMAGE', '/var/www/html/ocs23/image/');
define('DIR_LANGUAGE', '/var/www/html/ocs23/admin/language/');
define('DIR_TEMPLATE', '/var/www/html/ocs23/admin/view/template/');
define('DIR_CONFIG', '/var/www/html/ocs23/system/config/');
define('DIR_CACHE', '/var/www/html/ocs23/system/storage/cache/');
define('DIR_DOWNLOAD', '/var/www/html/ocs23/system/storage/download/');
define('DIR_LOGS', '/var/www/html/ocs23/system/storage/logs/');
define('DIR_MODIFICATION', '/var/www/html/ocs23/system/storage/modification/');
define('DIR_UPLOAD', '/var/www/html/ocs23/system/storage/upload/');
define('DIR_CATALOG', '/var/www/html/ocs23/catalog/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'database');
define('DB_USERNAME', 'docker');
define('DB_PASSWORD', 'docker');
define('DB_DATABASE', 'ocs23');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');

// OpenCartForum API
define('OPENCARTFORUM_SERVER', 'https://opencartforum.com/');
