<?php
/**
 * Created by PhpStorm.
 * 设置
 * User: fesiong
 * Date: 4/5/18
 * Time: 10:03 PM
 */
namespace Fesion\Models;

class Setting extends ModelsBase
{
    const CACHE_KEY = 'system_settings';

    public $id;
    public $name;
    public $value;
    public $add_time;

    public static function find($parameters = null){
        if(!$parameters){
            return parent::find([
                "cache" => [
                    "key" => static::CACHE_KEY
                ]
            ]);
        }
        return parent::find($parameters);
    }
}