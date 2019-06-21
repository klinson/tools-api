<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 生成数据集合
        $users = factory(User::class)
            ->times(100)
            ->make();

        // 让隐藏字段可见，并将数据集合转换为数组
        $user_array = $users->makeVisible(['password'])->toArray();

        // 插入到数据库中
        User::insert($user_array);

        // 单独处理几个用户的数据
        $user = User::find(1);
        $user->wxapp_openid = 'oGfri5AXoXORAWxVMSRS7qdnJEBA';
        $user->name = 'klinson';
        $user->sex = 1;
        $user->nickname = 'klinson';
        $user->avatar = 'https://wx.qlogo.cn/mmopen/vi_32/ZxZRZWq5o7173DQ2pccYaZcsgzvT9bHeKsWMD48u3cDwUvMdaKEwyp6lZwLmeG0JpicjM33ibVLCogGdDZK2lIZQ/132';
        $user->wechat_info = '{"city": "Dongguan", "gender": 1, "country": "China", "language": "zh_CN", "nickName": "klinson", "province": "Guangdong", "avatarUrl": "https://wx.qlogo.cn/mmopen/vi_32/ZxZRZWq5o7173DQ2pccYaZcsgzvT9bHeKsWMD48u3cDwUvMdaKEwyp6lZwLmeG0JpicjM33ibVLCogGdDZK2lIZQ/132"}';

        $user->save();
    }
}
