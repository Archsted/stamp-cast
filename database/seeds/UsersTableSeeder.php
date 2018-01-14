<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $testUsers = [
            [
                'id' => 1,
                'login_id' => 'archsted',
                'name' => '会長',
                'password' => bcrypt('kmc1345h'),
                'remember_token' => str_random(10),
            ],
        ];

        foreach ($testUsers as $testUser) {
            // fillable外のremenber_tokenを指定するため
            // マスアサインメントではなく手動で値をセットする
            $user = new User;

            foreach ($testUser as $key => $value) {
                $user->{$key} = $value;
            }

            $user->save();
        }
    }
}
