<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\User as Model;

class UserTransformer extends TransformerAbstract
{
    protected $token;
    public function __construct($token = 'simple')
    {
        $this->token = $token;
    }

    public function transform(Model $model)
    {
        if ($this->token) {
            if ($this->token === 'simple') {
                return [
                    'id' => $model->id,
                    'wxapp_openid' => $model->wxapp_openid,
                    'nickname' => $model->nickname,
                    'sex' => $model->sex,
                    'avatar' => $model->avatar,
                ];
            } else {
                return [
                    'user' => [
                        'id' => $model->id,
                        'wxapp_openid' => $model->wxapp_openid,
                        'nickname' => $model->nickname,
                        'name' => $model->name,
                        'sex' => $model->sex,
                        'avatar' => $model->avatar ?: asset('/images/avatar.png'),
                        'mobile' => $model->mobile,
                        'signature' => $model->signature,
                        'images' => $model->images ?: [],
                        'created_at' => $model->created_at->toDateTimeString(),
                    ],
                    'token' => $this->token,
                ];
            }
        } else {
            return [
                'id' => $model->id,
                'wxapp_openid' => $model->wxapp_openid,
                'nickname' => $model->nickname,
                'name' => $model->name,
                'sex' => $model->sex,
                'avatar' => $model->avatar,
                'mobile' => $model->mobile,
                'signature' => $model->signature,
                'images' => $model->images ?: [],
                'created_at' => $model->created_at,
            ];
        }
    }
}