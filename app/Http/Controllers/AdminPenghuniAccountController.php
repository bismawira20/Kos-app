<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class AdminPenghuniAccountController extends Controller
{
    public function index(): View
    {
        $users = User::where('role', 'penghuni')->orderBy('name')->get();

        return view('admin.akun_penghuni.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.akun_penghuni.create');
    }

    public function edit(User $user): View
    {
        if ($user->role !== 'penghuni') {
            abort(404);
        }

        return view('admin.akun_penghuni.edit', compact('user'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'penghuni',
        ]);

        return redirect()->route('akun-penghuni.index')->with('status', 'Akun penghuni berhasil dibuat.');
    }

    public function destroy(User $user): RedirectResponse
    {
        // only allow deleting penghuni-role users and only if not linked to Penghuni model
        if ($user->role !== 'penghuni') {
            return redirect()->route('akun-penghuni.index')->with('error', 'Hanya akun penghuni yang dapat dihapus.');
        }

        if ($user->penghuni) {
            return redirect()->route('akun-penghuni.index')->with('error', 'Akun ini terhubung dengan data penghuni. Hapus hubungan terlebih dahulu.');
        }

        $user->delete();

        return redirect()->route('akun-penghuni.index')->with('status', 'Akun penghuni dihapus.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'penghuni') {
            return redirect()->route('akun-penghuni.index')->with('error', 'Operasi tidak diizinkan.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('akun-penghuni.index')->with('status', 'Akun diperbarui.');
    }
}
