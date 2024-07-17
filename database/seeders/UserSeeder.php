<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name"=>"ahmed katoon",
            "email"=>"a@a.com",
            "password"=>Hash::make("123456789"),
            "phone_number"=>"01067270742",
            
        ]);

        DB::table("users")->insert([
            "name"=>"ahmed katoon",
            "email"=>"k@k.com",
            "password"=>Hash::make("123456789"),
            "phone_number"=>"123456789",
        ]);
    }
}
