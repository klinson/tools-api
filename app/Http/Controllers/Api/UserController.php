<?php

namespace App\Http\Controllers\Api;

use App\Models\UserLocation;
use App\Transformers\UserLocationTransformer;
use App\Transformers\UserTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use Lvht\GeoHash;

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
            'signature' => '个性签名',
            'images' => '个性照'
        ]);

        Auth::user()->fill($request->only(['nickname', 'name', 'sex', 'mobile', 'avatar', 'signature', 'images']));
        Auth::user()->save();
        return $this->response->item(Auth::user(), new UserTransformer(''));
    }

    // 更新位置信息
    public function updateLocation(Request $request)
    {
        if ($request->longitude && $request->latitude) {
            $data = $request->only(['longitude', 'latitude', 'user_id', 'point']);
            $data['user_id'] = Auth::id();
            $data['point'] = \DB::raw("ST_GeomFromText ('POINT({$request->longitude} {$request->latitude})')");
            $data['geohash'] = GeoHash::encode($request->longitude, $request->latitude, 0.000001);
            $data['created_at'] = Carbon::now()->toDateTimeString();

            $userLocation = UserLocation::updateOrCreate(
                ['user_id' => Auth::id()],
                $data
            );
            return $this->response->item($userLocation, new UserLocationTransformer());
        } else {
            return $this->response->errorBadRequest('位置更新失败，经度维度必须');
        }
    }
}
