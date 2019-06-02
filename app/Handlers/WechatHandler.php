<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 18-11-21
 * Time: 上午10:42
 */

namespace App\Handlers;

use Storage;

class WechatHandler
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

    public function getWxacode($width, $path = '')
    {
        if (! $path) {
            $path = 'pages/tools/index';
        }
        $code_filename = 'mini_code/'.str_replace('/', '_', $path).'_'.$width.'.png';

        // 二维码文件
        if (! Storage::disk('wechat')->exists($code_filename)) {
            $stream = app('wechat.mini_program')->app_code->get($path, [
                'width' => $width
            ]);
            if ($stream instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
                // 以内容 md5 为文件名存到本地
                //      $stream->save('abc');
                // 自定义文件名，不需要带后缀
                //      $stream->saveAs('abc', 'aaa');

                Storage::disk('wechat')->put($code_filename, $stream);
            }
        }

        $code_filepath = Storage::disk('wechat')->path($code_filename);
        return $code_filepath;
    }
}