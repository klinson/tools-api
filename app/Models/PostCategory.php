<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PostCategory extends Model
{
    use SoftDeletes;

    public static function getByCache($reset = false)
    {
        // 缓存1天,非生产环境不缓存
        ($reset || app()->environment() !== 'production') && cache()->delete('post_categories');
        return cache()->remember('post_categories', 1440, function () {
            return self::sort()->recent()->get(['id', 'title']);
        });
    }
}
