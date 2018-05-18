<?php
namespace Fesion\Controllers;

use Fesion\Library\Helper;
use Fesion\Models\Admin;
use Fesion\Models\SiteVisit;
use Fesion\Models\User;
use Fesion\Models\UserGroup;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        //nothing
        /*$group = new UserGroup();
        $group->title = '超级管理员组';
        $group->save();*/
        /*$user = new User();
        $user->user_name = '超级管理员';
        $user->mobile    = '12345678901';
        $user->password  = $this->security->hash('123456');
        $user->email     = 'tpyzlxy@163.com';
        $user->group_id  = 1;
        $user->save();
        $admin = new Admin();
        $admin->uid = $user->uid;
        $admin->password = $this->security->hash('123456');
        $admin->save();*/
    }

    public function countAction()
    {
        $site_id = $this->request->get('sid');
        $width   = $this->request->get('sw');
        $height  = $this->request->get('sh');
        $refer   = $this->request->get('rf');
        $url     = $this->request->getHTTPReferer();
        $ip      = $this->request->getClientAddress();
        $ua      = $this->request->getUserAgent();
        $os      = $this->_getOs($ua);
        $browser = $this->_getBrowser($ua);
        $device  = $this->_getDevice($ua);

        //一分钟内只记录一次
        $cacheKey = md5($site_id . $ip . $width . $height . $refer . $url . $ua);
        if($this->dataCache->get($cacheKey, 60)){
            return false;
        }
        $this->dataCache->save($cacheKey, true, 60);
        //end
        $visit = new SiteVisit();
        $visit->site_id    = $site_id;
        $visit->resolution = $width . '×' .$height;
        $visit->refer      = $refer;
        $visit->url        = $url;
        $visit->ip         = ip2long($ip);
        $visit->device     = $device;
        $visit->os         = $os;
        $visit->browser    = $browser;
        $visit->ua         = $ua;
        $visit->save();
    }

    private function _getDevice($agent){
        if (preg_match('/micromessenger/i', $agent)) {
            return SiteVisit::WEIXIN;
        }
        if (preg_match('/playstation/i', $agent) OR preg_match('/ipad/i', $agent) OR preg_match('/ucweb/i', $agent)) {
            return SiteVisit::PC;
        }
        if (preg_match('/iemobile/i', $agent) OR preg_match('/mobile\ssafari/i', $agent) OR preg_match('/iphone\sos/i', $agent) OR preg_match('/android/i', $agent) OR preg_match('/symbian/i', $agent) OR preg_match('/series40/i', $agent)) {
            return SiteVisit::MOBILE;
        }
        return SiteVisit::PC;
    }

    private function _getBrowser($agent){
        if (stripos($agent, "Firefox/") > 0) {
            preg_match("/Firefox\/([^;)]+)+/i", $agent, $b);
            $exp[0] = "Firefox";
            $exp[1] = $b[1];
        } elseif (stripos($agent, "Maxthon") > 0) {
            preg_match("/Maxthon\/([\d\.]+)/", $agent, $aoyou);
            $exp[0] = "傲游";
            $exp[1] = $aoyou[1];
        } elseif (stripos($agent, "MSIE") > 0) {
            preg_match("/MSIE\s+([^;)]+)+/i", $agent, $ie);
            $exp[0] = "IE";
            $exp[1] = $ie[1];
        } elseif (stripos($agent, "OPR") > 0) {
            preg_match("/OPR\/([\d\.]+)/", $agent, $opera);
            $exp[0] = "Opera";
            $exp[1] = $opera[1];
        } elseif(stripos($agent, "Edge") > 0) {
            //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
            preg_match("/Edge\/([\d\.]+)/", $agent, $Edge);
            $exp[0] = "Edge";
            $exp[1] = $Edge[1];
        } elseif (stripos($agent, "Chrome") > 0) {
            preg_match("/Chrome\/([\d\.]+)/", $agent, $google);
            $exp[0] = "Chrome";
            $exp[1] = $google[1];
        } elseif(stripos($agent,'rv:')>0 && stripos($sys,'Gecko')>0){
            preg_match("/rv:([\d\.]+)/", $agent, $IE);
            $exp[0] = "IE";
            $exp[1] = $IE[1];
        }else {
            $exp[0] = "未知浏览器";
            $exp[1] = "";
        }
        return $exp[0].'('.$exp[1].')';
    }

    private function _getOs($agent)
    {
        if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) {
            $os = 'Windows 7';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) {
            $os = 'Windows 8';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) {
            $os = 'Windows 10';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) {
            $os = 'Windows XP';
        } else if (preg_match('/win/i', $agent)) {
            $os = 'Windows NT';
        } else if (preg_match('/linux/i', $agent)) {
            $os = 'Linux';
        } else if (preg_match('/unix/i', $agent)) {
            $os = 'Unix';
        } else if (preg_match('/Mac OS/i', $agent)) {
            $os = 'Macintosh';
        } else {
            $os = '未知操作系统';
        }

        return $os;
    }

    public function notificationAction(){
        Helper::json_output(0, null, [
            'newmsg' => 0
        ]);
    }

    public function pushSuccessAction()
    {
        echo system('cd /data/wwwroot/hospital/ && git pull', $output);
        var_dump($output);
        file_put_contents(APP_PATH . 'need_pull.txt', time());
        if ($_POST['hook']) {
            $hook = json_decode($_POST['hook'], true);
            if ($hook['password'] == 'hospital') {
            }
            die('success');
        }
        die('success run');
    }
}

