<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-slate-800">Profil akun</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola data login, kata sandi, dan informasi penghuni bila ada</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:col-span-2">
                @include('profile.partials.update-profile-information-form')
            </div>
            <div class="rounded-2xl bg-gradient-to-br from-indigo-700 to-violet-900 p-6 text-white shadow-lg ring-1 ring-indigo-900/20">
                <p class="text-xs uppercase tracking-[0.25em] text-indigo-200">Ringkasan akun</p>
                <h3 class="mt-3 text-2xl font-semibold">{{ $user->name }}</h3>
                <p class="mt-2 text-sm text-indigo-100">{{ $user->email }}</p>
                <div class="mt-5 rounded-2xl bg-white/10 p-4 text-sm">
                    <p class="text-indigo-200">Peran</p>
                    <p class="mt-1 font-semibold">{{ strtoupper($user->role) }}</p>
                    @if ($penghuni)
                        <div class="mt-4 border-t border-white/15 pt-4">
                            <p class="text-indigo-200">Data penghuni</p>
                            <p class="mt-1 font-semibold">{{ $penghuni->nama }}</p>
                            <p class="text-indigo-100">Kamar {{ $penghuni->kamar?->nomor_kamar ?? '—' }}</p>
                            <p class="text-indigo-100">{{ $penghuni->no_hp }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if ($penghuni)
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                @include('profile.partials.update-guardian-information-form')
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                @include('profile.partials.update-password-form')
            </div>
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
