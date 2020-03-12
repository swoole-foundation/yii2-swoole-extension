# Yii2 Swoole Extension

Running Yii2 application on Swoole Environment.

This extension based on Component-Driven development.

There is no side effects to your business or Yii2 framework.

[中文文档](README-CN.md)

## Get Started

1. Initialize your Yii application
     ```bash
     composer create-project --prefer-dist yiisoft/yii2-app-basic basic
     ```

2. Install this package by composer
     ```bash
     composer require swoole-foundation/yii2-swoole-extension
     ```

3. Create server configuration file.
	```php
	// config/server.php
	<?php
	  return [
	   'host' => 'localhost',
	   'port' => 9501,
	   'mode' => SWOOLE_PROCESS,
	   'sockType' => SWOOLE_SOCK_TCP,
	   'app' => require __DIR__ . '/swoole.php', 
	   'options' => [ // options for swoole server
	       'pid_file' => __DIR__ . '/../runtime/swoole.pid',
	       'worker_num' => 2,
	       'daemonize' => 0,
	       'task_worker_num' => 2,
	   ]
	];
	```

4. Create swoole.php and replace the default web components of Yii2。
	
	> Thanks for [@RicardoSette](https://github.com/RicardoSette)
	
	```php
	// config/swoole.php
	<?php
	
	$config = require __DIR__ . '/web.php';
	
	$config['components']['response']['class'] = swoole\foundation\web\Response::class;
	$config['components']['request']['class'] = swoole\foundation\web\Request::class;
	$config['components']['errorHandler']['class'] = swoole\foundation\web\ErrorHandler::class;
	
	return $config;
	```
	
	
	
5. Create bootstrap file.

  ```php
  // bootstrap.php
  <?php
  /**
   * @author xialeistudio
   * @date 2019-05-17
   */
  
  use swoole\foundation\web\Server;
  use Swoole\Runtime;
  
  // Warning: singleton in coroutine environment is untested!
  Runtime::enableCoroutine();
  defined('YII_DEBUG') or define('YII_DEBUG', true);
  defined('YII_ENV') or define('YII_ENV', getenv('PHP_ENV') === 'development' ? 'dev' : 'prod');
  
  require __DIR__ . '/vendor/autoload.php';
  require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
  
  // require your server configuration
  $config = require __DIR__ . '/config/server.php';
  // construct a server instance
  $server = new Server($config);
  // start the swoole server
  $server->start();
  ```

6. Start your app.
  ```bash
  php bootstrap.php
  ```

7. Congratulations! Your first Yii2 Swoole Application is running!

## Examples

Theres is an complete application in `tests` directory.

## Todo

- [ ] Fix coroutine environment
- [ ] Support for docker
- [ ] Add test case
- [ ] Work with travis-ci

## Contribution

This Project only works because of contributions by users like you!

1. Fork this project
2. Create your branch
3. Make a pull request
4. Wait for merge
