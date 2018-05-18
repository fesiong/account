<?php
/**
 * Created by PhpStorm.
 * User: tpyzl
 * Date: 2016/6/16
 * Time: 17:48
 */
error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('Etc/GMT-8');

use Fesion\Fesion;
use Phalcon\Config;
use Phalcon\DiInterface;
use Phalcon\Events\Manager as EventsManager;

include_once realpath(dirname(dirname(__FILE__))) . '/app/config/env.php';
include_once APP_PATH . 'app/Fesion.php';

try {

    $bootstrap = new Fesion();

    //$bootstrap->run();

} catch (Exception $e){
    echo $e->getMessage();
}