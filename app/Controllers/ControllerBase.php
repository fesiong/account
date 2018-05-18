<?php

namespace Fesion\Controllers;

use Fesion\Library\Helper;
use Fesion\Library\Paginator;
use Fesion\Models\Admin;
use Fesion\Models\Site;
use Fesion\Models\User;
use Phalcon\Config;
use Phalcon\Exception;
use Phalcon\Mvc\Controller;

/**
 * Class ControllerBase
 * @package Fesion\Controllers
 * @property \Phalcon\Cache\BackendInterface $dataCache
 * @property \Fesion\Library\Tag $tag
 * @property \Phalcon\Logger\AdapterInterface $logger
 * @property Site $currentSite
 * @property Config $config
 * @property Admin $admin
 * @property User $user_name
 * @property int $user_id
 */

class ControllerBase extends Controller
{
    public $perPage   = 10;

    public function showError($title = '出错啦', $message = '系统找不到需要显示的内容', $url = null, $code = 404){
        $error = $code . ' ' . $message . ' in ' . $this->dispatcher->getControllerClass() . ' action ' . $this->dispatcher->getActionName() . ' from ' . $this->request->getURI();

        throw new Exception($error);
    }

    public function _formatUser(User $user = null){
        if(!$user){
            return [];
        }
        $userArr = $user->toArray();
        $userArr['avatar'] = $user->getAvatar();
        unset($userArr['password'], $userArr['token'], $user);

        return $userArr;
    }
}
