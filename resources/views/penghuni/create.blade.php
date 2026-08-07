<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Penghuni Baru</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if ($kamar->isEmpty())
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    Tidak ada kamar kosong. <a href="{{ route('kamar.create') }}" class="font-medium underline">Tambah kamar</a> terlebih dahulu.
                </div>
            @elseif ($users->isEmpty())
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    Tidak ada akun penghuni yang tersedia. Pastikan penghuni sudah melakukan registrasi terlebih dahulu.
                </div>
            @else
                <div class="rounded-xl bg-white p-6 shadow ring-1 ring-gray-100">
                    <form action="{{ route('penghuni.store') }}" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <x-input-label for="user_id" value="Pilih Akun Penghuni" />
                            <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required onchange="autoFillNama()">
                                <option value="">— Pilih akun yang sudah terdaftar —</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}" data-name="{{ $u->name }}" @selected(old('user_id') == $u->id)>
                                        {{ $u->name }} ({{ $u->email }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Hanya menampilkan akun yang sudah registrasi dan belum terhubung dengan data penghuni.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                        </div>

                        <div>
                            <x-input-label for="nama_display" value="Nama Penghuni" />
                            <x-text-input id="nama_display" type="text" class="mt-1 block w-full bg-slate-50" readonly placeholder="Otomatis terisi dari akun yang dipilih" />
                            <p class="mt-1 text-xs text-gray-400">Nama diambil otomatis dari data akun.</p>
                        </div>

                        <div>
                            <x-input-label for="no_hp" value="No. HP (WhatsApp)" />
                            <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full" :value="old('no_hp')" required placeholder="Contoh: 081234567890" pattern="^[0-9]{10,13}$" minlength="10" maxlength="13" title="Nomor HP harus berupa angka dengan panjang 10 hingga 13 digit" />
                            <x-input-error class="mt-2" :messages="$errors->get('no_hp')" />
                        </div>

                        <div>
                            <x-input-label for="alamat" value="Alamat Penghuni (Opsional)" />
                            <textarea id="alamat" name="alamat" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400" placeholder="Alamat asal atau domisili penghuni (opsional)">{{ old('alamat') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
                        </div>

                        <hr class="border-gray-100 my-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Kontak Darurat (Opsional)</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="nama_wali" value="Nama Kontak Darurat" />
                                <x-text-input id="nama_wali" name="nama_wali" type="text" class="mt-1 block w-full" :value="old('nama_wali')" placeholder="Nama kontak darurat" pattern="^[a-zA-Z\s]+$" title="Nama hanya boleh berisi huruf dan spasi" />
                                <x-input-error class="mt-2" :messages="$errors->get('nama_wali')" />
                            </div>
                            <div>
                                <x-input-label for="no_hp_wali" value="No. HP Kontak Darurat" />
                                <x-text-input id="no_hp_wali" name="no_hp_wali" type="text" class="mt-1 block w-full" :value="old('no_hp_wali')" placeholder="Contoh: 081234567890" pattern="^[0-9]{10,13}$" minlength="10" maxlength="13" title="Nomor HP harus berupa angka dengan panjang 10 hingga 13 digit" />
                                <x-input-error class="mt-2" :messages="$errors->get('no_hp_wali')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <x-input-label for="hubungan" value="Hubungan" />
                                <select id="hubungan" name="hubungan" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Pilih Hubungan --</option>
                                    @foreach (['Ayah', 'Ibu', 'Saudara', 'Suami', 'Istri', 'Teman', 'Lainnya'] as $rel)
                                        <option value="{{ $rel }}" @selected(old('hubungan') === $rel)>{{ $rel }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('hubungan')" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="alamat_wali" value="Alamat Lengkap Kontak Darurat" />
                            <textarea id="alamat_wali" name="alamat_wali" rows="2" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Alamat domisili kontak darurat">{{ old('alamat_wali') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('alamat_wali')" />
                        </div>

                        <hr class="border-gray-100 my-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Informasi Kamar &amp; Masa Sewa Awal</h3>

                        <!-- Info Banner Masa Sewa Awal -->
                        <div class="rounded-xl border border-indigo-200 bg-indigo-50/70 p-4 text-sm text-indigo-900 mb-4">
                            <div class="flex items-center gap-2 font-bold text-indigo-900 mb-1">
                                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Ketentuan Masa Sewa Penghuni Baru</span>
                            </div>
                            <p class="text-xs leading-relaxed text-indigo-800">
                                Penghuni baru wajib menjalani masa sewa awal selama <strong>12 Bulan</strong>. Pembayaran dilakukan dalam dua tahap, masing-masing <strong>6 Bulan</strong>. Pilihan durasi 3/6 bulan baru tersedia setelah kedua pembayaran tahap 6 bulan selesai (status Penghuni Lama).
                            </p>
                        </div>

                        <div>
                            <x-input-label for="kamar_id" value="Kamar Kos" />
                            <select id="kamar_id" name="kamar_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @foreach ($kamar as $k)
                                    <option value="{{ $k->id }}" @selected(old('kamar_id') == $k->id)>
                                        Kamar {{ $k->nomor_kamar }} — Rp {{ number_format($k->harga, 0, ',', '.') }}/bulan
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('kamar_id')" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="tanggal_masuk" value="Tanggal Masuk" />
                                <x-text-input id="tanggal_masuk" name="tanggal_masuk" type="date" class="mt-1 block w-full" :value="old('tanggal_masuk', now()->toDateString())" required onchange="updateTanggalSelesai()" />
                                <x-input-error class="mt-2" :messages="$errors->get('tanggal_masuk')" />
                            </div>

                            <div>
                                <x-input-label for="durasi_display" value="Masa Sewa Awal" />
                                <x-text-input id="durasi_display" type="text" class="mt-1 block w-full bg-slate-100 font-semibold text-indigo-700" value="12 Bulan (2x Tahap @ 6 Bln)" readonly />
                                <input type="hidden" name="durasi_kontrak" value="12" />
                            </div>

                            <div>
                                <x-input-label for="tanggal_selesai" value="Tanggal Berakhir Sewa Awal" />
                                <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="mt-1 block w-full bg-slate-50" :value="old('tanggal_selesai')" required readonly />
                                <x-input-error class="mt-2" :messages="$errors->get('tanggal_selesai')" />
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-3">
                            <x-primary-button>Simpan Data Penghuni Baru</x-primary-button>
                            <a href="{{ route('penghuni.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <script>
        function autoFillNama() {
            const select = document.getElementById('user_id');
            const namaDisplay = document.getElementById('nama_display');
            const selected = select.options[select.selectedIndex];

            if (selected && selected.value) {
                namaDisplay.value = selected.getAttribute('data-name') || '';
            } else {
                namaDisplay.value = '';
            }
        }

        function updateTanggalSelesai() {
            const tglMasukInput = document.getElementById('tanggal_masuk');
            const tglSelesaiInput = document.getElementById('tanggal_selesai');

            if (!tglMasukInput || !tglSelesaiInput) return;

            const startVal = tglMasukInput.value;
            if (!startVal) return;

            let date = new Date(startVal);
            date.setMonth(date.getMonth() + 12);
            date.setDate(date.getDate() - 1);

            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');

            tglSelesaiInput.value = `${yyyy}-${mm}-${dd}`;
        }

        document.addEventListener("DOMContentLoaded", function() {
            autoFillNama();
            updateTanggalSelesai();
        });
    </script>
</x-app-layout>
