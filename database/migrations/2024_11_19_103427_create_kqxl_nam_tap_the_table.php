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
        Schema::create('kqxl_nam_tap_the', function (Blueprint $table) {
            $table->id();            
            $table->string('ma_phong');
            $table->string('nam_danh_gia');
            $table->string('ket_qua_xep_loai');
            $table->string('ma_can_bo_cap_nhat');
            $table->tinyInteger('ma_trang_thai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kqxl_nam_tap_the');
    }
};
