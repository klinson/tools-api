<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\User as Model;

class UserTransformer extends TransformerAbstract
{
    protected $token;
    protected $point;
    public function __construct($token = 'simple', $point = null)
    {
        $this->token = $token;
        $this->point = $point;

    }

    public function transform(Model $model)
    {
        $avatar = $model->avatar ?: asset('/images/avatar_'.$model->sex.'.png');
        switch ($this->token) {
            case '':
                return [
                    'id' => $model->id,
                    'wxapp_openid' => $model->wxapp_openid,
                    'nickname' => $model->nickname,
                    'name' => $model->name,
                    'sex' => $model->sex,
                    'avatar' => $avatar,
                    'mobile' => $model->mobile,
                    'signature' => $model->signature,
                    'images' => $model->images ?: [$avatar],
                    'created_at' => $model->created_at,
                ];
                break;
            case 'friend':
                return [
                    'id' => $model->id,
                    'wxapp_openid' => $model->wxapp_openid,
                    'nickname' => $model->nickname,
                    'sex' => $model->sex,
                    'avatar' => $avatar,
                    'signature' => $model->signature,
                    'images' => $model->images ?: [$avatar],
                ];
                break;
            case 'simple':
                return [
                    'id' => $model->id,
                    'wxapp_openid' => $model->wxapp_openid,
                    'nickname' => $model->nickname,
                    'sex' => $model->sex,
                    'avatar' => $avatar,
                ];
                break;
            case 'location':
                return [
                    'id' => $model->id,
                    'wxapp_openid' => $model->wxapp_openid,
                    'nickname' => $model->nickname,
                    'name' => $model->name,
                    'sex' => $model->sex,
                    'avatar' => $avatar,
                    'mobile' => $model->mobile,
                    'signature' => $model->signature,
                    'images' => $model->images ?: [$avatar],
                    'created_at' => $model->created_at->toDateTimeString(),
                    // 距离字段
                    'distance' => get_distance(
                        $model->location->latitude,
                        $model->location->longitude,
                        $this->point['latitude'],
                        $this->point['longitude']
                    )
                ];
                break;
            default:
                return [
                    'user' => [
                        'id' => $model->id,
                        'wxapp_openid' => $model->wxapp_openid,
                        'nickname' => $model->nickname,
                        'name' => $model->name,
                        'sex' => $model->sex,
                        'avatar' => $avatar,
                        'mobile' => $model->mobile,
                        'signature' => $model->signature,
                        'images' => $model->images ?: [$avatar],
                        'created_at' => $model->created_at->toDateTimeString(),
                    ],
                    'token' => $this->token,
                ];
                break;
        }
    }
}