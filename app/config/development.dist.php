<?php
/**
 * Created by PhpStorm.
 * User: fesiong
 * Date: 16/8/18
 * Time: 上午11:50
 */
use Phalcon\Config;

return new Config([
    'metadata' => [
        'adapter' => 'Memory',
    ],
    'dataCache' => [
        'backend'  => 'Memory',
        'frontend' => 'None',
    ],
    'modelsCache' => [
        'backend'  => 'Memory',
        'frontend' => 'None',
    ],
    'viewCache' => [
        'backend' => 'Memory',
    ],
]);