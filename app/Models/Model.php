<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    protected $perPage = 10;

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeSort($query)
    {
        return $query->orderBy('sort', 'desc');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeMine($query)
    {
        return $query->where('user_id', \Auth::id());
    }
}
