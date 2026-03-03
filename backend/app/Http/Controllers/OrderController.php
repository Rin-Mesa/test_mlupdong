<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:restaurant_tables,id',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_name' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($validated) {
            $totalAmount = 0;
            $itemsToCreate = [];

            foreach ($validated['items'] as $itemData) {
                $menuItem = MenuItem::find($itemData['id']);
                $price = $menuItem->price;
                $totalAmount += $price * $itemData['quantity'];

                $itemsToCreate[] = [
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $itemData['quantity'],
                    'price' => $price,
                    'notes' => $itemData['notes'] ?? null,
                ];
            }

            $order = Order::create([
                'table_id' => $validated['table_id'],
                'total_amount' => $totalAmount,
                'status' => 'Pending',
                'customer_name' => $validated['customer_name'] ?? null,
                'estimated_waiting_time' => 20 // Default 20 mins
            ]);

            foreach ($itemsToCreate as $item) {
                $order->items()->create($item);
            }

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order->load('items.menuItem')
            ], 201);
        });
    }

    public function show($id)
    {
        $order = Order::with('items.menuItem', 'table')->findOrFail($id);
        return response()->json($order);
    }

    public function orderHistory(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array'
        ]);

        $orders = Order::with('items.menuItem')
            ->whereIn('id', $request->order_ids)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    // Kitchen Methods
    public function kitchenIndex()
    {
        $orders = Order::with('items.menuItem', 'table')
            ->whereIn('status', ['Pending', 'Cooking', 'Ready'])
            ->orderBy('created_at', 'asc') // FIFO
            ->get();

        return response()->json($orders);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Cooking,Ready,Served'
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => "Order status updated to {$request->status}",
            'order' => $order->load('items.menuItem', 'table')
        ]);
    }

    public function transferTable(Request $request, $id)
    {
        $request->validate([
            'new_table_id' => 'required|exists:restaurant_tables,id'
        ]);

        $order = Order::findOrFail($id);
        $order->table_id = $request->new_table_id;
        $order->save();

        return response()->json([
            'message' => 'Order transferred successfully',
            'order' => $order->load('table')
        ]);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order cancelled/voided successfully']);
    }
}
