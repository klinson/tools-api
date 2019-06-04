<?php

namespace App\Http\Controllers\Api;

use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Auth;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $this->validate($request, [
            'nickname' => 'required',
            'name' => 'required',
            'sex' => 'required',
            'avatar' => 'required',
        ], [], [
            'nickname' => '昵称',
            'name' => '姓名',
            'sex' => '性别',
            'mobile' => '手机号',
            'avatar' => '头像',
        ]);

        Auth::user()->fill($request->only(['nickname', 'name', 'sex', 'mobile', 'avatar']));
        Auth::user()->save();
        return $this->response->item(Auth::user(), new UserTransformer(''));
    }
}
