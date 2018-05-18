<?php
/**
 * Created by PhpStorm.
 * 网站流量统计
 * User: fesiong
 * Date: 4/5/18
 * Time: 10:30 PM
 */
namespace Fesion\Models;

class SiteVisit extends ModelsBase
{
    const PC     = 1;
    const MOBILE = 2;
    const WEIXIN = 3;

    public $id;
    public $add_time;
    public $site_id;
    public $ip;
    public $url;
    public $refer;
    /** @var string $os 系统 */
    public $os;
    /**
     * @var string $resolution 分辨率
     */
    public $resolution;
    /**
     * @var string $device = 1,pc|2,mobile|3,weixin 设备
     */
    public $device;
    public $ua;
    public $browser;
}