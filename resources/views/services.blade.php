<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-slate-900">Layanan E-PayKos</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola semua kebutuhan pengelolaan kos dari satu platform terpusat</p>
        </div>
    </x-slot>

    <div class="space-y-12">
        {{-- Services Grid --}}
        <section>
            <div class="mb-8">
                <h3 class="text-2xl font-bold text-slate-900">Fitur Utama</h3>
                <p class="mt-2 text-slate-600">Akses semua layanan pengelolaan kos dengan mudah</p>
            </div>

            @php
                if(Auth::user()->role === 'admin') {
                    $services = [
                        [
                            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
                            'title' => 'Data Penghuni',
                            'description' => 'Kelola data semua penghuni kos, informasi pribadi, dan riwayat huni mereka dengan mudah.',
                            'route' => 'penghuni.index',
                            'color' => 'indigo'
                        ],
                        [
                            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2" ry="2"></rect><path d="M16 11H8v6h8V11z"></path><path d="M8 7h8"></path></svg>',
                            'title' => 'Manajemen Kamar',
                            'description' => 'Pantau ketersediaan kamar, harga, dan informasi detail untuk setiap unit di kos Anda.',
                            'route' => 'kamar.index',
                            'color' => 'emerald'
                        ],
                        [
                            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2" ry="2"></rect><line x1="12" y1="2" x2="12" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line></svg>',
                            'title' => 'Tagihan & Pembayaran',
                            'description' => 'Kelola tagihan penghuni dan verifikasi semua bukti pembayaran dengan sistem terorganisir.',
                            'route' => 'pembayaran.index',
                            'color' => 'violet'
                        ],
                        [
                            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path></svg>',
                            'title' => 'Transaksi Operasional',
                            'description' => 'Catat pemasukan dan pengeluaran operasional harian untuk monitoring keuangan yang akurat.',
                            'route' => 'transaksi-operasional.index',
                            'color' => 'rose'
                        ],
                        [
                            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>',
                            'title' => 'Laporan Kendala',
                            'description' => 'Pantau dan kelola laporan kendala/perbaikan dari penghuni dengan status tracking real-time.',
                            'route' => 'kendala.index',
                            'color' => 'amber'
                        ],
                        [
                            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12s4.48 10 10 10"></path><polyline points="15 8 9 14 6 11"></polyline></svg>',
                            'title' => 'Dashboard Analytics',
                            'description' => 'Lihat statistik lengkap occupancy rate, pendapatan bulanan, dan laporan finansial real-time.',
                            'route' => 'dashboard',
                            'color' => 'cyan'
                        ]
                    ];
                } else {
                    $services = [
                        [
                            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
                            'title' => 'Dashboard Penghuni',
                            'description' => 'Lihat informasi kamar, ringkasan tagihan, dan status pembayaran Anda secara realtime.',
                            'route' => 'dashboard.penghuni',
                            'color' => 'indigo'
                        ],
                        [
                            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2" ry="2"></rect><line x1="12" y1="2" x2="12" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line></svg>',
                            'title' => 'Tagihan Anda',
                            'description' => 'Kelola tagihan bulanan Anda, lihat rincian biaya, dan upload bukti pembayaran dengan mudah.',
                            'route' => 'pembayaran.index',
                            'color' => 'emerald'
                        ],
                        [
                            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="12 1 23 8 23 16 12 23 1 16 1 8 12 1"></polyline><polyline points="12 12 23 7 23 16 12 22 1 16 1 7 12 12"></polyline></svg>',
                            'title' => 'Riwayat Pembayaran',
                            'description' => 'Lihat histori lengkap pembayaran Anda, status verifikasi, dan bukti transaksi yang telah disimpan.',
                            'route' => 'pembayaran.riwayat',
                            'color' => 'violet'
                        ],
                        [
                            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle><path d="M12 9v2"></path><path d="M12 15v2"></path></svg>',
                            'title' => 'Laporkan Kendala',
                            'description' => 'Laporkan masalah atau perbaikan yang diperlukan di kamar Anda, pantau status penyelesaiannya.',
                            'route' => 'kendala.index',
                            'color' => 'rose'
                        ],
                        [
                            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
                            'title' => 'Profil & Akun',
                            'description' => 'Kelola data pribadi, ubah password, dan lihat informasi akun Anda secara aman dan terpercaya.',
                            'route' => 'profile.edit',
                            'color' => 'cyan'
                        ]
                    ];
                }
            @endphp

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($services as $service)
                    @php
                        $colorClasses = [
                            'indigo' => 'from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800',
                            'emerald' => 'from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800',
                            'violet' => 'from-violet-600 to-violet-700 hover:from-violet-700 hover:to-violet-800',
                            'rose' => 'from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800',
                            'amber' => 'from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800',
                            'cyan' => 'from-cyan-600 to-cyan-700 hover:from-cyan-700 hover:to-cyan-800',
                        ];
                        $bgClass = $colorClasses[$service['color']] ?? $colorClasses['indigo'];
                    @endphp
                    <a href="{{ route($service['route']) }}" class="group">
                        <div class="h-full rounded-2xl bg-gradient-to-br {{ $bgClass }} p-[1px] shadow-lg transition-transform duration-300 hover:shadow-xl hover:scale-105">
                            <div class="h-full rounded-[1.9rem] bg-gradient-to-br from-slate-950 to-slate-900 p-6 text-white transition-all duration-300">
                                <div class="mb-4 inline-flex rounded-xl bg-white/10 p-3 backdrop-blur-sm transition-transform duration-300 group-hover:scale-110">
                                    <div class="h-6 w-6 stroke-white">
                                        {!! $service['icon'] !!}
                                    </div>
                                </div>
                                <h3 class="mb-2 text-lg font-bold leading-tight">{{ $service['title'] }}</h3>
                                <p class="text-sm text-slate-300">{{ $service['description'] }}</p>
                                <div class="mt-4 flex items-center text-sm font-semibold text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                    Akses layanan
                                    <svg class="ml-1 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Procedures Section --}}
        <section class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <div class="mb-8">
                <h3 class="text-2xl font-bold text-slate-900">Cara Menggunakan Layanan</h3>
                <p class="mt-2 text-slate-600">Panduan langkah demi langkah untuk memaksimalkan penggunaan platform E-PayKos</p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="flex gap-4">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100">
                        <span class="text-lg font-bold text-indigo-600">1</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-900">Login ke Akun Anda</h4>
                        <p class="mt-1 text-sm text-slate-600">Gunakan kredensial Anda untuk masuk ke dashboard personal</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100">
                        <span class="text-lg font-bold text-emerald-600">2</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-900">Pilih Layanan</h4>
                        <p class="mt-1 text-sm text-slate-600">Klik kartu layanan yang sesuai dengan kebutuhan Anda</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-violet-100">
                        <span class="text-lg font-bold text-violet-600">3</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-900">Isi Data Lengkap</h4>
                        <p class="mt-1 text-sm text-slate-600">Lengkapi semua informasi yang diminta dengan akurat</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-rose-100">
                        <span class="text-lg font-bold text-rose-600">4</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-900">Unggah File (jika diperlukan)</h4>
                        <p class="mt-1 text-sm text-slate-600">Upload dokumen atau bukti pembayaran sesuai kebutuhan</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-amber-100">
                        <span class="text-lg font-bold text-amber-600">5</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-900">Tunggu Verifikasi</h4>
                        <p class="mt-1 text-sm text-slate-600">Tim admin akan memproses dan memverifikasi pengajuan Anda</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-cyan-100">
                        <span class="text-lg font-bold text-cyan-600">6</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-900">Selesai & Konfirmasi</h4>
                        <p class="mt-1 text-sm text-slate-600">Dapatkan notifikasi saat pengajuan Anda telah disetujui</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Quick Contact Section --}}
        <section class="rounded-3xl bg-gradient-to-r from-slate-900 to-slate-800 p-8 text-white shadow-lg">
            <div class="grid gap-8 md:grid-cols-2">
                <div>
                    <h3 class="text-2xl font-bold">Butuh Bantuan?</h3>
                    <p class="mt-3 text-slate-300">Tim support E-PayKos siap membantu Anda menyelesaikan semua kebutuhan pengelolaan kos dengan cepat dan profesional.</p>
                </div>
                <div class="flex flex-col gap-3">
                    <div class="rounded-xl bg-white/10 p-4 backdrop-blur-sm">
                        <p class="text-sm text-slate-300">📧 Email Support</p>
                        <p class="mt-1 font-semibold">support@epaykos.local</p>
                    </div>
                    <div class="rounded-xl bg-white/10 p-4 backdrop-blur-sm">
                        <p class="text-sm text-slate-300">📞 WhatsApp</p>
                        <p class="mt-1 font-semibold">+62 812-3456-7890</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

</x-app-layout>
