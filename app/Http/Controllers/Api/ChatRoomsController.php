<?php

namespace App\Http\Controllers\Api;

use App\Models\ChatRoom;
use App\Transformers\ChatRoomTransformer;
use Illuminate\Http\Request;
use Auth;

class ChatRoomsController extends Controller
{
    public function index(Request $request)
    {
        $query = ChatRoom::query();
        $query->whereHas('hasUsers', function ($query) {
            $query->where('user_id', Auth::id());
        });

        $list = $query->recent()->get();

        return $this->response->paginator($list, new ChatRoomTransformer());
    }

    public function getRoom(Request $request)
    {
        if ($request->room_id) {
            $room = ChatRoom::find($request->room_id);
        } else if ($request->friend_id) {
            $room = ChatRoom::getRoom(\Auth::user(), $request->friend_id);
        } else {
            $room = null;
        }
        if (! $room) {
            return $this->response->errorBadRequest('房间不存在');
        }

        return $this->response->item($room, new ChatRoomTransformer());
    }

}
