<?php

namespace App\Http\Controllers\Api;

use App\Models\ChatRoom;
use App\Models\Friend;
use App\Transformers\ChatRoomTransformer;
use Illuminate\Http\Request;
use Auth;
use DB;

class ChatRoomsController extends Controller
{
    public function index(Request $request)
    {
        $query = ChatRoom::query();
        $query->whereHas('hasUsers', function ($query) {
            $query->where('user_id', Auth::id());
        });
        $prefix = DB::connection()->getConfig('prefix');

        $subQuery = DB::table('chat_messages')
            ->select(['chat_room_id', DB::raw('max(created_at) as last_message_at')])
            ->groupBy('chat_room_id')
            ->whereNull('deleted_at');

        $query->leftJoin(DB::raw("({$subQuery->toSql()}) as {$prefix}sub"), 'sub.chat_room_id', '=', 'chat_rooms.id');

//        $query->select(['chat_rooms.*', 'chat_messages.created_at as message_created_at', 'chat_messages.content as message_content']);
//        $query->orderBy('message_created_at', 'desc');
        $list = $query->get();

        return $this->response->collection($list, new ChatRoomTransformer());
    }

    public function show(Request $request)
    {
        if ($request->room_id) {
            $room = ChatRoom::find($request->room_id);
        } else if ($request->friend_id) {
            $friend = Friend::find($request->friend_id);
            if (empty($friend) || ! $friend->isThisFriend(\Auth::user())) {
                return $this->response->errorBadRequest('与其不是好友关系，请刷新');
            }
            $room = ChatRoom::createC2C(\Auth::user(), $friend->friend_id);
//        } else if ($request->friend_user_id) {
//            if (Friend::isFriend(\Auth::user(), $request->friend_user_id)) {
//                $room = ChatRoom::createC2C(\Auth::user(), $request->friend_user_id);
//            } else {
//                return $this->response->errorBadRequest('与其不是好友关系，请刷新');
//            }
        } else {
            $room = null;
        }
        if (! $room) {
            return $this->response->errorBadRequest('房间不存在');
        }

        return $this->response->item($room, new ChatRoomTransformer());
    }

}
