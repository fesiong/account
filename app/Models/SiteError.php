<?php
/**
 * Created by PhpStorm.
 * 站点报错日志
 * User: fesiong
 * Date: 4/5/18
 * Time: 10:27 PM
 */
namespace Fesion\Models;

class SiteError extends ModelsBase
{
    public $id;
    public $add_time;
    public $site_id;
    public $url;
    public $file;
    public $line;
    public $message;
}