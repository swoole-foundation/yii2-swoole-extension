<?php
/**
 * @author xialeistudio
 * @date 2019-05-17
 */

namespace swoole\foundation\web;

use Exception;
use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;

/**
 * Class WebServer
 * @package app\servers
 */
class Server extends BaseObject
{
    /**
     * @var string listen address
     */
    public $host = 'localhost';
    /**
     * @var int listen port
     */
    public $port = 9501;
    /**
     * @var int process mode
     */
    public $mode = SWOOLE_PROCESS;
    /**
     * @var int socket type
     */
    public $sockType = SWOOLE_SOCK_TCP;
    /**
     * @var array options for swoole server
     */
    public $options = [
        'worker_num' => 2,
        'daemonize' => 0,
        'task_worker_num' => 2
    ];
    /**
     * @var array application configuration
     */
    public $app = [];
    /**
     * @var \Swoole\Http\Server swoole server instance
     */
    public $server;

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (empty($this->app)) {
            throw new InvalidConfigException('The "app" property mus be set.');
        }

        if (!$this->server instanceof \Swoole\Http\Server) {
            $this->server = new \Swoole\Http\Server($this->host, $this->port, $this->mode, $this->sockType);
            $this->server->set($this->options);
        }

        foreach ($this->events() as $event => $callback) {
            $this->server->on($event, $callback);
        }
    }

    /**
     * listen swoole events
     * @return array
     */
    public function events()
    {
        return [
            'start' => [$this, 'onStart'],
            'workerStart' => [$this, 'onWorkerStart'],
            'workerError' => [$this, 'onWorkerError'],
            'request' => [$this, 'onRequest'],
            'task' => [$this, 'onTask']
        ];
    }

    /**
     * start the server
     * @return bool
     */
    public function start()
    {
        return $this->server->start();
    }

    /**
     * @param \Swoole\Http\Server $server
     */
    public function onStart(\Swoole\Http\Server $server)
    {
        printf("listen on %s:%d\n", $server->host, $server->port);
    }

    /**
     * initialize Yii application on worker started.
     * every worker process has a Yii application
     * @param \Swoole\Http\Server $server
     * @param int $workerId
     * @throws InvalidConfigException
     */
    public function onWorkerStart(\Swoole\Http\Server $server, $workerId)
    {
        new Application($this->app);
        Yii::$app->set('server', $server);
    }


    /**
     * handle worker error
     * @param \Swoole\Http\Server $server
     * @param $workerId
     * @param $workerPid
     * @param $exitCode
     * @param $signal
     */
    public function onWorkerError(\Swoole\Http\Server $server, $workerId, $workerPid, $exitCode, $signal)
    {
        fprintf(STDERR, "worker error. id=%d pid=%d code=%d signal=%d\n", $workerId, $workerPid, $exitCode, $signal);
    }

    /**
     * handle web request
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     */
    public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        Yii::$app->request->setRequest($request);
        Yii::$app->response->setResponse($response);
        Yii::$app->run();
        Yii::$app->response->clear();
    }

    /**
     * dispatch task
     * @param \Swoole\Http\Server $server
     * @param $taskId
     * @param $workerId
     * @param $data
     * @return mixed
     */
    public function onTask(\Swoole\Http\Server $server, $taskId, $workerId, $data)
    {
        try {
            $handler = $data[0];
            $params = $data[1] ?? [];
            list($class, $action) = $handler;

            $obj = new $class();
            return call_user_func_array([$obj, $action], $params);
        } catch (Throwable $e) {
            Yii::$app->errorHandler->handleException($e);
            return 1;
        }
    }
}