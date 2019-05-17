<?php
/**
 * web应用配置
 * @author xialeistudio
 * @date 2019-05-17
 */

use swoole\foundation\web\ErrorHandler;
use swoole\foundation\web\Request;
use swoole\foundation\web\Response;
use yii\caching\FileCache;

return [
    'id' => 'tests',
    'name' => 'tests',
    'basePath' => dirname(__DIR__),
    'language' => 'zh-CN',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'tests\\controllers',
    'taskNamespace' => 'tests\\tasks',
    'aliases' => [
        '@tests' => dirname(__DIR__),
    ],
    'components' => [
        'response' => [
            'class' => Response::class,
            'format' => Response::FORMAT_JSON
        ],
        'request' => [
            'class' => Request::class,
            'cookieValidationKey' => '123456'
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ]
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'class' => ErrorHandler::class
        ],
        'db' => [
            'class' => '\yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=bbs',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8mb4',
        ],
        'cache' => [
            'class' => FileCache::class
        ]
    ],
    'params' => require __DIR__ . '/params.php'
];