<?php
/**
 * Created by PhpStorm.
 * 管理员
 * User: fesiong
 * Date: 16/8/23
 * Time: 上午11:52
 */

namespace Fesion\Models;

class Admin extends ModelsBase
{
    public $id;
    public $uid;
    public $password;
    public $add_time;
    public $token;

    public function initialize(){
        $this->hasOne(
            'uid',
            'Fesion\Models\User',
            'uid',
            [
                'alias'    => 'user',
                'reusable' => true
            ]
        );
    }

    public function updateToken(){
        $this->token = md5($this->token . uniqid('', true));
        $this->save();
    }
}