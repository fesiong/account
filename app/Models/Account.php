<?php
/**
 * Created by PhpStorm.
 * 文档
 * User: fesiong
 * Date: 16/8/23
 * Time: 上午11:21
 */

namespace Fesion\Models;

class Account extends ModelsBase
{
    public $id;
    public $title;
    public $message;
    public $parent_id;
    public $add_time;
    public $end_time;
    public $views;
    public $category_id;
    public $type;
    public $uid;
    public $user_name;
    public $password;

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
        $this->hasOne(
            'parent_id',
            'Fesion\Models\Account',
            'id',
            [
                'alias'    => 'parent',
                'reusable' => true
            ]
        );
    }

    public function beforeCreate(){
        $this->add_time = TIMESTAMP;
    }
}