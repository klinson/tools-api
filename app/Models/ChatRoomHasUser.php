<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ChatRoomHasUser extends Model
{
    use SoftDeletes;

    protected $fillable = ['chat_room_id', 'user_id'];
}
