@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Page Header --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <header class="mb-0">
                    <h2 class="text-2xl font-bold text-gray-800">Edit Profile</h2>
                    <p class="text-gray-600 mt-1">Kelola informasi profil Anda untuk pengalaman yang lebih personal di WEWARIS</p>
                </header>
            </div>
        </div>

        {{-- Informasi Profil --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <header class="mb-6 pb-4 border-b border-gray-200">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Akun</p>
                    <h3 class="text-lg font-bold text-gray-800">Informasi Profil</h3>
                    <p class="text-sm text-gray-600 mt-1">Perbarui informasi profil dan alamat email akun Anda.</p>
                </header>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('patch')

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input id="name" name="name" type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                            value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input id="email" name="email" type="email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                            value="{{ old('email', $user->email) }}" required autocomplete="email">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-sm" />

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm text-yellow-700">
                                    {{ __('Email Anda belum terverifikasi.') }}
                                    <button form="send-verification" class="underline text-blue-600 hover:text-blue-800 font-medium">
                                        {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                                    </button>
                                </p>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input id="phone" name="phone" type="tel"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                            value="{{ old('phone', $user->phone) }}" autocomplete="tel" placeholder="0812-3456-7890">
                        <x-input-error :messages="$errors->get('phone')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-2">
                        @if (session('status') === 'profile-updated')
                            <p class="text-sm text-green-600 font-medium"
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 2000)">
                                {{ __('Profile berhasil diperbarui.') }}
                            </p>
                        @endif

                        <button type="submit"
                            class="px-8 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-sm hover:shadow-blue-200 transition-all active:scale-95">
                            {{ __('Simpan Perubahan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Ubah Password --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <header class="mb-6 pb-4 border-b border-gray-200">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Keamanan</p>
                    <h3 class="text-lg font-bold text-gray-800">Ubah Password</h3>
                    <p class="text-sm text-gray-600 mt-1">Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.</p>
                </header>

                <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('put')

                    <div x-data="{ show: false }">
                        <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-1">
                            Password Saat Ini
                        </label>
                        <div class="relative">
                            <input id="update_password_current_password" name="current_password"
                                :type="show ? 'text' : 'password'"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition pr-10">
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition-colors">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <div x-data="{ show: false }">
                        <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-1">
                            Password Baru
                        </label>
                        <div class="relative">
                            <input id="update_password_password" name="password"
                                :type="show ? 'text' : 'password'"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition pr-10">
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition-colors">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <div x-data="{ show: false }">
                        <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                            Konfirmasi Password Baru
                        </label>
                        <div class="relative">
                            <input id="update_password_password_confirmation" name="password_confirmation"
                                :type="show ? 'text' : 'password'"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition pr-10">
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition-colors">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        @if (session('status') === 'password-updated')
                            <p class="text-sm text-green-600 font-medium mr-4"
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 2000)">
                                {{ __('Password berhasil diubah.') }}
                            </p>
                        @endif

                        <button type="submit"
                            class="px-8 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-sm hover:shadow-blue-200 transition-all active:scale-95">
                            {{ __('Ubah Password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Hapus Akun --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white">
                <header class="mb-6 pb-4 border-b border-gray-200">
                    <p class="text-xs font-semibold text-red-400 uppercase tracking-widest mb-1">Zona Berbahaya</p>
                    <h3 class="text-lg font-bold text-gray-800">Hapus Akun</h3>
                </header>

                <div class="p-4 bg-red-50 border border-red-200 rounded-lg mb-6">
                    <p class="text-sm text-red-700">
                        Tindakan ini <strong>tidak dapat dibatalkan</strong>. Semua data Anda akan dihapus secara permanen dari sistem kami.
                    </p>
                </div>

                <button x-data x-on:click="$dispatch('open-modal', 'confirm-user-deletion')"
                    class="px-8 py-2.5 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 shadow-sm hover:shadow-red-200 transition-all active:scale-95">
                    {{ __('Hapus Akun Sekarang') }}
                </button>
            </div>
        </div>

    </div>
</div>
@endsection