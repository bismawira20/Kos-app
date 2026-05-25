<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah penghuni</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if ($kamar->isEmpty())
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    Tidak ada kamar kosong. <a href="{{ route('kamar.create') }}" class="font-medium underline">Tambah kamar</a> terlebih dahulu.
                </div>
            @else
                <div class="rounded-xl bg-white p-6 shadow ring-1 ring-gray-100">
                    <form action="{{ route('penghuni.store') }}" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <x-input-label for="nama" value="Nama lengkap" />
                            <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama')" required autofocus pattern="^[a-zA-Z\s]+$" title="Nama hanya boleh berisi huruf dan spasi" />
                            <x-input-error class="mt-2" :messages="$errors->get('nama')" />
                        </div>

                        <div>
                            <x-input-label for="no_hp" value="No. HP (WhatsApp)" />
                            <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full" :value="old('no_hp')" required placeholder="62812..." pattern="^[0-9]{10,13}$" minlength="10" maxlength="13" title="Nomor HP harus berupa angka dengan panjang 10 hingga 13 digit" />
                            <x-input-error class="mt-2" :messages="$errors->get('no_hp')" />
                        </div>

                        <hr class="border-gray-100 my-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Data Wali (Opsional)</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="nama_wali" value="Nama Wali" />
                                <x-text-input id="nama_wali" name="nama_wali" type="text" class="mt-1 block w-full" :value="old('nama_wali')" placeholder="Nama orang tua / wali" pattern="^[a-zA-Z\s]+$" title="Nama hanya boleh berisi huruf dan spasi" />
                                <x-input-error class="mt-2" :messages="$errors->get('nama_wali')" />
                            </div>
                            <div>
                                <x-input-label for="no_hp_wali" value="No. HP Wali" />
                                <x-text-input id="no_hp_wali" name="no_hp_wali" type="text" class="mt-1 block w-full" :value="old('no_hp_wali')" placeholder="62812..." pattern="^[0-9]{10,13}$" minlength="10" maxlength="13" title="Nomor HP harus berupa angka dengan panjang 10 hingga 13 digit" />
                                <x-input-error class="mt-2" :messages="$errors->get('no_hp_wali')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="alamat_wali" value="Alamat Asal / Wali" />
                            <textarea id="alamat_wali" name="alamat_wali" rows="2" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Alamat domisili wali">{{ old('alamat_wali') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('alamat_wali')" />
                        </div>

                        <hr class="border-gray-100 my-4">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="kamar_id" value="Kamar" />
                                <select id="kamar_id" name="kamar_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    @foreach ($kamar as $k)
                                        <option value="{{ $k->id }}" @selected(old('kamar_id') == $k->id)>
                                            {{ $k->nomor_kamar }} — Rp {{ number_format($k->harga, 0, ',', '.') }}/bln
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('kamar_id')" />
                            </div>

                            <div>
                                <x-input-label for="tanggal_masuk" value="Tanggal Masuk Kos" />
                                <x-text-input id="tanggal_masuk" name="tanggal_masuk" type="date" class="mt-1 block w-full" :value="old('tanggal_masuk', now()->toDateString())" />
                                <x-input-error class="mt-2" :messages="$errors->get('tanggal_masuk')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="user_id" value="Hubungkan akun login (opsional)" />
                            <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">— Tidak ada —</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}" @selected(old('user_id') == $u->id)>
                                        {{ $u->name }} ({{ $u->email }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Pilih akun dengan peran penghuni yang belum terhubung ke data kos.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                        </div>

                        <div class="flex items-center gap-3">
                            <x-primary-button>Simpan</x-primary-button>
                            <a href="{{ route('penghuni.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
