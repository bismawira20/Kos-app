<x-guest-layout>
    <h2 class="text-xl font-bold text-slate-900">Lupa password</h2>
    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
        Masukkan email Anda. Kami akan kirim tautan reset password.
    </p>

    <x-auth-session-status class="mb-4 mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-1 block w-full rounded-lg border-slate-300" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-violet-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow">
            Kirim tautan reset
        </button>
    </form>

    <p class="mt-6 text-center text-sm">
        <a class="font-medium text-indigo-600 hover:text-indigo-800" href="{{ route('login') }}">← Kembali ke login</a>
    </p>
</x-guest-layout>
