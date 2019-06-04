<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\PostCategory as Model;

class PostCategoryTransformer extends TransformerAbstract
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