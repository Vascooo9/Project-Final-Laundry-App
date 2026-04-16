@extends('layouts.app')

@section('title', 'Daftar Order')
@section('page-title', 'Daftar Order')

@section('content')
<div class="space-y-4">

    {{-- Status Tabs --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        @php
            $statuses = [
                ''          => ['label' => 'Semua',         'count' => $statusCounts['all'],        'color' => 'gray'],
                'pending'   => ['label' => 'Menunggu',      'count' => $statusCounts['pending'],    'color' => 'yellow'],
                'processing'=> ['label' => 'Dicuci',        'count' => $statusCounts['processing'], 'color' => 'blue'],
                'done'      => ['label' => 'Selesai',       'count' => $statusCounts['done'],       'color' => 'green'],
                'picked_up' => ['label' => 'Sudah Diambil', 'count' => $statusCounts['picked_up'],  'color' => 'gray'],
            ];
        @endphp

        @foreach($statuses as $key => $info)
        <a href="{{ route('orders.index', array_merge(request()->except(['status', 'page']), $key ? ['status' => $key] : [])) }}"
           class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium whitespace-nowrap transition
                  {{ request('status', '') === $key
                     ? 'bg-sky-600 text-white shadow-md'
                     : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
            {{ $info['label'] }}
            <span class="px-1.5 py-0.5 rounded text-xs font-bold
                         {{ request('status', '') === $key ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600' }}">
                {{ $info['count'] }}
            </span>
        </a>
        @endforeach
    </div>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('orders.index') }}" class="flex items-center gap-3">
        @if(request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <div class="flex-1 relative">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari no. order atau nama customer..."
                   class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        </div>

        <input type="date" name="date" value="{{ request('date') }}"
               class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">

        <button type="submit" class="btn-primary">Cari</button>
        @if(request()->hasAny(['search', 'date']))
        <a href="{{ route('orders.index', request()->only('status')) }}" class="btn-secondary">Reset</a>
        @endif
    </form>

    {{-- Orders Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($orders->isEmpty())
        <div class="py-16 text-center">
            <span class="text-5xl block mb-3">📋</span>
            <p class="text-gray-500 font-medium">Tidak ada order ditemukan</p>
            <a href="{{ route('orders.create') }}" class="btn-primary mt-4 inline-flex">+ Input Order Baru</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Order</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Pengambilan</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Est. Selesai</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Bayar</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-mono font-semibold text-gray-800 text-xs">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-sky-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-sky-700 font-bold text-xs">{{ strtoupper(substr($order->customer->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $order->customer->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $order->customer->phone ?: '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($order->delivery_type === 'delivery')
                            <span class="inline-flex items-center gap-1 text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">🛵 Diantar</span>
                            @else
                            <span class="inline-flex items-center gap-1 text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">🏪 Ambil</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="{{ $order->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-600' }} text-xs">
                                {{ $order->estimated_done->format('d/m/Y') }}
                                @if($order->isOverdue()) ⚠️ @endif
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge-{{ $order->status }}">{{ $order->status_label }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($order->payment_status === 'paid')
                            <span class="inline-flex items-center gap-1 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">✅ Lunas</span>
                            @else
                            <span class="inline-flex items-center gap-1 text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">⏳ Belum</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <span class="font-semibold text-gray-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('orders.show', $order) }}"
                               class="text-sky-600 hover:text-sky-700 font-medium text-xs">Detail →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection