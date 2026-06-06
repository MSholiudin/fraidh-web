<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasusWaris extends Model
{
    use HasFactory;

    protected $table = 'kasus_waris';

    protected $fillable = [
        'user_id',
        'nama_mayit',
        'jenis_kelamin_mayit',
        'total_harta',
        'hutang',
        'wasiat',
        'haji_amanat',
        'harta_bersih',
    ];

    protected $casts = [
        'total_harta'  => 'decimal:2',
        'hutang'       => 'decimal:2',
        'wasiat'       => 'decimal:2',
        'haji_amanat'  => 'decimal:2',
        'harta_bersih' => 'decimal:2',
    ];

    // =========================================================
    // RELATIONS
    // =========================================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailAhliWaris()
    {
        return $this->hasMany(DetailAhliWaris::class, 'kasus_id');
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Apakah kasus ini sudah memiliki hasil islah?
     */
    public function hasIslah(): bool
    {
        return $this->detailAhliWaris()
            ->whereNotNull('nominal_fuzzy')
            ->where('nominal_fuzzy', '>', 0)
            ->exists();
    }

    /**
     * Jumlah ahli waris aktif (yang mendapat bagian)
     */
    public function jumlahAhliWaris(): int
    {
        return $this->detailAhliWaris()
            ->where('nominal_faraidh', '>', 0)
            ->count();
    }
}