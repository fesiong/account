<?php
/**
 * Created by PhpStorm.
 * 站点后台登录日志
 * User: fesiong
 * Date: 4/5/18
 * Time: 10:29 PM
 */
namespace Fesion\Models;

class SiteLogin extends ModelsBase
{
    public $id;
    public $site_id;
    public $add_time;
    public $uid;
    public $ip;
    public $error_times;
    public $ua;
}