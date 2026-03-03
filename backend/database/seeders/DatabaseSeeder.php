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

        // Sample Menu Items
        \App\Models\MenuItem::create([
            'name' => 'Spicy Khmer Basil Chicken',
            'description' => 'Stir-fried with fresh holy basil, bird\'s eye chili, and garlic.',
            'price' => 6.50,
            'category' => 'Food',
            'image_path' => 'menu_items/basil-chicken.png',
            'is_available' => true
        ]);

        \App\Models\MenuItem::create([
            'name' => 'Beef Lok Lak',
            'description' => 'Stir-fried marinated beef served with lime and pepper sauce.',
            'price' => 7.25,
            'category' => 'Food',
            'image_path' => 'menu_items/beef-lok-lak.png',
            'is_available' => true
        ]);

        \App\Models\MenuItem::create([
            'name' => 'Coconut Coffee',
            'description' => 'Creamy coconut milk with strong Khmer espresso.',
            'price' => 3.50,
            'category' => 'Drink',
            'image_path' => 'menu_items/coconut-coffee.png',
            'is_available' => true
        ]);

        \App\Models\MenuItem::create([
            'name' => 'Lemongrass Tea',
            'description' => 'Refreshing tea with house-made lemongrass syrup.',
            'price' => 2.50,
            'category' => 'Drink',
            'is_available' => true
        ]);

        \App\Models\MenuItem::create([
            'name' => 'Mango Sticky Rice',
            'description' => 'Sweet ripe mango with coconut infused glutinous rice.',
            'price' => 4.50,
            'category' => 'Sweet Treats',
            'is_available' => true
        ]);
    }
}
