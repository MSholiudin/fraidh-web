<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\-]+$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ], [
                'phone.regex' => 'Format nomor telepon tidak valid. Gunakan format: 0812-3456-7890',
                'phone.max' => 'Nomor telepon maksimal 15 karakter',
            ]);

            // Format phone number jika ada
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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $phone,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME)->with('success', 'Registrasi berhasil! Selamat datang di WEWARIS');
    }
}
