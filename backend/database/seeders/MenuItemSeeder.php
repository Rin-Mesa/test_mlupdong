<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;

class MenuItemSeeder extends Seeder
{
    public function run()
    {
        // Clear existing items to avoid duplicates if needed
        // MenuItem::truncate(); 

        $items = [
            [
                'name' => 'Royal Fish Amok',
                'description' => 'Traditional Khmer steamed fish curry with coconut milk, kroeung paste, and noni leaves, served in a banana leaf.',
                'price' => 12.50,
                'category' => 'Main Course',
                'image_path' => 'https://images.unsplash.com/photo-1598514983318-2f64f8f4796c?q=80&w=800&auto=format&fit=crop',
                'is_available' => true,
            ],
            [
                'name' => 'Premium Beef Lok Lak',
                'description' => 'Sautéed beef cubes in a savory brown sauce, served with lime-pepper dipping sauce and steamed jasmine rice.',
                'price' => 14.00,
                'category' => 'Main Course',
                'image_path' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?q=80&w=800&auto=format&fit=crop',
                'is_available' => true,
            ],
            [
                'name' => 'Signature Bai Sach Chrouk',
                'description' => 'Grilled pork marinated in coconut milk and garlic, served with pickled vegetables and ginger-infused rice.',
                'price' => 8.50,
                'category' => 'Main Course',
                'image_path' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=800&auto=format&fit=crop',
                'is_available' => true,
            ],
            [
                'name' => 'Khmer Seafood Noodles',
                'description' => 'Rice noodles served with fresh seafood, lemongrass, and a light fish-based broth with aromatic herbs.',
                'price' => 10.50,
                'category' => 'Main Course',
                'image_path' => 'https://images.unsplash.com/photo-1552611052-33e04de081de?q=80&w=800&auto=format&fit=crop',
                'is_available' => true,
            ],
            [
                'name' => 'Hand-Rolled Spring Rolls',
                'description' => 'Crispy fried rolls filled with minced pork, glass noodles, and vegetables, served with sweet chili sauce.',
                'price' => 6.00,
                'category' => 'Appetizers',
                'image_path' => 'https://images.unsplash.com/photo-1534080564617-38740520696b?q=80&w=800&auto=format&fit=crop',
                'is_available' => true,
            ],
            [
                'name' => 'Lotus Root Salad',
                'description' => 'Crisp lotus roots tossed with grilled shrimp, herbs, peanuts, and a tangy lime dressing.',
                'price' => 7.50,
                'category' => 'Appetizers',
                'image_path' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=800&auto=format&fit=crop',
                'is_available' => true,
            ],
            [
                'name' => 'Mango Sticky Rice',
                'description' => 'Fresh sweet mango served with warm coconut-infused sticky rice and topped with toasted mung beans.',
                'price' => 5.50,
                'category' => 'Desserts',
                'image_path' => 'https://images.unsplash.com/photo-1562601579-599dec554e8d?q=80&w=800&auto=format&fit=crop',
                'is_available' => true,
            ],
            [
                'name' => 'Khmer Coconut Flan',
                'description' => 'Silk-smooth coconut custard with a rich caramel sauce, inspired by traditional recipes.',
                'price' => 4.50,
                'category' => 'Desserts',
                'image_path' => 'https://images.unsplash.com/photo-1511914678378-2906b1f69dcf?q=80&w=800&auto=format&fit=crop',
                'is_available' => true,
            ],
            [
                'name' => 'Traditional Iced Coffee',
                'description' => 'Robust dark-roasted coffee served with sweet condensed milk over ice.',
                'price' => 3.50,
                'category' => 'Beverages',
                'image_path' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?q=80&w=800&auto=format&fit=crop',
                'is_available' => true,
            ],
            [
                'name' => 'Fresh Royal Coconut',
                'description' => 'Whole fresh young coconut chilled and served ready to drink.',
                'price' => 4.00,
                'category' => 'Beverages',
                'image_path' => 'https://images.unsplash.com/photo-1526402924151-54600d367a73?q=80&w=800&auto=format&fit=crop',
                'is_available' => true,
            ],
        ];

        foreach ($items as $item) {
            MenuItem::updateOrCreate(['name' => $item['name']], $item);
        }
    }
}
