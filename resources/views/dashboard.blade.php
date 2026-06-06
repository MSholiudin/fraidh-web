@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Selamat datang, {{ Auth::user()->name }}!</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <a href="{{ route('kalkulator.index') }}" 
                           class="block p-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg text-white hover:from-blue-600 hover:to-blue-700 transition">
                            <div class="flex items-center">
                                <div class="mr-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-xl font-bold">Hitung Waris</h4>
                                    <p>Mulai perhitungan baru</p>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('materi.index') }}" 
                           class="block p-6 bg-gradient-to-r from-green-500 to-green-600 rounded-lg text-white hover:from-green-600 hover:to-green-700 transition">
                            <div class="flex items-center">
                                <div class="mr-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-xl font-bold">Pelajari Faraidh</h4>
                                    <p>Baca materi pembelajaran</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    @if(Auth::user()->isAdmin())
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h4 class="font-bold text-yellow-800 mb-2">👨‍💼 Panel Admin</h4>
                        <p class="text-yellow-700">Anda login sebagai administrator.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection