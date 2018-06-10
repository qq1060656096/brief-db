# brief-db
> brief-db为了减少Doctrine2数据库操作的一些重复工作

|驱动类型   | 驱动名   | 是否支持  |
| -------- |:-------:| -------- |
| MYSQL    | mysql   | 支持      |
| Sqlite   | sqlite  | 暂不支持(未开发) |


## 1 安装(Install)
> 1. 通过Composer安装
> 2. 创建composer.json文件,并写入以下内容:

```php
{
 "repositories": [
   {
     "type": "vcs",
     "url": "https://github.com/qq1060656096/brief-db.git"
   }
 ],
 "require": {
 "wei/brief-db": "1.0.0"
 }
}
```
> 3. 执行composer install

> 4. 请在项目根目录增加下创建"config/db.php"文件增加一下内容:

```php
<?php
//数据库配置
return [
    // 默认数据库配置
   'default' => [
       'driver'    => 'mysql',// msyql驱动
       'host'      => 'localhost',// 主机
       'port'      => 3306,// 端口
       'user'      => 'root',// 账户
       'password'  => 'root',// 密码
       'dbname'    => 'demo',// 数据库名
       'table_prefix'  => 'tbl_',// 表前缀
   ],
];
```

## 2. 所有驱动名都在lib\Database\Driver\DriverName.php中定义

## 3. 数据操作
```php
<?php
// 引入要用的类
use Zwei\BriefDB\Database\Db;

// 删除
Db::getDelete()
->from('test')->condition('name', 'test_01')->delete();

// 删除2
Db::getSelect()
->from('test')
//like查询
->condition('name', 'test_01%', 'like')
// in条件
->condition('name', ['xiao1','xiao2'])
//复杂条件
->conditionComplex("(age >= ? and age <= ?) or created like ?", [60, 70, '2017-05-18 17:28%'])
->delete();

// 插入
$insertData = [
    'name' => 'insert01',
    'age' => 1,
    'uid' => 1,
];
Db::getInsert()
    ->from('test')->insert($insertData);

// 批量插入
$insertData = [
    [
        'name' => 'insertAll01',
        'age' => 1,
        'uid' => 2,
    ],
    [
        'name' => 'insertAll02',
        'age' => 3,
        'uid' => 4,
    ]
];
$result = Db::getInsert()
    ->from('test')->insertAll($insertData);


// 更改
$updateData = [
    'name' => 'update01',
    'age' => 33,
    'uid' => [
        'raw' => "? + ? - ?",
        1,
        2,
        3
    ],
];
// set部分: `name` = '20170531.0848',`age` = '173710',`uid` = '1' + '2' - '3'
Db::getUpdate()
    ->from('test')->condition('name', 'update01')->update($updateData);


// 批量更改
$bathUpdate = new BatchUpdate();
$condition  = new Condition('AND');
$condition->condition('name', 'updateBatch01', 'like');
$data       = [
    'age' => 1815080100,
    'uid' => 18150801200,
    'created' => '2017-05-18 15:08:01',
];
$bathUpdate->addData($condition, $data);
$data = [
    'age' => 1815080200,
    'uid' => 18150802200,
    'created' => '2017-05-18 15:08:02',
];
$condition  = new Condition('AND');
$condition->condition('name', 'updateBatch02', 'like');
// in条件
$condition->condition('name', ['xiao1','xiao2']);
$bathUpdate->addData($condition, $data);
Db::getUpdate()
    ->from('test')->updateAll($bathUpdate);
    
    
// 查询
$select = Db::getSelect()
    ->fields('name,id')
    ->fields(['uid', 'age'])
    ->from('test')
    ->condition('name', 'QueryFactorSelect00%', 'like')
//  ->groupBy('age')
    ->orderBy('id', 'DESC');
// 查询单行数据
$row = $select->findOne();
// 查询多行数据
$rows = $select->findAll();
// 查询总条数
$count = $select->findCount();

// 关联查询
$select = Db::getSelect();
$select->from('test t1');
$select->condition('t2.name', 'SelectTest::testFindAll-20170518-1256%', 'like');
// in查询
$select->condition('t2.name', ['xiao1','xiao2']);
$select->fields('*');
// 左联查询
$select->leftJoin('test t2', 'on t2.id = t1.id');
// 右联查询
$select->rightJoin('test t3', 'on t3.id = t2.id');
// 内联查询
$select->innerJoin('test t4', 'on t4.id = t3.id');
$select->findAll();


// 获取执行sql
// 启用sql日志
Db::enabledSqlLog();
// 查询: SELECT count(*) FROM test WHERE name LIKE '20170816.2359.se%'
$count = Db::getSelect()->from('test')
    ->condition('name', '20170816.2359.se%', 'like')
    ->findCount();
// 获取执行sql
$rawSql = Db::getLastRawSql();
// 打印sql日志
print_r($rawSql);
```

## 4. 获取配置文件
```php
use Zwei\Base\Config\Config;
Config::get('table_prefix', 'db.php');
```

### 单元测试使用

> --bootstrap 在测试前先运行一个 "bootstrap" PHP 文件
* **--bootstrap引导测试:** phpunit --bootstrap ./tests/TestInit.php ./tests/
** 测试打印原生sql **
* phpunit --bootstrap ./tests/TestInit.php ./tests/Database/DbTest.php --filter testGetRawSql