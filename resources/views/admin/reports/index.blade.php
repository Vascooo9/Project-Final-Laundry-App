@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan Pendapatan')

@section('content')
<div class="space-y-6">

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex items-end gap-4 flex-wrap">
            <div>
                <label class="label">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                       class="input-field">
            </div>
            <div>
                <label class="label">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                       class="input-field">
            </div>
            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('admin.reports.index') }}" class="btn-secondary">Bulan Ini</a>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-sky-600 to-blue-700 rounded-2xl p-5 text-white shadow-md">
            <p class="text-sky-100 text-sm">Total Pendapatan</p>
            <p class="text-2xl font-bold mt-1">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <p class="text-gray-500 text-sm">Total Order</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['total_orders'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <p class="text-gray-500 text-sm">💵 Cash</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($summary['cash_revenue'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <p class="text-gray-500 text-sm">📱 Transfer</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($summary['transfer_revenue'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Riwayat Transaksi ({{ $transactions->count() }})</h3>
        </div>

        @if($transactions->isEmpty())
        <div class="py-12 text-center text-gray-400">
            <span class="text-4xl block mb-2">📊</span>
            <p>Tidak ada transaksi di periode ini</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">No. Order</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Waktu Bayar</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Metode</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($transactions as $trx)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <a href="{{ route('orders.show', $trx->order) }}"
                               class="font-mono text-xs text-sky-600 hover:text-sky-700 font-semibold">
                                {{ $trx->order->order_number }}
                            </a>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $trx->order->customer->name }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $trx->paid_at->locale('id')->isoFormat('D MMM YYYY · HH:mm') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($trx->payment_method === 'transfer')
                            <span class="inline-flex items-center gap-1 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">📱 Transfer</span>
                            @else
                            <span class="inline-flex items-center gap-1 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">💵 Cash</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-gray-900">
                            Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-sky-50 border-t-2 border-sky-200">
                    <tr>
                        <td colspan="4" class="px-5 py-3 font-bold text-gray-800 text-right">TOTAL PERIODE</td>
                        <td class="px-5 py-3 font-bold text-sky-700 text-right text-base">
                            Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
