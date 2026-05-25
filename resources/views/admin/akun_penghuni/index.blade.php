<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Pengelolaan Akun Penghuni</h2>
                <p class="mt-1 text-sm text-slate-600">Kelola data pengguna penghuni kos Anda</p>
            </div>
            <a href="{{ route('akun-penghuni.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 font-semibold text-white shadow hover:bg-indigo-700 transition active:scale-95">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Buat Akun Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl py-8">

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Nama</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Email</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Terdaftar</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="border-b border-slate-200 transition hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <div class="h-9 w-9 rounded-full bg-red-100 flex items-center justify-center text-sm font-semibold text-red-700">{{ substr($user->name, 0, 1) }}</div>
                                        <span class="font-medium text-slate-900">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-slate-600">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-center text-slate-600">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('akun-penghuni.edit', $user) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-200 active:scale-95">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </a>
                                        <button type="button" onclick="document.getElementById('delete-user-{{ $user->id }}').showModal()" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-100 px-3 py-1.5 text-sm font-medium text-rose-700 transition hover:bg-rose-200 active:scale-95">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus</span>
                                        </button>

                                        {{-- Delete Modal --}}
                                        <dialog id="delete-user-{{ $user->id }}" class="w-full max-w-sm rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/50 border border-slate-100 overflow-hidden">
                                            <div class="p-8 text-center">
                                                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border-4 border-amber-400 text-amber-400 mb-6">
                                                    <span class="text-5xl font-light leading-none -mt-1">!</span>
                                                </div>
                                                <h3 class="text-2xl font-bold text-slate-800 tracking-wider uppercase mb-2">DELETE</h3>
                                                <p class="text-slate-600 mb-8">Hapus Data Akun : <span class="font-semibold">{{ $user->name }}</span> ?</p>
                                                <div class="flex items-center justify-center gap-4">
                                                    <form method="POST" action="{{ route('akun-penghuni.destroy', $user) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="rounded-lg bg-rose-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-rose-700 shadow-md transition active:scale-95">
                                                            Yes, delete!
                                                        </button>
                                                    </form>
                                                    <button type="button" onclick="document.getElementById('delete-user-{{ $user->id }}').close()" class="rounded-lg bg-slate-400 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-500 shadow-md transition active:scale-95">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </div>
                                        </dialog>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <p class="mt-4 text-base font-semibold text-slate-900">Tidak ada akun penghuni</p>
                                        <p class="mt-1 text-sm text-slate-600">Mulai dengan membuat akun penghuni baru.</p>
                                        <a href="{{ route('akun-penghuni.create') }}" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            <span>Buat Akun</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
