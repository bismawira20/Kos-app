<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardPenghuniController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\TipeKamarController;
use App\Http\Controllers\KendalaLaporanController;
use App\Http\Controllers\KendalaPenghuniController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PenghuniController;
use App\Http\Controllers\PenghuniRiwayatController;
use App\Http\Controllers\PenghuniTagihanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagihanController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/redirect', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return Auth::user()->role === 'admin'
        ? redirect()->route('dashboard')
        : redirect()->route('dashboard.penghuni');
})->middleware('auth')->name('redirect');


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('kamar', KamarController::class)->except(['show']);
    Route::resource('tipe-kamar', TipeKamarController::class)->only(['index', 'edit', 'update']);
    Route::resource('penghuni', PenghuniController::class)->except(['show']);
    Route::post('/penghuni/{penghuni}/perpanjang', [PenghuniController::class, 'perpanjang'])->name('penghuni.perpanjang');
    Route::post('/penghuni/{penghuni}/reset-password', [PenghuniController::class, 'resetPassword'])->name('penghuni.reset-password');
    Route::resource('tagihan', TagihanController::class)->only(['index']);
    Route::post('/tagihan/generate', [TagihanController::class, 'generate'])->name('tagihan.generate');

    Route::resource('pembayaran', PembayaranController::class)->only(['index', 'create', 'store']);
    Route::post('/pembayaran/{id}/acc', [PembayaranController::class, 'acc'])->name('pembayaran.acc');
    Route::post('/pembayaran/{id}/tolak', [PembayaranController::class, 'tolak'])->name('pembayaran.tolak');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/csv', [LaporanController::class, 'exportCsv'])->name('laporan.export');
    Route::get('/laporan/print', [LaporanController::class, 'print'])->name('laporan.print');


    Route::get('/laporan-kendala', [KendalaLaporanController::class, 'index'])->name('kendala.index');
    Route::get('/laporan-kendala/export', [KendalaLaporanController::class, 'export'])->name('kendala.export');
    Route::post('/laporan-kendala/setujui-semua', [KendalaLaporanController::class, 'setujuiSemua'])->name('kendala.setujui-semua');
    Route::post('/laporan-kendala/{id}/setujui', [KendalaLaporanController::class, 'setujui'])->name('kendala.setujui');
    Route::post('/laporan-kendala/{id}/tolak', [KendalaLaporanController::class, 'tolak'])->name('kendala.tolak');
    Route::post('/laporan-kendala/{id}/kerjakan', [KendalaLaporanController::class, 'kerjakan'])->name('kendala.kerjakan');
    Route::post('/laporan-kendala/{id}/diperbaiki', [KendalaLaporanController::class, 'diperbaiki'])->name('kendala.diperbaiki');
});

Route::middleware(['auth', 'role:penghuni'])->group(function () {
    Route::get('/dashboard-penghuni', [DashboardPenghuniController::class, 'index'])->name('dashboard.penghuni');

    Route::get('/penghuni/tagihan', [PenghuniTagihanController::class, 'index'])->name('penghuni.tagihan.index');
    Route::get('/penghuni/tagihan/{tagihan}/bayar', [PenghuniTagihanController::class, 'bayar'])->name('penghuni.tagihan.bayar');
    Route::post('/penghuni/tagihan/{tagihan}/bayar', [PenghuniTagihanController::class, 'kirim'])->name('penghuni.tagihan.kirim');
    Route::get('/penghuni/tagihan/{tagihan}/midtrans', [\App\Http\Controllers\MidtransController::class, 'pay'])->name('penghuni.tagihan.midtrans');
    Route::get('/penghuni/tagihan/{tagihan}/batal-midtrans', [\App\Http\Controllers\MidtransController::class, 'cancel'])->name('penghuni.tagihan.batal-midtrans');
    Route::get('/penghuni/tagihan/{tagihan}/sukses-midtrans', [\App\Http\Controllers\MidtransController::class, 'sukses'])->name('penghuni.tagihan.sukses-midtrans');
    Route::get('/penghuni/tagihan/{tagihan}/invoice', [PenghuniTagihanController::class, 'downloadInvoice'])->name('penghuni.tagihan.invoice');

    Route::get('/penghuni/riwayat', [PenghuniRiwayatController::class, 'index'])->name('penghuni.riwayat');

    Route::get('/penghuni/kendala', [KendalaPenghuniController::class, 'index'])->name('penghuni.kendala.index');
    Route::get('/penghuni/kendala/buat', [KendalaPenghuniController::class, 'create'])->name('penghuni.kendala.create');
    Route::post('/penghuni/kendala', [KendalaPenghuniController::class, 'store'])->name('penghuni.kendala.store');
    Route::get('/penghuni/kendala/{kendala}/edit', [KendalaPenghuniController::class, 'edit'])->name('penghuni.kendala.edit');
    Route::put('/penghuni/kendala/{kendala}', [KendalaPenghuniController::class, 'update'])->name('penghuni.kendala.update');
    Route::delete('/penghuni/kendala/{kendala}', [KendalaPenghuniController::class, 'destroy'])->name('penghuni.kendala.destroy');
    Route::post('/penghuni/kendala/{id}/konfirmasi', [KendalaPenghuniController::class, 'konfirmasiSelesai'])->name('penghuni.kendala.konfirmasi');
    Route::post('/penghuni/kendala/{id}/lapor-ulang', [KendalaPenghuniController::class, 'laporkanUlang'])->name('penghuni.kendala.lapor-ulang');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/guardian', [ProfileController::class, 'updateGuardian'])->name('profile.guardian.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
