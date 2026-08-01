<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'penghuni' => $request->user()?->loadMissing('penghuni.kamar')->penghuni,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->safe()->only(['name', 'email']));

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        if ($request->user()->penghuni) {
            $request->user()->penghuni->update([
                'nama' => $request->input('name'),
                'no_hp' => $request->input('no_hp'),
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's associated guardian information.
     */
    public function updateGuardian(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if (! $user->penghuni) {
            return back()->with('error', 'Anda tidak memiliki data penghuni yang terhubung.');
        }

        $validated = $request->validate([
            'nama_wali' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'no_hp_wali' => ['nullable', 'string', 'digits_between:10,13'],
            'alamat_wali' => ['nullable', 'string'],
            'hubungan' => ['nullable', 'string', 'in:Ayah,Ibu,Saudara,Suami,Istri,Teman,Lainnya'],
        ], [
            'nama_wali.regex' => 'Nama kontak darurat hanya boleh berisi huruf dan spasi.',
            'no_hp_wali.digits_between' => 'Nomor HP kontak darurat harus berupa angka dengan panjang antara 10 hingga 13 digit.',
        ]);

        $user->penghuni()->update($validated);

        return Redirect::route('profile.edit')->with('status', 'guardian-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
