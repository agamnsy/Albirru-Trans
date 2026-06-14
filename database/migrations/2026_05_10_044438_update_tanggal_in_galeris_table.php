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
        Schema::table('galeris', function (Blueprint $table) {
            // Hapus kolom lama
            $table->dropColumn([
                'tanggal_mulai',
                'tanggal_selesai',
            ]);

            // Tambah kolom baru
            $table->date('tanggal_penyewaan')
                ->after('media');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galeris', function (Blueprint $table) {
            $table->dropColumn('tanggal_penyewaan');

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
        });
    }
};
