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
        'text' => [
            1 => '文字加密',
            2 => '文字解密',
            3 => '句子错别字审查',
        ],
        'image' => [
            1 => '通用文字识别',
            2 => '通用文字识别（高精度版）',
            3 => '网络图片文字识别',
            4 => '身份证识别',
            5 => '银行卡识别',
            6 => '营业执照识别',
            7 => '驾驶证识别',
            8 => '行驶证识别',
            9 => '车牌识别',
            10 => '通用票据识别',
            11 => '火车票识别',
        ],
        'text_text' => '请选择操作功能？
[1]：文字加密,
[2]：文字解密,
[3]：句子错别字审查',
        'image_text' => '请选择操作功能？
[1]：通用文字识别
[2]：通用文字识别（高精度版）
[3]：网络图片文字识别
[4]：身份证识别
[5]：银行卡识别
[6]：营业执照识别
[7]：驾驶证识别
[8]：行驶证识别
[9]：车牌识别
[10]：通用票据识别
[11]：火车票识别',


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
        if (isset(self::message_menus[$type.'_text'])) {
            return self::message_menus[$type.'_text'];
        }
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