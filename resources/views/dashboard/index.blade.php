@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Order Hari Ini --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-sky-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_orders_today'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Order Hari Ini</p>
        </div>

        {{-- Sedang Proses --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                @if($stats['overdue_orders'] > 0)
                <span class="text-xs font-bold text-red-600 bg-red-100 px-2 py-0.5 rounded-full">{{ $stats['overdue_orders'] }} terlambat</span>
                @endif
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_orders'] + $stats['processing_orders'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Sedang Diproses</p>
        </div>

        {{-- Siap Diambil --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['done_orders'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Siap Diambil</p>
        </div>

        {{-- Pendapatan Hari Ini --}}
        <div class="bg-gradient-to-br from-sky-600 to-blue-700 rounded-2xl p-5 text-white shadow-md">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-xl font-bold">Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</p>
            <p class="text-sky-100 text-sm mt-1">Pendapatan Hari Ini</p>
            <p class="text-sky-200 text-xs mt-1">Bulan: Rp {{ number_format($stats['revenue_month'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Recent Orders --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800">Order Terbaru</h3>
                <a href="{{ route('orders.index') }}" class="text-sm text-sky-600 hover:text-sky-700 font-medium">Lihat Semua →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentOrders as $order)
                <div class="px-6 py-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-sky-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-sky-700 font-bold text-sm">{{ strtoupper(substr($order->customer->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $order->customer->name }}</p>
                                <p class="text-xs text-gray-500">{{ $order->order_number }} · {{ $order->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="badge-{{ $order->status }}">{{ $order->status_label }}</span>
                            <p class="text-xs font-semibold text-gray-700 mt-1">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-400">
                    <span class="text-4xl block mb-2">📋</span>
                    Belum ada order hari ini
                </div>
                @endforelse
            </div>
        </div>

        {{-- Ready for Pickup --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <h3 class="font-bold text-gray-800">Siap Diambil</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($readyPickup as $order)
                <div class="px-5 py-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $order->customer->name }}</p>
                            <p class="text-xs text-gray-500">{{ $order->order_number }}</p>
                            @if($order->delivery_type === 'delivery')
                            <span class="inline-block mt-1 text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">🛵 Diantar</span>
                            @else
                            <span class="inline-block mt-1 text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">🏪 Ambil Sendiri</span>
                            @endif
                        </div>
                        <a href="{{ route('orders.show', $order) }}"
                           class="text-xs text-sky-600 hover:text-sky-700 font-medium flex-shrink-0">Detail →</a>
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-gray-400">
                    <span class="text-3xl block mb-2">✅</span>
                    <p class="text-sm">Tidak ada order siap</p>
                </div>
                @endforelse
            </div>
            <div class="px-5 py-3 border-t border-gray-100">
                <a href="{{ route('orders.index', ['status' => 'done']) }}"
                   class="text-sm text-sky-600 hover:text-sky-700 font-medium">Lihat semua yang selesai →</a>
            </div>
        </div>
    </div>
</div>
@endsection
