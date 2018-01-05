<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(StampsTableSeeder::class);
        $this->call(RoomsTableSeeder::class);
    }
}
