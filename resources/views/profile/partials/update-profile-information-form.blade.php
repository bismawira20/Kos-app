<section>
    <header>
        <h3 class="text-lg font-bold text-slate-900">
            Data Pribadi
        </h3>
        <p class="mt-1 text-sm text-slate-500">
            Perbarui informasi nama lengkap, alamat email, dan nomor telepon Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6 max-w-2xl">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" pattern="^[a-zA-Z\s]+$" title="Nama hanya boleh berisi huruf dan spasi" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" pattern=".*@gmail\.com" title="Harus menggunakan domain @gmail.com" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        @if ($user->penghuni)
            <div>
                <x-input-label for="no_hp" :value="__('No. Telepon / HP')" />
                <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full" :value="old('no_hp', $user->penghuni->no_hp)" required placeholder="Contoh: 081234567890" pattern="^[0-9]{10,13}$" minlength="10" maxlength="13" title="Nomor HP harus berupa angka dengan panjang 10 hingga 13 digit" />
                <x-input-error class="mt-2" :messages="$errors->get('no_hp')" />
            </div>
        @endif

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-800">
                    {{ __('Alamat email Anda belum terverifikasi.') }}

                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                    </p>
                @endif
            </div>
        @endif

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 font-semibold text-white shadow-sm hover:bg-indigo-700 active:scale-95 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Simpan</span>
            </button>

            @if (session('status') === 'profile-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-semibold text-emerald-600"
                >Data pribadi berhasil disimpan.</span>
            @endif
        </div>
    </form>
</section>
