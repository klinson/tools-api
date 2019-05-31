<?php

namespace App\Http\Controllers\Api;

use App\Handlers\BaiduAIPHandler;
use Illuminate\Http\Request;

/**
 * 文字识别
 * Class CharacterRecognitionController
 * @package App\Http\Controllers\Api
 * @author klinson <klinson@163.com>
 */
class CharacterRecognitionController extends Controller
{
    // 通用
    public function general(Request $request)
    {
        $img_base64 = $request->img;
        $res = BaiduAIPHandler::getInstance('ocr')->basicGeneral('', [
            'image' => $img_base64,
        ]);
        $data = '';
        if (! isset($res['error_code']) && $res['words_result_num'] > 0) {

            $data = $res['words_result'];
        }
        return $this->response->array([
            'data' => $data
        ]);
    }
}
