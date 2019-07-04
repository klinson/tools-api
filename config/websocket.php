<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/7/4
 * Time: 00:24
 */

return [
    'host' => env('WEBSOCKET_HOST', '0.0.0.0'),
    'port' => env('WEBSOCKET_PORT', 10901),
    'max_connections' => env('WEBSOCKET_MAX_CONNECTIONs', 100),

    'redis' => [
        'connection' => env('WEBSOCKET_REDIS_CONNECTION', 'default'),
        'uid2fd_key' => env('WEBSOCKET_REDIS_UID2FD_KEY', 'websocket_uid2fd_list'),
        'fd2uid_key' => env('WEBSOCKET_REDIS_FD2Uid_KEY', 'websocket_fd2uid_list'),
    ]


];