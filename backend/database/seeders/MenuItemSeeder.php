<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;

class MenuItemSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'name' => 'Wagyu Truffle Burger',
                'description' => 'Aged wagyu beef, black truffle aioli, caramelized onions, and gruyere on a brioche bun.',
                'price' => 28.50,
                'category' => 'Main Course',
                'is_available' => true,
            ],
            [
                'name' => 'Glazed Salmon Fillet',
                'description' => 'Miso-glazed Atlantic salmon served with ginger-infused jasmine rice and bok choy.',
                'price' => 24.00,
                'category' => 'Main Course',
                'is_available' => true,
            ],
            [
                'name' => 'Burrata & Heirloom Tomato',
                'description' => 'Creamy burrata, heirloom tomatoes, fresh basil, and balsamic glaze.',
                'price' => 16.00,
                'category' => 'Appetizers',
                'is_available' => true,
            ],
            [
                'name' => 'Calamari Fritti',
                'description' => 'Crispy fried calamari rings served with zesty lemon aioli.',
                'price' => 14.50,
                'category' => 'Appetizers',
                'is_available' => true,
            ],
            [
                'name' => 'Espresso Martini',
                'description' => 'A sophisticated blend of vodka, fresh espresso, and coffee liqueur.',
                'price' => 12.00,
                'category' => 'Beverages',
                'is_available' => true,
            ],
            [
                'name' => 'Chocolate Lava Cake',
                'description' => 'Warm chocolate cake with a molten center, served with vanilla bean gelato.',
                'price' => 9.50,
                'category' => 'Desserts',
                'is_available' => true,
            ],
        ];

        foreach ($items as $item) {
            MenuItem::create($item);
        }
    }
}
