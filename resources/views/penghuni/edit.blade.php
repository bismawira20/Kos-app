<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ubah data penghuni</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-6 shadow ring-1 ring-gray-100">
                <form action="{{ route('penghuni.update', $penghuni) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nama" value="Nama lengkap" />
                        <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $penghuni->nama)" required autofocus pattern="^[a-zA-Z\s]+$" title="Nama hanya boleh berisi huruf dan spasi" />
                        <x-input-error class="mt-2" :messages="$errors->get('nama')" />
                    </div>

                    <div>
                        <x-input-label for="no_hp" value="No. HP (WhatsApp)" />
                        <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full" :value="old('no_hp', $penghuni->no_hp)" required placeholder="62812..." pattern="^[0-9]{10,13}$" minlength="10" maxlength="13" title="Nomor HP harus berupa angka dengan panjang 10 hingga 13 digit" />
                        <x-input-error class="mt-2" :messages="$errors->get('no_hp')" />
                    </div>

                    <hr class="border-gray-100 my-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Kontak Darurat (Opsional)</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="nama_wali" value="Nama Kontak Darurat" />
                            <x-text-input id="nama_wali" name="nama_wali" type="text" class="mt-1 block w-full" :value="old('nama_wali', $penghuni->nama_wali)" placeholder="Nama kontak darurat" pattern="^[a-zA-Z\s]+$" title="Nama hanya boleh berisi huruf dan spasi" />
                            <x-input-error class="mt-2" :messages="$errors->get('nama_wali')" />
                        </div>
                        <div>
                            <x-input-label for="no_hp_wali" value="No. HP Kontak Darurat" />
                            <x-text-input id="no_hp_wali" name="no_hp_wali" type="text" class="mt-1 block w-full" :value="old('no_hp_wali', $penghuni->no_hp_wali)" placeholder="62812..." pattern="^[0-9]{10,13}$" minlength="10" maxlength="13" title="Nomor HP harus berupa angka dengan panjang 10 hingga 13 digit" />
                            <x-input-error class="mt-2" :messages="$errors->get('no_hp_wali')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <x-input-label for="hubungan" value="Hubungan" />
                            <select id="hubungan" name="hubungan" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                        <textarea id="alamat_wali" name="alamat_wali" rows="2" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Alamat domisili kontak darurat">{{ old('alamat_wali', $penghuni->alamat_wali) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('alamat_wali')" />
                    </div>

                    <hr class="border-gray-100 my-4">

                    <div>
                        <x-input-label for="kamar_id" value="Kamar" />
                        <select id="kamar_id" name="kamar_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @foreach ($kamar as $k)
                                <option value="{{ $k->id }}" @selected(old('kamar_id', $penghuni->kamar_id) == $k->id)>
                                    {{ $k->nomor_kamar }} — Rp {{ number_format($k->harga, 0, ',', '.') }}/bln
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('kamar_id')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="tanggal_masuk" value="Tanggal Masuk" />
                            <x-text-input id="tanggal_masuk" name="tanggal_masuk" type="date" class="mt-1 block w-full" :value="old('tanggal_masuk', $penghuni->tanggal_masuk ? $penghuni->tanggal_masuk->toDateString() : '')" required onchange="updateTanggalSelesai()" />
                            <x-input-error class="mt-2" :messages="$errors->get('tanggal_masuk')" />
                        </div>

                        <div>
                            <x-input-label for="durasi_kontrak" value="Durasi Sewa" />
                            <select id="durasi_kontrak" name="durasi_kontrak" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required onchange="updateTanggalSelesai()">
                                @php
                                    $currentDur = (int) $penghuni->durasi_kontrak;
                                    $standardDurs = [3, 6, 12];
                                @endphp
                                @if(!in_array($currentDur, $standardDurs))
                                    <option value="{{ $currentDur }}" selected>{{ $currentDur }} Bulan</option>
                                @endif
                                <option value="3" @selected(old('durasi_kontrak', $currentDur) == 3)>3 Bulan</option>
                                <option value="6" @selected(old('durasi_kontrak', $currentDur) == 6)>6 Bulan</option>
                                <option value="12" @selected(old('durasi_kontrak', $currentDur) == 12)>12 Bulan</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('durasi_kontrak')" />
                        </div>

                        <div>
                            <x-input-label for="tanggal_selesai" value="Tanggal Berakhir Masa Sewa" />
                            <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="mt-1 block w-full" :value="old('tanggal_selesai', $penghuni->tanggal_selesai ? $penghuni->tanggal_selesai->toDateString() : '')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('tanggal_selesai')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="user_id" value="Hubungkan akun login (opsional)" />
                        <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— Tidak ada —</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" @selected(old('user_id', $penghuni->user_id) == $u->id)>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Pilih akun dengan peran penghuni yang belum terhubung ke data kos.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Perbarui</x-primary-button>
                        <a href="{{ route('penghuni.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateTanggalSelesai() {
            const tglMasukInput = document.getElementById('tanggal_masuk');
            const durasiInput = document.getElementById('durasi_kontrak');
            const tglSelesaiInput = document.getElementById('tanggal_selesai');

            if (!tglMasukInput || !durasiInput || !tglSelesaiInput) return;

            const startVal = tglMasukInput.value;
            const durasiVal = parseInt(durasiInput.value);

            if (!startVal || isNaN(durasiVal)) return;

            let date = new Date(startVal);
            date.setMonth(date.getMonth() + durasiVal);
            date.setDate(date.getDate() - 1);

            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');

            tglSelesaiInput.value = `${yyyy}-${mm}-${dd}`;
        }
    </script>
</x-app-layout>
