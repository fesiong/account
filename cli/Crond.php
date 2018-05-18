<?php

/**
 * Created by PhpStorm.
 * User: tpyzl
 * Date: 2016/6/16
 * Time: 17:55
 */
use Fesion\Library\Helper;

require 'cli.php';

ini_set('display_errors', 1);
$time = time();//当前时间
$old_time = strtotime('-3 day');

if(isset($_SERVER['argv'][1]))
{
    $do = $_SERVER['argv'][1];
    if($_SERVER['argv'][2])
    {
        $no = $_SERVER['argv'][2];
    }
    else
    {
        $no = '';
    }

    if($_SERVER['argv'][3])
    {
        $no2 = $_SERVER['argv'][3];
    }
}
else
{
    if($_GET['do'])
    {
        $do = $_GET['do'];
    }
    else
    {
        die('error do');
    }

    $no = $_GET['arg1'];
    $no2 = $_GET['arg2'];
}

$funcname = $do;

if(!function_exists($funcname))
{
    die('not allow access!');
}

call_user_func($funcname,$no, $no2);

/**
 * 统计站点数据, 每天凌晨执行一次
 */
function countContents(){

}