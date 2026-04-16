<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LaundryPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-blue-50 flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-sky-600 rounded-2xl shadow-lg mb-4">
                <span class="text-3xl">🧺</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">LaundryPro</h1>
            <p class="text-gray-500 text-sm mt-1">Sistem Informasi Laundry</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Selamat Datang 👋</h2>

            @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                {{ $errors->first() }}
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="email@laundrypro.id"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition"
                        required autofocus>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition"
                        required>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-sky-600 border-gray-300">
                        Ingat saya
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-3 px-4 bg-sky-600 hover:bg-sky-700 text-white font-semibold rounded-xl transition-all duration-200 active:scale-95 shadow-md shadow-sky-200">
                    Masuk
                </button>
            </form>
        </div>

        {{-- Demo credentials --}}
        <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-xl">
            <p class="text-xs font-semibold text-amber-800 mb-2">🔑 Akun Demo:</p>
            <div class="space-y-1 text-xs text-amber-700">
                <p><span class="font-medium">Admin:</span> admin@laundrypro.id / password</p>
                <p><span class="font-medium">Karyawan:</span> budi@laundrypro.id / password</p>
            </div>
        </div>
    </div>
</body>
</html>
