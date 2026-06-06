<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-black text-gray-900">Buat Password Baru</h1>
        <p class="text-gray-500 text-sm mt-1">Masukkan password baru Anda di bawah ini.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4"
          x-data="{ showNew: false, showConfirm: false }">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email readonly --}}
        <div>
            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Email</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </span>
                <input type="email" name="email" value="{{ old('email', $request->email) }}"
                       required autocomplete="username" readonly
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-500 bg-gray-50 outline-none cursor-not-allowed">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1"/>
        </div>

        {{-- Password Baru --}}
        <div>
            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Password Baru</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </span>
                <input :type="showNew ? 'text' : 'password'" name="password" required
                       placeholder="••••••••" autocomplete="new-password"
                       class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-800 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition bg-gray-50 focus:bg-white">
                <button type="button" @click="showNew = !showNew"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-500 transition-colors">
                    <svg x-show="!showNew" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="showNew" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1"/>
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Konfirmasi Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </span>
                <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required
                       placeholder="••••••••" autocomplete="new-password"
                       class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-800 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition bg-gray-50 focus:bg-white">
                <button type="button" @click="showConfirm = !showConfirm"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-500 transition-colors">
                    <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="showConfirm" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1"/>
        </div>

        <button type="submit"
                class="w-full py-3 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95 uppercase tracking-wider text-sm">
            Reset Password
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 text-xs text-gray-400 hover:text-gray-700 transition-colors font-bold">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke halaman login
            </a>
        </div>
    </form>
</x-guest-layout>