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
        Schema::create('mau01B', function (Blueprint $table) {
            $table->id();           
            $table->string('ma_tieu_chi')->unique();
            $table->string('tieu_chi_me')->nullable();
            $table->string('loai_tieu_chi')->nullable();
            $table->string('tt')->nullable();
            $table->string('noi_dung');            
            $table->tinyInteger('diem_toi_da');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mau01B');
    }
};
