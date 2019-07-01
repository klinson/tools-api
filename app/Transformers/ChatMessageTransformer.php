<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ChatMessage as Model;

class ChatMessageTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['fromUser'];

    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        if ($model->withdraw_at) {
            return [
                'id' => $model->id,
                'chat_room_id' => $model->chat_room_id,
                'from_user_id' => $model->from_user_id,
                'withdraw_at' => $model->withdraw_at,
                'created_at' => $model->created_at->toDateTimeString(),
            ];
        } else {
            return [
                'id' => $model->id,
                'chat_room_id' => $model->chat_room_id,
                'from_user_id' => $model->from_user_id,
                'to_user_id' => $model->to_user_id,
                'content' => $model->content,
                'type' => $model->type,
                'withdraw_at' => $model->withdraw_at,
                'created_at' => $model->created_at->toDateTimeString(),
            ];
        }
    }


    public function includeFromUser(Model $model)
    {
        return $this->item($model->fromUser, new UserTransformer('simple'));
    }
}