# 🍗 DBWrapper 🍗

DBWrapper is a php wrapper for mysql databases.

## installation

install once with composer:

```
composer require dev-iegomaa/php_mysql_wrapper
```

## usage

```php

/* first make instance */
use DevIegomaa\PhpMysqlWrapper\DB;
$db = new DB('127.0.0.1', 'username', 'password', 'database', 3306);

/* select */
$db->select('users', '*')->query()->getAll();
$db->select('users', 'id', 'name', 'email')->query()->getAll();

$db->select('users', '*')->query()->getRow();
$db->select('users', 'id', 'name', 'email')->query()->getRow();

$db->select('users', '*')->where('id', '=', 5)->query()->getRow();
$db->select('users', '*')->where('id', '>', 5)->query()->getAll();

$db->select('users', '*')->where('id', '>', 5)->andWhere('email', '=', 'ibrahim@admin.com')->query()->getAll();
$db->select('users', '*')->where('id', '>', 5)->orWhere('email', '=', 'ibrahim@admin.com')->query()->getAll();

$db->select('users', '*')->betweenAnd('id', [5, 10])->query()->getAll();

/* insert */
$records = [
    'name' => 'ahmed',
    'email' => 'ahmed@admin.com',
    'password' => '123'
];
$db->insertOrUpdate('insert into', 'users', $records)->query();

/* update */
$records = [
    'name' => 'ahmed',
    'email' => 'ahmed@admin.com',
    'password' => '123'
];
$db->insertOrUpdate('update', 'users', $records)->where('id', '=', 4)->query();
$db->insertOrUpdate('update', 'users', $records)->where('id', '=', 4)->andWhere('email', '=', 'ahmed@admin.com')->query();

/* delete */
$db->delete('users')->query();
$db->delete('users')->where('id','=',4)->query();
$db->delete('users')->where('id','=',4)->orWhere('id', '=', 5)->query();

```

