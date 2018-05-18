<?php
/**
 * Created by PhpStorm.
 * 站点
 * User: fesiong
 * Date: 4/5/18
 * Time: 10:04 PM
 */
namespace Fesion\Models;

class Site extends ModelsBase
{
    public $id;
    public $title;
    public $remark;
    public $category_id;
    public $add_time;
    public $uid;
    public $sub_domain;
    public $domains;
    public $end_time;
    public $money;
    public $server_id;
}