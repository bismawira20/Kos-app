<?php

namespace App\Http\Controllers;

use App\Models\Penghuni;
use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagihanController extends Controller
{
    public function index(Request $request): View
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        // Fetch bills for current selected month/year
        $currentPeriodTagihans = Tagihan::with(['penghuni', 'kamar'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderBy('kamar_id')
            ->get();

        // Fetch past unpaid bills
        $pastUnpaidTagihans = Tagihan::with(['penghuni', 'kamar'])
            ->where(function ($query) use ($bulan, $tahun) {
                $query->where('tahun', '<', $tahun)
                      ->orWhere(function ($q) use ($bulan, $tahun) {
                          $q->where('tahun', $tahun)
                            ->where('bulan', '<', $bulan);
                      });
            })
            ->whereIn('status', ['belum_bayar', 'melewati_batas_toleransi', 'menunggu'])
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderBy('kamar_id')
            ->get();

        foreach ($currentPeriodTagihans as $t) {
            $t->is_tunggakan = false;
        }
        foreach ($pastUnpaidTagihans as $t) {
            $t->is_tunggakan = true;
        }

        $tagihans = $currentPeriodTagihans->concat($pastUnpaidTagihans);

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return view('tagihan.index', compact('tagihans', 'bulan', 'tahun', 'namaBulan'));
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

        Tagihan::updateOrCreate(
            [
                'penghuni_id' => $penghuni->id,
                'tahun' => $validated['tahun'],
                'bulan' => $validated['bulan'],
            ],
            [
                'kamar_id' => $penghuni->kamar_id,
                'jumlah' => $penghuni->kamar->harga,
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

        // Get all active tenants
        $penghunis = Penghuni::with('kamar')->get();
        $count = 0;

        foreach ($penghunis as $penghuni) {
            if (!$penghuni->kamar) {
                continue;
            }

            $startDate = Carbon::parse($penghuni->tanggal_masuk)->startOfDay();
            $duration = (int) $penghuni->durasi_kontrak;

            $shouldHaveBill = false;
            $due = null;
            $amount = 0;

            for ($i = 0; $i < $duration; $i += 6) {
                $monthsInTerm = min(6, $duration - $i);
                $termStart = $startDate->copy()->addMonths($i);
                
                if ($termStart->month === $bulan && $termStart->year === $tahun) {
                    $shouldHaveBill = true;
                    $due = $termStart->toDateString();
                    $amount = $penghuni->kamar->harga * $monthsInTerm;
                    break;
                }
            }

            if ($shouldHaveBill) {
                // Check if a bill already exists in database
                $existingTagihan = Tagihan::where('penghuni_id', $penghuni->id)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->first();

                if (!$existingTagihan) {
                    // Check if there is an orphaned lunas payment for this period
                    $pembayaranLunas = \App\Models\Pembayaran::where('penghuni_id', $penghuni->id)
                        ->whereNull('tagihan_id')
                        ->where('status', 'lunas')
                        ->first();

                    $newTagihan = Tagihan::create([
                        'penghuni_id' => $penghuni->id,
                        'kamar_id' => $penghuni->kamar_id,
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                        'jumlah' => $amount,
                        'jatuh_tempo' => $due,
                        'status' => $pembayaranLunas ? 'lunas' : 'belum_bayar',
                    ]);

                    if ($pembayaranLunas) {
                        $pembayaranLunas->update(['tagihan_id' => $newTagihan->id]);
                    }

                    $count++;
                } else if ($existingTagihan->status === 'menunggu_generate') {
                    $existingTagihan->update(['status' => 'belum_bayar']);
                    $count++;
                }
            }
        }

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
