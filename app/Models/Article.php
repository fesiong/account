<?php
/**
 * Created by PhpStorm.
 * 文档
 * User: fesiong
 * Date: 16/8/23
 * Time: 上午11:21
 */

namespace Fesion\Models;

class Article extends ModelsBase
{
    public $id;
    public $title;
    public $message;
    public $add_time;
    public $views;
    public $category_id;
    public $type;

    public function initialize(){
        $this->hasOne(
            'category_id',
            'Fesion\Models\Category',
            'id',
            [
                'alias'    => 'category',
                'reusable' => true
            ]
        );
    }

    public function beforeCreate(){
        $this->add_time = TIMESTAMP;
    }
}