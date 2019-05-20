<?php
/**
 * @author xialeistudio
 * @date 2019-05-17
 */

use swoole\foundation\web\Server;
use Swoole\Runtime;

Runtime::enableCoroutine(false);
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', getenv('PHP_ENV') === 'development' ? 'dev' : 'prod');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';


$config = require __DIR__ . '/config/server.php';
$server = new Server($config);
$server->start();