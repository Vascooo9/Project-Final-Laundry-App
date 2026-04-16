<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        $transactions = Transaction::with(['order.customer', 'order.items.service', 'receiver'])
                                   ->whereBetween('paid_at', [$startDate, $endDate])
                                   ->latest('paid_at')
                                   ->get();

        $summary = [
            'total_revenue'    => $transactions->sum('amount'),
            'total_orders'     => $transactions->count(),
            'cash_revenue'     => $transactions->where('payment_method', 'cash')->sum('amount'),
            'transfer_revenue' => $transactions->where('payment_method', 'transfer')->sum('amount'),
        ];

        // Daily chart data
        $dailyData = $transactions->groupBy(fn($t) => $t->paid_at->format('Y-m-d'))
            ->map(fn($group) => $group->sum('amount'))
            ->sortKeys();

        return view('admin.reports.index', compact(
            'transactions', 'summary', 'dailyData', 'startDate', 'endDate'
        ));
    }
}
