<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailAhliWaris extends Model
{
    use HasFactory;

    protected $table = 'detail_ahli_waris';

    protected $fillable = [
        'kasus_id',
        'hubungan',
        'jumlah_orang',
        'bagian_faraidh',
        'nominal_faraidh',
        'usia',
        'penghasilan',
        'aset',
        'bobot_fuzzy',
        'nominal_fuzzy',
    ];

    protected $casts = [
        'nominal_faraidh' => 'decimal:2',
        'penghasilan'     => 'decimal:2',
        'aset'            => 'decimal:2',
        'bobot_fuzzy'     => 'float',
        'nominal_fuzzy'   => 'decimal:2',
    ];

    // =========================================================
    // RELATIONS
    // =========================================================

    public function kasusWaris()
    {
        return $this->belongsTo(KasusWaris::class, 'kasus_id');
    }
}