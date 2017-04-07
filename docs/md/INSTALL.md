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

4步 使用单元测试必须创建test表
-------------------------
```php
CREATE TABLE IF NOT EXISTS `test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `age` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=284 ;

```

5步 请在项目根目录增加config/db.php
-------------------------
> 增加一下内容:
```php
<?php
//数据库配置
return [
    'db_user' => 'root',
    'db_pass' => 'root',
    'db_host' => 'localhost',
    'db_port' => 3306,
    'db_name' => 'demo',
];


```