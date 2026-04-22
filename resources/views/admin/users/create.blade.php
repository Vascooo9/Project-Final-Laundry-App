@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded-2xl shadow border border-gray-100">

    <h2 class="text-xl font-bold text-gray-800 mb-5">Tambah User</h2>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600 mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-sky-200">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-sky-200">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600 mb-1">Password</label>
            <input type="password" name="password"
                   class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-sky-200">
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600 mb-1">Role</label>
            <select name="role"
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-sky-200">
                <option value="admin">Admin</option>
                <option value="karyawan">Karyawan</option>
                <option value="ceo">CEO</option>
            </select>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100">
                Batal
            </a>

            <button class="bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection