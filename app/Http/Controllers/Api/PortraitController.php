<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 19-5-31
 * Time: 下午5:17
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use MsXiaoIce\XiaoIce;

/**
 * 人像识别
 * Class PortraitController
 * @package App\Http\Controllers\Api
 * @author klinson <klinson@163.com>
 */
class PortraitController extends Controller
{
    public function score(Request $request)
    {
        $img_base64 = $request->img;
        $res = XiaoIce::getInstance()->appraiseScore($img_base64, true);
        return $this->response->array($res);
    }
}