<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-slate-800">Profil Saya</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola informasi pribadi, kontak darurat, dan keamanan akun Anda</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Profile Overview Banner -->
        <div class="rounded-2xl bg-gradient-to-r from-indigo-700 via-indigo-800 to-slate-900 p-6 text-white shadow-md">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 text-2xl font-bold backdrop-blur-sm ring-1 ring-white/20">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">{{ $user->name }}</h3>
                        <p class="text-xs text-indigo-200">{{ $user->email }}</p>
                        <div class="mt-1.5 flex flex-wrap gap-2">
                            <span class="inline-flex rounded-full bg-indigo-500/30 px-2.5 py-0.5 text-[11px] font-semibold text-indigo-100 ring-1 ring-indigo-400/30">
                                {{ strtoupper($user->role) }}
                            </span>
                            @if ($penghuni && $penghuni->kamar)
                                <span class="inline-flex rounded-full bg-emerald-500/30 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-100 ring-1 ring-emerald-400/30">
                                    Kamar {{ $penghuni->kamar->nomor_kamar }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submenu Tab Navigation -->
        <div class="rounded-2xl bg-white p-2 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-wrap gap-1 border-b border-slate-100 p-2">
                <a href="{{ route('profile.edit', ['tab' => 'pribadi']) }}" class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition {{ $tab === 'pribadi' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    <span>Data Pribadi</span>
                </a>

                @if ($penghuni)
                    <a href="{{ route('profile.edit', ['tab' => 'wali']) }}" class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition {{ $tab === 'wali' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <span>Data Wali</span>
                    </a>
                @endif

                <a href="{{ route('profile.edit', ['tab' => 'keamanan']) }}" class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition {{ $tab === 'keamanan' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    <span>Keamanan Akun</span>
                </a>
            </div>

            <!-- Tab Content Area -->
            <div class="p-6">
                @if ($tab === 'pribadi')
                    <!-- Submenu: Data Pribadi (1 Form, 1 Simpan Button) -->
                    @include('profile.partials.update-profile-information-form')

                @elseif ($tab === 'wali' && $penghuni)
                    <!-- Submenu: Data Wali (1 Form, 1 Simpan Button) -->
                    @include('profile.partials.update-guardian-information-form')

                @elseif ($tab === 'keamanan')
                    <!-- Submenu: Keamanan Akun (1 Form, 1 Simpan Button) -->
                    <div class="space-y-8">
                        @include('profile.partials.update-password-form')

                        <hr class="border-slate-200">

                        <div class="pt-2">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
