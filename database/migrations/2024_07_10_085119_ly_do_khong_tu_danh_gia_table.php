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
        Schema::create('ly_do_khong_tu_danh_gia', function (Blueprint $table) {
            $table->id();
            $table->string('ly_do');
            $table->tinyInteger('diem');
            $table->string('xep_loai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ly_do_khong_tu_danh_gia');
    }
};
