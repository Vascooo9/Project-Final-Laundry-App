<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Vasco Laundry</title>

    {{-- Tailwind CSS via CDN (gunakan Vite untuk production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe',
                            300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6',
                            600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        brand: {
                            50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd',
                            500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-item { @apply flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200; }
        .sidebar-item:hover { @apply bg-white/10 text-white; }
        .sidebar-item.active { @apply bg-white text-sky-700 shadow-md; }
        .card { @apply bg-white rounded-2xl shadow-sm border border-gray-100 p-6; }
        .btn-primary { @apply inline-flex items-center gap-2 px-4 py-2 bg-sky-600 text-white text-sm font-semibold rounded-xl hover:bg-sky-700 active:scale-95 transition-all duration-200; }
        .btn-secondary { @apply inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 active:scale-95 transition-all duration-200; }
        .badge-pending    { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800; }
        .badge-processing { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800; }
        .badge-done       { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800; }
        .badge-picked_up  { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600; }
        .input-field { @apply w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition; }
        .label { @apply block text-sm font-semibold text-gray-700 mb-1.5; }
    </style>
</head>
<body class="h-full bg-gray-50">

<div class="flex h-screen overflow-hidden">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="w-64 bg-gradient-to-b from-sky-700 to-sky-900 flex flex-col flex-shrink-0">
        {{-- Logo --}}
        <div class="p-6 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow">
                    <img src="{{ asset('images/logolaundry.jpeg') }}" alt="Logo">
                </div>
                <div>
                    <h1 class="text-white font-bold text-lg leading-tight">Vasco Laundry</h1>
                    <p class="text-sky-200 text-xs">By Vasco Company</p>
                </div>
            </div>
        </div>

        {{-- User Info --}}
        <div class="px-4 py-3 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                    <span class="inline-block px-1.5 py-0.5 bg-white/20 text-white/80 text-xs rounded-md capitalize">{{ auth()->user()->role }}</span>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}"
               class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : 'text-sky-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <a href="{{ route('orders.index') }}"
               class="sidebar-item {{ request()->routeIs('orders.*') ? 'active' : 'text-sky-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                Daftar Order
            </a>

            <a href="{{ route('orders.create') }}"
               class="sidebar-item text-sky-100 hover:bg-white/10 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Input Order Baru
            </a>

            @if(auth()->user()->isAdmin())
            <div class="pt-4 pb-1">
                <p class="px-4 text-xs font-semibold text-sky-300 uppercase tracking-wider">Admin</p>
            </div>

            <a href="{{ route('admin.services.index') }}"
               class="sidebar-item {{ request()->routeIs('admin.services.*') ? 'active' : 'text-sky-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                Kelola Layanan
            </a>

            <a href="{{ route('admin.reports.index') }}"
               class="sidebar-item {{ request()->routeIs('admin.reports.*') ? 'active' : 'text-sky-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Laporan
            </a>
            @endif
        </nav>

        {{-- Logout --}}
        <div class="p-4 border-t border-white/10">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sky-100 hover:text-white hover:bg-white/10 rounded-lg text-sm font-medium transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                <p class="text-xs text-gray-500">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
            </div>
            <div class="flex items-center gap-3">
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="flex items-center gap-2 px-3 py-2 bg-green-50 text-green-700 rounded-lg text-sm border border-green-200">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="flex items-center gap-2 px-3 py-2 bg-red-50 text-red-700 rounded-lg text-sm border border-red-200">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ session('error') }}
                </div>
                @endif
                <a href="{{ route('orders.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Order Baru
                </a>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
