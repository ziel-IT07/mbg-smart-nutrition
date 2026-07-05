<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - MBG Smart Nutrition</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center text-green-700">MBG Smart Nutrition</h1>
        <h2 class="text-lg font-semibold mb-4 text-center">Buat Akun Baru</h2>

        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 text-red-700 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="mt-1 w-full rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="mt-1 w-full rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Daftar Sebagai</label>
                <select name="role" required
                    class="mt-1 w-full rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500 shadow-sm">
                    <option value="">-- Pilih Role --</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="guru" {{ old('role') === 'guru' ? 'selected' : '' }}>Guru</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" required
                    class="mt-1 w-full rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                    class="mt-1 w-full rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500 shadow-sm">
            </div>

            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 rounded-lg transition">
                Daftar
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-green-700 font-medium hover:underline">Login di sini</a>
        </p>
    </div>
</body>
</html>