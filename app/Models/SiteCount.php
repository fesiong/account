<?php
/**
 * Created by PhpStorm.
 * 站点数据统计，一天一统计
 * User: fesiong
 * Date: 4/5/18
 * Time: 10:09 PM
 */
namespace Fesion\Models;

class SiteCount extends ModelsBase
{
    public $id;
    public $add_time;
    public $user_count;
    public $category_count;
    public $topic_count;
    public $answer_count;
    public $comment_count;
    public $ip_count;
    public $pv_count;
    public $uv_count;
    public $site_id;
    public $count_date;
}