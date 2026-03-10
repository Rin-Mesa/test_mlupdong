<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Table;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $tables = Table::all();
        $menuItems = MenuItem::all();

        if ($tables->isEmpty() || $menuItems->isEmpty()) {
            return;
        }

        // Create 20 orders over the last 7 days
        for ($i = 0; $i < 20; $i++) {
            $date = Carbon::now()->subDays(rand(0, 6))->subHours(rand(0, 23));
            $table = $tables->random();
            
            $status = 'Served';
            if ($i < 2) $status = 'Pending';
            else if ($i < 5) $status = 'Cooking';
            else if ($i < 8) $status = 'Ready';

            $order = Order::create([
                'table_id' => $table->id,
                'customer_name' => 'Guest ' . ($i + 1),
                'total_amount' => 0,
                'status' => $status,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            $total = 0;
            $itemsCount = rand(1, 4);
            $selectedItems = $menuItems->random($itemsCount);

            foreach ($selectedItems as $item) {
                $qty = rand(1, 2);
                $subtotal = $item->price * $qty;
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item->id,
                    'quantity' => $qty,
                    'price' => $item->price,
                    'notes' => rand(0, 10) > 8 ? 'No spicy' : null,
                ]);
            }

            $order->update(['total_amount' => $total]);
        }
    }
}
