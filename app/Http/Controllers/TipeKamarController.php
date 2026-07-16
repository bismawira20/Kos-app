<?php

namespace App\Http\Controllers;

use App\Models\TipeKamar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TipeKamarController extends Controller
{
    public function index(): View
    {
        $tipeKamar = TipeKamar::orderBy('nama')->get();
        return view('kamar.tipe.index', compact('tipeKamar'));
    }

    public function edit(TipeKamar $tipeKamar): View
    {
        return view('kamar.tipe.edit', compact('tipeKamar'));
    }

    public function update(Request $request, TipeKamar $tipeKamar): RedirectResponse
    {
        $request->merge(['harga' => str_replace('.', '', $request->harga)]);
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'integer', 'min:0'],
        ]);

        $tipeKamar->update($validated);

        return redirect()->route('tipe-kamar.index')->with('status', 'Tipe kamar berhasil diperbarui.');
    }
}
