@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded-2xl shadow border border-gray-100">

    <h2 class="text-xl font-bold text-gray-800 mb-5">Edit User</h2>

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm text-gray-600 mb-1">Nama</label>
            <input type="text" name="name" value="{{ $user->name }}"
                   class="w-full border rounded-lg px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm text-gray-600 mb-1">Email</label>
            <input type="email" name="email" value="{{ $user->email }}"
                   class="w-full border rounded-lg px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm text-gray-600 mb-1">Password (opsional)</label>
            <input type="password" name="password"
                   class="w-full border rounded-lg px-3 py-2">
        </div>

        <div class="mb-5">
            <label class="block text-sm text-gray-600 mb-1">Role</label>
            <select name="role" class="w-full border rounded-lg px-3 py-2">
                <option value="admin" {{ $user->role=='admin'?'selected':'' }}>Admin</option>
                <option value="karyawan" {{ $user->role=='karyawan'?'selected':'' }}>Karyawan</option>
                <option value="ceo" {{ $user->role=='ceo'?'selected':'' }}>CEO</option>
            </select>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">
                Batal
            </a>

            <button class="bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700">
                Update
            </button>
        </div>
    </form>
</div>
@endsection