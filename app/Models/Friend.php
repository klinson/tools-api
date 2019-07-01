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
                'user_id' => is_object($user) ? $user->id : $user,
                'friend_id' => is_object($author) ? $author->id : $author
            ]);
            static::firstOrCreate([
                'user_id' => is_object($author) ? $author->id : $author,
                'friend_id' => is_object($user) ? $user->id : $user,
            ]);
        });
    }

    public static function isFriend($user, $author)
    {
        return static::where([
                'user_id' => is_object($user) ? $user->id : $user,
                'friend_id' => is_object($author) ? $author->id : $author
            ])->first() && static::where([
                'user_id' => is_object($author) ? $author->id : $author,
                'friend_id' => is_object($user) ? $user->id : $user,
            ])->first();
    }

    public function isThisFriend($user)
    {
        return (is_object($user) ? $user->id : $user) == $this->user_id;
    }

    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    public function getKeyAttribute()
    {
        $alias = $this->alias ?: $this->friend->nickname;
        if ($key = pinyin_abbr(mb_substr($alias, 0,  1))) {
            if (is_numeric($key)) {
                return '#';
            } else {
                return strtoupper($key);
            }
        } else {
            return '#';
        }
    }
}
