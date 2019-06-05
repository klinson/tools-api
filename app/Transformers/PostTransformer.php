<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Post as Model;

class PostTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['owner', 'category'];

    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'user_id' => $model->user_id,
            'category_id' => $model->category_id,
            'content' => $model->content,
            'images' => $model->images,
            'address' => $model->address,
            'point' => $model->point,
            'is_top' => $model->is_top,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
        ];
    }

    public function includeOwner(Model $model)
    {
        return $this->item($model->owner, new UserTransformer());
    }

    public function includeCategory(Model $model)
    {
        return $this->item($model->category, new PostCategoryTransformer());
    }
}