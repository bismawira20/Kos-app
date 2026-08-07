<section>
    <header>
        <h3 class="text-lg font-bold text-slate-900">
            Data Wali / Kontak Darurat
        </h3>
        <p class="mt-1 text-sm text-slate-500">
            Perbarui data orang tua, wali, atau kontak darurat yang dapat dihubungi saat situasi mendesak.
        </p>
    </header>

    <form method="post" action="{{ route('profile.guardian.update') }}" class="mt-6 space-y-6 max-w-2xl">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <x-input-label for="nama_wali" :value="__('Nama Kontak Darurat')" />
                <x-text-input id="nama_wali" name="nama_wali" type="text" class="mt-1 block w-full" :value="old('nama_wali', $penghuni->nama_wali)" placeholder="Nama lengkap wali" />
                <x-input-error class="mt-2" :messages="$errors->get('nama_wali')" />
            </div>

            <div>
                <x-input-label for="no_hp_wali" :value="__('No. HP Kontak Darurat')" />
                <x-text-input id="no_hp_wali" name="no_hp_wali" type="text" class="mt-1 block w-full" :value="old('no_hp_wali', $penghuni->no_hp_wali)" placeholder="Contoh: 081234567890" />
                <x-input-error class="mt-2" :messages="$errors->get('no_hp_wali')" />
            </div>

            <div class="sm:col-span-2">
                <x-input-label for="hubungan" :value="__('Hubungan / Relasi')" />
                <select id="hubungan" name="hubungan" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Pilih Hubungan --</option>
                    @foreach (['Ayah', 'Ibu', 'Saudara', 'Suami', 'Istri', 'Teman', 'Lainnya'] as $rel)
                        <option value="{{ $rel }}" @selected(old('hubungan', $penghuni->hubungan) === $rel)>{{ $rel }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('hubungan')" />
            </div>

            <div class="sm:col-span-2">
                <x-input-label for="alamat_wali" :value="__('Alamat Lengkap Kontak Darurat')" />
                <textarea id="alamat_wali" name="alamat_wali" rows="3" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Masukkan alamat tempat tinggal kontak darurat secara lengkap">{{ old('alamat_wali', $penghuni->alamat_wali) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('alamat_wali')" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 font-semibold text-white shadow-sm hover:bg-indigo-700 active:scale-95 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Simpan</span>
            </button>

            @if (session('status') === 'guardian-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-semibold text-emerald-600"
                >Data wali berhasil disimpan.</span>
            @endif
        </div>
    </form>
</section>
