<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ChatRoom as Model;

class ChatRoomTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['toUser'];

    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'announcement' => $model->announcement,
            'create_user_id' => $model->create_user_id,
            'owner_user_id' => $model->owner_user_id,
            'type' => $model->type,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
        ];
    }

    public function includeToUser(Model $model)
    {
        return $this->item($model->toUser[0], new UserTransformer('friend'));
    }
}