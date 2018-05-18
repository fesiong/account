<?php
/**
 * Created by PhpStorm.
 * User: fesiong
 * Date: 16/8/24
 * Time: 下午5:48
 */

namespace Fesion\Library;

use AlibabaAliqinFcSmsNumSendRequest;
use Fesion\Library\Phpanalysis\Phpanalysis;
use Fesion\Library\YouKu\Uploader;
use Fesion\Models\Setting;
use Fesion\Models\User;
use Phalcon\Image;
use Phalcon\Image\Adapter\Imagick;
use Phalcon\Mvc\User\Component;
use TopClient;

class Helper extends Component
{
    public static function getStaticDI(){
        $helper = new Helper();
        return $helper->getDI();
    }

    public static function isMobile($text){
        if(!preg_match('/^1[34578]{1}[0-9]{9}$/',$text)){
            return false;
        }
        return true;
    }

    public static function json_output($code, $msg = null, $data = null, $extra = null)
    {
        $output = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
        if(is_array($extra)){
            foreach ($extra as $key => $val){
                $output[$key] = $val;
            }
        }
        echo json_encode($output, JSON_UNESCAPED_UNICODE);
        die;
    }

    public static function isImageType($type){
        if(in_array($type, [
            'image/png',
            'image/jpeg',
            'image/gif'
        ])){
            return true;
        }
        return false;
    }

    /**
     * 获取$uid分割后的路径
     *
     * 举个例子：$uid=12345，那么头像路径会被存储为/000/01/23/45_min.jpg
     *
     * @param  int
     * @param  string
     * @param  int
     * @return string
     */
    public static function getAvatar($uid, $size = 'min', $return_type = 0)
    {
        $size = in_array($size, array(
            'max',
            'mid',
            'min',
            'thumb'
        )) ? $size : 'real';

        $uid = abs(intval($uid));
        $uid = sprintf('%\'09d', $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);

        if ($return_type == 1)
        {
            return $dir1 . '/' . $dir2 . '/' . $dir3 . '/';
        }

        if ($return_type == 2)
        {
            return substr($uid, -2) . '_' . $size . '.jpg';
        }

        return $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, -2) . '_' . $size . '.jpg';
    }

    public static function makeDir($dir, $permission = 0777){
        $dir = rtrim($dir, '/') . '/';
        if (is_dir($dir)) {
            return TRUE;
        }
        if (! self::makeDir(dirname($dir), $permission)) {
            return FALSE;
        }
        return @mkdir($dir, $permission);
    }

    public static function getSetting($name = null){
        static $settings;
        if(!$settings){
            $tmpData = Setting::find();
            foreach ($tmpData as $key => $val)
            {
                $settings[$val->name] = $val->value;
            }
        }

        if($name){
            if($name == 'upload_dir'){
                return APP_PATH . 'public/uploads';
            }
            if($name == 'upload_url'){
                return '/uploads';
            }
            return $settings[$name];
        }
        return $settings;
    }

    public static function dateFriendly($timestamp, $time_limit = 31556926, $out_format = 'Y-m-d H:i', $formats = null, $time_now = null)
    {
        if (!$timestamp)
        {
            return false;
        }

        if ($formats == null)
        {
            $formats = array('YEAR' => '%s 年前', 'MONTH' => '%s 个月前', 'WEEK' => '%s 周前', 'DAY' => '%s 天前', 'HOUR' => '%s 小时前', 'MINUTE' => '%s 分钟前', 'SECOND' => '%s 秒前');
        }

        $time_now = $time_now == null ? time() : $time_now;
        $seconds = $time_now - $timestamp;

        if ($seconds == 0)
        {
            $seconds = 1;
        }

        if (!$time_limit OR $seconds > $time_limit)
        {
            return date($out_format, $timestamp);
        }

        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $days = floor($hours / 24);
        $weeks = floor($days / 7);
        $months = floor($days / 30);
        $years = floor($months / 12);

        if ($years > 0)
        {
            $diffFormat = 'YEAR';
        }
        else
        {
            if ($months > 0)
            {
                $diffFormat = 'MONTH';
            }
            else
            {
                if($weeks > 0){
                    $diffFormat = 'WEEK';
                }else{
                    if ($days > 0)
                    {
                        $diffFormat = 'DAY';
                    }
                    else
                    {
                        if ($hours > 0)
                        {
                            $diffFormat = 'HOUR';
                        }
                        else
                        {
                            $diffFormat = ($minutes > 0) ? 'MINUTE' : 'SECOND';
                        }
                    }
                }
            }
        }

        $dateDiff = null;

        switch ($diffFormat)
        {
            case 'YEAR' :
                $dateDiff = sprintf($formats[$diffFormat], $years);
                break;
            case 'MONTH' :
                $dateDiff = sprintf($formats[$diffFormat], $months);
                break;
            case 'WEEK' :
                $dateDiff = sprintf($formats[$diffFormat], $weeks);
                break;
            case 'DAY' :
                $dateDiff = sprintf($formats[$diffFormat], $days);
                break;
            case 'HOUR' :
                $dateDiff = sprintf($formats[$diffFormat], $hours);
                break;
            case 'MINUTE' :
                $dateDiff = sprintf($formats[$diffFormat], $minutes);
                break;
            case 'SECOND' :
                $dateDiff = sprintf($formats[$diffFormat], $seconds);
                break;
        }

        return $dateDiff;
    }

    public static function getFileType($fileName){
        $parts      = explode('.', $fileName);
        $ext        = array_pop($parts);
        return $ext;
    }

    public static function getUuid(){
        /*$format = '%s-%s-%04x-%04x-%s';
        return sprintf($format,
            bin2hex(openssl_random_pseudo_bytes(4)),
            bin2hex(openssl_random_pseudo_bytes(2)),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            bin2hex(openssl_random_pseudo_bytes(6))
        );*/

        return uniqid();
    }

    /*发送通知
        $data =[$item_id,$content,$link]
    */
    public static function sendNotice($sender_uid, $recipient_uid, $action_type, $data = array()){
        if (!$recipient_uid OR !$action_type) {
            return false;
        }

        $notice = new Notice();
        $notice->sender_uid = intval($sender_uid);
        $notice->recipient_uid = intval($recipient_uid);
        $notice->action_type = intval($action_type);
        $notice->read_flag = 0;
        $notice->data = $data;

        $notice->save();

        if($user = User::findFirst(intval($recipient_uid))){
            $user->notice_unread = Notice::count([
                'read_flag = 0 AND recipient_uid = :recipient_uid:',
                'bind' => ['recipient_uid' => intval($recipient_uid)]
            ]);
            $user->save();
        }

        return $notice->id;
    }

    /*阅读通知
    */
    public static function readNotice($notice_id, $uid){
        if(!$uid){
            return false;
        }
        $notice = Notice::findFirst(intval($notice_id));
        if($notice->recipient_uid != $uid){
            return false;
        }
        $notice->read_flag = 1;
        $notice->save();

        $user = User::findFirst($notice->recipient_uid);
        $user->notice_unread = Notice::count([
            'read_flag = 0 AND recipient_uid = :recipient_uid:',
            'bind' => ['recipient_uid' => $notice->recipient_uid]
        ]);
        $user->save();
    }

    public static function exportExcel($filename, $content){
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/vnd.ms-execl");
        header("Content-Type: application/force-download");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment; filename=". mb_convert_encoding($filename, 'GBK'));
        header("Content-Transfer-Encoding: binary");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo mb_convert_encoding($content, 'GBK');
    }

    public static function request($url, $method, $post_fields = null, $time_out = 5)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($curl, CURLOPT_TIMEOUT, $time_out);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);

        switch (strtoupper($method))
        {
            case 'POST' :
                curl_setopt($curl, CURLOPT_POST, TRUE);
                if ($post_fields)
                {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);
                }
                break;
            case 'DELETE' :
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if ($post_fields)
                {
                    $url = "{$url}?{$post_fields}";
                }
                break;
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);

        if (substr($url, 0, 8) == 'https://')
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public static function resizeImage($source_file, $new_file, $width, $height, $quality = 90){
        $image = new Imagick($source_file);
        $image->resize($width, $height, Image::PRECISE);
        $image->crop($width,$height, ($image->getWidth() - $width) / 2, ($image->getHeight()-$height) / 2);

        return $image->save($new_file, $quality);
    }

    public static function fetchFileLists($dir, $file_type = null){
        if($file_type){
            if(substr($file_type, 0, 1) == '.'){
                $file_type = substr($file_type, 1);
            }
        }
        $base_dir = realpath($dir);
        $dir_handle = opendir($base_dir);
        $files_list = array();
        while(($file = readdir($dir_handle)) !== false){
            if(substr($file, 0, 1) != '.' AND !is_dir($base_dir . '/' . $file)){
                if(($file_type AND Helper::getFileType($file) == $file_type) OR !$file_type){
                    $files_list[] = $base_dir . '/' . $file;
                }
            }else if(substr($file, 0, 1) != '.' AND is_dir($base_dir . '/' . $file)){
                if($sub_dir_lists = self::fetchFileLists($base_dir . '/' . $file, $file_type)){
                    $files_list = array_merge($files_list, $sub_dir_lists);
                }
            }
        }
        closedir($dir_handle);
        return $files_list;
    }

    public static function sensitiveWordExists($content){
        if(!$content){
            return false;
        }
        if(!$sensitiveWords = Helper::getSetting('sensitive_words')){
            return false;
        }

        if (is_array($content)) {
            foreach($content as $key => $val) {
                if(self::sensitiveWordExists($val)) {
                    return true;
                }
            }
            return false;
        }

        $sensitiveWords = explode("\n", $sensitiveWords);

        foreach($sensitiveWords as $word) {
            $word = trim($word);

            if (!$word) {
                continue;
            }

            if (substr($word, 0, 1) == '{' AND substr($word, -1, 1) == '}') {
                if (preg_match(substr($word, 1, -1), $content)) {
                    return true;
                }
            } else {
                if (strstr($content, $word)) {
                    return true;
                }
            }
        }
    }

    public static function parseAtUser($content, $popup = false, $with_user = false, $to_uid = false){
        //这里增加一道语音的转换,就不另开一个函数了
        $content = Helper::parseVoice($content);
        $content = Helper::parseVideo($content);

        preg_match_all('/@([^@,:\s,，]+)/i', strip_tags($content), $matches);

        if (is_array($matches[1])) {
            $match_name = array();
            foreach ($matches[1] as $key => $user_name) {
                if (in_array($user_name, $match_name)) {
                    continue;
                }
                $match_name[] = $user_name;
            }
            $match_name = array_unique($match_name);
            arsort($match_name);
            $all_users = array();
            $content_uid = $content;

            foreach ($match_name as $key => $user_name) {
                if (preg_match('/^[0-9]+$/', $user_name)) {
                    $user_info = User::findFirstByUid($user_name);
                } else {
                    $user_info = User::findFirstByUser_name($user_name);
                }

                if ($user_info) {
                    if ($with_user) {
                        $all_users[] = $user_info->uid;
                    }else if ($to_uid) {
                        $content_uid = str_replace('@' . $user_name, '@' . $user_info->uid, $content_uid);
                    }else{
                        $content = str_replace('@' . $user_name, '<a href="' . $user_info->getUrl() . '"' . (($popup) ? ' target="_blank"' : '') . ' class="fe-user-name" data-id="' . $user_info->uid . '" rel="nofollow">@' . $user_info->user_name . '</a>', $content);
                    }

                }
            }
        }

        if ($with_user) {
            return $all_users;
        }

        if ($to_uid) {
            return $content_uid;
        }

        return $content;
    }

    public static function parseVoice($content){
        if(strpos($content, '{voice:') !== false){
            return preg_replace_callback('/\{voice:([a-z0-9]+)\}/i', function($matches){
                if ($attach = WeixinVoice::findFirstByFile_key($matches[1]))
                {
                    return Helper::getStaticDI()->getShared('view')->getPartial('partials/voice', ['voice' => $attach]);
                }
            }, $content);
        }

        return $content;
    }
    public static function parseVideo($content){
        if(strpos($content, '{video:') !== false){
            return preg_replace_callback('/\{video:([0-9]+)\}/i', function($matches){
                if ($video = Video::findFirstById($matches[1]))
                {
                    return Helper::getStaticDI()->getShared('view')->getPartial('partials/video', ['video' => $video]);
                }
            }, $content);
        }

        return $content;
    }

    /**
     * 发送模板短信
     */
    public static function sendSMS($mobile, $code)
    {
        require_once( "Sms/TopSdk.php");
        $c = new TopClient;
        $c->appkey = static::getSetting('sms_account');
        $c->secretKey = static::getSetting('sms_apikey');
        $req = new AlibabaAliqinFcSmsNumSendRequest;

        $req->setSmsType("normal");
        $req->setSmsFreeSignName(Helper::getSetting('sms_signname'));
        $req->setSmsParam("{\"code\":\"".$code."\"}");
        $req->setRecNum($mobile);
        $req->setSmsTemplateCode(Helper::getSetting('sms_content'));

        $resp = $c->execute($req);

        return true;
    }

    /**
     * 判断是否是合格的手机客户端
     *
     * @return boolean
     */
    public static function inMobile(){
        if(!$_GET['debug']){
            return false;
        }
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

        if (preg_match('/playstation/i', $user_agent) OR preg_match('/ipad/i', $user_agent) OR preg_match('/ucweb/i', $user_agent)) {
            return false;
        }

        if (preg_match('/iemobile/i', $user_agent) OR preg_match('/mobile\ssafari/i', $user_agent) OR preg_match('/iphone\sos/i', $user_agent) OR preg_match('/android/i', $user_agent) OR preg_match('/symbian/i', $user_agent) OR preg_match('/series40/i', $user_agent)) {
            return true;
        }
        return false;
    }

    //自动生成一个avatar给用户
    public static function autoMakeAvatar(User $user){
        $avatar_list = Helper::fetchFileLists(APP_PATH . 'public/img/avatar/', 'jpg');
        $avatar_id = mt_rand(0, count($avatar_list) - 1);

        $fileDir  = Helper::getSetting('upload_dir') . '/avatar/' . Helper::getAvatar($user->uid, null, 1);
        if(!is_dir($fileDir)){
            Helper::makeDir($fileDir);
        }
        $fileName = Helper::getAvatar($user->uid, null, 2);
        copy($avatar_list[$avatar_id], $fileDir . $fileName);

        //分割
        foreach(static::getStaticDI()->getShared('config')->image->avatar_thumbnail AS $key => $val)
        {
            $thumb_file[$key] = $fileDir . Helper::getAvatar($user->uid, $key, 2);

            $result = Helper::resizeImage($fileDir . $fileName, $thumb_file[$key], $val->w, $val->h);
        }

        if(!$result){
            return false;
        }

        $user->avatar = Helper::getAvatar($user->uid, 'max');
        $user->save();
    }

    public static function analysisKeyword($string){
        $analysis = new Phpanalysis();
        $analysis->SetSource(strtolower($string));
        $analysis->StartAnalysis();

        if ($result = explode(',', $analysis->GetFinallyResult(','))) {
            $result = array_unique($result);

            foreach ($result as $key => $keyword) {
                if(mb_strlen($keyword) < 2){
                    unset($result[$key]);
                }else{
                    $result[$key] = trim($keyword);
                }
            }
        }

        return $result;
    }

    /**
     * 根据特定规则对数组进行排序
     *
     * 提取多维数组的某个键名，以便把数组转换成一位数组进行排序（注意：不支持下标，否则排序会出错）
     *
     * @param  array
     * @param  string
     * @param  string
     * @return array
     */
    public static function aaSort($source_array, $order_field, $sort_type = 'DESC')
    {
        if (! is_array($source_array) or sizeof($source_array) == 0)
        {
            return false;
        }

        foreach ($source_array as $array_key => $array_row)
        {
            $sort_array[$array_key] = $array_row[$order_field];
        }

        $sort_func = ($sort_type == 'ASC' ? 'asort' : 'arsort');

        $sort_func($sort_array);

        // 重组数组
        foreach ($sort_array as $key => $val)
        {
            $sorted_array[$key] = $source_array[$key];
        }

        return $sorted_array;
    }

    //有台上传到优酷
    public static function upToYouku(Video $video){
        $fileLocation = Helper::getSetting('upload_dir') . $video->file_location;
        if(!$fileLocation OR !file_exists($fileLocation)){
            return false;
        }
        $client_id = Helper::getSetting('youku_client_id');
        $client_secret = Helper::getSetting('youku_client_secret');

        $params['access_token'] = "";
        $params['refresh_token'] = "";

        set_time_limit(0);
        ini_set('memory_limit', '128M');
        $youkuUploader = new Uploader($client_id, $client_secret);

        $uploadInfo = array(
            "title" => $video->title,
            "tags" => "LifeStyle", //tags, split by space
            "file_name" => $fileLocation, //video file name
            "file_md5" => @md5_file($fileLocation), //video file's md5sum
            "file_size" => filesize($fileLocation) //video file size
        );
        $progress = true; //if true,show the uploading progress
        $youku_id = $youkuUploader->upload($progress, $params,$uploadInfo);

        $video->youku_id = $youku_id;
        $video->save();
    }

    public static function array_column_multi(array $input, array $column_keys) {
        $result = array();
        $column_keys = array_flip($column_keys);
        foreach($input as $key => $el) {
            $result[$key] = array_intersect_key($el, $column_keys);
        }
        return $result;
    }

    public static function getPinyin($str, $ishead = 0, $isclose = 0){
        static $pinyins;

        $restr = '';
        $str = str_replace(' ', '-', trim($str));
        $slen = strlen($str);

        if($slen<2)
        {
            return $str;
        }

        if (count($pinyins) == 0) {
            $fp = fopen(APP_PATH . 'app/Library/encoding/pinyin-utf8.dat', 'r');
            while (!feof($fp)) {
                $line = trim(fgets($fp));
                $pinyins[$line[0] . $line[1] . $line[2]] = substr($line, 4, strlen($line) - 4);
            }
            fclose($fp);
        }
        for ($i = 0; $i < $slen; $i++) {
            if (ord($str[$i]) > 0x80) {
                $c = $str[$i] . $str[$i + 1] . $str[$i + 2];
                $i = $i + 2;
                if (isset($pinyins[$c])) {
                    if ($ishead == 0) {
                        $restr .= $pinyins[$c];
                    } else {
                        $restr .= $pinyins[$c][0];
                    }
                } else {
                    $restr .= "-";
                }
            } elseif (preg_match("/[a-z0-9\\-]/is", $str[$i])) {
                $restr .= $str[$i];
            } else {
                $restr .= "";
            }
        }

        if($isclose == 1)
        {
            unset($pinyins);
        }

        return $restr;
    }
}