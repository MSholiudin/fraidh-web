<?php

namespace App\Http\Controllers;

use App\Models\MateriPembelajaran;
use App\Models\EdukasiAhliWaris;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    public function index()
    {
        $materi = MateriPembelajaran::orderBy('urutan')->get()->groupBy('kategori');
        $ahliWaris = EdukasiAhliWaris::orderBy('urutan')->get()->groupBy('kelompok');
        
        return view('materi.index', compact('materi', 'ahliWaris'));
    }
    
    public function show($slug)
    {
        $materi = MateriPembelajaran::where('slug', $slug)->firstOrFail();
        return view('materi.show', compact('materi'));
    }
    
    public function ahliWaris($slug)
    {
        $ahliWaris = EdukasiAhliWaris::where('slug', $slug)->firstOrFail();
        return view('materi.ahli-waris', compact('ahliWaris'));
    }
}