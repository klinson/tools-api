<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/7/2
 * Time: 21:23
 */

namespace App\Handlers;
use \Swoole\WebSocket\Server as WebSocket;

class WebSocketHandler
{
    protected $server;
    protected $config = [
        'host' => '0.0.0.0',
        'port' => 10901
    ];
    const unauthorized_code = 401;
    const fd_not_found = 404;
    const messages = [
        self::unauthorized_code => 'unauthorized',
        self::fd_not_found => 'fd not found',
    ];

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, config('websocket', []), $config);
        $this->server = new WebSocket($this->config['host'], $this->config['port']);

        $this->server->on('open', [$this, 'onOpen']);
        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('close', [$this, 'onClose']);
    }

    public function start()
    {
        $this->server->start();
    }

    public function open(\Closure $function)
    {
        $this->server->on('open', $function);
        return $this;
    }

    public function message(\Closure $function)
    {
        $this->server->on('message', $function);
        return $this;
    }

    public function close(\Closure $function)
    {
        $this->server->on('close', $function);
        return $this;
    }

    public function onOpen(WebSocket $server, $request)
    {
        //TODO: 验证header->Authorization验证登录
//        var_dump($request);

        //TODO: 未登录进行断开，已登录的进行记录
//        $this->disconnect($request->fd, self::unauthorized_code);

        echo "server: handshake success with fd{$request->fd}\n";
    }

    public function onMessage(WebSocket $server, $frame)
    {
        var_dump($server);
        var_dump($frame);
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        $this->push($frame->fd, 'xxxxx');
    }

    public function onClose(WebSocket $server, $fd)
    {
        // TODO: 清除系统记录的fd列表
        var_dump($server);
        var_dump($fd);
        echo "client {$fd} closed\n";
    }

    protected function disconnect($fd, $code)
    {
        $this->server->disconnect($fd, $code, self::messages[$code]);
    }

    public function push($fd, $content, $throwError = false)
    {
        if ($this->server->isEstablished($fd)) {
            $this->server->push($fd, $content);
            return true;
        }
        if ($throwError) {
            throw new \Exception(self::messages[self::fd_not_found], self::fd_not_found);
        }
        return false;
    }
}