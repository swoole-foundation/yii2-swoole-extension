<?php
/**
 * @author xialeistudio
 * @date 2019-05-17
 */
$s = new \Swoole\Http\Server('localhost', 9501);
$s->on('request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    foreach ($request->server as $key => $value) {
        $_SERVER[strtoupper($key)] = $value;
    }
    $response->end(json_encode($_SERVER));
});
$s->start();