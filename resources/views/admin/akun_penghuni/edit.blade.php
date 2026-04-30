@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="mb-8">
            <a href="{{ route('akun-penghuni.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-slate-900 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Kembali</span>
            </a>
            <h2 class="mt-2 text-2xl font-bold text-slate-900">Edit Akun Penghuni</h2>
            <p class="mt-1 text-sm text-slate-600">Perbarui data penghuni <span class="font-semibold">{{ $user->name }}</span></p>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-900 shadow-sm">
                <p class="mb-3 font-semibold">Terjadi kesalahan:</p>
                <ul class="list-inside list-disc space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('akun-penghuni.update', $user) }}" class="space-y-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-900 mb-2">Nama Lengkap</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $user->name) }}"
                    required
                    class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 shadow-sm transition focus:border-red-500 focus:ring-2 focus:ring-red-200 placeholder:text-slate-400"
                />
                @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-900 mb-2">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 shadow-sm transition focus:border-red-500 focus:ring-2 focus:ring-red-200 placeholder:text-slate-400"
                />
                @error('email')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2 rounded-lg bg-blue-50 p-4 border border-blue-200">
                <p class="text-sm font-medium text-blue-900">💡 Ubah Password (Opsional)</p>
                <p class="text-xs text-blue-800">Biarkan kosong jika tidak ingin mengubah password</p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-900 mb-2">Password Baru</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 shadow-sm transition focus:border-red-500 focus:ring-2 focus:ring-red-200 placeholder:text-slate-400"
                        placeholder="Minimal 8 karakter (opsional)"
                    />
                    @error('password')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-900 mb-2">Konfirmasi Password</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 shadow-sm transition focus:border-red-500 focus:ring-2 focus:ring-red-200 placeholder:text-slate-400"
                        placeholder="Ulangi password (opsional)"
                    />
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-6 py-2.5 font-semibold text-white shadow-lg shadow-red-600/30 transition hover:bg-red-700 hover:shadow-xl active:scale-95">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
                <a href="{{ route('akun-penghuni.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-6 py-2.5 font-semibold text-slate-900 transition hover:bg-slate-200 active:scale-95">
                    <span>Batal</span>
                </a>
            </div>
        </form>
    </div>
@endsection
