<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-black text-gray-900">Lupa Password?</h1>
        <p class="text-gray-500 text-sm mt-1">
            Masukkan email Anda dan kami akan mengirimkan tautan untuk mereset password.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl">
            <p class="text-sm font-bold text-green-700">{{ session('status') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Email</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="nama@email.com"
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-800 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition bg-gray-50 focus:bg-white">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1"/>
        </div>

        <button type="submit"
                class="w-full py-3 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95 uppercase tracking-wider text-sm">
            Kirim Link Reset Password
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