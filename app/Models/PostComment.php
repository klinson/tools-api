<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PostComment extends Model
{
    use SoftDeletes;

    protected $fillable = ['to_comment_id', 'to_user_id', 'content', 'images', 'user_id'];

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id', 'id');
    }

    public function toComment()
    {
        return $this->belongsTo(self::class, 'to_comment_id', 'id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function getImagesAttribute($content)
    {
        if (is_string($content)) {
            return json_decode($content, true);
        }
        return $content;
    }
    public function setImagesAttribute($content)
    {
        if (is_array($content)) {
            $this->attributes['images'] = json_encode($content);
        }
    }
}
