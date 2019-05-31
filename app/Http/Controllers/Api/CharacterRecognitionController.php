<?php

namespace App\Http\Controllers\Api;

use App\Handlers\BaiduAIPHandler;
use Illuminate\Http\Request;

class CharacterRecognitionController extends Controller
{
    public function general(Request $request)
    {
        $img_base64 = $request->img;
        $res = BaiduAIPHandler::getInstance('ocr')->basicGeneral('', [
            'image' => $img_base64,
        ]);
        $data = '';
        if ($res['words_result_num'] > 0) {

            $data = $res['words_result'];
        }
        return $this->response->array([
            'data' => $data
        ]);
    }
}
