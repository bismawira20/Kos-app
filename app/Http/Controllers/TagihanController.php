<?php

namespace App\Http\Controllers;

use App\Models\Penghuni;
use App\Models\Tagihan;
use App\Models\Kontrak;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagihanController extends Controller
{
    public function index(Request $request): View
    {
        Kontrak::autoTransition();
        Tagihan::checkTolerance();
        
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
            'batas_toleransi' => ['required', 'date', 'after_or_equal:jatuh_tempo'],
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
                'batas_toleransi' => $validated['batas_toleransi'],
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

        $tagihans = Tagihan::where('status', 'menunggu_generate')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        $count = 0;
        foreach ($tagihans as $t) {
            $t->update(['status' => 'belum_bayar']);
            $count++;
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
