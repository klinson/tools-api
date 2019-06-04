<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 18-10-22
 * Time: 下午12:49
 */

namespace App\Handlers;

use Amap\Amap;

class AmapHandler
{
    protected $Amap;
    protected static $instance;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $options = [
            'sign' => config('amap.sign'), //是否进行数字签名，默认不签名
            'private_key' => config('amap.private_key'), //数字签名私钥，sign=true时必填
            'key' => config('amap.key')//api调用key，必填
        ];

        $this->Amap = new Amap($options);
    }

    public function getAmap()
    {
        return $this->Amap;
    }
}