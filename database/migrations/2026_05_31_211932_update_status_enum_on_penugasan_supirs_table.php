<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE penugasan_supirs 
            MODIFY status ENUM('ditugaskan', 'diterima', 'ditolak', 'dibatalkan') 
            DEFAULT 'ditugaskan'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE penugasan_supirs 
            MODIFY status ENUM('ditugaskan', 'diterima', 'ditolak') 
            DEFAULT 'ditugaskan'
        ");
    }
};
