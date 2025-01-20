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
        Schema::create('kqxl_nam', function (Blueprint $table) {
            $table->id();
            $table->string('ma_kqxl')->unique();            
            $table->string('so_hieu_cong_chuc');
            $table->string('ma_chuc_vu')->nullable();
            $table->string('ma_phong');
            $table->string('ma_don_vi');
            $table->string('nam_danh_gia');
            $table->string('kqxl'); 
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
        Schema::dropIfExists('kqxl_nam');
    }
};
