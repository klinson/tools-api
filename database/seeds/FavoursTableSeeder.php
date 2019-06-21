<?php

use \Illuminate\Database\Seeder;

class FavoursTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('favours')->delete();

        \App\Models\User::chunk(500, function ($users) {
            $list = [];
            foreach ($users as $user) {
                $list[] = [
                    'from_user_id' => $user->id,
                    'to_user_id' => 1,
                    'is_favoured' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            \DB::table('favours')->insert($list);
        });
    }
}
