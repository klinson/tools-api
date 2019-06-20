<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Transformers\FavourTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Auth;
use Lvht\GeoHash;

class FavoursController extends Controller
{
    // 附近未匹配人
    public function nearbyUsers(Request $request)
    {
        $longitude = $request->longitude;
        $latitude = $request->latitude;

        $geohash = GeoHash::encode($longitude, $latitude, 0.000001);
//        dd($geohash);
        $geohash_perfix = substr($geohash, 0 ,2);
//        $res = GeoHash::expand($geohash, 1);

        $favours = \DB::table('favours')->where('from_user_id', Auth::id())->select('to_user_id')->get()->pluck('to_user_id')->toArray();
        if (Auth::check()) {
            $favours[] = Auth::id();
        }

//        $count = \DB::table('user_locations')
//            ->where('geohash', 'like', $res[0].'%')
//            ->whereNotIn('user_id', $favours)
//            ->count();
//        dd($count);

        $users = User::with('location')->whereHas('location', function ($query) use ($geohash_perfix, $favours) {
            $query->where('geohash', 'like', $geohash_perfix.'%');

            if ($favours) {
                $query->whereNotIn('user_id', $favours);
            }
        })->limit(10)->get();

        return $this->response->collection($users, new UserTransformer('location', [
            'longitude' => $longitude,
            'latitude' => $latitude
        ]));
    }

    public function favour(User $user)
    {
        Auth::user()->favour($user);
        if (Auth::user()->isFavourMe($user)) {
            // 成为组合
            // TODO: 创建聊天室
            return $this->response->array([
                'is_coupled' => 1,
            ]);
        } else {
            return $this->response->array([
                'is_coupled' => 0,
            ]);
        }
    }

    public function unfavour(User $user)
    {
        Auth::user()->favour($user, false);
        return $this->response->array([
            'is_coupled' => 0,
        ]);
    }
}
