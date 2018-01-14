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
                'user_id' => 1,
                'name' => '会長ch',
                'description' => '会長chの配信画面にスタンプを送る事ができます。',
            ],
        ];

        foreach ($testRooms as $testRoom) {
            Room::create($testRoom);
        }
    }
}
