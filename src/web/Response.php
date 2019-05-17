<?php
/**
 * @author xialeistudio
 * @date 2019-05-17
 */

namespace swoole\foundation\web;

use Yii;
use yii\base\InvalidConfigException;

/**
 * Swoole Response Proxy
 * Class Response
 * @package swoole\foundation\web
 */
class Response extends \yii\web\Response
{
    /**
     * @var \Swoole\Http\Response
     */
    private $_response;

    /**
     * @return \Swoole\Http\Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @param \Swoole\Http\Response $response
     */
    public function setResponse($response)
    {
        $this->_response = $response;
    }

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    protected function sendHeaders()
    {
        foreach ($this->getHeaders() as $name => $values) {
            $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
            foreach ($values as $value) {
                $this->_response->header($name, $value);
            }
        }
        $this->_response->status($this->getStatusCode());
        $this->sendCookies();
    }

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    protected function sendCookies()
    {
        if ($this->getCookies() === null) {
            return;
        }
        $request = Yii::$app->getRequest();
        if ($request->enableCookieValidation) {
            if ($request->cookieValidationKey == '') {
                throw new InvalidConfigException(get_class($request) . '::cookieValidationKey must be configured with a secret key.');
            }
            $validationKey = $request->cookieValidationKey;
        }
        foreach ($this->getCookies() as $cookie) {
            $value = $cookie->value;
            if ($cookie->expire != 1 && isset($validationKey)) {
                /** @noinspection PhpUndefinedMethodInspection */
                $value = Yii::$app->getSecurity()->hashData(serialize([$cookie->name, $value]), $validationKey);
            }
            $this->_response->cookie($cookie->name, $value, $cookie->expire, $cookie->path, $cookie->domain, $cookie->secure, $cookie->httpOnly);
        }
    }

    /**
     * @inheritDoc
     */
    protected function sendContent()
    {
        if ($this->stream === null) {
            $this->_response->end($this->content);
            return;
        }

        set_time_limit(0); // Reset time limit for big files
        $chunkSize = 8 * 1024 * 1024; // 8MB per chunk

        if (is_array($this->stream)) {
            list($handle, $begin, $end) = $this->stream;
            fseek($handle, $begin);
            while (!feof($handle) && ($pos = ftell($handle)) <= $end) {
                if ($pos + $chunkSize > $end) {
                    $chunkSize = $end - $pos + 1;
                }
                $this->_response->write(fread($handle, $chunkSize));
            }
            fclose($handle);
            return;
        } else {
            while (!feof($this->stream)) {
                $this->_response->write(fread($this->stream, $chunkSize));
            }
            fclose($this->stream);
        }
        $this->_response->end();
    }
}