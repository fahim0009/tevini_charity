<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            [
               'name'=>'Admin',
               'email'=>'admin@gmail.com',
                'is_type'=>'admin',
               'password'=> bcrypt('123456'),
            ],
            [
                'name'=>'Writer',
                'email'=>'writer@gmail.com',
                 'is_type'=>'merchant',
                'password'=> bcrypt('123456'),
             ],
            [
               'name'=>'User',
               'email'=>'user@gmail.com',
                'is_type'=>'user',
               'password'=> bcrypt('123456'),
            ],
            [
                'name'=>'Seller',
                'email'=>'seller@gmail.com',
                 'is_type'=>'seller',
                'password'=> bcrypt('123456'),
             ],
        ];

        foreach ($user as $key => $value) {
            User::create($value);
        }
    }
}
