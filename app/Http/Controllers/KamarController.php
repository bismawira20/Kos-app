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
        $kamar = Kamar::orderBy('nomor_kamar')->get();

        return view('kamar.index', compact('kamar'));
    }

    public function create(): View
    {
        return view('kamar.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['harga' => str_replace('.', '', $request->harga)]);
        $validated = $request->validate([
            'nomor_kamar' => ['required', 'string', 'max:50'],
            'harga' => ['required', 'integer', 'min:0'],
            'lantai' => ['nullable', 'integer', 'min:0', 'max:127'],
            'kapasitas' => ['nullable', 'integer', 'min:1', 'max:20'],
            'fasilitas' => ['nullable', 'string', 'max:2000'],
            'status' => ['nullable', 'in:kosong,terisi'],
        ]);

        $validated['status'] = $validated['status'] ?? 'kosong';

        Kamar::create($validated);

        return redirect()->route('kamar.index')->with('status', 'Kamar berhasil ditambahkan.');
    }

    public function edit(Kamar $kamar): View
    {
        return view('kamar.edit', compact('kamar'));
    }

    public function update(Request $request, Kamar $kamar): RedirectResponse
    {
        $request->merge(['harga' => str_replace('.', '', $request->harga)]);
        $validated = $request->validate([
            'nomor_kamar' => ['required', 'string', 'max:50'],
            'harga' => ['required', 'integer', 'min:0'],
            'lantai' => ['nullable', 'integer', 'min:0', 'max:127'],
            'kapasitas' => ['nullable', 'integer', 'min:1', 'max:20'],
            'fasilitas' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:kosong,terisi'],
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
