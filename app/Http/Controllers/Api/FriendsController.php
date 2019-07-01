<?php

namespace App\Http\Controllers\Api;

use App\Models\Friend;
use App\Transformers\FriendTransformer;
use Illuminate\Http\Request;
use Auth;
use Lvht\GeoHash;

class FriendsController extends Controller
{
    public function index(Request $request)
    {
        $query = Friend::with('friend')->where('user_id', \Auth::id())->orderBy('alias');
        if ($request->q) {
            $query->where(function ($query) use ($request) {
                $query->whereHas('friend', function ($query) use ($request) {
                    $query->where('nickname', 'like', $request->q);
                });
                $query->orWhere('alias', 'like', $request->q);
            });
        }

       $friends = $query->get();

       $list = [];
       foreach ($friends as $friend) {
           $alias = $friend->alias ?: $friend->friend->nickname;
           $item = [
               'id' => $friend->id,
               'friend_id' => $friend->friend_id,
               'key' => $friend->key,
               'alias' => $alias,
               'wxapp_openid' => $friend->friend->wxapp_openid,
               'nickname' => $friend->friend->nickname,
               'sex' => $friend->friend->sex,
               'avatar' => $friend->friend->avatar ?: asset('/images/avatar_'.$friend->friend->sex.'.png'),
           ];
           if (isset($list[$item['key']]['list'])) {
               $list[$item['key']]['list'][] = $item;
           } else {
               $list[$item['key']] = [
                   'title' => $item['key'],
                   'list' => [$item]
               ];
           }
       }
       ksort($list);

       return [
           'list' => array_values($list),
           'keys' => array_keys($list)
       ];
    }

    public function show(Friend $friend)
    {
        $this->authorize('is-mine', $friend);

        return $this->response->item($friend, new FriendTransformer());
    }
}
