<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function index()
    {
        return MenuItem::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'description', 'price', 'category', 'is_available']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menu_items', 'public');
            $data['image_path'] = $path;
        }

        $menuItem = MenuItem::create($data);

        return response()->json($menuItem, 201);
    }

    public function update(Request $request, $id)
    {
        $menuItem = MenuItem::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'description', 'price', 'category', 'is_available']);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($menuItem->image_path) {
                Storage::disk('public')->delete($menuItem->image_path);
            }
            $path = $request->file('image')->store('menu_items', 'public');
            $data['image_path'] = $path;
        }

        $menuItem->update($data);

        return response()->json($menuItem);
    }

    public function destroy($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        
        if ($menuItem->image_path) {
            Storage::disk('public')->delete($menuItem->image_path);
        }

        $menuItem->delete();

        return response()->json(['message' => 'Item deleted']);
    }
}
