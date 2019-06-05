<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'category_id', 'content', 'images', 'address', 'point', 'user_id', 'is_top'];

    public function scopeTop($query)
    {
        return $query->orderBy('is_top', 'desc');
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

    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'category_id', 'id');
    }
}
