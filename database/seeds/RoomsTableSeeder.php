<?php

use App\Room;
use Illuminate\Database\Seeder;

class RoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $testRooms = [
            [
                'id' => 1,
                'code' => 'kaicho_ch',
                'user_id' => null,
                'name' => '会長ch',
                'description' => '会長chの配信画面にスタンプを送る事ができます。',
            ],
            [
                'id' => 2,
                'code' => 'test',
                'user_id' => null,
                'name' => 'テストルーム',
                'description' => 'スタンプ配信のテストに使います',
            ],
        ];

        foreach ($testRooms as $testRoom) {
            Room::create($testRoom);
        }
    }
}
