<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Data Penghuni</h2>
                <p class="mt-1 text-sm text-slate-600">Kelola informasi diri, penempatan kamar, dan perpanjangan masa sewa</p>
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
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Status</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Akun Login</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($penghuni as $p)
                            @php
                                $isLama = $p->isPenghuniLama();
                                $hasUnpaidBills = $p->tagihan->whereIn('status', ['belum_bayar', 'menunggu', 'ditolak'])->count() > 0;
                                $startDatePerpanjang = $p->tanggal_selesai ? $p->tanggal_selesai->copy()->addDay() : \Carbon\Carbon::now();
                                $hargaTerbaru = $p->kamar?->harga ?? 0;
                            @endphp
                            <tr class="border-b border-slate-200 transition hover:bg-slate-50">
                                <td class="px-6 py-4 text-center font-semibold text-slate-900">{{ $p->nama }}</td>
                                <td class="px-6 py-4 text-center text-slate-600 font-medium">{{ $p->no_hp }}</td>
                                <td class="px-6 py-4 text-center text-slate-600 font-medium">
                                    <span class="inline-flex rounded-full bg-indigo-50 border border-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                                        Kamar {{ $p->kamar?->nomor_kamar ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($isLama)
                                        <span class="inline-flex rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">
                                            Penghuni Lama
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-indigo-50 border border-indigo-200 px-2.5 py-0.5 text-xs font-semibold text-indigo-800">
                                            Penghuni Baru
                                        </span>
                                    @endif
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
                                        @if ($isLama)
                                            <button type="button" onclick="document.getElementById('perpanjang-penghuni-{{ $p->id }}').showModal()" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-700 active:scale-95 shadow-sm">
                                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>Perpanjang Sewa</span>
                                            </button>

                                            {{-- Modal Perpanjang Sewa --}}
                                            <dialog id="perpanjang-penghuni-{{ $p->id }}" class="w-full max-w-lg rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/50 border border-slate-100 overflow-hidden text-left">
                                                <form action="{{ route('penghuni.perpanjang', $p) }}" method="POST">
                                                    @csrf
                                                    <div class="border-b border-slate-100 px-6 py-4 bg-slate-50 flex items-center justify-between">
                                                        <div class="pr-4">
                                                            <h3 class="text-lg font-bold text-slate-900 leading-snug whitespace-normal break-words">Perpanjang Masa Sewa</h3>
                                                            <p class="text-xs text-slate-500 mt-1 whitespace-normal break-words leading-relaxed">
                                                                Penghuni: <span class="font-semibold text-slate-800">{{ $p->nama }}</span> (Kamar {{ $p->kamar?->nomor_kamar ?? '-' }})
                                                            </p>
                                                        </div>
                                                        <button type="button" onclick="document.getElementById('perpanjang-penghuni-{{ $p->id }}').close()" class="text-slate-400 hover:text-slate-600 font-bold text-lg p-1 shrink-0">✕</button>
                                                    </div>
                                                    
                                                    <div class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                                                        @if ($hasUnpaidBills)
                                                            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-900 flex items-start gap-3 leading-relaxed whitespace-normal break-words">
                                                                <svg class="h-5 w-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                <div>
                                                                    <p class="font-bold text-rose-950 text-sm">Perpanjangan Dibatalkan</p>
                                                                    <p class="mt-1 leading-relaxed">Perpanjangan masa sewa tidak dapat dilakukan karena masih terdapat tagihan yang belum diselesaikan.</p>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div>
                                                            <label for="durasi-{{ $p->id }}" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 whitespace-normal break-words">Durasi Perpanjangan Sewa</label>
                                                            <select id="durasi-{{ $p->id }}" name="durasi_kontrak" required class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2" onchange="calcPerpanjangDate({{ $p->id }}, '{{ $startDatePerpanjang->format('Y-m-d') }}')" @disabled($hasUnpaidBills)>
                                                                <option value="3">3 Bulan</option>
                                                                <option value="6">6 Bulan</option>
                                                                <option value="12">12 Bulan</option>
                                                            </select>
                                                        </div>

                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                            <div>
                                                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 whitespace-normal break-words">Tanggal Mulai Sewa (Otomatis)</label>
                                                                <input type="text" value="{{ $startDatePerpanjang->format('d/m/Y') }}" readonly class="w-full rounded-lg border-slate-200 bg-slate-100 text-xs font-semibold text-slate-700 py-2">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 whitespace-normal break-words">Tanggal Berakhir Sewa</label>
                                                                <input type="text" id="tgl-selesai-prev-{{ $p->id }}" readonly class="w-full rounded-lg border-slate-200 bg-slate-100 text-xs font-semibold text-emerald-700 py-2">
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 whitespace-normal break-words">Harga Sewa Terbaru (Kontrak Baru)</label>
                                                            <input type="text" value="Rp {{ number_format($hargaTerbaru, 0, ',', '.') }} / bulan" readonly class="w-full rounded-lg border-slate-200 bg-slate-100 text-sm font-bold text-indigo-900 py-2">
                                                            <p class="text-[11px] text-slate-500 mt-1.5 leading-normal whitespace-normal break-words">
                                                                Harga ini akan dikunci sebagai harga kontrak baru seluruh tagihan masa sewa berikutnya.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="border-t border-slate-100 px-6 py-4 bg-slate-50 flex items-center justify-end gap-3">
                                                        <button type="button" onclick="document.getElementById('perpanjang-penghuni-{{ $p->id }}').close()" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">Batal</button>
                                                        @if ($hasUnpaidBills)
                                                            <button type="button" disabled class="rounded-xl bg-slate-200 px-4 py-2 text-xs font-bold text-slate-400 cursor-not-allowed">
                                                                Tagihan Belum Selesai
                                                            </button>
                                                        @else
                                                            <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white hover:bg-emerald-700 shadow-sm transition active:scale-95">
                                                                Simpan Perpanjangan
                                                            </button>
                                                        @endif
                                                    </div>
                                                </form>
                                            </dialog>
                                        @endif

                                        <button type="button" onclick="document.getElementById('detail-penghuni-{{ $p->id }}').showModal()" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-100 px-3 py-1.5 text-xs font-medium text-indigo-700 transition hover:bg-indigo-200 active:scale-95">
                                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            <span>Detail</span>
                                        </button>

                                        <a href="{{ route('penghuni.edit', $p) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-200 active:scale-95">
                                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </a>

                                        <button type="button" onclick="document.getElementById('delete-penghuni-{{ $p->id }}').showModal()" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-100 px-3 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-200 active:scale-95">
                                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
                                                    <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-28">Nama:</span> {{ $p->nama }}</p>
                                                    <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-28">No. HP:</span> {{ $p->no_hp }}</p>
                                                    <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-28">Alamat:</span> <span class="{{ $p->alamat ? 'text-slate-800 font-medium' : 'text-slate-400 italic' }}">{{ $p->alamat ?: 'Belum diisi' }}</span></p>
                                                    <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-28">Kamar:</span> {{ $p->kamar?->nomor_kamar ?? '—' }}</p>
                                                    <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-28">Status:</span> {{ $p->status_penghuni }}</p>
                                                    <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-28">Harga Kontrak:</span> Rp {{ number_format($p->harga_sewa_effective, 0, ',', '.') }}/bulan</p>
                                                    <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-28">Tgl Masuk:</span> {{ $p->tanggal_masuk ? $p->tanggal_masuk->format('d M Y') : '—' }}</p>
                                                    <p class="text-slate-800">
                                                        <span class="font-medium text-slate-500 inline-block w-28">Tgl Selesai:</span> 
                                                        {{ $p->tanggal_selesai ? $p->tanggal_selesai->format('d M Y') : '—' }}
                                                    </p>
                                                    <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-28">Durasi:</span> {{ $p->durasi_kontrak }} Bulan</p>
                                                </div>
                                                
                                                <hr class="border-slate-100">

                                                <div>
                                                    <h4 class="text-xs font-bold tracking-wider uppercase text-slate-400 mb-2">Kontak Darurat / Wali</h4>
                                                    @if ($p->nama_wali || $p->no_hp_wali)
                                                        <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-28">Nama Wali:</span> {{ $p->nama_wali ?: 'Belum diisi' }}</p>
                                                        <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-28">No. HP Wali:</span> {{ $p->no_hp_wali ?: 'Belum diisi' }}</p>
                                                        <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-28">Hubungan:</span> {{ $p->hubungan ?: 'Belum diisi' }}</p>
                                                        <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-28">Alamat Wali:</span> {{ $p->alamat_wali ?: 'Belum diisi' }}</p>
                                                    @else
                                                        <p class="text-slate-400 italic">Belum ada data kontak darurat.</p>
                                                    @endif
                                                </div>

                                                <hr class="border-slate-100">

                                                <div>
                                                    <h4 class="text-xs font-bold tracking-wider uppercase text-slate-400 mb-2">Akun Login &amp; Keamanan</h4>
                                                    @if ($p->user_id && $p->user)
                                                        <p class="text-slate-800"><span class="font-medium text-slate-500 inline-block w-28">Email:</span> <span class="font-semibold text-indigo-700">{{ $p->user->email }}</span></p>
                                                    @else
                                                        <p class="text-slate-400 italic">Belum terhubung ke akun login.</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <form method="dialog" class="border-t border-slate-100 px-5 py-3 bg-slate-50 flex justify-end">
                                                <button class="rounded-lg bg-white border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm active:scale-95 transition">Tutup</button>
                                            </form>
                                        </dialog>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">Belum ada data penghuni.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function calcPerpanjangDate(penghuniId, startYmd) {
            const selectEl = document.getElementById(`durasi-${penghuniId}`);
            const targetEl = document.getElementById(`tgl-selesai-prev-${penghuniId}`);
            if (!selectEl || !targetEl || !startYmd) return;

            const months = parseInt(selectEl.value) || 3;
            let d = new Date(startYmd);
            d.setMonth(d.getMonth() + months);
            d.setDate(d.getDate() - 1);

            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');

            targetEl.value = `${dd}/${mm}/${yyyy}`;
        }

        document.addEventListener("DOMContentLoaded", function() {
            @foreach ($penghuni as $p)
                @if ($p->isPenghuniLama())
                    calcPerpanjangDate({{ $p->id }}, '{{ ($p->tanggal_selesai ? $p->tanggal_selesai->copy()->addDay() : \Carbon\Carbon::now())->format('Y-m-d') }}');
                @endif
            @endforeach
        });
    </script>
</x-app-layout>
