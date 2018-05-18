<?php

return [
    'site' => [
        'name'        => '我的账号管理',
        'scheme'      => $di->getShared('request')->getScheme() . '://',
        'baseUri'     => $di->getShared('request')->getScheme() . '://' . $di->getShared('request')->getHttpHost() . '/',
    ],
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        'password'    => '123456',
        'dbname'      => 'account',
        'charset'     => 'utf8mb4',
        'prefix'      => 'fe_',
    ],
    'debug' => true
];