<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAdmin = DB::table('roles')->where('name', '=', 'ADMIN')->first();

        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'avatar' => null,
            'email_verified_at' => now(),
            'password' => Hash::make(env('PASSWORD_ADMIN')),
            'role_id' => $roleAdmin->id,
        ]);
    }
}
