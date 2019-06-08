<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\PostComment as Model;

class PostCommentTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['owner', 'toComment'];

    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'post_id' => $model->post_id,
            'user_id' => $model->user_id,
            'content' => $model->content,
            'images' => $model->images,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
        ];
    }

    public function includeOwner(Model $model)
    {
        return $this->item($model->owner, new UserTransformer());
    }

    public function includeToComment(Model $model)
    {
        if ($model->toComment) {
            return $this->item($model->toComment, new self());
        } else {
            return null;
        }
    }
}