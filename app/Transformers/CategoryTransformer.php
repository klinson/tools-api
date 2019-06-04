<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Category as Model;

class CategoryTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
        ];
    }
}