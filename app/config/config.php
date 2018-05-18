<?php

use Phalcon\Config;
use Phalcon\Logger;

/**
 * @var $di Phalcon\Di\FactoryDefault
 */


$config = [
    'application' => [
        'appDir'         => APP_PATH . 'app/',
        'controllersDir' => APP_PATH . 'app/Controllers/',
        'modelsDir'      => APP_PATH . 'app/Models/',
        'migrationsDir'  => APP_PATH . 'app/Migrations/',
        'viewsDir'       => APP_PATH . 'app/views/',
        'pluginsDir'     => APP_PATH . 'app/Plugins/',
        'libraryDir'     => APP_PATH . 'app/Library/',
        'cacheDir'       => APP_PATH . 'app/cache/',
        'phalconDir'     => APP_PATH . 'Phalcon/',
        'adminDir'       => APP_PATH . 'app/Admin/Controllers/',
        'adminViewsDir'  => APP_PATH . 'app/Admin/views/',

    ],
    'metadata' => [
        'adapter'     => 'Files',
        'metaDataDir' => APP_PATH . 'app/cache/metaData/',
    ],
    'dataCache' => [
        'backend'  => 'File',
        'frontend' => 'Data',
        'lifetime' => 30 * 24 * 60 * 60,
        'prefix'   => 'fesiong-data-cache-',
        'cacheDir' => APP_PATH . 'app/cache/data/',
    ],
    'modelsCache' => [
        'backend'  => 'File',
        'frontend' => 'Data',
        'lifetime' => 30 * 24 * 60 * 60,
        'prefix'   => 'fesiong-models-cache-',
        'cacheDir' => APP_PATH . 'app/cache/models/',
    ],
    'beanstalk' => [
        'disabled' => true,
        'host'     => '127.0.0.1'
    ],
    'image' => [
        'avatar_thumbnail' => [
            'min' => [
                "w" => 32,
                "h" => 32
            ],
            'mid' => [
                "w" => 50,
                "h" => 50
            ],
            'max' => [
                "w" => 100,
                "h" => 100
            ],
            'thumb' => [
                "w" => 150,
                "h" => 185
            ],
        ],
        'attachment_thumbnail' => [
            'mid' => [
                'w' => 640,
                'h' => 360
            ],
            'min' => [
                'w' => 160,
                'h' => 160
            ],
            //event 的海报尺寸
            'thumb' => [
                'w' => 175,
                'h' => 260
            ]
        ]
    ],
    'adminMenu' => [
        [
            'title' => '全局设置',
            'cname' => 'gear',
            'children' => [
                [
                    'id' => 1,
                    'title' => '站点信息',
                    'url' => 'setting/site/'
                ],
                [
                    'id' => 2,
                    'title' => '注册访问',
                    'url' => 'setting/register/'
                ],
                [
                    'id' => 3,
                    'title' => '内容设置',
                    'url' => 'setting/content/'
                ],
                [
                    'id' => 4,
                    'title' => '开放平台设置',
                    'url' => 'setting/oauth/'
                ],
                [
                    'id' => 5,
                    'title' => '短信接口配置',
                    'url' => 'setting/sms/'
                ],
                [
                    'id' => 6,
                    'title' => '系统维护',
                    'url' => 'setting/setting/'
                ]
            ]
        ],
        [
            'title' => '内容管理',
            'cname' => 'file-text',
            'children' => [
                [
                    'id' => 101,
                    'title' => '资讯管理',
                    'url' => 'blog/list/'
                ],
                [
                    'id' => 102,
                    'title' => '主题管理',
                    'url' => 'topic/list/'
                ],
                /*[
                    'id' => 103,
                    'title' => '圈子管理',
                    'url' => 'group/list/'
                ],*/
                [
                    'id' => 104,
                    'title' => '公开课管理',
                    'url' => 'course/list/'
                ],
                [
                    'id' => 105,
                    'title' => '病历',
                    'url' => 'medical/list/'
                ],
                [
                    'id' => 106,
                    'title' => '单页管理',
                    'url' => 'page/list/'
                ],
                [
                    'id' => 107,
                    'title' => '话题管理',
                    'url' => 'category/list/'
                ],
                [
                    'id' => 108,
                    'title' => '回应管理',
                    'url' => 'answer/list/'
                ],
                [
                    'id' => 109,
                    'title' => '评论管理',
                    'url' => 'comment/list/'
                ],
                [
                    'id' => 110,
                    'title' => '康复日记',
                    'url' => 'medical/note/'
                ],
                [
                    'id' => 111,
                    'title' => '感谢信',
                    'url' => 'thanks/list/'
                ],
                [
                    'id' => 112,
                    'title' => '服务中心',
                    'url' => 'service/list/'
                ]
            ]
        ],
        [
            'title' => '用户管理',
            'cname' => 'user',
            'children' => [
                [
                    'id' => 201,
                    'title' => '用户列表',
                    'url' => 'user/list/'
                ],
                [
                    'id' => 202,
                    'title' => '系统管理员',
                    'url' => 'user/admin/'
                ]
            ]
        ],
        [
            'title' => '审核管理',
            'cname' => 'question',
            'children' => [
                [
                    'id' => 301,
                    'title' => '用户举报',
                    'url' => 'examine/report/'
                ],
                [
                    'id' => 302,
                    'title' => '主题审核',
                    'url' => 'examine/topic/'
                ],
                [
                    'id' => 303,
                    'title' => '主题回应审核',
                    'url' => 'examine/answer/'
                ],
                [
                    'id' => 304,
                    'title' => '公开课回应审核',
                    'url' => 'examine/courseanswer/'
                ],
                [
                    'id' => 305,
                    'title' => '感谢信审核',
                    'url' => 'examine/thanks/'
                ],
                [
                    'id' => 306,
                    'title' => '评论审核',
                    'url' => 'examine/comment/'
                ]
            ]
        ],
        [
            'title' => '微信管理',
            'cname' => 'wechat',
            'children' => [
                [
                    'id' => 401,
                    'title' => '微信菜单管理',
                    'url' => 'weixin/menu/'
                ],
                [
                    'id' => 402,
                    'title' => '自定义回应',
                    'url' => 'weixin/reply/'
                ],
                [
                    'id' => 403,
                    'title' => '接收的消息',
                    'url' => 'weixin/message/'
                ],
            ]
        ]
    ],
];

$config = array_merge($config, require 'site.php');

return new Config($config);