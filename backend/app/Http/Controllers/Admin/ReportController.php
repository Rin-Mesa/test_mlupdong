<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function dashboardStats()
    {
        $today = Carbon::today();
        
        $stats = [
            'daily_revenue' => Order::whereDate('created_at', $today)->where('status', 'Served')->sum('total_amount'),
            'total_orders' => Order::whereDate('created_at', $today)->count(),
            'active_tables' => Order::where('status', '!=', 'Served')->distinct('table_id')->count(),
            'pending_orders' => Order::where('status', 'Pending')->count()
        ];

        return response()->json($stats);
    }

    public function salesReport(Request $request)
    {
        $period = $request->query('period', 'daily'); // daily, monthly, yearly
        
        $query = Order::where('status', 'Served');

        if ($period == 'daily') {
            $data = $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        } elseif ($period == 'monthly') {
             $data = $query->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        } else {
             $data = $query->select(
                DB::raw('YEAR(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        }

        return response()->json($data);
    }

    public function bestSellers()
    {
        $bestSellers = OrderItem::select('menu_item_id', DB::raw('SUM(quantity) as total_sold'))
            ->with('menuItem:id,name')
            ->groupBy('menu_item_id')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        return response()->json($bestSellers);
    }

    public function peakHours()
    {
        $peakHours = Order::select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return response()->json($peakHours);
    }
}
