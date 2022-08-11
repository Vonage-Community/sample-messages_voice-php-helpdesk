<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         User::factory()->create([
             'name' => 'Super Admin',
             'email' => 'admin@vonage.com',
             'password' => Hash::make('password')
         ]);
    }
}
