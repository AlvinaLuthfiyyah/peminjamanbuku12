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
        Schema::table('borrowings', function (Blueprint $table) {
             DB::statement("
        ALTER TABLE borrowings 
        MODIFY status ENUM('menunggu','dipinjam','dikembalikan') 
        DEFAULT 'menunggu'
    ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
             DB::statement("
        ALTER TABLE borrowings 
        MODIFY status ENUM('dipinjam','dikembalikan')
    ");
        });
    }
};
