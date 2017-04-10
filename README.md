# Base
> 常用公共库

### 1. 获取配置文件
```php
use Wei\Base\Config\Config;
Config::get('table_prefix', 'db.php');
```

### 2. 模型操作
```php
1. 批量插入操作
$rows = [
    [
        'name' => 'data1',
        'age' => 1,
        'uid' => '1',
        'created' => '2017-04-06 20:28',
    ],
    [
        'name' => 'data2',
        'age' => 2,
        'uid' => ['raw'=>'2017+1'],//插入原始数据
        'created' => '2017-04-06 20:28',
    ]
];
$query = new Query();
$result = $query->insertAll($rows);

2. 批量更新
$batchOperation = new BatchOperation();
$where = [
    'name' => '20170406--2340-update1'
];
$data = [
    'name' => '20170406--2340-update11',
    'age' => 17011,
    'uid' => '11',
    'created' => '2017-04-06 23:40:28',
];
$batchOperation->addData($where, $data);
$where = [
    'name' => '20170406--2340-update2'
];
$data = [
    'name' => '20170406--2340-update22',
    'age' => 17022,
    'uid' => '22',
    'created' => '2017-04-06 23:40:28',
];
$batchOperation->addData($where, $data);

$batchOperation->addData($where, $data);
$query = new Query();
$query->from('test');
$result = $query->updateAll($batchOperation);
```

### 单元测试使用

> --bootstrap 在测试前先运行一个 "bootstrap" PHP 文件
* **--bootstrap引导测试:** phpunit --bootstrap ./tests/TestInit.php ./tests/