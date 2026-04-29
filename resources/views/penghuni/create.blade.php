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
                            <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('nama')" />
                        </div>

                        <div>
                            <x-input-label for="no_hp" value="No. HP (WhatsApp)" />
                            <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full" :value="old('no_hp')" required placeholder="62812..." />
                            <x-input-error class="mt-2" :messages="$errors->get('no_hp')" />
                        </div>

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
