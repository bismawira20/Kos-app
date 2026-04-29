<x-guest-layout>
    <h2 class="text-xl font-bold text-slate-900">Daftar</h2>
    <p class="mt-1 text-sm text-slate-500">Buat akun penghuni {{ config('app.name', 'E-PayKos') }}</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <x-input-label for="name" value="Nama" />
            <x-text-input id="name" class="mt-1 block w-full rounded-lg border-slate-300" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-1 block w-full rounded-lg border-slate-300" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="mt-1 block w-full rounded-lg border-slate-300" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi password" />
            <x-text-input id="password_confirmation" class="mt-1 block w-full rounded-lg border-slate-300" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-violet-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:from-violet-700 hover:to-indigo-700">
            Daftar
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
        Sudah punya akun?
        <a class="font-medium text-indigo-600 hover:text-indigo-800" href="{{ route('login') }}">Login</a>
    </p>
</x-guest-layout>
