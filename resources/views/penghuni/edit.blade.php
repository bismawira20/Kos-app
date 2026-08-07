<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Ubah Data Penghuni</h2>
                <p class="mt-1 text-sm text-slate-600">Perbarui data identitas diri, kamar, kontak darurat, dan akun login penghuni</p>
            </div>
            <a href="{{ route('penghuni.index') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <form action="{{ route('penghuni.update', $penghuni) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nama" value="Nama Lengkap" />
                        <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $penghuni->nama)" required autofocus pattern="^[a-zA-Z\s]+$" title="Nama hanya boleh berisi huruf dan spasi" />
                        <x-input-error class="mt-2" :messages="$errors->get('nama')" />
                    </div>

                    <div>
                        <x-input-label for="no_hp" value="No. HP (WhatsApp)" />
                        <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full" :value="old('no_hp', $penghuni->no_hp)" required placeholder="Contoh: 081234567890" pattern="^[0-9]{10,13}$" minlength="10" maxlength="13" title="Nomor HP harus berupa angka dengan panjang 10 hingga 13 digit" />
                        <x-input-error class="mt-2" :messages="$errors->get('no_hp')" />
                    </div>

                    <div>
                        <x-input-label for="alamat" value="Alamat Penghuni (Opsional)" />
                        <textarea id="alamat" name="alamat" rows="2" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-slate-400" placeholder="Alamat asal atau domisili penghuni (opsional)">{{ old('alamat', $penghuni->alamat) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
                    </div>

                    <div>
                        <x-input-label for="kamar_id" value="Kamar Kos" />
                        <select id="kamar_id" name="kamar_id" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @foreach ($kamar as $k)
                                <option value="{{ $k->id }}" @selected(old('kamar_id', $penghuni->kamar_id) == $k->id)>
                                    Kamar {{ $k->nomor_kamar }} — Rp {{ number_format($k->harga, 0, ',', '.') }}/bulan
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('kamar_id')" />
                    </div>

                    <hr class="border-slate-100 my-4">

                    <!-- Information Read-Only Section for Lease Contract -->
                    <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 space-y-3">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <h3 class="text-sm font-bold text-slate-800">Informasi Masa Sewa (Read-Only)</h3>
                        </div>
                        <p class="text-xs text-slate-600">
                            Masa sewa dan kontrak tidak dapat diubah dari halaman ini. Untuk memperpanjang atau memperbarui durasi sewa, gunakan tombol <strong>"Perpanjang Sewa"</strong> pada halaman Kelola Penghuni.
                        </p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                            <div>
                                <x-input-label value="Tanggal Mulai Sewa" />
                                <x-text-input type="text" class="mt-1 block w-full bg-white text-slate-700 font-semibold cursor-not-allowed" :value="$penghuni->tanggal_masuk ? $penghuni->tanggal_masuk->format('d/m/Y') : '-'" readonly disabled />
                            </div>

                            <div>
                                <x-input-label value="Durasi Sewa Saat Ini" />
                                <x-text-input type="text" class="mt-1 block w-full bg-white text-indigo-700 font-semibold cursor-not-allowed" :value="$penghuni->durasi_kontrak . ' Bulan'" readonly disabled />
                            </div>

                            <div>
                                <x-input-label value="Tanggal Berakhir Sewa" />
                                <x-text-input type="text" class="mt-1 block w-full bg-white text-slate-700 font-semibold cursor-not-allowed" :value="$penghuni->tanggal_selesai ? $penghuni->tanggal_selesai->format('d/m/Y') : '-'" readonly disabled />
                            </div>
                        </div>
                    </div>

                    <hr class="border-slate-100 my-4">
                    <h3 class="text-sm font-bold text-slate-800 mb-3">Kontak Darurat / Wali (Opsional)</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="nama_wali" value="Nama Kontak Darurat" />
                            <x-text-input id="nama_wali" name="nama_wali" type="text" class="mt-1 block w-full" :value="old('nama_wali', $penghuni->nama_wali)" placeholder="Nama kontak darurat" pattern="^[a-zA-Z\s]+$" title="Nama hanya boleh berisi huruf dan spasi" />
                            <x-input-error class="mt-2" :messages="$errors->get('nama_wali')" />
                        </div>
                        <div>
                            <x-input-label for="no_hp_wali" value="No. HP Kontak Darurat" />
                            <x-text-input id="no_hp_wali" name="no_hp_wali" type="text" class="mt-1 block w-full" :value="old('no_hp_wali', $penghuni->no_hp_wali)" placeholder="Contoh: 081234567890" pattern="^[0-9]{10,13}$" minlength="10" maxlength="13" title="Nomor HP harus berupa angka dengan panjang 10 hingga 13 digit" />
                            <x-input-error class="mt-2" :messages="$errors->get('no_hp_wali')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <x-input-label for="hubungan" value="Hubungan" />
                            <select id="hubungan" name="hubungan" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Pilih Hubungan --</option>
                                @foreach (['Ayah', 'Ibu', 'Saudara', 'Suami', 'Istri', 'Teman', 'Lainnya'] as $rel)
                                    <option value="{{ $rel }}" @selected(old('hubungan', $penghuni->hubungan) === $rel)>{{ $rel }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('hubungan')" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="alamat_wali" value="Alamat Lengkap Kontak Darurat" />
                        <textarea id="alamat_wali" name="alamat_wali" rows="2" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Alamat domisili kontak darurat">{{ old('alamat_wali', $penghuni->alamat_wali) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('alamat_wali')" />
                    </div>

                    <hr class="border-slate-100 my-4">

                    <div>
                        <x-input-label for="user_id" value="Akun Login Terhubung" />
                        <select id="user_id" name="user_id" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— Tidak Ada —</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" @selected(old('user_id', $penghuni->user_id) == $u->id)>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                    </div>

                    <div class="flex items-center gap-3 pt-3">
                        <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 font-semibold text-white shadow-sm hover:bg-indigo-700 active:scale-95 transition">
                            Simpan Perubahan Identitas
                        </button>
                        <a href="{{ route('penghuni.index') }}" class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
