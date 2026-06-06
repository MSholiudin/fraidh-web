<?php

namespace App\Http\Controllers;

use App\Models\KasusWaris;
use App\Models\DetailAhliWaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class RiwayatController extends Controller
{
    // =========================================================
    // INDEX — Daftar semua riwayat user
    // =========================================================

    public function index(Request $request)
    {
        $query = KasusWaris::where('user_id', Auth::id())
            ->withCount(['detailAhliWaris as jumlah_ahli_waris'])
            ->latest();

        if ($request->filled('q')) {
            $query->where('nama_mayit', 'like', '%' . $request->q . '%');
        }

        $riwayat = $query->paginate(10)->withQueryString();

        return view('riwayat.index', compact('riwayat'));
    }

    // =========================================================
    // SHOW — Detail satu riwayat
    // =========================================================

    public function show($id)
    {
        $kasus = KasusWaris::where('user_id', Auth::id())
            ->with('detailAhliWaris')
            ->findOrFail($id);

        // Format hasil faraidh untuk tampilan
        $hasilFaraidh = $kasus->detailAhliWaris
            ->groupBy('hubungan')
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'hubungan' => $first->hubungan,
                    'jumlah'   => $group->sum('jumlah_orang'),
                    'bagian'   => $first->bagian_faraidh,
                    'nominal'  => $group->sum('nominal_faraidh'),
                ];
            })->values();

        // Format hasil islah jika ada
        $hasilIslah = null;
        if ($kasus->hasIslah()) {
            $hasilIslah = $kasus->detailAhliWaris
                ->groupBy('hubungan')
                ->map(function ($group) {
                    $first = $group->first();
                    return [
                        'hubungan'    => $first->hubungan,
                        'jumlah'      => $group->sum('jumlah_orang'),
                        'faraidh'     => $group->sum('nominal_faraidh'),
                        'total_islah' => $group->sum('nominal_fuzzy'),
                        'bobot'       => $group->avg('bobot_fuzzy'),
                    ];
                })->values();
        }

        return view('riwayat.show', compact('kasus', 'hasilFaraidh', 'hasilIslah'));
    }

    public function exportPdf($id)
    {
        $kasus = KasusWaris::where('user_id', Auth::id())
            ->with('detailAhliWaris')
            ->findOrFail($id);

        // Sama persis dengan logic di show()
        $hasilFaraidh = $kasus->detailAhliWaris
            ->groupBy('hubungan')
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'hubungan' => $first->hubungan,
                    'jumlah'   => $group->sum('jumlah_orang'),
                    'bagian'   => $first->bagian_faraidh,
                    'nominal'  => $group->sum('nominal_faraidh'),
                ];
            })->values();

        $hasilIslah = null;
        if ($kasus->hasIslah()) {
            $hasilIslah = $kasus->detailAhliWaris
                ->groupBy('hubungan')
                ->map(function ($group) {
                    $first = $group->first();
                    return [
                        'hubungan'    => $first->hubungan,
                        'jumlah'      => $group->sum('jumlah_orang'),
                        'faraidh'     => $group->sum('nominal_faraidh'),
                        'total_islah' => $group->sum('nominal_fuzzy'),
                        'bobot'       => $group->avg('bobot_fuzzy'),
                    ];
                })->values();
        }

        $pdf = Pdf::loadView('riwayat.pdf', compact('kasus', 'hasilFaraidh', 'hasilIslah'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'dpi'                  => 150,
            ]);

        $filename = 'Waris_' . str_replace(' ', '_', $kasus->nama_mayit) . '.pdf';

        return $pdf->download($filename);
    }

    // =========================================================
    // SIMPAN — Dipanggil dari halaman hasil faraidh atau islah
    // =========================================================

    public function simpan(Request $request)
    {
        if (!Auth::check()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Login terlebih dahulu untuk menyimpan riwayat.'], 401);
            }
            return redirect()->route('login')->with('error', 'Login terlebih dahulu.');
        }

        $faraidhData = Session::get('faraidh_data');
        $hasilFaraidh = Session::get('hasil_faraidh');
        $hasilIslah   = Session::get('hasil_islah_grouped');

        if (!$faraidhData || !$hasilFaraidh) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Data perhitungan tidak ditemukan.'], 404);
            }
            return redirect()->route('kalkulator.index')->with('error', 'Data perhitungan tidak ditemukan.');
        }

        $kasus = KasusWaris::create([
            'user_id'             => Auth::id(),
            'nama_mayit'          => $faraidhData['nama_mayit'],
            'jenis_kelamin_mayit' => 'L', 
            'total_harta'         => $faraidhData['total_harta'] ?? $faraidhData['harta_bersih'],
            'hutang'              => 0,
            'wasiat'              => 0,
            'haji_amanat'         => 0,
            'harta_bersih'        => $faraidhData['harta_bersih'],
        ]);

        $hasilRaw = $faraidhData['hasil_faraidh_raw'] ?? [];

        $grouped = [];
        foreach ($hasilRaw as $item) {
            if ($item['hubungan'] === 'CATATAN') continue;
            
            $key = $item['hubungan'] . '|' . $item['label'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'hubungan' => $item['hubungan'],
                    'bagian'   => $item['label'],
                    'jumlah'   => 0,
                    'nominal'  => 0,
                ];
            }
            $grouped[$key]['jumlah']++;
            $grouped[$key]['nominal'] += $item['nominal'];
        }

        foreach ($grouped as $item) {
            $islahItem = null;
            if ($hasilIslah) {
                $islahItem = collect($hasilIslah)->firstWhere('hubungan', $item['hubungan']);
            }

            DetailAhliWaris::create([
                'kasus_id'        => $kasus->id,
                'hubungan'        => $item['hubungan'],
                'jumlah_orang'    => $item['jumlah'],
                'bagian_faraidh'  => $item['bagian'],
                'nominal_faraidh' => $item['nominal'],
                'usia'            => 0,
                'penghasilan'     => 0,
                'aset'            => 0,
                'bobot_fuzzy'     => $islahItem['bobot'] ?? 0,
                'nominal_fuzzy'   => $islahItem['total_islah'] ?? 0,
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => 'Riwayat perhitungan berhasil disimpan!'
            ]);
        }

        return redirect()->route('riwayat.show', $kasus->id)
            ->with('success', 'Riwayat perhitungan berhasil disimpan!');
    }

    // =========================================================
    // DESTROY — Hapus satu riwayat
    // =========================================================

    public function destroy($id)
    {
        $kasus = KasusWaris::where('user_id', Auth::id())->findOrFail($id);
        $kasus->detailAhliWaris()->delete();
        $kasus->delete();

        return redirect()->route('riwayat.index')
            ->with('success', 'Riwayat berhasil dihapus.');
    }
}