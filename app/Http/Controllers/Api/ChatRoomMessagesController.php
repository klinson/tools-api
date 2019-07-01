<?php

namespace App\Http\Controllers\Api;

use App\Models\ChatRoom;
use App\Transformers\ChatMessageTransformer;
use Illuminate\Http\Request;
use Auth;

class ChatRoomMessagesController extends Controller
{
    public function index(ChatRoom $room, Request $request)
    {
        $query = $room->messages()->recent()->limit(10);

        if ($request->last_id) {
            $query->where('id', '<', intval($request->last_id));
        }

        $messages = $query->get();

        return $this->response->collection($messages, new ChatMessageTransformer());
    }

    public function store(ChatRoom $room, Request $request)
    {
        $this->validate($request, [
            'content' => 'required',
            'type' => 'required',
        ], [], [
            'content' => '内容',
            'type' => '类型',
        ]);
        $params = $request->only(['content', 'type']);
        $params['from_user_id'] = Auth::id();

        $message = $room->messages()->create($params);

        return $this->response->item($message, new ChatMessageTransformer());
    }
}
