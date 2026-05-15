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

        $tagihans = Tagihan::with(['penghuni', 'kamar'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderBy('kamar_id')
            ->get();

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
            'tahun' => ['required', 'integer', 'min:2020', 'max:2100'],
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'hari_jatuh_tempo' => ['required', 'integer', 'min:1', 'max:28'],
        ]);

        $tahun = $validated['tahun'];
        $bulan = $validated['bulan'];
        $due = Carbon::create($tahun, $bulan, $validated['hari_jatuh_tempo'])->toDateString();

        $count = 0;
        foreach (Penghuni::with('kamar')->get() as $p) {
            if (! $p->kamar) {
                continue;
            }
            Tagihan::firstOrCreate(
                [
                    'penghuni_id' => $p->id,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                ],
                [
                    'kamar_id' => $p->kamar_id,
                    'jumlah' => $p->kamar->harga,
                    'jatuh_tempo' => $due,
                    'status' => 'belum_bayar',
                ]
            );
            $count++;
        }

        return redirect()->route('tagihan.index', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('status', "Generate selesai. {$count} penghuni diproses (tagihan baru dibuat jika belum ada).");
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
