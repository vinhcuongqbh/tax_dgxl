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
        Schema::create('phieu_danh_gia', function (Blueprint $table) {
            $table->id();
            $table->string('so_hieu_cong_chuc'); 
            $table->date('thoi_diem_danh_gia');
            $table->string('ma_phieu_danh_gia')->unique();
            $table->string('mau_phieu_danh_gia');                                               
            $table->string('ma_chuc_vu')->nullable();
            $table->string('ma_phong');
            $table->string('ma_don_vi');       
            $table->tinyInteger('diem_tu_cham')->nullable(); 
            $table->tinyInteger('diem_cong_tu_cham')->nullable(); 
            $table->tinyInteger('diem_tru_tu_cham')->nullable();
            $table->tinyInteger('tong_diem_tu_cham')->nullable();
            $table->tinyInteger('diem_danh_gia')->nullable(); 
            $table->tinyInteger('diem_cong_danh_gia')->nullable(); 
            $table->tinyInteger('diem_tru_danh_gia')->nullable();
            $table->tinyInteger('tong_diem_danh_gia')->nullable();
            $table->string('ma_cap_tren_danh_gia')->nullable();
            $table->string('ma_cap_tren_phe_duyet')->nullable(); 
            $table->string('ca_nhan_tu_xep_loai')->nullable();
            $table->string('ket_qua_xep_loai')->nullable();
            $table->tinyInteger('ma_trang_thai')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_danh_gia');
    }
};
