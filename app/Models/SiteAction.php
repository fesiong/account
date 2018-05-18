<?php
/**
 * Created by PhpStorm.
 * 站点操作日志
 * User: fesiong
 * Date: 4/5/18
 * Time: 10:07 PM
 */
namespace Fesion\Models;

class SiteAction extends ModelsBase
{
    public $id;
    public $add_time;
    public $site_id;
    public $uid;
    public $ip;
    public $message;
    public $type;
}