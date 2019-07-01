<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use SoftDeletes;

    protected $fillable = ['content', 'type', 'from_user_id', 'withdraw_at'];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }
}
