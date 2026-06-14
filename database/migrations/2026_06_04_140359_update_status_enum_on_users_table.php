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
        DB::statement("
            ALTER TABLE users 
            MODIFY status ENUM('aktif', 'bertugas', 'nonaktif') 
            DEFAULT 'aktif'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE users 
            MODIFY status ENUM('aktif', 'nonaktif') 
            DEFAULT 'aktif'
        ");
    }
};
