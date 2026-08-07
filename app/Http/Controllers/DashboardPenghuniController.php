<?php

namespace App\Http\Controllers;

use App\Models\KendalaLaporan;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardPenghuniController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $user->load(['penghuni.kamar.tipeKamar']);
        $penghuni = $user->penghuni;

        if (! $penghuni) {
            return view('dashboard_penghuni', [
                'penghuni' => null,
                'tagihanAktif' => null,
                'tagihanTerbaru' => null,
                'laporanTerakhir' => null,
                'stats' => [],
            ]);
        }

        // Tagihan Aktif (Belum Bayar / Menunggu Verifikasi / Ditolak)
        $tagihanAktif = Tagihan::where('penghuni_id', $penghuni->id)
            ->whereIn('status', ['belum_bayar', 'menunggu', 'ditolak'])
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->first();

        // Tagihan Terbaru (untuk ringkasan/arsip)
        $tagihanTerbaru = Tagihan::where('penghuni_id', $penghuni->id)
            ->where('status', '!=', 'menunggu_generate')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->first();

        // Sisa Hari ke Jatuh Tempo untuk Tagihan Aktif
        $hariKeJatuhTempo = null;
        if ($tagihanAktif && $tagihanAktif->jatuh_tempo) {
            $due = $tagihanAktif->jatuh_tempo->copy()->startOfDay();
            $hariKeJatuhTempo = (int) now()->startOfDay()->diffInDays($due, false);
        }

        // Laporan Kendala Terakhir
        $laporanTerakhir = KendalaLaporan::where('penghuni_id', $penghuni->id)
            ->orderByDesc('created_at')
            ->first();

        $stats = [
            'tagihan_aktif' => $tagihanAktif,
            'hari_jatuh_tempo' => $hariKeJatuhTempo,
            'laporan_terakhir' => $laporanTerakhir,
        ];

        return view('dashboard_penghuni', compact('penghuni', 'tagihanAktif', 'tagihanTerbaru', 'laporanTerakhir', 'stats'));
    }
}
