# yii2 swoole extension
在swoole环境下运行Yii2应用。 

yii2-swoole基于Yii2组件化进行编程，对业务和Yii2无侵入性。

## 快速开始

1. 初始化Yii2应用
1. 安装扩展
    ```bash
    composer require swoole-foundation/yii2-swoole-extension
    ```
1. 新建服务器配置(`config/server.php`)
    ```php
   <?php
   /**
    * @author xialeistudio
    * @date 2019-05-17
    */
   return [
       'host' => 'localhost',
       'port' => 9501,
       'mode' => SWOOLE_PROCESS,
       'sockType' => SWOOLE_SOCK_TCP,
       'app' => require __DIR__ . '/web.php', // 原来的web.php配置
       'options' => [
           'pid_file' => __DIR__ . '/../runtime/swoole.pid',
           'worker_num' => 2,
           'daemonize' => 0,
           'task_worker_num' => 2,
       ]
   ];
    ```
1. 新增启动脚本(`index.php`)
    ```php
    <?php
    /**
     * @author xialeistudio
     * @date 2019-05-17
     */
    
    use swoole\foundation\web\Server;
    use Swoole\Runtime;
    
    Runtime::enableCoroutine();
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', getenv('PHP_ENV') === 'development' ? 'dev' : 'prod');
    
    require __DIR__ . '/vendor/autoload.php';
    require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
    
    
    $config = require __DIR__ . '/config/server.php';
    $server = new Server($config);
    $server->start();
    ```
1. 启动应用
    ```bash
    php index.php
    ```
 
## 示例项目

**tests** 目录下有测试用的完整项目。

## TODO

+ [ ] 协程环境下兼容