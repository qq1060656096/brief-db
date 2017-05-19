<?php
//数据库配置
return [
    'default' => [
        'driver'    => 'mysql',// msyql驱动
        'host'      => 'localhost',// 主机
        'port'      => 3306,// 端口
        'user'      => 'root',// 账户
        'password'  => 'root',// 密码
        'dbname'    => 'demo',// 数据库名
        'table_prefix'  => 'tbl_',// 表前缀
    ],
    'sqlite' => [
        'driver'    => 'sqlite',// sqlite驱动类型
        'user'      => 'root',// 账户
        'password'  => 'root',// 密码
        'path'      => __DIR__.'/sqlite/sqlite.db.php',// sqlite数据库路径
        'memory'    => false,
        'table_prefix'  => 'tbl_',// 表前缀
    ],
];
