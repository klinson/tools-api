<?php

use Illuminate\Database\Seeder;

class PostCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('post_categories')->delete();
        
        \DB::table('post_categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '跳蚤市场',
                'sort' => 8,
                'created_at' => '2019-06-04 21:44:04',
                'updated_at' => '2019-06-04 21:46:41',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => '兼职',
                'sort' => 7,
                'created_at' => '2019-06-04 21:44:15',
                'updated_at' => '2019-06-04 21:46:36',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'title' => '家教',
                'sort' => 9,
                'created_at' => '2019-06-04 21:44:21',
                'updated_at' => '2019-06-04 21:46:29',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'title' => '拼车',
                'sort' => 0,
                'created_at' => '2019-06-04 21:44:42',
                'updated_at' => '2019-06-04 21:44:42',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'title' => '校园生活',
                'sort' => 6,
                'created_at' => '2019-06-04 21:45:07',
                'updated_at' => '2019-06-04 21:46:48',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}