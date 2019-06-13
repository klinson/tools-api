<?php

namespace App\Models;

class UserLocation extends Model
{
    protected $primaryKey = 'user_id';

    public $timestamps = false;

    protected $fillable = ['longitude', 'latitude', 'point', 'user_id', 'geohash', 'created_at'];

}
