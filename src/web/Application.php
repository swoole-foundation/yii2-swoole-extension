<?php
/**
 * @author xialeistudio
 * @date 2019-05-17
 */

namespace swoole\foundation\web;

use Throwable;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class Application
 * @package swoole\foundation\web
 * @property Request $request
 * @property Response $response
 * @property \Swoole\Http\Server $server
 */
class Application extends \yii\web\Application
{
    /**
     * @var string 任务处理器命名空间
     */
    public $taskNamespace = 'app\\tasks';

    public function run()
    {
        try {
            $this->state = self::STATE_BEFORE_REQUEST;
            $this->trigger(self::EVENT_BEFORE_REQUEST);

            $this->state = self::STATE_HANDLING_REQUEST;
            $response = $this->handleRequest($this->getRequest());

            $this->state = self::STATE_AFTER_REQUEST;
            $this->trigger(self::EVENT_AFTER_REQUEST);

            $this->state = self::STATE_SENDING_RESPONSE;
            $response->send();

            $this->state = self::STATE_END;

            return $response->exitStatus;
        } catch (Throwable $e) {
            Yii::$app->errorHandler->handleException($e);
            return 1;
        }
    }

    /**
     * @return \Swoole\Http\Server|mixed
     * @throws InvalidConfigException
     */
    public function getServer()
    {
        return $this->get('server');
    }
}