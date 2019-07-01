<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Friend as Model;

class FriendTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['friend'];

    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'friend_id' => $model->friend_id,
            'alias' => $model->alias,
            'created_at' => $model->created_at->toDateTimeString(),
        ];
    }

    public function includeFriend(Model $model)
    {
        return $this->item($model->friend, new UserTransformer('friend'));
    }
}