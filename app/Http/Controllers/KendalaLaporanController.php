<?php

namespace App\Http\Controllers;

use App\Models\KendalaLaporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KendalaLaporanController extends Controller
{
    public function index(Request $request): View
    {
        KendalaLaporan::autoResolveOverdue();

        $status = $request->input('status');

        $q = KendalaLaporan::with('penghuni.kamar')->orderByDesc('created_at');
        if ($status) {
            $q->where('status', $status);
        }

        return view('admin.kendala.index', [
            'laporan' => $q->get(),
            'filterStatus' => $status,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        KendalaLaporan::autoResolveOverdue();

        $status = $request->input('status');

        $q = KendalaLaporan::with('penghuni.kamar')->orderByDesc('created_at');
        if ($status) {
            $q->where('status', $status);
        }

        $rows = $q->get();

        $filename = 'kendala-penghuni-'.now()->format('Ymd-His').'.csv';

        return Response::streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['No', 'Nama', 'No. Telepon', 'Kamar', 'Jenis / Uraian Kendala', 'Status', 'Tanggal'], ';');
            foreach ($rows as $i => $k) {
                fputcsv($out, [
                    $i + 1,
                    $k->penghuni?->nama,
                    $k->penghuni?->no_hp,
                    $k->penghuni?->kamar?->nomor_kamar,
                    $k->deskripsi,
                    $this->statusLabel($k->status),
                    $k->created_at?->format('Y-m-d H:i'),
                ], ';');
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function setujuiSemua(): RedirectResponse
    {
        $updated = KendalaLaporan::whereIn('status', ['menunggu', 'proses'])->update([
            'status' => 'selesai',
            'ditinjau_at' => now(),
            'diperbaiki_at' => now(),
            'alasan_tolak' => null,
            'feedback_penghuni' => null,
        ]);

        return back()->with('status', $updated > 0
            ? "Berhasil menyetujui {$updated} laporan kendala."
            : 'Tidak ada laporan yang statusnya diproses.');
    }

    public function kerjakan(int $id): RedirectResponse
    {
        $kendala = KendalaLaporan::findOrFail($id);
        abort_unless(in_array($kendala->status, ['menunggu']), 403);

        $kendala->update([
            'status' => 'proses',
            'ditinjau_at' => now(),
        ]);

        return back()->with('status', 'Laporan kendala mulai dikerjakan.');
    }

    public function diperbaiki(Request $request, int $id): RedirectResponse
    {
        $kendala = KendalaLaporan::findOrFail($id);
        abort_unless(in_array($kendala->status, ['menunggu', 'proses']), 403);

        $kendala->update([
            'status' => 'diperbaiki',
            'catatan_admin' => null,
            'diperbaiki_at' => now(),
            'alasan_tolak' => null,
        ]);

        return back()->with('status', 'Laporan ditandai sudah diperbaiki. Menunggu konfirmasi penghuni.');
    }

    public function setujui(Request $request, int $id): RedirectResponse
    {
        $kendala = KendalaLaporan::findOrFail($id);
        abort_unless(in_array($kendala->status, ['menunggu', 'proses']), 403);

        $validated = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:1000'],
        ]);

        $kendala->update([
            'status' => 'selesai',
            'catatan_admin' => $validated['catatan_admin'] ?? null,
            'ditinjau_at' => now(),
            'diperbaiki_at' => now(),
            'alasan_tolak' => null,
            // Setelah aktivitas selesai, feedback penghuni dihapus.
            'feedback_penghuni' => null,
        ]);

        return back()->with('status', 'Laporan disetujui langsung.');
    }

    public function tolak(Request $request, int $id): RedirectResponse
    {
        $kendala = KendalaLaporan::findOrFail($id);
        abort_unless(in_array($kendala->status, ['menunggu', 'proses']), 403);

        $validated = $request->validate([
            'alasan_tolak' => ['required', 'string', 'max:1000'],
        ]);

        $kendala->update([
            'status' => 'ditolak',
            'alasan_tolak' => $validated['alasan_tolak'],
            'ditinjau_at' => now(),
        ]);

        return back()->with('status', 'Laporan ditolak.');
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'menunggu' => 'Menunggu',
            'proses' => 'Sedang Dikerjakan',
            'diperbaiki' => 'Sudah Diperbaiki',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => $status,
        };
    }
}


