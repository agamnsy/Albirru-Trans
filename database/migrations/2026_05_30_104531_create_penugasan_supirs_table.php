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
        Schema::create('penugasan_supirs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('penyewaan_id')
                ->constrained('penyewaans')
                ->cascadeOnDelete();

            $table->foreignId('supir_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('status', [
                'ditugaskan',
                'diterima',
                'ditolak',
            ])->default('ditugaskan');

            $table->text('alasan_penolakan')->nullable();

            $table->timestamp('assigned_at')->nullable();

            $table->timestamp('responded_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penugasan_supirs');
    }
};
