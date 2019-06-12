<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Redis;

class MessagesController extends Controller
{
    public function count()
    {
        $return = [
            'comment_message_count' => Redis::hget('klinson:user_comment_message_count', Auth::id())
        ];
        return $this->response->array($return);
    }
}
