<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_ahli_waris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasus_id')->constrained('kasus_waris')->onDelete('cascade');
            $table->string('hubungan');
            $table->integer('jumlah_orang');
            $table->string('bagian_faraidh')->nullable();
            $table->decimal('nominal_faraidh', 15, 2)->nullable();
            $table->integer('usia')->nullable();
            $table->decimal('penghasilan', 15, 2)->nullable();
            $table->decimal('aset', 15, 2)->nullable();
            $table->float('bobot_fuzzy')->nullable();
            $table->decimal('nominal_fuzzy', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_ahli_waris');
    }
};