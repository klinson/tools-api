<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
class ChatRoom extends Model
{
    use SoftDeletes;

    protected $fillable = ['create_user_id', 'owner_user_id', 'title', 'announcement', 'type'];

    public static function createC2C($fromUser, $toUser)
    {
        $room = new static([
            'create_user_id' => $fromUser,
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

        return $room;
    }

    public function hasUsers()
    {
        return $this->hasMany(ChatRoomHasUser::class, 'chat_room_id', 'id');
    }
    
    public function toUser()
    {
        return $this->users()->where('id', '<>', \Auth::id());
    }

    public function users()
    {
        return $this->belongsToMany('chat_room_has_users', User::class, 'chat_room_id', 'user_id');
    }
}
