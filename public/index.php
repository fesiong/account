<?php
/**
 * Created by PhpStorm.
 * User: fesiong
 * Date: 16/8/17
 * Time: 下午7:24
 */
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
use Fesion\Fesion;


try {
    include_once realpath(dirname(dirname(__FILE__))) . '/app/config/env.php';
    include_once APP_PATH . 'app/Fesion.php';

    $bootstrap = new Fesion();
    echo $bootstrap->run();
} catch (\Exception $e){
    header("HTTP/1.1 404 Not Found");
    header("Status: 404 Not Found");

    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}