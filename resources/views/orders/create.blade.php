@extends('layouts.app')

@section('title', 'Input Order Baru')
@section('page-title', 'Input Order Baru')

@section('content')
    <div class="max-w-3xl mx-auto" x-data="orderForm()">

        <form action="{{ route('orders.store') }}" method="POST">
            @csrf

            <div class="space-y-6">

                {{-- Step 1: Data Customer --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div
                            class="w-8 h-8 bg-sky-600 text-white rounded-lg flex items-center justify-center font-bold text-sm">
                            1</div>
                        <h3 class="font-bold text-gray-800 text-lg">Data Customer</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-4" x-data='{
                            customers: @json($customers),
                            name: @json(old("customer_name")),
                            phone: @json(old("customer_phone")),

                            selectCustomer(id) {
                            const c = this.customers.find(x => x.id == id);
                            if (c) {
                            this.selectedId = c.id;
                            this.name = c.name;
                            this.phone = c.phone ?? "";
                            this.isMember = c.is_member ?? false;}
                            }
                        }'>

                        <input type="hidden" name="customer_id" :value="selectedId">

                        <!-- SELECT CUSTOMER -->
                        <div class="col-span-2">
                            <label class="label">Pilih Customer Lama</label>
                            <select @change="selectCustomer($event.target.value)" class="input-field">
                                <option value="">-- Pilih Customer --</option>
                                <template x-for="c in customers" :key="c.id">
                                    <option :value="c.id" x-text="c.name + ' - ' + (c.phone ?? '-')">
                                    </option>
                                </template>
                            </select>
                        </div>

                        <!-- NAMA -->
                        <div class="col-span-2">
                            <label class="label">Nama Customer <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" x-model="name" placeholder="Masukkan nama lengkap"
                                class="input-field @error('customer_name') border-red-400 @enderror" required>
                            @error('customer_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- HP -->
                        <div>
                            <label class="label">No. HP / WhatsApp</label>
                            <input type="tel" name="customer_phone" x-model="phone" placeholder="08xxxxxxxxxx"
                                class="input-field">
                        </div>
                        {{-- yang member member aja --}}
                        <div class="col-span-2 flex items-center gap-2 mt-2">
                            <input type="checkbox" name="is_member" value="1" x-model="isMember">
                            <label class="text-sm font-semibold text-gray-700">
                                Jadikan Member (Diskon 10%)
                            </label>
                        </div>

                        <!-- ESTIMASI -->
                        <div>
                            <label class="label">Estimasi Selesai <span class="text-red-500">*</span></label>
                            <input type="date" name="estimated_done"
                                value="{{ old('estimated_done', now()->addDays(2)->format('Y-m-d')) }}"
                                min="{{ now()->format('Y-m-d') }}"
                                class="input-field @error('estimated_done') border-red-400 @enderror" required>
                            @error('estimated_done')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Step 2: Layanan --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div
                            class="w-8 h-8 bg-sky-600 text-white rounded-lg flex items-center justify-center font-bold text-sm">
                            2</div>
                        <h3 class="font-bold text-gray-800 text-lg">Pilih Layanan</h3>
                    </div>

                    <div class="space-y-3">
                        {{-- ✅ Alpine loop — options di-render dari JS (serviceOptions), BUKAN Blade @foreach --}}
                        <template x-for="(item, index) in services" :key="index">
                            <div>
                                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl">

                                    <div class="flex-1">
                                        <label class="label">Layanan</label>
                                        <select :name="`services[${index}][id]`" x-model="item.service_id"
                                            @change="updateServicePrice(index)" class="input-field" required>
                                            <option value="">-- Pilih Layanan --</option>
                                            <template x-for="svc in serviceOptions" :key="svc.id">
                                                <option :value="svc.id" x-text="svc.label"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div class="w-32">
                                        <label class="label"
                                            x-text="item.type === 'per_item' ? 'Jumlah (item)' : 'Berat (kg)'">
                                            Berat (kg)
                                        </label>
                                        <input type="number" :name="`services[${index}][qty]`" x-model.number="item.qty"
                                            @input="calculateSubtotal(index)" step="0.1" min="0.1" placeholder="0"
                                            class="input-field" required>
                                    </div>

                                    <div class="w-36">
                                        <label class="label">Subtotal</label>
                                        <div
                                            class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700">
                                            Rp <span x-text="formatRupiah(item.subtotal)">0</span>
                                        </div>
                                    </div>

                                    <div class="pt-7">
                                        <button type="button" @click="removeService(index)" x-show="services.length > 1"
                                            class="w-9 h-9 bg-red-50 hover:bg-red-100 text-red-500 rounded-xl flex items-center justify-center transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="px-1 mt-2">
                                    <input type="text" :name="`services[${index}][note]`" x-model="item.note"
                                        placeholder="Catatan item (opsional, mis: Seprai motif bunga)"
                                        class="input-field text-xs">
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="addService()"
                            class="w-full py-2.5 border-2 border-dashed border-sky-300 text-sky-600 hover:border-sky-400 hover:bg-sky-50 rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Layanan Lain
                        </button>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                        <span class="font-semibold text-gray-700">Total</span>
                        <span class="text-xl font-bold text-sky-600">
                            Rp <span x-text="formatRupiah(total)">0</span>
                        </span>
                    </div>
                </div>

                {{-- Step 3: Pengambilan --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div
                            class="w-8 h-8 bg-sky-600 text-white rounded-lg flex items-center justify-center font-bold text-sm">
                            3</div>
                        <h3 class="font-bold text-gray-800 text-lg">Metode Pengambilan</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="delivery_type" value="pickup" x-model="deliveryType"
                                class="sr-only peer" {{ old('delivery_type', 'pickup') === 'pickup' ? 'checked' : '' }}>
                            <div
                                class="p-4 border-2 rounded-xl peer-checked:border-sky-500 peer-checked:bg-sky-50 border-gray-200 transition-all">
                                <div class="text-2xl mb-1">🏪</div>
                                <p class="font-semibold text-gray-800 text-sm">Ambil Sendiri</p>
                                <p class="text-xs text-gray-500">Customer ambil ke toko</p>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="delivery_type" value="delivery" x-model="deliveryType"
                                class="sr-only peer" {{ old('delivery_type') === 'delivery' ? 'checked' : '' }}>
                            <div
                                class="p-4 border-2 rounded-xl peer-checked:border-sky-500 peer-checked:bg-sky-50 border-gray-200 transition-all">
                                <div class="text-2xl mb-1">🛵</div>
                                <p class="font-semibold text-gray-800 text-sm">Diantar</p>
                                <p class="text-xs text-gray-500">Diantar ke alamat customer</p>
                            </div>
                        </label>
                    </div>

                    <div x-show="deliveryType === 'delivery'" x-transition
                        class="space-y-3 mt-4 p-4 bg-purple-50 rounded-xl border border-purple-100">
                        <p class="text-sm font-semibold text-purple-800 mb-2">📍 Detail Pengiriman</p>

                        <div>
                            <label class="label">Alamat Pengiriman <span class="text-red-500">*</span></label>
                            <textarea name="delivery_address" rows="2" placeholder="Masukkan alamat lengkap"
                                class="input-field @error('delivery_address') border-red-400 @enderror">{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="label">No. HP Penerima <span class="text-red-500">*</span></label>
                            <input type="tel" name="delivery_phone" value="{{ old('delivery_phone') }}"
                                placeholder="08xxxxxxxxxx"
                                class="input-field @error('delivery_phone') border-red-400 @enderror">
                            @error('delivery_phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="label">Catatan Tambahan</label>
                        <textarea name="notes" rows="2" placeholder="Catatan khusus untuk order ini..."
                            class="input-field">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="flex-1 py-3 px-6 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl transition-all duration-200 active:scale-95 shadow-md shadow-sky-200 text-sm">
                        ✅ Simpan Order & Lihat Detail
                    </button>
                    <a href="{{ route('orders.index') }}" class="btn-secondary">Batal</a>
                </div>

            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            const SERVICE_OPTIONS = @json($services);

            function orderForm() {
                return {
                    deliveryType: '{{ old('delivery_type', 'pickup') }}',
                    serviceOptions: SERVICE_OPTIONS,
                    services: [{ service_id: '', qty: 1, price: 0, type: 'per_kg', subtotal: 0, note: '' }],
                    total: 0,

                    addService() {
                        this.services.push({ service_id: '', qty: 1, price: 0, type: 'per_kg', subtotal: 0, note: '' });
                    },

                    removeService(index) {
                        if (this.services.length > 1) {
                            this.services.splice(index, 1);
                            this.calculateTotal();
                        }
                    },

                    updateServicePrice(index) {
                        const item = this.services[index];
                        const svc = this.serviceOptions.find(s => s.id == item.service_id);
                        if (svc) {
                            item.price = svc.price;
                            item.type = svc.type;
                        } else {
                            item.price = 0;
                            item.type = 'per_kg';
                        }
                        this.calculateSubtotal(index);
                    },

                    calculateSubtotal(index) {
                        const item = this.services[index];
                        item.subtotal = item.price * (item.qty || 0);
                        this.calculateTotal();
                    },

                    calculateTotal() {
                        this.total = this.services.reduce((sum, i) => sum + (i.subtotal || 0), 0);
                    },

                    formatRupiah(val) {
                        return new Intl.NumberFormat('id-ID').format(val || 0);
                    }
                }
            }
        </script>
    @endpush
@endsection