<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//return [
//    // 生成应用公共文件
//    '__file__' => ['common.php', 'config.php', 'database.php'],
//
//    // 定义demo模块的自动生成 （按照实际定义的文件名生成）
//    'demo'     => [
//        '__file__'   => ['common.php'],
//        '__dir__'    => ['behavior', 'controller', 'model', 'view'],
//        'controller' => ['Index', 'Test', 'UserType'],
//        'model'      => ['User', 'UserType'],
//        'view'       => ['index/index'],
//    ],
//    // 其他更多的模块定义
//];

return [
    // 生成应用公共文件
    '__file__' => ['common.php', 'config.php', 'database.php'],

    // 定义api模块的自动生成 （按照实际定义的文件名生成）
    'api'     => [
        '__file__'   => ['common.php','config.php','database.php'], // 生成的配置文件
        '__dir__'    => ['behavior', 'controller', 'model', 'view'], // 生成文件夹
        'controller' => [], // 生成控制器
        'model'      => [], // 生成模型层
        'view'       => [], // 生成视图
    ],
    // 其他更多的模块定义
];