<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function show($token)
    {
        $table = Table::where('qr_token', $token)->first();

        if (!$table) {
            abort(404, 'Table verification failed. Invalid session.');
        }

        $menuItems = MenuItem::where('is_available', true)
            ->get()
            ->groupBy('category');

        return view('menu', compact('table', 'menuItems'));
    }
}
