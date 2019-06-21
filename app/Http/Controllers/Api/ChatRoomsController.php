<?php

namespace App\Http\Controllers\Api;

use App\Models\ChatRoom;
use App\Transformers\ChatRoomTransformer;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Redis;

class ChatRoomsController extends Controller
{
    public function index(Request $request)
    {
        $query = ChatRoom::query();
        $query->whereHas('hasUsers', function ($query) {
            $query->where('user_id', Auth::id());
        });

        $list = $query->recent()->paginate($request->per_page);

        return $this->response->paginator($list, new ChatRoomTransformer());
    }

}
