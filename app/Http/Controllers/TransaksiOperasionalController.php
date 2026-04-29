<?php

namespace App\Http\Controllers;

use App\Models\TransaksiOperasional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransaksiOperasionalController extends Controller
{
    public function index(): View
    {
        $transaksi = TransaksiOperasional::orderByDesc('tanggal')->orderByDesc('created_at')->get();

        $pemasukan = (int) $transaksi->where('jenis', 'pemasukan')->sum('jumlah');
        $pengeluaran = (int) $transaksi->where('jenis', 'pengeluaran')->sum('jumlah');

        return view('admin.transaksi_operasional.index', compact('transaksi', 'pemasukan', 'pengeluaran'));
    }

    public function create(): View
    {
        return view('admin.transaksi_operasional.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', 'in:pemasukan,pengeluaran'],
            'kategori' => ['required', 'string', 'max:100'],
            'deskripsi' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'sumber' => ['nullable', 'string', 'max:100'],
        ]);

        TransaksiOperasional::create($validated);

        return redirect()->route('transaksi-operasional.index')->with('status', 'Transaksi operasional disimpan.');
    }

    public function destroy(TransaksiOperasional $transaksiOperasional): RedirectResponse
    {
        $transaksiOperasional->delete();

        return redirect()->route('transaksi-operasional.index')->with('status', 'Transaksi dihapus.');
    }
}
