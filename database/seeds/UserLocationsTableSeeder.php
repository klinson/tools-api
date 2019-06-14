<?php

use \Illuminate\Database\Seeder;

class UserLocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('post_categories')->delete();

        \App\Models\User::chunk(500, function ($users) {
            foreach ($users as $user) {
                list($longitude, $latitude) = $this->getPoint();
                $data = [];
                $data['longitude'] = $longitude;
                $data['latitude'] = $latitude;
                $data['user_id'] = $user->id;
                $data['point'] = \DB::raw("ST_GeomFromText ('POINT({$longitude} {$latitude})')");
                $data['geohash'] = \Lvht\GeoHash::encode($longitude, $latitude);

                $data['created_at'] = \Carbon\Carbon::now()->subHours(rand(0, 48))->toDateTimeString();

                // 更新位置
                \App\Models\UserLocation::updateOrCreate(
                    ['user_id' => $user->id],
                    $data
                );
            }
        });
    }

    protected function getPoint()
    {
        // 东莞地处东经113°31′ -114°15′=>0.84,北纬22°39′-23°09′=>0.7
        $longitude = round(rand(0, 84) * 0.01 + 113.31, 2);
        $latitude = round(rand(0, 70) * 0.01 + 22.39, 2);
        return [$longitude, $latitude];
    }
}
