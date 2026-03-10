<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Staff Users
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@mlupdong.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
            'avatar_path' => 'avatars/admin.png'
        ]);

        \App\Models\User::create([
            'name' => 'Chef Roth',
            'email' => 'chef@mlupdong.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'chef',
            'avatar_path' => 'avatars/chef.png'
        ]);

        \App\Models\User::create([
            'name' => 'Waiter Alex',
            'email' => 'waiter@mlupdong.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'waiter',
            'avatar_path' => null
        ]);

        \App\Models\Table::factory(10)->create();

        $this->call([
            MenuItemSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
