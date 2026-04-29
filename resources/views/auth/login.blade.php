<x-guest-layout>
    <h2 class="text-xl font-bold text-slate-900">Login</h2>
    <p class="mt-1 text-sm text-slate-500">Masuk ke akun {{ config('app.name', 'E-PayKos') }}</p>

    <x-auth-session-status class="mb-4 mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Username (email)" />
            <x-text-input id="email" class="mt-1 block w-full rounded-lg border-slate-300" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="mt-1 block w-full rounded-lg border-slate-300" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">Ingat saya</span>
            </label>
        </div>

        <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-violet-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:from-violet-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2">
            Login
        </button>
    </form>

    <div class="mt-6 space-y-2 text-center text-sm">
        @if (Route::has('register'))
            <p class="text-slate-600">
                Belum punya akun?
                <a class="font-medium text-indigo-600 hover:text-indigo-800" href="{{ route('register') }}">Daftar</a>
            </p>
        @endif
        @if (Route::has('password.request'))
            <p>
                <a class="font-medium text-indigo-600 hover:text-indigo-800" href="{{ route('password.request') }}">Lupa password?</a>
            </p>
        @endif
    </div>

    <p class="mt-6 border-t border-slate-200 pt-4 text-center text-xs leading-relaxed text-slate-500">
        Satu browser menyimpan satu sesi aktif. Untuk membuka beberapa akun bersamaan, gunakan
        <strong class="font-medium text-slate-600">jendela penyamaran (Incognito)</strong> atau browser lain.
        Aktifkan <strong class="font-medium text-slate-600">Ingat saya</strong> agar tidak sering diminta login ulang.
    </p>
</x-guest-layout>
