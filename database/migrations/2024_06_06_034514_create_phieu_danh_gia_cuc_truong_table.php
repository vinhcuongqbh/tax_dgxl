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
        Schema::create('phieu_danh_gia_cuc_truong', function (Blueprint $table) {
            $table->id();
            $table->string('so_hieu_cong_chuc');
            $table->date('thoi_diem_danh_gia');
            $table->string('ma_phieu_danh_gia');      
            $table->string('ma_cap_tren_danh_gia');    
            $table->string('ma_chuc_vu_cap_tren')->nullable();
            $table->string('ma_phong_cap_tren');
            $table->string('ma_don_vi_cap_tren');
            $table->tinyInteger('diem_tu_cham');
            $table->tinyInteger('diem_cong_tu_cham');
            $table->tinyInteger('diem_tru_tu_cham');
            $table->tinyInteger('tong_diem_tu_cham');     
            $table->tinyInteger('diem_danh_gia'); 
            $table->tinyInteger('diem_cong_danh_gia'); 
            $table->tinyInteger('diem_tru_danh_gia');
            $table->tinyInteger('tong_diem_danh_gia');   
            $table->string('ca_nhan_tu_xep_loai');         
            $table->string('ket_qua_xep_loai');
            $table->tinyInteger('ma_trang_thai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_danh_gia_cuc_truong');
    }
};
