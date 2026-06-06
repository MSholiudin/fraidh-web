<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('edukasi_ahli_waris', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ahli_waris');
            $table->string('slug')->unique();
            $table->string('kelompok'); // Utama, Atas, Bawah, Samping
            
            $table->text('deskripsi_aturan');
            $table->text('dalil_arab')->nullable();
            $table->text('dalil_terjemahan')->nullable();
            $table->json('studi_kasus')->nullable();

            $table->integer('parent_id')->nullable();
            $table->string('hijab_oleh')->nullable();
            $table->integer('urutan')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edukasi_ahli_waris');
    }
};