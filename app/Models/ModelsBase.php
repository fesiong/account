<?php
/**
 * Created by PhpStorm.
 * 基础模型
 * User: fesiong
 * Date: 16/8/19
 * Time: 上午10:58
 */

namespace Fesion\Models;

use \Phalcon\Mvc\Model;

class ModelsBase extends Model
{
    public $add_time;

    public function getSource()
    {
        $source = explode('\\', get_class($this));
        $source = preg_split("/(?=[A-Z])/", lcfirst(end($source)));

        return strtolower($this->getDI()->getShared('config')->get('database')->prefix . implode('_', $source));
    }

    public static function getCacheKey($string){
        return md5('fesion-' . $string);
    }

    public function beforeCreate(){
        $this->add_time = TIMESTAMP;
    }
}