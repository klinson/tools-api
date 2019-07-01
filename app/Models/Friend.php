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
