<?php
/**
 * Created by PhpStorm.
 * User: fesiong
 * Date: 16/8/18
 * Time: 上午10:43
 */
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('Etc/GMT-8');

define('APP_PATH', realpath(__DIR__ . '/../../') . '/');

/* 环境
 * production/development
 */
define('TIMESTAMP', time());
define('ENV_PRODUCTION', 'production');
define('ENV_DEVELOPMENT', 'development');
define('APPLICATION_ENV', getenv('APP_ENV') ?: ENV_PRODUCTION);
define('APP_START_TIME', microtime(true));
define('APP_START_MEMORY', memory_get_usage());
define('HOSTNAME', explode('.', gethostname())[0]);
define('CRYPT_KEY', 'QdFe8WwNhd9eSJ8d');
define('VERSION', '1.0.0');

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('utf-8');
}

if (function_exists('mb_substitute_character')) {
    mb_substitute_character('none');
}