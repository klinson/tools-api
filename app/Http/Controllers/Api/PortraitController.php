<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 19-5-31
 * Time: 下午5:17
 */

namespace App\Http\Controllers\Api;

use App\Handlers\WechatHandler;
use Illuminate\Http\Request;
use Storage;
use MsXiaoIce\XiaoIce;
use Image;

/**
 * 人像识别
 * Class PortraitController
 * @package App\Http\Controllers\Api
 * @author klinson <klinson@163.com>
 */
class PortraitController extends Controller
{
    protected $types = [
        'score' => [
            'api' => 'appraiseScore',
            'qr_path' => 'pages/tools/portrait/index?type=score'
        ],
        'pk' => [
            'api' => 'competeAppearance',
            'qr_path' => 'pages/tools/portrait/index?type=pk'
        ],
        'cp' => [
            'api' => 'speculateCP',
            'qr_path' => 'pages/tools/portrait/index?type=cp'
        ],
        'who_treat' => [
            'api' => 'whoTreat',
            'qr_path' => 'pages/tools/portrait/index?type=who_treat'
        ],
    ];
    public function index($type, Request $request)
    {
        try {
            $img_base64 = $request->img;
            $method = $this->types[$type]['api'];

            $res = XiaoIce::getInstance()->$method($img_base64, true);
//            $res = [
//                'image_url' => '',
//                'text' => '在各类人群中，德国女士给这张脸评分最高，8.4分。讲真的，这人的下巴，看上去很有傲气'
//            ];

            $res['image_url'] = $this->saveWithQrcode($res['image_url'], $res['text'], $this->types[$type]['qr_path']);
            return $this->response->array($res);
        } catch (\Exception $exception) {
            if ($exception->getCode() <= 4 && $exception->getCode() > 0) {
                return $this->response->errorBadRequest('系统评分发生错误，请稍后重试');
            }
            return $this->response->errorBadRequest($exception->getMessage());
        }
    }


    protected function saveWithQrcode($url, $text = '', $qr_path = '')
    {
        // 文件本地存储
        $file_path = date('Ymd').'-'.uniqid().'1.png';
//        $file_path = '20190602-5cf3b8e625ee5.png';
        Storage::disk('portraits')->put($file_path, file_get_contents($url));
        $file_realpath = Storage::disk('portraits')->path($file_path);
        $image = Image::make($file_realpath);

        // 裁剪去掉小冰水印
        $image->crop($image->width(), $image->height() - 60, 0, 0);

        $add_height = 280;
        $image->resizeCanvas(0, $image->height()-60+$add_height, 'top', false, '#000000');

        // 加上邀请二维码
        $qrcode_path = WechatHandler::getInstance()->getWxacode($add_height, $qr_path);
        $image->insert($qrcode_path, 'bottom-right', 0, 0);

        // 加入文字
        $len = mb_strlen($text);
        if ($len > 0) {
            $i = 0;
            $font_size = $add_height / 3 - 30;
            $line_font_number = 15;
            $line_number = ceil($len / $line_font_number);
            $start_height = $image->height()-$font_size*$line_number+40;

            while ($i < $line_number) {
                $line_text = mb_substr($text, $i*$line_font_number, $line_font_number);

                $image->text($line_text, 20, $start_height+$font_size*$i, function ($font) use ($font_size) {
                    $font->file('font/kaiti.ttf');
                    $font->size($font_size);
                    $font->color('#FF0000');
                });
                $i++;
            }

        }

        $image->save($file_realpath);

        return Storage::disk('portraits')->url($file_path);
    }

    protected function bestCodeWith($height)
    {
        $widths = [
            '1280',
            '860',
            '430',
            '344',
            '280',
        ];
        $tmp = round($height * 0.125);
        $return = $widths[0];
        foreach ($widths as $width) {
            if ($width < $tmp) {
                return $return;
            }
            $return = $width;
        }
        return $return;
    }
}

