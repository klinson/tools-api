<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Friend extends Model
{
    protected $fillable = ['user_id', 'friend_id'];
    use SoftDeletes;

    public static function toBeFriend($user, $author)
    {
        \DB::transaction(function () use ($user, $author) {
            static::firstOrCreate([
                'user_id' => $user->id,
                'friend_id' => $author->id
            ]);
            static::firstOrCreate([
                'user_id' => $author->id,
                'friend_id' => $user->id
            ]);
        });
    }
}
