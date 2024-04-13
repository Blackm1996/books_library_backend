<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //php artisan db:seed --class=SuperAdminSeeder
        DB::table('users')->insert([
            'name' => "admin",
            'email' => "admin",
            'password' => Hash::make('admin'),
            'role' => 1996,
        ]);
    }
}
