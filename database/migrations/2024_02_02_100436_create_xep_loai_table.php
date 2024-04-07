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
        Schema::create('xep_loai', function (Blueprint $table) {
            $table->id();
            $table->string('ma_xep_loai')->unique();
            $table->string('ten_xep_loai');
            $table->tinyInteger('diem_toi_thieu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xep_loai');
    }
};
