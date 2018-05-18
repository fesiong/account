<?php
/**
 * Created by PhpStorm.
 * 站点存活日志
 * User: fesiong
 * Date: 4/5/18
 * Time: 10:08 PM
 */
namespace Fesion\Models;

class SiteAlive extends ModelsBase
{
    public $id;
    public $add_time;
    public $site_id;
    public $domain;
    public $status;
    public $used_time;
    public $title;
}