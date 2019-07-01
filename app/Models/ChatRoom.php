<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
class ChatRoom extends Model
{
    use SoftDeletes;

    protected $fillable = ['create_user_id', 'owner_user_id', 'title', 'announcement', 'type'];

    public static function getRoom($user, $author)
    {
        if (empty($user) && empty($author)) {
            return null;
        }
        $room = ChatRoom::where('type', 1)
            ->whereHas('hasUsers', function ($query) use ($user) {
                $query->where('user_id', is_object($user) ? $user->id : $user);
            })->whereHas('hasUsers', function ($query) use ($author) {
                $query->where('user_id', is_object($author) ? $author->id : $author);
            })->first();
        return $room;
    }

    // 用户私聊创建房间
    public static function createC2C($fromUser, $toUser)
    {
        $room = static::getRoom($fromUser, $toUser);
        if (empty($room)) {
            $room = new static([
                'create_user_id' => $fromUser->id,
                'owner_user_id' => 0,
                'type' => 1,
            ]);
            DB::transaction(function () use (&$room, $fromUser, $toUser) {
                $room->save();
                $room->hasUsers()->createMany([
                    [
                        'user_id' => $fromUser->id,
                    ],
                    [
                        'user_id' => $toUser->id,
                    ]
                ]);
            });
        }

        return $room;
    }

    public function hasUsers()
    {
        return $this->hasMany(ChatRoomHasUser::class, 'chat_room_id', 'id');
    }

    public function toUser()
    {
        return $this->users()->where('users.id', '<>', \Auth::id());
    }

    public function users()
    {
        return $this->belongsToMany(User::class,'chat_room_has_users', 'chat_room_id', 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_room_id');
    }
}
