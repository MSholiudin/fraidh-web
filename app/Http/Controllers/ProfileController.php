<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        $request->validate([
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\-]+$/'],
        ], [
            'phone.regex' => 'Format nomor telepon tidak valid. Gunakan format: 0812-3456-7890',
            'phone.max' => 'Nomor telepon maksimal 15 karakter',
        ]);

        $phone = $request->phone;
        if ($phone) {
            // Hapus semua karakter non-digit
            $phone = preg_replace('/\D/', '', $phone);
            
            if (strlen($phone) > 0) {
                if (strlen($phone) <= 4) {
                    $formatted = $phone;
                } elseif (strlen($phone) <= 8) {
                    $formatted = substr($phone, 0, 4) . '-' . substr($phone, 4);
                } else {
                    $formatted = substr($phone, 0, 4) . '-' . 
                                substr($phone, 4, 4) . '-' . 
                                substr($phone, 8, 4);
                }
                $phone = $formatted;
            }
        }

        $user = $request->user();
        
        // Update data user
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $phone,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

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

        return Redirect::to('/')->with('success', 'Akun Anda telah berhasil dihapus.');
    }
}