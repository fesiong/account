<?php
/**
 * Created by PhpStorm.
 * User: fesiong
 * Date: 4/5/18
 * Time: 10:37 AM
 */
namespace Fesion\Library;

use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\View\Simple;

class Error extends Component
{
    public static $logFile = APP_PATH . 'app/logs/error.log';
    public static $error;

    public static function logger(){
        $messages = [
            'time'    => date('Y-m-d H:i:s'),
            'code'    => static::$error['type'],
            'file'    => static::$error['file'],
            'line'    => static::$error['line'],
            'message' => static::$error['message'],
            'trace'   => static::$error['trace'],
            'end'     => "\r\n"
        ];
        //todo 这里还需要上报错误到接口

        file_put_contents(static::$logFile, implode("\t", $messages), FILE_APPEND);
        unset($messages);

        static::showError();
    }

    public static function showError($message = null, $code = 0){
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");

        if(!$message){
            $message = static::$error['message'];
        }

        Helper::json_output(-1, $message);
        //exit($message);
    }

    public static function handleException($exception){
        static::$error = [
            'type'    => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
            'trace'   => $exception->getTraceAsString(),
        ];
        static::logger();
    }

    public static function handleError($type, $message, $file, $line){
        static::$error = [
            'type'    => $type,
            'message' => $message,
            'file'    => $file,
            'line'    => $line,
        ];

        static::logger();
    }

    public static function handleShutdown(){
        if(($error = error_get_last()) && $error['type'] && !in_array($error['type'], [8]))
        {
            static::$error = $error;
            static::logger();
        }
    }
}