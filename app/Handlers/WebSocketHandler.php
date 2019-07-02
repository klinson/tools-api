<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/7/2
 * Time: 21:23
 */

namespace App\Handlers;


class WebSocketHandler
{
    protected static $instance;
    protected function __construct()
    {
    }
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function open()
    {
        $server = new Swoole\WebSocket\Server("0.0.0.0", 9501);
    }
}