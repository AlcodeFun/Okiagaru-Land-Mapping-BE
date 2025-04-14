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
        Schema::create('karakteristik_angka', function (Blueprint $table) {
            $table->id('id_karakteristik_angka');
            $table->unsignedBigInteger('id_lahan');
            $table->unsignedBigInteger('id_karakteristik_lahan');
            $table->double('nilai_angka');
        
         
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karakteristik_angka');
    }
};
