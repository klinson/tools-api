<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Auth;

class MessagesController extends Controller
{
    public function count()
    {
        $return = [
            'comment_message_count' => Redis::hget('klinson:user_comment_message_count', \Auth::id()) ?: 0,
            'other_count' => 0
        ];
        return $this->response->array($return);
    }

    public function clearMessageCount(Request $request)
    {
        switch ($request->type) {
            case 'all':
                $redis_key = 'klinson:user_comment_message_count';
                Redis::hset($redis_key, Auth::id(), 0);
                break;
            case 'comment':
                $redis_key = 'klinson:user_comment_message_count';
                Redis::hset($redis_key, Auth::id(), 0);
                break;
            default:
                break;
        }

        return $this->response->noContent();
    }
}
