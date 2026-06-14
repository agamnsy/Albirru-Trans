<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('aktivitas_perjalanans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('penyewaan_id')
                ->constrained('penyewaans')
                ->cascadeOnDelete();

            $table->foreignId('supir_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('status', [
                'sampai_penjemputan',
                'mulai_perjalanan',
                'sampai_tujuan',
                'perjalanan_pulang',
                'sampai_garasi',
                'selesai',
            ]);
            $table->text('catatan')->nullable();

            $table->json('foto')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aktivitas_perjalanans');
    }
};
