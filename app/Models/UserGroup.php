<?php
/**
 * Created by PhpStorm.
 * 用户组
 * User: fesiong
 * Date: 4/5/18
 * Time: 10:42 PM
 */
namespace Fesion\Models;

class UserGroup extends ModelsBase
{
    public $id;
    public $title;
    public $permission;

    public function beforeSave(){
        $this->permission = json_encode($this->permission);
    }

    public function afterSave(){
        $this->permission = json_decode($this->permission, true);
    }

    public function afterFetch(){
        $this->permission = json_decode($this->permission, true);
    }
}