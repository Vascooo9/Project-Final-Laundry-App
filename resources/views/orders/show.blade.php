@extends('layouts.app')

@section('title', 'Detail Order ' . $order->order_number)
@section('page-title', 'Detail Order')

@section('content')
    <style>
        [x-cloak] {
            display: none !important;
        }
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
                        {{ $order->created_at->locale('id')->isoFormat('dddd, D MMMM YYYY · HH:mm') }}
                    </p>
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
                            <!-- Sini subtotal -->
                            <tr>
                                <td colspan="3" class="px-5 py-2 text-right text-gray-600">Subtotal</td>
                                <td class="px-5 py-2 text-right">
                                    Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- ini pajaknya nih -->
                            <tr>
                                <td colspan="3" class="px-5 py-2 text-right text-gray-600">Tax</td>
                                <td class="px-5 py-2 text-right">
                                    Rp {{ number_format($order->transaction->tax_amount ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>

                            {{-- ini table diskon --}}
                            <tr>
                                <td colspan="3" class="text-right text-gray-600">Diskon</td>
                                <td class="text-right text-green-500">
                                    - Rp {{ number_format($order->discount_amount ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- ini grandTotal -->
                            <tr>
                                <td colspan="3" class="px-5 py-3 font-bold text-gray-800 text-right">TOTAL</td>
                                <td class="px-5 py-3 font-bold text-sky-700 text-right text-lg">
                                    Rp {{ number_format($order->transaction->amount ?? $order->total_amount, 0, ',', '.') }}
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
                            @if($order->payment_method === 'cash' && $order->transaction)
                                <div class="mt-2 pt-2 border-t border-green-200 text-xs text-left">
                                    <div class="flex justify-between">
                                        <span class="text-green-700">Dibayar:</span>
                                        <span class="font-semibold text-green-800">Rp
                                            {{ number_format($order->transaction->cash_received, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between mt-0.5">
                                        <span class="text-green-700">Kembalian:</span>
                                        <span class="font-semibold text-green-800">Rp
                                            {{ number_format($order->transaction->change_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between mt-0.5">
                                        <span class="text-green-700">Tax</span>
                                        <span class="font-semibold text-green-800">Rp
                                            {{ number_format($order->transaction->tax_amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else

                        @php
                            $grandTotal = $order->total_amount * 1.1;
                        @endphp
                        <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-center mb-3">
                            <span class="text-2xl">⏳</span>
                            <p class="font-bold text-red-700 mt-1">Belum Bayar</p>
                            <p class="text-lg font-bold text-red-600 mt-1">Rp
                                {{ number_format($grandTotal, 0, ',', '.') }}
                            </p>
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

        <div x-show="payModal" x-cloak x-transition
            class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            @click.self="payModal = false">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" @click.stop>

                <h3 class="font-bold text-gray-900 text-lg mb-1">💳 Proses Pembayaran</h3>
                <p class="text-gray-500 text-sm mb-5">
                    {{ $order->order_number }} · {{ $order->customer->name }}
                </p>

                <!-- ✅ CORE LOGIC DI SINI -->
                <div x-data="{
                                cashReceived: 0,
                                subtotal: {{ $order->subtotal ?? 0 }},
                                discount: {{ $order->discount_amount ?? 0 }},
                                voucherCode: '',
                                voucherDiscount: 0,
                                taxRate: 0.1,

                                formatRupiah(val) {
                                return new Intl.NumberFormat('id-ID').format(val || 0)
                                },

                                applyVoucher() {
                                let base = Number(this.subtotal || 0) - Number(this.discount || 0);

                                if (this.voucherCode === 'DISKON10') {
                                this.voucherDiscount = base * 0.1;
                                } else if (this.voucherCode === 'HEMAT5') {
                                this.voucherDiscount = 5000;
                                } else {
                                this.voucherDiscount = 0;
                                }},

                                get afterDiscount() {
                                return Number(this.subtotal || 0) - Number(this.discount || 0)
                                },

                                et afterVoucher() {
                                return this.afterDiscount - Number(this.voucherDiscount || 0)
                                },

                                get tax() {
                                return this.afterVoucher * Number(this.taxRate || 0)
                                },

                                get grandTotal() {
                                return this.afterVoucher + this.tax
                                },

                                get change() {
                                return Number(this.cashReceived || 0) - this.grandTotal
                                }}">

                    <!-- TOTAL + TAX -->
                    <div class="bg-sky-50 border border-sky-200 rounded-xl p-4 text-center mb-5">
                        <p class="text-sm text-sky-600 font-medium">Subtotal</p>
                        <p class="text-lg font-semibold text-gray-700">
                            Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                        </p>

                        <p class="text-sm text-green-600">
                            Diskon: - Rp {{ number_format($order->discount_amount, 0, ',', '.') }}
                        </p>

                        <p class="text-sm text-gray-600 mt-2">
                            Tax (10%): Rp
                            <span x-text="new Intl.NumberFormat('id-ID').format(tax)"></span>
                        </p>

                        <p class="text-2xl font-bold text-sky-700 mt-2">
                            Rp <span x-text="new Intl.NumberFormat('id-ID').format(grandTotal)"></span>
                        </p>
                    </div>

                    <form action="{{ route('orders.payment', $order) }}" method="POST">
                        @csrf

                        <!-- PAYMENT METHOD -->
                        <div class="mb-4 text-left">
                            <label class="text-sm font-semibold text-gray-700 block mb-2">
                                Metode Pembayaran
                            </label>

                            <div class="mt-3">
                                <label class="text-sm font-semibold text-gray-700">Kode Voucher</label>
                                <input type="text" x-model="voucherCode" @input="applyVoucher"
                                    placeholder="Masukkan kode voucher"
                                    class="w-full mt-1 px-3 py-2 border rounded-lg text-sm">
                            </div>

                            <p class="text-xs text-green-600 mt-1" x-show="voucherDiscount > 0">
                                Voucher aktif: -Rp <span x-text="formatRupiah(voucherDiscount)"></span>
                            </p>

                            <div class="grid grid-cols-2 gap-3">

                                <!-- CASH -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="cash" x-model="method"
                                        class="sr-only peer">
                                    <div class="p-3 border-2 rounded-xl 
                                        peer-checked:border-sky-500 
                                        peer-checked:bg-sky-50 
                                        border-gray-200 text-center">
                                        💵 Cash
                                    </div>
                                </label>

                                <!-- TRANSFER -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="transfer" x-model="method"
                                        class="sr-only peer">
                                    <div class="p-3 border-2 rounded-xl 
                                        peer-checked:border-sky-500 
                                        peer-checked:bg-sky-50 
                                        border-gray-200 text-center">
                                        📱 Transfer
                                    </div>
                                </label>

                            </div>
                        </div>

                        <!-- TRANSFER -->
                        <div x-show="method === 'transfer'" class="mb-4 text-left">
                            <input type="text" name="reference_number" placeholder="No referensi"
                                class="w-full border rounded-lg px-3 py-2">
                        </div>

                        <!-- CASH -->
                        <div x-show="method === 'cash'" class="mb-4 text-left">
                            <input type="number" name="cash_received" x-model.number="cashReceived"
                                placeholder="Jumlah uang" class="w-full border rounded-lg px-3 py-2">

                            <div x-show="cashReceived > 0" class="mt-2 text-sm">

                                <!-- KEMBALIAN -->
                                <template x-if="cashReceived >= grandTotal">
                                    <p class="text-green-600 font-medium">
                                        Kembalian: Rp
                                        <span x-text="formatRupiah(change)"></span>
                                    </p>
                                </template>

                                <!-- KURANG -->
                                <template x-if="cashReceived < grandTotal">
                                    <p class="text-red-500 font-medium">
                                        Uang kurang: Rp
                                        <span
                                            x-text="new Intl.NumberFormat('id-ID').format(grandTotal - cashReceived)"></span>
                                    </p>
                                </template>

                            </div>
                        </div>

                        <!-- SUBMIT -->
                        <div class="flex gap-3 mt-6">
                            <button type="submit" class="flex-1 py-3 bg-sky-600 text-white font-bold rounded-xl">
                                ✅ Bayar
                            </button>

                            <button type="button" @click="payModal = false" class="px-4 py-3 bg-gray-100 rounded-xl">
                                Batal
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection