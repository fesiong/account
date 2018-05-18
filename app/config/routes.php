<?php
/**
 * Created by PhpStorm.
 * User: fesiong
 * Date: 16/8/18
 * Time: 下午3:14
 */

$routes['Index'] = [
    [
        'method' => 'get',
        'route'  => '/index',
        'action' => 'index',
        'authentication' => false
    ], [
        'method' => 'get',
        'route'  => '/nk',
        'action' => 'count',
        'authentication' => false
    ], [
        'method' => 'post',
        'route'  => '/token',
        'action' => 'login',
        'authentication' => false
    ], [
        'method' => 'delete',
        'route'  => '/token',
        'action' => 'logout'
    ], [
        'method' => 'post',
        'route'  => '/user',
        'action' => 'register'
    ], [
        'method' => 'get',
        'route'  => '/notification',
        'action' => 'notification'
    ]
];
$routes['Setting'] = [
    [
        'method' => 'get',
        'route'  => '/setting/clearCache',
        'action' => 'clearCache'
    ], [
        'method' => 'get',
        'route'  => '/setting/clearError',
        'action' => 'clearError'
    ], [
        'method' => 'get',
        'route'  => '/setting/getErrorLog',
        'action' => 'getErrorLog',
        'authentication' => false
    ], [
        'method' => 'get',
        'route'  => '/setting/getSetting/{type}',
        'action' => 'getSetting'
    ], [
        'method' => 'post',
        'route'  => '/setting/saveSetting/{type}',
        'action' => 'saveSetting'
    ]
];
$routes['User'] = [
    [
        'method' => 'post',
        'route'  => '/user/login',
        'action' => 'login',
        'authentication' => false
    ], [
        'method' => 'get',
        'route'  => '/user/logout',
        'action' => 'logout'
    ], [
        'method' => 'get',
        'route'  => '/user/session',
        'action' => 'getUser'
    ], [
        'method' => 'post',
        'route'  => '/user/changePassword',
        'action' => 'changePassword'
    ], [
        'method' => 'get',
        'route'  => '/user/user/list',
        'action' => 'userList'
    ], [
        'method' => 'post',
        'route'  => '/user/user/save',
        'action' => 'saveUser'
    ], [
        'method' => 'get',
        'route'  => '/user/user/get/{id}',
        'action' => 'getUser'
    ], [
        'method' => 'post',
        'route'  => '/user/user/remove',
        'action' => 'removeUser'
    ], [
        'method' => 'get',
        'route'  => '/user/admin/list',
        'action' => 'adminList'
    ], [
        'method' => 'post',
        'route'  => '/user/admin/save',
        'action' => 'saveAdmin'
    ], [
        'method' => 'get',
        'route'  => '/user/admin/get/{id}',
        'action' => 'getAdmin'
    ], [
        'method' => 'post',
        'route'  => '/user/admin/remove',
        'action' => 'removeAdmin'
    ], [
        'method' => 'get',
        'route'  => '/user/group/list',
        'action' => 'groupList'
    ], [
        'method' => 'post',
        'route'  => '/user/group/save',
        'action' => 'saveGroup'
    ], [
        'method' => 'get',
        'route'  => '/user/group/get/{id}',
        'action' => 'getGroup'
    ], [
        'method' => 'post',
        'route'  => '/user/group/remove',
        'action' => 'removeGroup'
    ]
];
$routes['Account'] = [
    [
        'method' => 'get',
        'route'  => '/category/list',
        'action' => 'categoryList',
    ], [
        'method' => 'post',
        'route'  => '/category/save',
        'action' => 'saveCategory'
    ], [
        'method' => 'post',
        'route'  => '/category/remove',
        'action' => 'removeCategory'
    ],
    [
        'method' => 'get',
        'route'  => '/account/list',
        'action' => 'accountList',
    ], [
        'method' => 'get',
        'route'  => '/account/get/{id}',
        'action' => 'getAccount'
    ], [
        'method' => 'post',
        'route'  => '/account/save',
        'action' => 'saveAccount'
    ], [
        'method' => 'post',
        'route'  => '/account/remove',
        'action' => 'removeAccount'
    ]
];
$routes['Site'] = [
    [
        'method' => 'get',
        'route'  => '/site/list',
        'action' => 'list',
    ], [
        'method' => 'post',
        'route'  => '/site/save',
        'action' => 'saveSite'
    ], [
        'method' => 'get',
        'route'  => '/site/get/{id}',
        'action' => 'getSite'
    ], [
        'method' => 'post',
        'route'  => '/site/remove',
        'action' => 'removeSite'
    ]
];
$routes['Plugin'] = [
    [
        'method' => 'get',
        'route'  => '/plugin/list',
        'action' => 'list',
    ], [
        'method' => 'post',
        'route'  => '/plugin/save',
        'action' => 'savePlugin'
    ], [
        'method' => 'get',
        'route'  => '/plugin/get/{id}',
        'action' => 'getPlugin'
    ], [
        'method' => 'post',
        'route'  => '/plugin/remove',
        'action' => 'removePlugin'
    ]
];

$routes['Monitor'] = [
    [
        'method' => 'get',
        'route'  => '/monitor/visit',
        'action' => 'visit',
    ], [
        'method' => 'get',
        'route'  => '/monitor/visit/detail/{id}',
        'action' => 'visitDetail'
    ], [
        'method' => 'get',
        'route'  => '/monitor/action',
        'action' => 'action',
    ], [
        'method' => 'get',
        'route'  => '/monitor/action/detail/{id}',
        'action' => 'actionDetail'
    ], [
        'method' => 'get',
        'route'  => '/monitor/alive',
        'action' => 'alive',
    ], [
        'method' => 'get',
        'route'  => '/monitor/alive/detail/{id}',
        'action' => 'aliveDetail'
    ], [
        'method' => 'get',
        'route'  => '/monitor/error',
        'action' => 'error',
    ], [
        'method' => 'get',
        'route'  => '/monitor/error/detail/{id}',
        'action' => 'errorDetail'
    ], [
        'method' => 'get',
        'route'  => '/monitor/login',
        'action' => 'login',
    ], [
        'method' => 'get',
        'route'  => '/monitor/login/detail/{id}',
        'action' => 'loginDetail'
    ]
];
$routes['Count'] = [
    [
        'method' => 'get',
        'route'  => '/count/site',
        'action' => 'site',
    ], [
        'method' => 'get',
        'route'  => '/count/content',
        'action' => 'content'
    ], [
        'method' => 'get',
        'route'  => '/count/user',
        'action' => 'user'
    ]
];

return $routes;