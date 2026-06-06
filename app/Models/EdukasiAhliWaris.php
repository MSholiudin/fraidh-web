<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EdukasiAhliWaris extends Model
{
    use HasFactory;

    protected $table = 'edukasi_ahli_waris';

    protected $fillable = [
        'nama_ahli_waris',
        'slug',
        'kelompok',
        'deskripsi_aturan',
        'dalil_arab',
        'dalil_terjemahan',
        'studi_kasus',
        'hijab_oleh',
        'urutan',
    ];

    protected $casts = [
        'studi_kasus' => 'array',
    ];

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopeUrut($query)
    {
        return $query->orderBy('urutan');
    }

    public function scopePerKelompok($query)
    {
        return $query->orderBy('kelompok')->orderBy('urutan');
    }

    // =========================================================
    // STATIC HELPERS
    // =========================================================

    /**
     * Ambil data ahli waris berdasarkan hubungan.
     * Nama hubungan di sistem sudah huruf kecil semua.
     */
    public static function cariByHubungan(string $hubungan): ?self
    {
        return static::where('nama_ahli_waris', strtolower(trim($hubungan)))->first();
    }

    /**
     * Ambil deskripsi aturan berdasarkan hubungan.
     * Dipakai di hasil faraidh untuk penjelasan singkat.
     */
    public static function getPenjelasan(string $hubungan): string
    {
        $data = static::cariByHubungan($hubungan);

        return $data?->deskripsi_aturan
            ?? 'Mendapatkan bagian sesuai kedudukan ahli waris dalam syariat Islam.';
    }

    /**
     * Ambil semua data dikelompokkan per kelompok.
     * Dipakai di halaman materi.
     */
    public static function perKelompok(): array
    {
        return static::urut()
            ->get()
            ->groupBy('kelompok')
            ->toArray();
    }
}