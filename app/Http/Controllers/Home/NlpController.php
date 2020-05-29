<?php
/**
 * Created by PhpStorm.
 * User: admin <klinson@163.com>
 * Date: 2020/5/29
 * Time: 16:47
 */

namespace App\Http\Controllers\Home;


use App\Handlers\BaiduAIPHandler;
use Illuminate\Http\Request;

class NlpController extends Controller
{
    public function simple(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->only(['text1', 'text2', 'model']);
            $res = BaiduAIPHandler::getInstance('nlp', 'nlp')->simnet($data['text1'], $data['text2'], [
//                'model' => $data['model'],
            ]);
            dd($res, $data);
            return [
                'ret' => 1,//1success,0error
                ''
            ];
        } else {
            return $this->view();
        }
    }

    protected function toGbk($str)
    {
        return iconv("UTF-8","gbk//TRANSLIT",$str);
    }
}