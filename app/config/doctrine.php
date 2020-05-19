<?php

namespace App;

return [
    'db.options_test' => [
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/../app/db/app.db',
    ],
    'db.options' => [
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'port' => '3306',
        'dbname' => 'atom',
        'user' => 'root',
        'password' => 'Root@123!!',
        'charset' => 'utf8',
    ],
    'orm.proxies_dir' => __DIR__ . '/../cache/doctrine/proxies',
    'orm.em.options' => [
        'default_cache' => 'array',
        'mappings' => [
            [
                'type' => 'annotation',
                'use_simple_annotation_reader' => false, // not document
                'namespace' => __NAMESPACE__ . '\Entities',
                'path' => __DIR__ . '/../src/' . __NAMESPACE__ . '/Entities',
            ],
        ],
    ],
];