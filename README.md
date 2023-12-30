# 🍗 DBWrapper 🍗

DBWrapper is a php wrapper for mysql databases.

## installation

install once with composer:

```
composer require dev-iegomaa/php_mysql_wrapper
```

## ✒️ Don't Forget ✒️

```php
require_once "../vendor/autoload.php";
```

## usage

```php

/** first go to env.php file to set setting */
const SERVER = 'server';
const USERNAME = 'username';
const PASSWORD = 'password';
const DBNAME = 'databasename';
const PORT = 3306;

/** take instance from DB class */
use DevIegomaa\PhpMysqlWrapper\DB;
$db = new DB();

/**
 **  Data Definition Language (DDL) **
 * Create
 * Drop
 * Alter
 * Rename
*/

/** Create New Table */

$schema = [
    'id' => 'tinyint unsigned primary key auto_increment',
    'name' => 'varchar(20) not null',
    'serial' => 'bigint unsigned zerofill not null unique',
    'category_id' => 'tinyint unsigned not null'
];

$db->create('products', $schema)->query();

/** Drop Table */

// Drop One Table
$db->drop(['products'])->query();

// Drop More Than One Table
$db->drop(['products', 'categories', 'users', 'admins'])->query();

/**
 ** Alter Table Methods **
 * ADD: add new item, add primary key, add foreign key.
 * CHANGE: change structure of item.
 * MODIFY: modify structure of item.
 * DROP: drop item structure.
 */

/**
 ** ADD Structure **
 * DataType Constraints Order.
 * By Defualt Added At The End But If You Want To Add It After Any Item Can Use order() function.
 * to add primary key or foreign key use functions [foreignKey, primaryKey]
*/

// Make It Latest Item At The Table
$db
    ->table('products')
    ->alter()
    ->add('price', 'smallint', 'unsigned', 'not null')
    ->query();

// Make It First Item At The Table
$db
    ->table('products')
    ->alter()
    ->add('price', 'smallint', 'unsigned', 'not null')
    ->order(null, 'first')
    ->query();

// Make It After Item At The Table
$db
    ->table('products')
    ->alter()
    ->add('price', 'smallint', 'unsigned', 'not null')
    ->order('name', 'after')
    ->query();

// Add Primary Key
$db
    ->table('products')
    ->alter()
    ->primaryKey('id')
    ->query();

// Add Foerign Key
$db
    ->table('products')
    ->alter()
    ->foreignKey('category_id', 'categories', 'id', 'CASCADE', 'CASCADE')
    ->query();

/**
 ** DROP Structure **
 * Drop index
 * Drop Primary Key
 * Drop Column Structure
 */

/** Drop Column Structure */
$db
    ->table('products')
    ->alter()
    ->dropColumnStructure('price')
    ->query();

/** Drop Indexes */
$db
    ->table('products')
    ->alter()
    ->dropIndex('price')
    ->query();

/**
 * Drop Primary Key
 * if it has auto_increment ? must first delete reset auto_increment then delete primary key.
 */

$db
    ->table('products')
    ->alter()
    ->dropPrimaryKey()
    ->query();

/**
 ** MODIFY **
 */

$db
    ->table('products')
    ->alter()
    ->modify('serial', 'tinyint')
    ->query();

$db
    ->table('products')
    ->alter()
    ->modify('serial', 'tinyint', null, 'AFTER id')
    ->query();

$db
    ->table('products')
    ->alter()
    ->modify('serial', 'tinyint', [
        'unsigned',
        'zerofill',
        'not null'
    ], 'AFTER name')
    ->query();

/**
 ** CHANGE **
 */

$db
    ->table('products')
    ->alter()
    ->change('serial', 'serial_number', 'tinyint')
    ->query();

$db
    ->table('products')
    ->alter()
    ->change('serial', 'serial_number', 'tinyint', null, 'AFTER id')
    ->query();

$db
    ->table('products')
    ->alter()
    ->change('serial', 'serial_number', 'tinyint', [
        'unsigned',
        'zerofill',
        'not null'
    ], 'AFTER name')
    ->query();

/**
 ** RENAME Structure **
 */

$db
    ->table('products')
    ->rename("category")
    ->query();

/**
 ** Data Manipulation Language (DML) **
 * INSERT
 * UPDATE
 * DELETE
 */

/** INSERT */

$data = [
    'id' => 1,
    'name' => 'category1',
    'serial_number' => 45,
    'category_id' => 1
];

/**
 * if id auto_increment you can't write it inside data.
 */

$db
    ->table('category')
    ->insert($data)
    ->query();

/** UPDATE */

$data = [
    'serial_number' => 78,
];

/** Signle Condition */
$db
    ->table('category')
    ->update($data)
    ->where('id', '=', 1)
    ->query();

/** Signle Condition With First Function: Recommended To Use */
$db
    ->table('category')
    ->update($data)
    ->where('id', '=', 1)
    ->first()
    ->query();

/** Multi Condition */
$db
    ->table('category')
    ->update($data)
    ->where('id', '=', 1)
    ->andWhere('name', '=', 'category1')
    ->andWhere('category_id', '=', 1)
    ->query();

/** Multi Condition */
$db
    ->table('category')
    ->update($data)
    ->where('id', '=', 1)
    ->orWhere('name', '=', 'category1')
    ->andWhere('category_id', '=', 1)
    ->query();

/** DELETE */

/** Delete One Record */
$db
    ->table('category')
    ->delete()
    ->where('id', '=', 1)
    ->query();

/** Delete One Record With First Function: Recommend To Use */
$db
    ->table('category')
    ->delete()
    ->where('id', '=', 1)
    ->first()
    ->query();

/** Delete Record */
$db
    ->table('category')
    ->delete()
    ->where('id', '>', 1)
    ->query();

/** Delete Multi Record */
$db
    ->table('category')
    ->delete()
    ->betweenAnd('id', [1, 10])
    ->query();

/** Delete All Records */
$db
    ->table('category')
    ->delete()
    ->query();


/**
 ** Data Query Language (DQL) **
 * SELECT
 */

/** Select All Record */

$schema = [
    'category' => ['*']
];

$db
    ->table('category')
    ->select($schema)
    ->query()
    ->getAll();

/** Select Special Record */

$schema = [
    'category' => ['id', 'name']
];

$db
    ->table('category')
    ->select($schema)
    ->query()
    ->getAll();

/** Select Only Record */

$schema = [
    'category' => ['id', 'name']
];

$db
    ->table('category')
    ->select($schema)
    ->query()
    ->getRow();

/** Order By */

/** ASC Is Defualt You Can Write It Or Non. */

$schema = [
    'category' => ['id', 'name']
];

$db
    ->table('category')
    ->select($schema)
    ->orderBy(['id', 'name'])
    ->query()
    ->getAll();

/** DESC */

$schema = [
    'category' => ['id', 'name']
];

$db
    ->table('category')
    ->select($schema)
    ->orderBy(['id', 'name'], 'DESC')
    ->query()
    ->getAll();

/** Limit: Determine Number Of Records You Want. */

$schema = [
    'category' => ['id', 'name']
];

$db
    ->table('category')
    ->select($schema)
    ->limit(5)
    ->query()
    ->getAll();


/** First: find first record ir will found at table. Recommended To use */

$schema = [
    'category' => ['id', 'name']
];

$db
    ->table('category')
    ->select($schema)
    ->first()
    ->query()
    ->getAll();

/** Find Record By Id. */

$schema = [
    'category' => ['id', 'name']
];

$db
    ->table('category')
    ->select($schema)
    ->find(1);

/** INNERJOIN */

$schema = [
    'users' => [
        'id',
        'name',
        'email'
    ],
    'doctors' => [
        '*'
    ]
];

$db
    ->table('users')
    ->select($schema)
    ->innerJoin('doctors', 'doctor_id', 'user_id')
    ->query()
    ->getAll();

/** LEFTJOIN */

$schema = [
    'users' => [
        'id',
        'name',
        'email'
    ],
    'doctors' => [
        '*'
    ]
];

$db
    ->table('users')
    ->select($schema)
    ->rightJoin('doctors', 'doctor_id', 'user_id')
    ->query()
    ->getAll();

/** RIGHTJOIN */

$schema = [
    'users' => [
        'id',
        'name',
        'email'
    ],
    'doctors' => [
        '*'
    ]
];

$db
    ->table('users')
    ->select($schema)
    ->rightJoin('doctors', 'doctor_id', 'user_id')
    ->query()
    ->getAll();

```

## Note ✒️

```php

/**
 ** after insert or update or delete recommended to use $db->affectedRow() and check retrivement value if 1 it means successfully otherwise failure.*/

($db->affectedRow() === 1) ? 'DONE' : 'ERROR';

```
