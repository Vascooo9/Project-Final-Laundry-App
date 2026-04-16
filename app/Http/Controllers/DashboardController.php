<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Stats
        $stats = [
            'total_orders_today'   => Order::whereDate('created_at', $today)->count(),
            'pending_orders'       => Order::where('status', 'pending')->count(),
            'processing_orders'    => Order::where('status', 'processing')->count(),
            'done_orders'          => Order::where('status', 'done')->count(),
            'revenue_today'        => Transaction::whereDate('paid_at', $today)->sum('amount'),
            'revenue_month'        => Transaction::whereMonth('paid_at', $today->month)
                                                  ->whereYear('paid_at', $today->year)
                                                  ->sum('amount'),
            'overdue_orders'       => Order::whereNotIn('status', ['done', 'picked_up'])
                                           ->where('estimated_done', '<', $today)
                                           ->count(),
        ];

        // Recent orders
        $recentOrders = Order::with(['customer', 'items.service'])
                             ->latest()
                             ->take(10)
                             ->get();

        // Orders ready for pickup (status = done)
        $readyPickup = Order::with('customer')
                            ->where('status', 'done')
                            ->latest()
                            ->take(5)
                            ->get();

        return view('dashboard.index', compact('stats', 'recentOrders', 'readyPickup'));
    }
}