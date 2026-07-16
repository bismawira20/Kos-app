<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KamarController extends Controller
{
    public function index(): View
    {
        $kamar = Kamar::with('tipeKamar')->orderBy('nomor_kamar')->get();

        return view('kamar.index', compact('kamar'));
    }

    public function create(): View
    {
        $tipeKamar = \App\Models\TipeKamar::orderBy('nama')->get();
        return view('kamar.create', compact('tipeKamar'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['harga' => str_replace('.', '', $request->harga)]);
        $validated = $request->validate([
            'nomor_kamar' => ['required', 'string', 'max:50', 'unique:kamars,nomor_kamar'],
            'tipe_kamar_id' => ['required', 'exists:tipe_kamars,id'],
            'harga' => ['required', 'integer', 'min:0'],
            'status' => ['nullable', 'in:kosong,terisi'],
        ], [
            'nomor_kamar.unique' => 'Kamar sudah ada sebelumnya.',
        ]);

        $validated['status'] = $validated['status'] ?? 'kosong';

        Kamar::create($validated);

        return redirect()->route('kamar.index')->with('status', 'Kamar berhasil ditambahkan.');
    }

    public function edit(Kamar $kamar): View
    {
        $tipeKamar = \App\Models\TipeKamar::orderBy('nama')->get();
        return view('kamar.edit', compact('kamar', 'tipeKamar'));
    }

    public function update(Request $request, Kamar $kamar): RedirectResponse
    {
        $request->merge(['harga' => str_replace('.', '', $request->harga)]);
        $validated = $request->validate([
            'nomor_kamar' => ['required', 'string', 'max:50', 'unique:kamars,nomor_kamar,' . $kamar->id],
            'tipe_kamar_id' => ['required', 'exists:tipe_kamars,id'],
            'harga' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:kosong,terisi'],
        ], [
            'nomor_kamar.unique' => 'Kamar sudah ada sebelumnya.',
        ]);

        $kamar->update($validated);

        return redirect()->route('kamar.index')->with('status', 'Data kamar diperbarui.');
    }

    public function destroy(Kamar $kamar): RedirectResponse
    {
        if ($kamar->status === 'terisi') {
            return redirect()->route('kamar.index')->with('error', 'Tidak bisa menghapus kamar yang masih terisi.');
        }

        $kamar->delete();

        return redirect()->route('kamar.index')->with('status', 'Kamar dihapus.');
    }
}
