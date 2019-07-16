<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/7/2
 * Time: 21:23
 */

namespace App\Handlers;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use \Swoole\WebSocket\Server as WebSocket;
use \Swoole\Http\Request as Request;
use \Auth;

class WebSocketHandler
{
    protected $server;
    protected $redis;
    protected $config = [
        'host' => '0.0.0.0',
        'port' => 10901
    ];
    const success_code = 4200;
    const unauthorized_code = 4401;
    const too_many_clients_login_code = 4402;
    const fd_not_found_code = 4404;
    const messages = [
        self::success_code => 'success',
        self::unauthorized_code => 'unauthorized',
        self::too_many_clients_login_code => 'too_many_clients_login',
        self::fd_not_found_code => 'fd not found',
    ];

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, config('websocket', []), $config);
        $this->server = new WebSocket($this->config['host'], $this->config['port']);

        $this->server->set([
            'max_conn' => $this->config['max_connections'],
        ]);
        $this->server->on('open', [$this, 'onOpen']);
        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('close', [$this, 'onClose']);

        $this->redis = Redis::connection($this->config['redis']['connection']);
        // 清空旧的连接
        $this->redis->del([$this->getUid2FdKey(), $this->getFd2UidKey()]);
    }

    public function onOpen(WebSocket $server, Request $request)
    {
        //获取get->_token验证登录
        if (isset($request->get['_token'])) {
            $token = $request->get['_token'];
        } else {
            $token = '';
        }
        $this->log('connecting', "fd{$request->fd}[{$token}]开始连接");

        if ($user = $this->checkToken($token)) {
            $this->login($request->fd, $user);
            $this->log('connected', "fd{$request->fd}连接成功");
            $this->pushInfo($request->fd, self::success_code);
        } else {
            // 未登录或者token不合法进行断开，已登录的进行记录
            $this->disconnect($request->fd, self::unauthorized_code);
        }
    }

    public function onMessage(WebSocket $server, $frame)
    {
//        var_dump($server);
//        var_dump($frame);
        $this->log('receive', "fd{$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}");
        try {
            $data = json_decode($frame->data, true);
            $this->handleMessage($data);
        } catch (\Exception $exception) {
            $this->log('message', "fd{$frame->fd}处理消息异常:{$exception->getMessage()}[code={$exception->getCode()}][line={$exception->getFile()}:{$exception->getLine()}]");
        }
    }

    /**
     * @param $data
     *
     * @author klinson <klinson@163.com>
     */
    protected function handleMessage($data)
    {
        switch ($data['api']) {
            // 接收聊天消息进行转发同聊天室的人
            case 'chat_message':
                /*
                $json_data = <<<JSON
{
    "api": "chat_message",
    "data": {
        "id": 0,
        "chat_room_id": 1,
        "from_user_id": 2,
        "to_user_id": null,
        "content": "hello, this is test websocket message",
        "type": "1",
        "withdraw_at": null,
        "created_at": "2019-07-14 22:20:45",
        "fromUser": {
            "id": 1,
            "wxapp_openid": "oGfri5AXoXORAWxVMSRS7qdnJEBA",
            "nickname": "klinson",
            "sex": 1,
            "avatar": "https://wx.qlogo.cn/mmopen/vi_32/ZxZRZWq5o7173DQ2pccYaZcsgzvT9bHeKsWMD48u3cDwUvMdaKEwyp6lZwLmeG0JpicjM33ibVLCogGdDZK2lIZQ/132"
        }
    }
}
JSON;
                $data = json_decode($json_data, true);
*/
                $room = ChatRoom::find($data['data']['chat_room_id']);
                foreach ($room->users as $user) {
                    if ($user->id == $data['data']['from_user_id']) {
                        continue;
                    }
                    if ($fd = $this->getFd($user->id)) {
                        // 在线的进行推送
                        if ($this->pushData($fd, $data)) {
                            // 成功就结束
                            continue;
                        }
                    }
                    // 不在线进入离线库
                }
                break;
            default:
                break;
        }
    }

    public function onClose(WebSocket $server, $fd)
    {
        // 清除系统记录的fd登录信息
        $this->logout($fd);
        $this->log('close', "fd{$fd}关闭连接");
    }

    protected function disconnect($fd, $code)
    {
        if ($this->server->isEstablished($fd)) {
            $this->pushInfo($fd, $code);
            $this->server->disconnect($fd, $code, self::messages[$code]);
        }
        $this->logout($fd);
        $this->log('disconnect', "fd{$fd}断开连接[code={$code}]");
    }

    // 统一推送消息
    public function push($fd, $content, $throwError = false)
    {
        $content = json_encode($content);
        $this->log('pushing', "fd{$fd}:{$content}");

        if ($this->server->isEstablished($fd)) {
            if ($this->server->push($fd, $content)) {
                $this->log('pushed-success', "fd{$fd}");
                return true;
            } else {
                $this->log('pushed-fail', "fd{$fd}");
                return false;
            }
        }
        $this->log('push-fail', "fd{$fd}不存在");
        $this->logout($fd);

        if ($throwError) {
            throw new \Exception(self::messages[self::fd_not_found_code], self::fd_not_found_code);
        }
        return false;
    }

    // 推送通知类消息
    public function pushInfo($fd, $code)
    {
        $content = [
            'code' => $code,
            'message' => self::messages[$code],
        ];
        return $this->push($fd, $content, false);
    }

    public function pushData($fd, $data = [])
    {
        $content = [
            'code' => self::success_code,
            'message' => self::messages[self::success_code],
            'data' => $data,
        ];
        return $this->push($fd, $content, false);
    }

    /**
     * 验证登录token
     * @param string $token
     * @author klinson <klinson@163.com>
     * @return bool|User
     */
    protected function checkToken($token)
    {
        if (empty($token)) {
            return false;
        }
        if ($user = Auth::guard('api')->setToken($token)->authenticate()) {
            return $user;
        }
        return false;
    }

    /**
     * 获取当前用户
     * @param $fd
     * @author klinson <klinson@163.com>
     * @return null|User
     */
    protected function getUser($fd)
    {
        if ($uid = $this->redis->hget($this->getFd2UidKey(), $fd)) {
            return User::find($uid);
        }
        return null;
    }

    /**
     * 根据uid获取fd
     * @param $uid
     * @author klinson <klinson@163.com>
     * @return bool
     */
    protected function getFd($uid)
    {
        $fd = $this->redis->hget($this->getUid2FdKey(), $uid);
        if ($this->server->isEstablished($fd)) {
            return $fd;
        }
        return false;
    }

    // 注册在线
    protected function login($fd, $user)
    {
        // 清除旧的fd信息
        if ($old_fd = $this->redis->hget($this->getUid2FdKey(), $user->id)) {
            $this->disconnect($old_fd, self::too_many_clients_login_code);
        }
        $this->redis->hset($this->getUid2FdKey(), $user->id, $fd);
        $this->redis->hset($this->getFd2UidKey(), $fd, $user->id);
    }

    // 删除会话
    protected function logout($fd)
    {
        if ($uid = $this->redis->hget($this->getFd2UidKey(), $fd)) {
            $this->redis->hdel($this->getUid2FdKey(), $uid);
            $this->redis->hdel($this->getFd2UidKey(), $fd);
        }
    }

    public function start()
    {
        $this->log('start', 'websocket启动');
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

    public function log($state, $info, ...$data)
    {
        echo "[".date('Ymd H:i:s')."][{$state}] {$info}\n";
        if ($data) {
            print_r($data);
        }
    }

    protected function getUid2FdKey()
    {
        return $this->config['redis']['uid2fd_key'];
    }

    protected function getFd2UidKey()
    {
        return $this->config['redis']['fd2uid_key'];
    }
}