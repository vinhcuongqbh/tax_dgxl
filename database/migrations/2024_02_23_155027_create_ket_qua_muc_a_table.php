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
        Schema::create('ket_qua_muc_a', function (Blueprint $table) {
            $table->id();
            $table->string('ma_phieu_danh_gia');
            $table->string('ma_tieu_chi');
            $table->tinyInteger('diem_toi_da');
            $table->tinyInteger('diem_tu_cham')->nullable();
            $table->tinyInteger('diem_danh_gia')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ket_qua_muc_a');
    }
};
