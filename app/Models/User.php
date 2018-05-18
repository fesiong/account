<?php
/**
 * Created by PhpStorm.
 * 用户表
 * User: fesiong
 * Date: 16/8/23
 * Time: 上午10:25
 */
/**
 * 超级管理员
 * 账号 17097218761
 * 密码 niaokan
 * 授权 niaokan
 */
namespace Fesion\Models;

use Fesion\Library\Helper;
use Phalcon\Mvc\Model\Behavior\SoftDelete;

class User extends ModelsBase
{
    const  NORMAL = 1;
    const  LOCK   = 0;
    const  DELETE = -1;
    public $uid;
    public $user_name;
    public $add_time;
    public $mobile;
    public $email;
    public $password;
    public $introduction;
    public $avatar;
    /**
     * @var int $status -1,删除; 0,审核; 1，正常
     */
    public $status;
    public $reg_ip;
    public $balance;
    public $last_active;
    public $last_login;
    /* @var int $group_id = 0,1, 99--游客*/
    public $group_id;
    /** @var int $gender = 0|1|2 未知,男,女 */
    public $gender;
    public $province;
    public $city;
    public $county;
    public $token;

    public function initialize(){
        $this->hasOne(
            'uid',
            'Fesion\Models\Admin',
            'uid',
            [
                'alias'    => 'admin',
                'reusable' => true
            ]
        );
        $this->addBehavior(
            new SoftDelete(
                [
                    'field' => 'status',
                    'value' => self::DELETE
                ]
            )
        );
    }

    public function beforeCreate(){
        parent::beforeCreate();
        $this->status   = in_array($this->status, [User::NORMAL, User::LOCK]) ? $this->status : User::NORMAL;
        if(!$this->user_name){
            $this->user_name = '用户' . sprintf("%u", crc32(uniqid()));
        }
        $this->last_active = $this->last_login = TIMESTAMP;
        $this->reg_ip   = ip2long($this->getDI()->getShared('request')->getClientAddress());
        $this->balance  = 0;
    }

    public function getAvatar($size = 'max', $anonymous = false){
        if($this->avatar AND !$anonymous){
            if(file_exists(Helper::getSetting('upload_dir') . '/avatar/' . Helper::getAvatar($this->uid, $size))){
                $fileTime = filemtime(Helper::getSetting('upload_dir') . '/avatar/' . Helper::getAvatar($this->uid, $size));
                return Helper::getSetting('upload_url') . '/avatar/' . Helper::getAvatar($this->uid, $size) . '?_t=' . $fileTime;
            }
            return '/img/avatar.png';
        }
        return '/img/avatar.png';
    }

    public function getStatus(){
        switch ($this->status){
            case self::DELETE:
                return '已删除';
                break;
            case self::LOCK:
                return '已锁定';
                break;
            case self::NORMAL;
                return '正常';
                break;
            default:
                return '未知';
                break;
        }
    }

    public function updateToken(){
        $this->token = md5($this->token . uniqid('', true));
        $this->save();
    }
}