<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'category_id', 'content', 'images', 'address', 'point', 'user_id', 'is_top'];

    public function scopeWitPoint($query)
    {
        return $query->select()->selectRaw('x(`point`) as point_x,y(`point`) as point_y');
    }

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
    public function getPointAttribute($content)
    {
        if (! is_array($content)) {
            return [$this->getAttribute('point_x'), $this->getAttribute('point_y')];
        }
        return $content;
    }
    public function setPointAttribute($content)
    {
        if (is_array($content)) {
            $this->attributes['point'] = \DB::raw("ST_GeomFromText ('POINT({$content[0]} {$content[1]})')");
        }
    }

    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'category_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class, 'post_id', 'id');
    }
}
