安装(Install)
=========================

1步 通过Composer安装
-------------------------
> 通过 Composer 安装
如果还没有安装 Composer，你可以按 [getcomposer.org](https://getcomposer.org/) 中的方法安装


2步 创建composer写入内容
-------------------------
> 创建composer.json文件,并写入以下内容

```php
{
	"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/qq1060656096/Cards.git"
        }
    ],
	"require-dev": {
		"wei/cards": "dev-develop"
    }
}	
```


3步 安装
-------------------------
```php
composer install
```

4步 使用单元测试必须创建以下表结构
-------------------------
```php

CREATE TABLE `tbl_demo1` (
  `did` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`did`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `tbl_demo2` (
  `did` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`did`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3965 DEFAULT CHARSET=utf8mb4;
```

5步 请在项目根目录增加config/db.php
-------------------------
> 增加一下内容:
```php
<?php
//数据库配置
return [
    'db_host' => 'localhost',//主机
    'db_port' => 3306,//端口
    'db_user' => 'root',//账户
    'db_pass' => 'root',//密码
    'db_name' => 'demo',//数据库名
    'table_prefix'  => 'tbl_',//表前缀
];



```