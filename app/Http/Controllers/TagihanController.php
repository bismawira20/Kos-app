<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TagihanController extends Controller
{
    /**
     * Hitung daftar virtual penghuni yang berada pada kondisi "Menunggu Generate"
     * untuk periode (bulan & tahun) yang dipilih.
     * HANYA berlaku untuk periode saat ini atau periode sebelumnya (bukan masa depan).
     */
    public static function getPendingGenerationForPeriod(int $bulan, int $tahun): \Illuminate\Support\Collection
    {
        $currentYear = (int) now()->year;
        $currentMonth = (int) now()->month;

        // Aturan: Tidak diperbolehkan generate tagihan untuk periode masa depan
        if ($tahun > $currentYear || ($tahun === $currentYear && $bulan > $currentMonth)) {
            return collect();
        }

        $penghunis = Penghuni::with('kamar')->whereNotNull('kamar_id')->get();
        $pendingList = collect();

        foreach ($penghunis as $penghuni) {
            if (!$penghuni->kamar || !$penghuni->tanggal_masuk) {
                continue;
            }

            $startDate = Carbon::parse($penghuni->tanggal_masuk)->startOfDay();
            $duration = (int) ($penghuni->durasi_kontrak ?? 12);
            $step = ($duration === 12) ? 6 : $duration;

            // Cek setiap istilah/termin pembayaran penghuni
            for ($termIdx = 0; $termIdx < 24; $termIdx++) {
                $termStart = $startDate->copy()->addMonths($termIdx * $step);

                if ((int)$termStart->month === $bulan && (int)$termStart->year === $tahun) {
                    // Cek apakah record tagihan SUDAH ADA di database
                    $exists = Tagihan::where('penghuni_id', $penghuni->id)
                        ->where('tahun', $tahun)
                        ->where('bulan', $bulan)
                        ->exists();

                    if (!$exists) {
                        // Gunakan harga kontrak yang terkunci pada data penghuni
                        $rentPrice = (int) ($penghuni->harga_kontrak ?? $penghuni->kamar->harga);
                        $amount = $rentPrice * $step;
                        $due = $termStart->toDateString();

                        $item = new Tagihan([
                            'penghuni_id' => $penghuni->id,
                            'kamar_id' => $penghuni->kamar_id,
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                            'jumlah' => $amount,
                            'jatuh_tempo' => $due,
                            'status' => 'menunggu_generate', // Status tampilan virtual saja
                        ]);
                        $item->setRelation('penghuni', $penghuni);
                        $item->setRelation('kamar', $penghuni->kamar);
                        $item->is_menunggu_generate = true;

                        $pendingList->push($item);
                    }
                    break;
                }
            }
        }

        return $pendingList;
    }

    public function index(Request $request): View
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        $currentYear = (int) now()->year;
        $currentMonth = (int) now()->month;
        $isFuturePeriod = ($tahun > $currentYear) || ($tahun === $currentYear && $bulan > $currentMonth);

        // 1. Ambil seluruh tagihan di database yang belum lunas (Belum Bayar, Menunggu Verifikasi, Ditolak)
        $activeTagihansDB = Tagihan::with(['penghuni', 'kamar'])
            ->whereIn('status', ['belum_bayar', 'menunggu', 'ditolak'])
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->orderBy('kamar_id', 'asc')
            ->get();

        foreach ($activeTagihansDB as $t) {
            $t->is_tunggakan = ($t->tahun < $tahun) || ($t->tahun === $tahun && $t->bulan < $bulan);
            $t->is_menunggu_generate = false;
        }

        // 2. Ambil daftar kondisi "Menunggu Generate" khusus periode bulan & tahun yang dipilih
        $pendingGenerateList = self::getPendingGenerationForPeriod($bulan, $tahun);

        // 3. Gabungkan dan urutkan berdasarkan periode paling lama terlebih dahulu (ascending)
        $tagihans = $pendingGenerateList->concat($activeTagihansDB)->sortBy([
            ['tahun', 'asc'],
            ['bulan', 'asc'],
            ['kamar_id', 'asc'],
        ]);

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $jumlahMenungguGenerate = $pendingGenerateList->count();

        return view('tagihan.index', compact(
            'tagihans',
            'bulan',
            'tahun',
            'namaBulan',
            'jumlahMenungguGenerate',
            'isFuturePeriod'
        ));
    }

    public function create(): View
    {
        $penghuni = Penghuni::with('kamar')->orderBy('nama')->get();

        return view('tagihan.create', compact('penghuni'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'penghuni_id' => ['required', 'exists:penghunis,id'],
            'tahun' => ['required', 'integer', 'min:2020', 'max:2100'],
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'jatuh_tempo' => ['required', 'date'],
        ]);

        $penghuni = Penghuni::with('kamar')->findOrFail($validated['penghuni_id']);

        if (!$penghuni->kamar) {
            return back()->withInput()->with('error', 'Penghuni ini belum memiliki kamar.');
        }

        $rentPrice = (int) ($penghuni->harga_kontrak ?? $penghuni->kamar->harga);

        Tagihan::updateOrCreate(
            [
                'penghuni_id' => $penghuni->id,
                'tahun' => $validated['tahun'],
                'bulan' => $validated['bulan'],
            ],
            [
                'kamar_id' => $penghuni->kamar_id,
                'jumlah' => $rentPrice,
                'jatuh_tempo' => $validated['jatuh_tempo'],
                'status' => 'belum_bayar',
            ]
        );

        return redirect()->route('tagihan.index', ['bulan' => $validated['bulan'], 'tahun' => $validated['tahun']])
            ->with('status', 'Tagihan manual disimpan.');
    }

    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer', 'min:2020', 'max:2100'],
        ]);

        $bulan = (int) $validated['bulan'];
        $tahun = (int) $validated['tahun'];

        $currentYear = (int) now()->year;
        $currentMonth = (int) now()->month;

        // Validasi Sisi Backend: Mencegah generate tagihan periode masa depan
        if ($tahun > $currentYear || ($tahun === $currentYear && $bulan > $currentMonth)) {
            return redirect()->route('tagihan.index', ['bulan' => $bulan, 'tahun' => $tahun])
                ->with('error', 'Tagihan hanya dapat diterbitkan sesuai periode yang sedang berjalan atau periode sebelumnya.');
        }

        // Ambil daftar penghuni pada kondisi "Menunggu Generate" khusus periode yang dipilih
        $pendingItems = self::getPendingGenerationForPeriod($bulan, $tahun);
        $count = 0;

        DB::transaction(function () use ($pendingItems, &$count) {
            foreach ($pendingItems as $item) {
                $exists = Tagihan::where('penghuni_id', $item->penghuni_id)
                    ->where('tahun', $item->tahun)
                    ->where('bulan', $item->bulan)
                    ->exists();

                if (!$exists) {
                    Tagihan::create([
                        'penghuni_id' => $item->penghuni_id,
                        'kamar_id' => $item->kamar_id,
                        'tahun' => $item->tahun,
                        'bulan' => $item->bulan,
                        'jumlah' => $item->jumlah,
                        'jatuh_tempo' => $item->jatuh_tempo,
                        'status' => 'belum_bayar',
                    ]);
                    $count++;
                }
            }
        });

        if ($count > 0) {
            return redirect()->route('tagihan.index', ['bulan' => $bulan, 'tahun' => $tahun])
                ->with('status', "Generate tagihan berhasil. Sebanyak {$count} tagihan berhasil diterbitkan.");
        }

        return redirect()->route('tagihan.index', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('status', "Tidak ada tagihan yang perlu digenerate pada periode yang dipilih.");
    }

    public function destroy(Tagihan $tagihan): RedirectResponse
    {
        $bulan = $tagihan->bulan;
        $tahun = $tagihan->tahun;
        $tagihan->delete();

        return redirect()->route('tagihan.index', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('status', 'Tagihan dihapus.');
    }
}
