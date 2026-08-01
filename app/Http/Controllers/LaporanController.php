<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Models\Penghuni;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $dari = Carbon::parse($request->get('dari', now()->startOfMonth()->toDateString()))->startOfDay();
        $sampai = Carbon::parse($request->get('sampai', now()->endOfMonth()->toDateString()))->endOfDay();
        $kamarId = $request->get('kamar_id');

        $q = Pembayaran::with(['penghuni.kamar', 'tagihan'])
            ->where('status', 'lunas')
            ->whereNotNull('tanggal_bayar')
            ->whereBetween('tanggal_bayar', [$dari->toDateString(), $sampai->toDateString()]);

        if ($kamarId) {
            $q->whereHas('penghuni', fn ($p) => $p->where('kamar_id', $kamarId));
        }

        $rows = $q->orderBy('tanggal_bayar')->get();
        $total = $rows->sum('jumlah');

        $chartLabels = [];
        $chartValues = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $chartLabels[] = $m->translatedFormat('M Y');
            $chartValues[] = (int) Pembayaran::where('status', 'lunas')
                ->whereYear('tanggal_bayar', $m->year)
                ->whereMonth('tanggal_bayar', $m->month)
                ->sum('jumlah');
        }

        $kamars = Kamar::orderBy('nomor_kamar')->get();

        $penghuniAktif = Penghuni::count();
        $kamarTerisi = Kamar::where('status', 'terisi')->count();
        $totalKamar = Kamar::count();

        return view('laporan.index', compact(
            'rows',
            'total',
            'dari',
            'sampai',
            'kamarId',
            'kamars',
            'chartLabels',
            'chartValues',
            'penghuniAktif',
            'kamarTerisi',
            'totalKamar'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $dari = Carbon::parse($request->get('dari', now()->startOfMonth()->toDateString()))->startOfDay();
        $sampai = Carbon::parse($request->get('sampai', now()->endOfMonth()->toDateString()))->endOfDay();
        $kamarId = $request->get('kamar_id');
        $tipe = $request->get('tipe', 'lunas');

        if ($tipe === 'belum_bayar') {
            $q = \App\Models\Tagihan::with(['penghuni.kamar'])
                ->where('status', '!=', 'lunas')
                ->whereBetween('jatuh_tempo', [$dari->toDateString(), $sampai->toDateString()]);

            if ($kamarId) {
                $q->where('kamar_id', $kamarId);
            }

            $rows = $q->orderBy('jatuh_tempo')->get();
            $filename = 'laporan-belum-bayar-'.now()->format('Ymd-His').'.csv';

            return response()->streamDownload(function () use ($rows) {
                $out = fopen('php://output', 'w');
                fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($out, ['No', 'Periode Tagihan', 'Penghuni', 'Kamar', 'Jatuh Tempo', 'Jumlah', 'Status'], ';');
                foreach ($rows as $i => $r) {
                    fputcsv($out, [
                        $i + 1,
                        $r->labelPeriode(),
                        $r->penghuni?->nama ?? '—',
                        $r->penghuni?->kamar?->nomor_kamar ?? '—',
                        $r->jatuh_tempo ? $r->jatuh_tempo->format('Y-m-d') : '—',
                        $r->jumlah,
                        strtoupper(str_replace('_', ' ', $r->status)),
                    ], ';');
                }
                fclose($out);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        } else {
            $q = Pembayaran::with(['penghuni.kamar', 'tagihan'])
                ->where('status', 'lunas')
                ->whereNotNull('tanggal_bayar')
                ->whereBetween('tanggal_bayar', [$dari->toDateString(), $sampai->toDateString()]);

            if ($kamarId) {
                $q->whereHas('penghuni', fn ($p) => $p->where('kamar_id', $kamarId));
            }

            $rows = $q->orderBy('tanggal_bayar')->get();
            $filename = 'laporan-pembayaran-lunas-'.now()->format('Ymd-His').'.csv';

            return response()->streamDownload(function () use ($rows) {
                $out = fopen('php://output', 'w');
                fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($out, ['No', 'Tanggal Bayar', 'Penghuni', 'Kamar', 'Periode Tagihan', 'Jumlah', 'Status'], ';');
                foreach ($rows as $i => $r) {
                    $periode = $r->tagihan ? $r->tagihan->labelPeriode() : '-';
                    fputcsv($out, [
                        $i + 1,
                        $r->tanggal_bayar,
                        $r->penghuni?->nama ?? '—',
                        $r->penghuni?->kamar?->nomor_kamar ?? '—',
                        $periode,
                        $r->jumlah,
                        $r->status,
                    ], ';');
                }
                fclose($out);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }
    }

    public function print(Request $request): View
    {
        $dari = Carbon::parse($request->get('dari', now()->startOfMonth()->toDateString()))->startOfDay();
        $sampai = Carbon::parse($request->get('sampai', now()->endOfMonth()->toDateString()))->endOfDay();
        $kamarId = $request->get('kamar_id');

        $q = Pembayaran::with(['penghuni.kamar', 'tagihan'])
            ->where('status', 'lunas')
            ->whereNotNull('tanggal_bayar')
            ->whereBetween('tanggal_bayar', [$dari->toDateString(), $sampai->toDateString()]);

        if ($kamarId) {
            $q->whereHas('penghuni', fn ($p) => $p->where('kamar_id', $kamarId));
        }

        $rows = $q->orderBy('tanggal_bayar')->get();
        $total = $rows->sum('jumlah');

        return view('laporan.print', compact('rows', 'total', 'dari', 'sampai'));
    }
}
