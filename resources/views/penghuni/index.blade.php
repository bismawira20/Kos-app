<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Data Penghuni</h2>
                <p class="mt-1 text-sm text-slate-600">Kelola informasi diri, nomor telepon, dan penempatan kamar penghuni</p>
            </div>
            <a href="{{ route('penghuni.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 font-semibold text-white shadow hover:bg-indigo-700 transition active:scale-95">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Penghuni</span>
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
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">No. HP</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Kamar</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Akun Login</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($penghuni as $p)
                            <tr class="border-b border-slate-200 transition hover:bg-slate-50">
                                <td class="px-6 py-4 text-center font-semibold text-slate-900">{{ $p->nama }}</td>
                                <td class="px-6 py-4 text-center text-slate-600 font-medium">{{ $p->no_hp }}</td>
                                <td class="px-6 py-4 text-center text-slate-600 font-medium">
                                    <span class="inline-flex rounded-full bg-indigo-50 border border-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                                        Kamar {{ $p->kamar?->nomor_kamar ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-slate-500">
                                    @if ($p->user_id && $p->user)
                                        <span class="text-indigo-600 font-medium">{{ $p->user->email }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Belum dihubungkan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2 whitespace-nowrap">
                                        <button type="button" onclick="document.getElementById('detail-penghuni-{{ $p->id }}').showModal()" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-100 px-3 py-1.5 text-sm font-medium text-indigo-700 transition hover:bg-indigo-200 active:scale-95">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            <span>Detail</span>
                                        </button>

                                        <a href="{{ route('penghuni.edit', $p) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-200 active:scale-95">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </a>

                                        <button type="button" onclick="document.getElementById('delete-penghuni-{{ $p->id }}').showModal()" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-100 px-3 py-1.5 text-sm font-medium text-rose-700 transition hover:bg-rose-200 active:scale-95">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus</span>
                                        </button>

                                        {{-- Delete Modal --}}
                                        <dialog id="delete-penghuni-{{ $p->id }}" class="w-full max-w-sm rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/50 border border-slate-100 overflow-hidden">
                                            <div class="p-6 text-center">
                                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-rose-100 text-rose-600 mb-4">
                                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </div>
                                                <h3 class="text-lg font-bold text-slate-900 mb-2">Hapus Data Penghuni</h3>
                                                <p class="text-sm text-slate-600 mb-6">Apakah Anda yakin ingin menghapus data penghuni ini?</p>
                                                <div class="flex items-center justify-center gap-3">
                                                    <button type="button" onclick="document.getElementById('delete-penghuni-{{ $p->id }}').close()" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition active:scale-95">
                                                        Batal
                                                    </button>
                                                    <form action="{{ route('penghuni.destroy', $p) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 shadow-sm transition active:scale-95">
                                                            Ya, Lanjutkan
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </dialog>

                                        {{-- Modal Detail --}}
                                        <dialog id="detail-penghuni-{{ $p->id }}" class="w-full max-w-md rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/40 text-left border border-slate-200 overflow-hidden">
                                            <div class="border-b border-slate-100 px-5 py-4 bg-slate-50 flex items-center justify-between">
                                                <h3 class="text-lg font-bold text-slate-900">Detail Penghuni</h3>
                                                <form method="dialog">
                                                    <button class="text-slate-400 hover:text-slate-600 text-sm font-semibold">✕</button>
                                                </form>
                                            </div>
                                            <div class="px-5 py-5 space-y-4 text-sm max-h-[75vh] overflow-y-auto">
                                                <div>
                                                    <h4 class="text-xs font-bold tracking-wider uppercase text-slate-400 mb-2">Data Utama</h4>
                                                    <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-24">Nama:</span> {{ $p->nama }}</p>
                                                    <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-24">No. HP:</span> {{ $p->no_hp }}</p>
                                                    <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-24">Kamar:</span> {{ $p->kamar?->nomor_kamar ?? '—' }}</p>
                                                    <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-24">Tgl Masuk:</span> {{ $p->tanggal_masuk ? $p->tanggal_masuk->format('d M Y') : '—' }}</p>
                                                    <p class="text-slate-800">
                                                        <span class="font-medium text-slate-500 inline-block w-24">Tgl Selesai:</span> 
                                                        {{ $p->tanggal_selesai ? $p->tanggal_selesai->format('d M Y') : ($p->tanggal_masuk ? $p->tanggal_masuk->addMonths($p->durasi_kontrak)->subDay()->format('d M Y') : '—') }}
                                                    </p>
                                                    <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-24">Durasi:</span> {{ $p->durasi_kontrak }} Bulan</p>
                                                </div>
                                                
                                                <hr class="border-slate-100">

                                                <div>
                                                    <h4 class="text-xs font-bold tracking-wider uppercase text-slate-400 mb-2">Akun Login &amp; Keamanan</h4>
                                                    @if ($p->user_id && $p->user)
                                                        <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-24">Email:</span> <span class="font-semibold text-indigo-700">{{ $p->user->email }}</span></p>
                                                        <div class="mt-3">
                                                            <button type="button" onclick="document.getElementById('detail-penghuni-{{ $p->id }}').close(); document.getElementById('reset-password-{{ $p->id }}').showModal()" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 border border-amber-300 px-3 py-1.5 text-xs font-bold text-amber-800 hover:bg-amber-100 transition active:scale-95 shadow-sm">
                                                                <svg class="h-4 w-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z" /></svg>
                                                                <span>Reset Password</span>
                                                            </button>
                                                        </div>
                                                    @else
                                                        <p class="text-slate-400 italic">Belum terhubung ke akun login.</p>
                                                    @endif
                                                </div>

                                                <hr class="border-slate-100">

                                                <div>
                                                    <h4 class="text-xs font-bold tracking-wider uppercase text-slate-400 mb-2">Data Wali / Kontak Darurat</h4>
                                                    <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-24">Nama Wali:</span> {{ $p->nama_wali ?? '—' }}</p>
                                                    <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-24">Hubungan:</span> {{ $p->hubungan ?? '—' }}</p>
                                                    <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-24">No. HP Wali:</span> {{ $p->no_hp_wali ?? '—' }}</p>
                                                    <div class="mt-1">
                                                        <span class="font-medium text-slate-500 inline-block w-24">Alamat:</span>
                                                        <div class="mt-1 bg-slate-50 p-2 rounded text-slate-700 text-xs border border-slate-100 whitespace-pre-wrap">
                                                            {{ $p->alamat_wali ?: 'Belum diisi' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <form method="dialog" class="border-t border-slate-100 px-5 py-3 bg-slate-50 flex justify-end">
                                                <button class="rounded-lg bg-white border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm active:scale-95 transition">Tutup</button>
                                            </form>
                                        </dialog>

                                        {{-- Modal Reset Password --}}
                                        @if ($p->user_id && $p->user)
                                            <dialog id="reset-password-{{ $p->id }}" class="w-full max-w-md rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/50 border border-slate-100 overflow-hidden text-left">
                                                <form action="{{ route('penghuni.reset-password', $p) }}" method="POST">
                                                    @csrf
                                                    <div class="border-b border-slate-100 px-6 py-4 bg-slate-50">
                                                        <h3 class="text-lg font-bold text-slate-900">Reset Password Penghuni</h3>
                                                        <p class="text-xs text-slate-500 mt-0.5">Penghuni: <span class="font-semibold text-slate-800">{{ $p->nama }}</span> ({{ $p->user->email }})</p>
                                                    </div>
                                                    <div class="p-6 space-y-4">
                                                        <div>
                                                            <label for="pass-{{ $p->id }}" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Password Baru</label>
                                                            <input type="password" id="pass-{{ $p->id }}" name="password" required minlength="8" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Masukkan password baru (min. 8 karakter)">
                                                        </div>
                                                        <div>
                                                            <label for="pass-confirm-{{ $p->id }}" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Konfirmasi Password Baru</label>
                                                            <input type="password" id="pass-confirm-{{ $p->id }}" name="password_confirmation" required minlength="8" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ketik ulang password baru">
                                                        </div>
                                                    </div>
                                                    <div class="border-t border-slate-100 px-6 py-4 bg-slate-50 flex items-center justify-end gap-3">
                                                        <button type="button" onclick="document.getElementById('reset-password-{{ $p->id }}').close()" class="rounded-lg bg-white border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                                                        <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-xs font-bold text-white hover:bg-amber-700 shadow-sm transition active:scale-95">Simpan Password Baru</button>
                                                    </div>
                                                </form>
                                            </dialog>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada penghuni.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
