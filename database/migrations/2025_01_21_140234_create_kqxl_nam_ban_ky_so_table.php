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
        Schema::create('kqxl_nam_ban_ky_so', function (Blueprint $table) {
            $table->id();
            $table->string('nam_danh_gia')->unique();
            $table->text('duong_dan_file');
            $table->string('ma_can_bo_cap_nhat');
            $table->tinyInteger('ma_trang_thai')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kqxl_nam_ban_ky_so');
    }
};
