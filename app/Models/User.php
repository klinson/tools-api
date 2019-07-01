<?php

namespace App\Models;

use App\Models\Traits\IntTimestampsHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'wxapp_openid', 'nickname', 'sex', 'avatar', 'wechat_info', 'signature', 'images'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'images' => 'array'
    ];

    /**
     * sub 内容
     * @author klinson <klinson@163.com>
     * @return mixed 默认返回当前主键的值
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * PAYLOAD 附加其他内容配置
     * @author klinson <klinson@163.com>
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function location()
    {
        return $this->hasOne(UserLocation::class, 'user_id', 'id');
    }

    public function favours()
    {
        return $this->belongsToMany(User::class, 'favours', 'from_user_id', 'to_user_id', 'id', 'id');
    }

    /**
     * 我喜欢或不喜欢对方
     * @param $user
     * @param bool $favour
     * @author klinson <klinson@163.com>
     */
    public function favour($user, $favour = true)
    {
        $favour = Favour::updateOrCreate([
            'from_user_id' => $this->id,
            'to_user_id' => $user->id
        ], [
            'is_favoured' => $favour ? 1 : 0
        ]);
        return $favour;
    }

    // 对方已经喜欢我
    public function isFavourMe($user)
    {
        if (Favour::where('from_user_id', $user->id)->where('to_user_id', $this->id)->where('is_favoured', 1)->count()) {
            return true;
        } else {
            return false;
        }
    }

    public function friendship($friend)
    {

    }
}
