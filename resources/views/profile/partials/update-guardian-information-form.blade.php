<section>
    <header>
        <h2 class="text-lg font-semibold text-slate-900">
            {{ __('Informasi Wali') }}
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            {{ __('Perbarui data orang tua atau wali untuk keperluan kontak darurat dan administrasi.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.guardian.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <x-input-label for="nama_wali" :value="__('Nama Wali')" />
                <x-text-input id="nama_wali" name="nama_wali" type="text" class="mt-1 block w-full" :value="old('nama_wali', $penghuni->nama_wali)" placeholder="Nama orang tua/wali" />
                <x-input-error class="mt-2" :messages="$errors->get('nama_wali')" />
            </div>

            <div>
                <x-input-label for="no_hp_wali" :value="__('No. HP Wali')" />
                <x-text-input id="no_hp_wali" name="no_hp_wali" type="text" class="mt-1 block w-full" :value="old('no_hp_wali', $penghuni->no_hp_wali)" placeholder="Contoh: 081234567890" />
                <x-input-error class="mt-2" :messages="$errors->get('no_hp_wali')" />
            </div>
        </div>

        <div>
            <x-input-label for="alamat_wali" :value="__('Alamat Lengkap Wali')" />
            <textarea id="alamat_wali" name="alamat_wali" rows="3" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Masukkan alamat tempat tinggal wali secara lengkap">{{ old('alamat_wali', $penghuni->alamat_wali) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('alamat_wali')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'guardian-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-medium"
                >{{ __('Berhasil diperbarui.') }}</p>
            @endif
        </div>
    </form>
</section>
