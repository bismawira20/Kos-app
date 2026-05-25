<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah kamar</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-6 shadow ring-1 ring-gray-100">
                <form action="{{ route('kamar.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="nomor_kamar" value="Nomor kamar" />
                        <x-text-input id="nomor_kamar" name="nomor_kamar" type="text" class="mt-1 block w-full" :value="old('nomor_kamar')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('nomor_kamar')" />
                    </div>

                    <div>
                        <x-input-label for="harga" value="Harga per bulan (Rp)" />
                        <x-text-input id="harga" name="harga" type="text" class="mt-1 block w-full" 
                            :value="old('harga') ? number_format((int)str_replace('.', '', old('harga')), 0, ',', '.') : ''" 
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                            required />
                        <x-input-error class="mt-2" :messages="$errors->get('harga')" />
                    </div>

                    <div>
                        <x-input-label for="status" value="Status awal" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="kosong" @selected(old('status', 'kosong') === 'kosong')>Kosong</option>
                            <option value="terisi" @selected(old('status') === 'terisi')>Terisi</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('status')" />
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Simpan</x-primary-button>
                        <a href="{{ route('kamar.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
