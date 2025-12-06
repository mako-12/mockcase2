<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'id' => 1,
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
            'role' => 1, //管理者
        ]);

        User::create([
            'id' => 2,
            'name' => '田中　一郎',
            'email' => 'itiro@example.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'role' => 0, //一般
        ]);

        User::create([
            'id' => 3,
            'name' => '佐藤　一花',
            'email' => 'ichika@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
            'role' => 0, //一般
        ]);

        User::create([
            'id' => 4,
            'name' => '木村　一颯',
            'email' => 'issa@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
            'role' => 0, //一般
        ]);
    }
}
