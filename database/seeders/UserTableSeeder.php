<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'role' => '0',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin'),
                'avatar' => 'avatar.png',
                'is_must_change_password' => 0
            ],
        ]);

        User::factory()->count(50)->create();
    }
}
