<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Favour as Model;

class FavourTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'from_user_id' => $model->from_user_id,
            'to_user_id' => $model->to_user_id,
            'is_favoured' => $model->is_favoured,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
        ];
    }
}