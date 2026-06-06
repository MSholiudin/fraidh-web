<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\KalkulatorController;
use App\Http\Controllers\RiwayatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Materi routes
Route::prefix('materi')->group(function () {
    Route::get('/', [MateriController::class, 'index'])->name('materi.index');
    Route::get('/{slug}', [MateriController::class, 'show'])->name('materi.show');
    Route::get('/ahli-waris/{id}', [MateriController::class, 'ahliWaris'])->name('materi.ahli-waris');
});

// Kalkulator routes
Route::prefix('kalkulator')->group(function () {
    Route::get('/', [KalkulatorController::class, 'index'])->name('kalkulator.index');
    Route::post('/hitung', [KalkulatorController::class, 'hitung'])->name('kalkulator.hitung');
    Route::get('/fuzzy', [KalkulatorController::class, 'fuzzy'])->name('kalkulator.fuzzy');
    Route::post('/hitung-fuzzy', [KalkulatorController::class, 'hitungFuzzy'])->name('kalkulator.hitung-fuzzy');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat/{id}', [RiwayatController::class, 'show'])->name('riwayat.show');
    Route::post('/riwayat/simpan', [RiwayatController::class, 'simpan'])->name('riwayat.simpan');
    Route::delete('/riwayat/{id}', [RiwayatController::class, 'destroy'])->name('riwayat.destroy');
    Route::get('riwayat/{id}/pdf', [RiwayatController::class, 'exportPdf'])->name('riwayat.pdf');
});

require __DIR__.'/auth.php';
