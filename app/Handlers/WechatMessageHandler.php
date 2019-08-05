<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 19-8-5
 * Time: 上午10:03
 */

namespace App\Handlers;


use Illuminate\Support\Facades\Redis;

class WechatMessageHandler
{
    const message_list_key = 'wechat_message_list:';
    const message_menus = [
        'image' => [
            1 => '通用文字识别',
            2 => '通用文字识别（高精度版）',
            3 => '网络图片文字识别',
            4 => '身份证识别',
            5 => '银行卡识别',
            6 => '营业执照识别',
            7 => '表格文字识别',
            8 => '驾驶证识别',
            9 => '行驶证识别',
            10 => '车牌识别',
            11 => '通用票据识别',
            12 => '火车票识别',
        ],
        'image_text' => '[1]：通用文字识别
[2]：通用文字识别（高精度版）
[3]：网络图片文字识别
[4]：身份证识别
[5]：银行卡识别
[6]：营业执照识别
[7]：表格文字识别
[8]：驾驶证识别
[9]：行驶证识别
[10]：车牌识别
[11]：通用票据识别
[12]：火车票识别',



    ];

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

    public function getMenu($type)
    {
        $text = '';
        foreach (self::message_menus[$type] as $key => $menu) {
            $text .= "[$key]：$menu\n";
        }
        return $text;
    }


    public function pushAction($action, $message, $openid = '')
    {
        $data = [
            'action' => $action,
            'data' => $message,
        ];
        if (! $openid) {
            $openid = $message['FromUserName'];
        }
        Redis::rpush(self::message_list_key.$openid, json_encode($data));
    }

    public function pushMessage($message, $openid = '')
    {
        if (! $openid) {
            $openid = $message['FromUserName'];
        }
        Redis::rpush(self::message_list_key.$openid, is_array($message) ? json_encode($message) : $message);
    }

    public function popMessage($openid)
    {
        $message = Redis::rpop(self::message_list_key.$openid);
        if ($info = json_decode($message, true)) {
            return $info;
        } else {
            return $message;
        }
    }

}