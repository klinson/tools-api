<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {
    $sex = $faker->randomElement([1, 2]);
    return [
        'wxapp_openid' => str_random(50),
        'nickname' => str_random(10),
        'name' => $faker->name,
        'avatar' => asset('images/avatar_'.$sex.'.png'),
        'sex' => $sex,
        'wechat_info' => '{}',
        'signature' => $faker->text(),
        'images' => '[]',
        'created_at' => date('Y-m-d'),
        'updated_at' => date('Y-m-d'),
    ];
});
