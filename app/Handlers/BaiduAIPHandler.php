<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 18-11-21
 * Time: 上午10:42
 */

namespace App\Handlers;


use Encore\Admin\Config\ConfigModel;
use Illuminate\Support\Facades\Cache;

class BaiduAIPHandler
{
    protected static $aip;
    protected function __construct()
    {
    }
    public static function getInstance($service, $use = 'default')
    {
        if (is_null(self::$aip)) {
            self::$aip = new \Qbhy\BaiduAIP\BaiduAIP(config('baidu_aip'));
        }
        self::$aip->use($use);

        return self::$aip->$service;
    }
}