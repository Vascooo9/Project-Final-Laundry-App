@extends('layouts.app')

@section('title', 'Kelola Layanan')
@section('page-title', 'Kelola Layanan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ editId: null, editData: {} }">

    <div class="grid md:grid-cols-2 gap-6">

        {{-- Tambah Layanan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">➕ Tambah Layanan Baru</h3>

            <form action="{{ route('admin.services.store') }}" method="POST" class="space-y-3">
                @csrf

                <div>
                    <label class="label">Nama Layanan</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="mis: Cuci Saja, Seprai, dll."
                           class="input-field" required>
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label">Tipe Harga</label>
                    <select name="type" class="input-field" required>
                        <option value="per_kg"   {{ old('type') === 'per_kg'   ? 'selected' : '' }}>Per Kilogram (kg)</option>
                        <option value="per_item" {{ old('type') === 'per_item' ? 'selected' : '' }}>Per Item / Satuan</option>
                    </select>
                </div>

                <div>
                    <label class="label">Harga</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">Rp</span>
                        <input type="number" name="price" value="{{ old('price') }}"
                               placeholder="0" min="0" step="500"
                               class="input-field pl-10" required>
                    </div>
                    @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label">Deskripsi (opsional)</label>
                    <input type="text" name="description" value="{{ old('description') }}"
                           placeholder="Keterangan singkat layanan"
                           class="input-field">
                </div>

                <button type="submit" class="btn-primary w-full justify-center">
                    Simpan Layanan
                </button>
            </form>
        </div>

        {{-- Daftar Layanan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">📋 Daftar Layanan ({{ $services->total() }})</h3>
            </div>

            <div class="divide-y divide-gray-50 max-h-[500px] overflow-y-auto">
                @forelse($services as $service)
                <div class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <p class="font-semibold text-gray-800 text-sm">{{ $service->name }}</p>
                            @if(!$service->is_active)
                            <span class="text-xs bg-red-100 text-red-600 px-1.5 py-0.5 rounded-md">Nonaktif</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Rp {{ number_format($service->price, 0, ',', '.') }}
                            {{ $service->type === 'per_kg' ? '/ kg' : '/ item' }}
                        </p>
                        @if($service->description)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $service->description }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-1">
                        <button
                            @click="editId = {{ $service->id }}; editData = {{ json_encode($service) }}"
                            class="w-7 h-7 bg-sky-50 hover:bg-sky-100 text-sky-600 rounded-lg flex items-center justify-center transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST"
                              onsubmit="return confirm('Nonaktifkan layanan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-7 h-7 bg-red-50 hover:bg-red-100 text-red-500 rounded-lg flex items-center justify-center transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-gray-400 text-sm">
                    <span class="text-3xl block mb-2">🧺</span>
                    Belum ada layanan. Tambahkan di sebelah kiri.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="editId !== null"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         @click.self="editId = null">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" @click.stop>
            <h3 class="font-bold text-gray-900 mb-4">✏️ Edit Layanan</h3>

            <form :action="`/admin/services/${editId}`" method="POST" class="space-y-3">
                @csrf
                @method('PUT')

                <div>
                    <label class="label">Nama Layanan</label>
                    <input type="text" name="name" x-model="editData.name"
                           class="input-field" required>
                </div>

                <div>
                    <label class="label">Tipe Harga</label>
                    <select name="type" x-model="editData.type" class="input-field">
                        <option value="per_kg">Per Kilogram (kg)</option>
                        <option value="per_item">Per Item / Satuan</option>
                    </select>
                </div>

                <div>
                    <label class="label">Harga</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                        <input type="number" name="price" x-model="editData.price"
                               class="input-field pl-10" required>
                    </div>
                </div>

                <div>
                    <label class="label">Deskripsi</label>
                    <input type="text" name="description" x-model="editData.description"
                           class="input-field">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1"
                           :checked="editData.is_active"
                           id="is_active_edit"
                           class="w-4 h-4 rounded text-sky-600 border-gray-300">
                    <label for="is_active_edit" class="text-sm font-medium text-gray-700">Layanan Aktif</label>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary flex-1 justify-center">
                        Simpan Perubahan
                    </button>
                    <button type="button" @click="editId = null" class="btn-secondary">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
