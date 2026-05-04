<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-slate-800">Tagihan &amp; pembayaran</h2>
            <p class="text-sm text-slate-500">Daftar tagihan Anda</p>
        </div>
    </x-slot>

    @if (! $penghuni)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 text-sm">Akun belum terhubung ke data penghuni.</div>
    @else
        <div x-data="{ 
            showDetail: false, 
            selectedTagihan: null,
            openModal(tagihan) {
                this.selectedTagihan = tagihan;
                this.showDetail = true;
            }
        }">
            <div class="mb-6 rounded-xl bg-indigo-50 px-4 py-3 text-sm text-indigo-900 ring-1 ring-indigo-100">
                <span class="font-medium">{{ $penghuni->nama }}</span>
                <span class="ml-2 rounded-full bg-indigo-200 px-2 py-0.5 text-xs">Kamar {{ $penghuni->kamar?->nomor_kamar ?? '—' }}</span>
            </div>

            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left">No</th>
                                <th class="px-4 py-3 text-left">Periode</th>
                                <th class="px-4 py-3 text-left">Jatuh tempo</th>
                                <th class="px-4 py-3 text-left">Jumlah</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($tagihans as $t)
                                @php
                                    $late = $t->jatuh_tempo && $t->jatuh_tempo->isPast() && $t->status !== 'lunas';
                                @endphp
                                <tr>
                                    <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $t->labelPeriode() }}</div>
                                        <div class="text-xs text-slate-500">Kamar {{ $t->kamar?->nomor_kamar }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $t->jatuh_tempo?->format('d/m/Y') }}
                                        @if ($late)
                                            <span class="ml-1 text-xs text-red-600">Terlambat</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-semibold">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2 py-0.5 text-xs font-medium
                                            {{ $t->status === 'lunas' ? 'bg-emerald-100 text-emerald-800' : ($t->status === 'menunggu' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                            {{ strtoupper(str_replace('_', ' ', $t->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button @click="openModal({{ json_encode($t) }})" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </button>
                                            
                                            @if ($t->status === 'belum_bayar')
                                                <a href="{{ route('penghuni.tagihan.midtrans', $t) }}" class="inline-block rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 shadow-sm transition-all">Midtrans</a>
                                                <a href="{{ route('penghuni.tagihan.bayar', $t) }}" class="inline-block rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700 shadow-sm transition-all">Manual</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada tagihan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL DETAIL -->
            <div x-show="showDetail" 
                 class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 style="display: none;">
                
                <div @click.away="showDetail = false" 
                     class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                    
                    <div class="relative bg-gradient-to-br from-indigo-600 to-violet-700 px-6 py-8 text-white">
                        <button @click="showDetail = false" class="absolute right-4 top-4 rounded-full bg-white/20 p-1 text-white hover:bg-white/30 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                        <p class="text-xs font-bold uppercase tracking-widest text-indigo-100">Detail Tagihan</p>
                        <h3 class="mt-2 text-2xl font-bold" x-text="selectedTagihan ? (new Date(selectedTagihan.tahun, selectedTagihan.bulan - 1).toLocaleString('id-ID', { month: 'long', year: 'numeric' })) : ''"></h3>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wider bg-white/20" x-text="selectedTagihan ? selectedTagihan.status.replace('_', ' ') : ''"></span>
                        </div>
                    </div>

                    <div class="p-6 space-y-4 text-slate-600">
                        <div class="flex justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm">Jumlah Tagihan</span>
                            <span class="font-bold text-slate-900" x-text="selectedTagihan ? 'Rp ' + new Intl.NumberFormat('id-ID').format(selectedTagihan.jumlah) : ''"></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm">Jatuh Tempo</span>
                            <span class="font-medium text-slate-900" x-text="selectedTagihan ? new Date(selectedTagihan.jatuh_tempo).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : ''"></span>
                        </div>
                        
                        <template x-if="selectedTagihan && selectedTagihan.pembayaran && selectedTagihan.pembayaran.length > 0">
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase text-slate-400">Catatan Admin</p>
                                <p class="mt-1 text-sm text-slate-700" x-text="selectedTagihan.pembayaran[selectedTagihan.pembayaran.length - 1].admin_komentar || 'Tidak ada catatan.'"></p>
                            </div>
                        </template>

                        <div class="mt-6 flex flex-col gap-2">
                            <template x-if="selectedTagihan && selectedTagihan.status === 'lunas'">
                                <a :href="'{{ url('penghuni/tagihan') }}/' + selectedTagihan.id + '/invoice'" 
                                   class="flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    Download Invoice (PDF)
                                </a>
                            </template>
                            
                            <button @click="showDetail = false" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
