<?php
/**
 * Created by PhpStorm.
 * 插件表
 * User: fesiong
 * Date: 4/5/18
 * Time: 9:57 PM
 */
namespace Fesion\Models;

class Plugin extends ModelsBase
{
    public $id;
    public $title;
    public $remark;
    public $message;
    public $money;
    public $location;
    public $order_count;
    public $version;
    public $add_time;
}