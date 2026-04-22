@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kelola User</h2>

        <a href="{{ route('admin.users.create') }}"
           class="bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700">
            + Tambah User
        </a>
    </div>

    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-5 py-3 text-left">Nama</th>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Role</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-800">
                        {{ $user->name }}
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $user->email }}
                    </td>

                    <td class="px-5 py-3">
                        <span class="px-2 py-1 rounded text-xs font-semibold
                            @if($user->role == 'admin') bg-blue-100 text-blue-700
                            @elseif($user->role == 'ceo') bg-purple-100 text-purple-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ strtoupper($user->role) }}
                        </span>
                    </td>

                    <td class="px-5 py-3 text-right space-x-2">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-xs">
                            Edit
                        </a>

                        <form action="{{ route('admin.users.destroy', $user) }}"
                              method="POST"
                              class="inline-block"
                              onsubmit="return confirm('Yakin hapus user ini?')">
                            @csrf
                            @method('DELETE')

                            <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-6 text-gray-500">
                        Belum ada user
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection