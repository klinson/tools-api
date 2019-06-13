<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\UserLocation as Model;

class UserLocationTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'user_id' => $model->user_id,
            'longitude' => $model->longitude,
            'latitude' => $model->latitude,
            'geohash' => $model->geohash,
            'created_at' => $model->created_at,
        ];
    }
}