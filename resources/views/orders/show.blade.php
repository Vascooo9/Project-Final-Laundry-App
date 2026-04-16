@extends('layouts.app')

@section('title', 'Detail Order ' . $order->order_number)
@section('page-title', 'Detail Order')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>
    <div class="max-w-3xl mx-auto space-y-6" x-data="{ payModal: false }">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('orders.index') }}"
                    class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-gray-900 text-lg">{{ $order->order_number }}</h2>
                    <p class="text-xs text-gray-500">
                        {{ $order->created_at->locale('id')->isoFormat('dddd, D MMMM YYYY · HH:mm') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge-{{ $order->status }} text-sm px-3 py-1">{{ $order->status_label }}</span>
                <a href="{{ route('orders.receipt', $order) }}" target="_blank" class="btn-secondary text-xs">🖨️ Cetak
                    Nota</a>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">

            {{-- Left: Order Details --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Customer Info --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span
                            class="w-6 h-6 bg-sky-100 text-sky-600 rounded-md flex items-center justify-center text-xs">👤</span>
                        Informasi Customer
                    </h3>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Nama</p>
                            <p class="font-semibold text-gray-800 mt-0.5">{{ $order->customer->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">No. HP</p>
                            <p class="font-semibold text-gray-800 mt-0.5">{{ $order->customer->phone ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Pengambilan</p>
                            <p class="font-semibold text-gray-800 mt-0.5">{{ $order->delivery_type_label }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Estimasi Selesai</p>
                            <p class="font-semibold mt-0.5 {{ $order->isOverdue() ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $order->estimated_done->locale('id')->isoFormat('D MMM YYYY') }}
                                @if($order->isOverdue())
                                    <span class="text-xs text-red-500">⚠️ Terlambat</span>
                                @endif
                            </p>
                        </div>
                        @if($order->delivery_type === 'delivery')
                            <div class="col-span-2">
                                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Alamat Pengiriman</p>
                                <p class="font-semibold text-gray-800 mt-0.5">{{ $order->delivery_address }}</p>
                                <p class="text-sm text-gray-600">📱 {{ $order->delivery_phone }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <span
                                class="w-6 h-6 bg-sky-100 text-sky-600 rounded-md flex items-center justify-center text-xs">🧺</span>
                            Detail Layanan
                        </h3>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Layanan</th>
                                <th
                                    class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Qty</th>
                                <th
                                    class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Harga</th>
                                <th
                                    class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3">
                                        <p class="font-semibold text-gray-800">{{ $item->service->name }}</p>
                                        @if($item->item_note)
                                            <p class="text-xs text-gray-500">📝 {{ $item->item_note }}</p>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-center text-gray-700">
                                        {{ $item->quantity }} {{ $item->service->type === 'per_kg' ? 'kg' : 'item' }}
                                    </td>
                                    <td class="px-3 py-3 text-right text-gray-600">
                                        Rp {{ number_format($item->price_per_unit, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3 text-right font-semibold text-gray-800">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-sky-50">
                            <tr>
                                <td colspan="3" class="px-5 py-3 font-bold text-gray-800 text-right">TOTAL</td>
                                <td class="px-5 py-3 font-bold text-sky-700 text-right text-lg">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($order->notes)
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm">
                        <p class="font-semibold text-amber-800 mb-1">📝 Catatan:</p>
                        <p class="text-amber-700">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Right: Actions & Payment --}}
            <div class="space-y-4">

                {{-- Payment Status --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-bold text-gray-800 mb-3">💳 Pembayaran</h3>

                    @if($order->payment_status === 'paid')
                        <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-center mb-3">
                            <span class="text-2xl">✅</span>
                            <p class="font-bold text-green-700 mt-1">Lunas</p>
                            <p class="text-xs text-green-600">
                                {{ ucfirst($order->payment_method) }} ·
                                {{ $order->transaction?->paid_at->locale('id')->isoFormat('D MMM · HH:mm') }}
                            </p>
                            @if($order->transaction?->reference_number)
                                <p class="text-xs text-green-600 mt-1">Ref: {{ $order->transaction->reference_number }}</p>
                            @endif
                        </div>
                    @else
                        <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-center mb-3">
                            <span class="text-2xl">⏳</span>
                            <p class="font-bold text-red-700 mt-1">Belum Bayar</p>
                            <p class="text-lg font-bold text-red-600 mt-1">Rp
                                {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        </div>
                    @endif
                </div>

                {{-- Status Update --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-bold text-gray-800 mb-3">📋 Update Status</h3>

                    @php
                        $statusFlow = [
                            'pending' => ['label' => 'Menunggu', 'next' => 'processing', 'next_label' => '➡️ Mulai Cuci'],
                            'processing' => ['label' => 'Dicuci', 'next' => 'done', 'next_label' => '✅ Tandai Selesai'],
                            'done' => ['label' => 'Selesai', 'next' => 'picked_up', 'next_label' => '📦 Sudah Diambil'],
                            'picked_up' => ['label' => 'Diambil', 'next' => null, 'next_label' => null],
                        ];
                        $current = $statusFlow[$order->status] ?? null;
                    @endphp

                    <div class="space-y-2 mb-3">
                        @foreach($statusFlow as $key => $step)
                            <div class="flex items-center gap-2 text-sm">
                                @if($key === $order->status)
                                    <div class="w-3 h-3 rounded-full bg-sky-500 ring-2 ring-sky-200 flex-shrink-0"></div>
                                    <span class="font-semibold text-sky-700">{{ $step['label'] }}</span>
                                @elseif(array_search($key, array_keys($statusFlow)) < array_search($order->status, array_keys($statusFlow)))
                                    <div class="w-3 h-3 rounded-full bg-gray-400 flex-shrink-0"></div>
                                    <span class="text-gray-400 line-through">{{ $step['label'] }}</span>
                                @else
                                    <div class="w-3 h-3 rounded-full border-2 border-gray-300 flex-shrink-0"></div>
                                    <span class="text-gray-400">{{ $step['label'] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($current && $current['next'])
                        <form action="{{ route('orders.updateStatus', $order) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $current['next'] }}">
                            <button type="submit"
                                class="w-full py-2 bg-gray-800 hover:bg-gray-900 text-white font-semibold rounded-xl text-sm transition-all active:scale-95">
                                {{ $current['next_label'] }}
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Input info --}}
                <div class="text-xs text-gray-400 text-center">
                    Diinput oleh {{ $order->user->name }}<br>
                    {{ $order->created_at->locale('id')->isoFormat('D MMM YYYY · HH:mm') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Modal --}}
    <div x-data="{ payModal: false, method: 'cash' }">
    
    <button @click="payModal = true" type="button"
            class="w-full py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl text-sm transition-all active:scale-95 shadow-lg shadow-sky-200">
        💰 Proses Pembayaran
    </button>

    <div x-show="payModal" 
         x-cloak
         x-transition:enter="ease-out duration-200" 
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100" 
         class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         @click.self="payModal = false">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" @click.stop>
            <h3 class="font-bold text-gray-900 text-lg mb-1">💳 Proses Pembayaran</h3>
            <p class="text-gray-500 text-sm mb-5">{{ $order->order_number }} · {{ $order->customer->name }}</p>

            <div class="bg-sky-50 border border-sky-200 rounded-xl p-4 text-center mb-5">
                <p class="text-sm text-sky-600 font-medium">Total Tagihan</p>
                <p class="text-3xl font-bold text-sky-700 mt-1">
                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                </p>
            </div>

            <form action="{{ route('orders.payment', $order) }}" method="POST">
                @csrf

                <div class="mb-4 text-left">
                    <label class="text-sm font-semibold text-gray-700 block mb-2">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" x-model="method" class="sr-only peer">
                            <div class="p-3 border-2 rounded-xl peer-checked:border-sky-500 peer-checked:bg-sky-50 border-gray-200 text-center transition hover:border-sky-300">
                                <div class="text-2xl">💵</div>
                                <p class="text-sm font-semibold mt-1 text-gray-700">Cash</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="transfer" x-model="method" class="sr-only peer">
                            <div class="p-3 border-2 rounded-xl peer-checked:border-sky-500 peer-checked:bg-sky-50 border-gray-200 text-center transition hover:border-sky-300">
                                <div class="text-2xl">📱</div>
                                <p class="text-sm font-semibold mt-1 text-gray-700">Transfer</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div x-show="method === 'transfer'" x-transition class="mb-4 text-left">
                    <label class="text-sm font-semibold text-gray-700 block mb-1">No. Referensi Transfer</label>
                    <input type="text" name="reference_number" placeholder="Masukkan nomor referensi..." 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-sky-500 focus:ring-sky-500 px-3 py-2 border">
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" @click="payModal = false"
                        class="flex-1 py-3 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl transition active:scale-95 shadow-md">
                        ✅ Konfirmasi Bayar
                    </button>
                    <button type="button" @click="payModal = false"
                        class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection