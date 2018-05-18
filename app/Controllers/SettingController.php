<?php

namespace Fesion\Controllers;

use Fesion\Library\Helper;
use Fesion\Models\Setting;

class SettingController extends ControllerBase
{

    public function clearCacheAction(){
        $this->dataCache->flush();
        $this->modelsCache->flush();

        Helper::json_output(0, '缓存清理成功');
    }

    public function clearErrorAction(){
        if(file_exists(APP_PATH . 'app/logs/error.log')){
            @unlink(APP_PATH . 'app/logs/error.log');
        }

        Helper::json_output(0, '错误日志清理成功');
    }

    public function getSettingAction($type = null){
        $settings = Helper::getSetting();
        switch ($type){
            case 'pay':
                $data = [
                    'alipay_app_id'      => $settings['alipay_app_id']?:'',
                    'alipay_private_key' => $settings['alipay_private_key']?:'',
                    'alipay_public_key'  => $settings['alipay_public_key']?:'',

                    'wxpay_appid'        => $settings['wxpay_appid']?:'',
                    'wxpay_mchid'        => $settings['wxpay_mchid']?:'',
                    'wxpay_key'          => $settings['wxpay_key']?:'',
                    'wxpay_appsecret'    => $settings['wxpay_appsecret']?:''
                ];
                break;
            case 'sms':
                $data = [
                    'sms_appid'    => $settings['sms_appid']?:'',
                    'sms_key'      => $settings['sms_key']?:'',
                    'sms_template' => $settings['sms_template']?:'',
                    'sms_signname' => $settings['sms_signname']?:'',
                ];
                break;
            case 'weixin':
                $data = [
                    'weixin_app_id'           => $settings['weixin_app_id']?:'',
                    'weixin_app_secret'       => $settings['weixin_app_secret']?:'',
                    'weixin_token'            => $settings['weixin_token']?:'',
                    'weixin_encoding_aes_key' => $settings['weixin_encoding_aes_key']?:'',
                ];
                break;
            case 'content':
                $data = [
                    'content_min_length'      => $settings['content_min_length']?:'',
                    'content_time_limit'      => $settings['content_time_limit']?:'',
                    'content_sensitive_words' => $settings['content_sensitive_words']?:'',
                ];
                break;
            case 'website':
                $data = [
                    'site_name'   => $settings['site_name']?:'',
                    'seo_title'   => $settings['seo_title']?:'',
                    'keywords'    => $settings['keywords']?:'',
                    'description' => $settings['description']?:'',
                    'copyright'   => $settings['copyright']?:'',
                ];
                break;
        }

        Helper::json_output(0, null, $data);
    }

    public function saveSettingAction($type = null){
        $settings = $this->request->getPost();
        switch ($type){
            case 'pay':
                $this->_saveSetting('alipay_app_id',      $settings['alipay_app_id']);
                $this->_saveSetting('alipay_private_key', $settings['alipay_private_key']);
                $this->_saveSetting('alipay_public_key',  $settings['alipay_public_key']);

                $this->_saveSetting('wxpay_appid',     $settings['wxpay_appid']);
                $this->_saveSetting('wxpay_mchid',     $settings['wxpay_mchid']);
                $this->_saveSetting('wxpay_key',       $settings['wxpay_key']);
                $this->_saveSetting('wxpay_appsecret', $settings['wxpay_appsecret'], true);
                break;
            case 'sms':
                $this->_saveSetting('sms_appid',    $settings['sms_appid']);
                $this->_saveSetting('sms_key',      $settings['sms_key']);
                $this->_saveSetting('sms_template', $settings['sms_template']);
                $this->_saveSetting('sms_signname', $settings['sms_signname'], true);
                break;
            case 'weixin':
                $this->_saveSetting('weixin_app_id',           $settings['weixin_app_id']);
                $this->_saveSetting('weixin_app_secret',       $settings['weixin_app_secret']);
                $this->_saveSetting('weixin_token',            $settings['weixin_token']);
                $this->_saveSetting('weixin_encoding_aes_key', $settings['weixin_encoding_aes_key'], true);
                break;
            case 'content':
                $this->_saveSetting('content_min_length',      $settings['content_min_length']);
                $this->_saveSetting('content_time_limit',      $settings['content_time_limit']);
                $this->_saveSetting('content_sensitive_words', $settings['content_sensitive_words'], true);
                break;
            case 'website':
                $this->_saveSetting('site_name',   $settings['site_name']);
                $this->_saveSetting('seo_title',   $settings['seo_title']);
                $this->_saveSetting('keywords',    $settings['keywords'], true);
                $this->_saveSetting('description', $settings['description'], true);
                $this->_saveSetting('copyright',   $settings['copyright'], true);
                break;
        }

        Helper::json_output(0, '设置保存成功');
    }

    /**
     * @param      $key
     * @param      $value
     * @param bool $removeCache
     */
    private function _saveSetting($key, $value, $removeCache = false){
        if($key){
            $setting = Setting::findFirst([
                'name = :name:',
                'bind' => [
                    'name' => $key
                ]
            ]);
            if(!$setting){
                $setting = new Setting();
                $setting->name = $key;
            }
            $setting->value = $value;
            $setting->save();
        }

        if($removeCache){
            $this->modelsCache->delete(Setting::CACHE_KEY);
        }
    }

    public function getErrorLogAction(){
        header('Content-Disposition: attachment; filename="error.log"');
        header("Content-Type: application/octet-stream");
        readfile(APP_PATH . 'app/logs/error.log');
    }
}

