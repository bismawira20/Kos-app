<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\KendalaLaporan;
use App\Models\Pembayaran;
use App\Models\Penghuni;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $bulan = (int) ($request->bulan ?? date('n'));
        $tahun = (int) ($request->tahun ?? date('Y'));

        $totalKamar = Kamar::count();
        $kamarKosong = Kamar::where('status', 'kosong')->count();
        $totalPenghuni = Penghuni::count();
        $kamarTerisi = Kamar::where('status', 'terisi')->count();

        // 1. Laporan Kendala Baru (hanya status 'menunggu')
        $jumlahKendalaBaru = KendalaLaporan::where('status', 'menunggu')->count();

        // 2. Pembayaran menunggu verifikasi
        $menungguVerifikasi = Pembayaran::where('status', 'menunggu')->count();

        // 3. Pemasukan bulan ini
        $pemasukanBulanIni = (int) Pembayaran::where('status', 'lunas')
            ->whereYear('tanggal_bayar', $tahun)
            ->whereMonth('tanggal_bayar', $bulan)
            ->sum('jumlah');

        // 4. Tagihan Belum Lunas (Seluruh tagihan aktif yang belum lunas)
        $tagihanBelumLunas = Tagihan::whereIn('status', ['belum_bayar', 'menunggu', 'ditolak'])->count();

        // 5. Menunggu Generate untuk periode bulan/tahun ini
        $jumlahMenungguGenerate = TagihanController::getPendingGenerationForPeriod($bulan, $tahun)->count();

        // 6. Tingkat Okupansi
        $occupancy = $totalKamar > 0 ? round(($kamarTerisi / $totalKamar) * 100) : 0;

        // Grafik Pembayaran Bulanan (Januari - Desember) untuk tahun yang dipilih
        $monthlyPayments = Pembayaran::selectRaw('MONTH(COALESCE(tanggal_bayar, created_at)) as bulan_num, SUM(jumlah) as total_nominal')
            ->whereYear(DB::raw('COALESCE(tanggal_bayar, created_at)'), $tahun)
            ->where('status', 'lunas')
            ->groupBy('bulan_num')
            ->pluck('total_nominal', 'bulan_num');

        $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartValues = [];
        $chartBackgrounds = [];
        $chartBorders = [];

        for ($m = 1; $m <= 12; $m++) {
            $chartValues[] = (int) ($monthlyPayments[$m] ?? 0);
            $chartBackgrounds[] = 'rgba(79, 70, 229, 0.85)';
            $chartBorders[] = 'rgb(67, 56, 202)';
        }

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return view('dashboard', compact(
            'totalKamar',
            'kamarKosong',
            'totalPenghuni',
            'kamarTerisi',
            'jumlahKendalaBaru',
            'menungguVerifikasi',
            'pemasukanBulanIni',
            'tagihanBelumLunas',
            'jumlahMenungguGenerate',
            'occupancy',
            'chartLabels',
            'chartValues',
            'chartBackgrounds',
            'chartBorders',
            'bulan',
            'tahun',
            'namaBulan'
        ));
    }
}
