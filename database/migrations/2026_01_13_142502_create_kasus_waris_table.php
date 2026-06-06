<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kasus_waris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('nama_mayit');
            $table->enum('jenis_kelamin_mayit', ['L', 'P']);
            $table->decimal('total_harta', 15, 2);
            $table->decimal('hutang', 15, 2)->default(0);
            $table->decimal('wasiat', 15, 2)->default(0);
            $table->decimal('haji_amanat', 15, 2)->default(0);
            $table->decimal('harta_bersih', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kasus_waris');
    }
};