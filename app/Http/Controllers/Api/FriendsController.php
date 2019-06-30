<?php

namespace App\Http\Controllers\Api;

use App\Models\ChatRoom;
use App\Models\Friend;
use App\Models\User;
use App\Transformers\FavourTransformer;
use App\Transformers\UserTransformer;
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
                $query->where('alias', 'like', $request->q);
            });
        }

       $friends = $query->get();

       $list = [];
       foreach ($friends as $friend) {
           $alias = $friend->alias ?: $friend->friend->nickname;
           $item = [
               'id' => $friend->id,
               'friend_id' => $friend->friend_id,
               'sort' => strtoupper(pinyin_abbr(mb_substr($alias, 0,  1))) ?: '#',
               'alias' => $alias,
               'wxapp_openid' => $friend->friend->wxapp_openid,
               'nickname' => $friend->friend->nickname,
               'sex' => $friend->friend->sex,
               'avatar' => $friend->friend->avatar ?: asset('/images/avatar_'.$friend->friend->sex.'.png'),
           ];
           if (isset($list[$item['sort']]['list'])) {
               $list[$item['sort']]['list'][] = $item;
           } else {
               $list[$item['sort']] = [
                   'title' => $item['sort'],
                   'list' => [$item]
               ];
           }
       }
       ksort($list);

       return [
           'list' => array_values($list),
           'keys' => key($list)
       ];
    }

}
