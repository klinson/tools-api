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
    protected static $services = [];
    protected static $map = [
        'image_censor' => \AipImageCensor::class,
        'image_classify' => \AipImageClassify::class,
        'image_search' => \AipSpeech::class,
        'body_analysis' => \AipBodyAnalysis::class,
        'ocr' => \AipOcr::class,
        'nlp' => \AipNlp::class,
        'speech ' => \AipSpeech::class,
        'kg' => \AipKg::class,
    ];
    protected function __construct() {}

    public static function getInstance($service, $use = null)
    {
        $configs = config('baidu_aip');
        if (is_null($use)) $use = $configs['use'];

        if (! isset(self::$services[$use][$service]) || empty(self::$services[$use][$service])) {
            $config = $configs['applications'][$use];
            $class_name = self::$map[$service];
            $serviceObj = new $class_name($config['app_id'], $config['api_key'], $config['secret_key']);

            self::$services[$use][$service] = $serviceObj;
        }

        return self::$services[$use][$service];
    }
}