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
        Schema::create('kqxl_thang', function (Blueprint $table) {
            $table->id();
            $table->string('ma_kqxl')->unique();            
            $table->string('so_hieu_cong_chuc');
            $table->string('nam_danh_gia');
            $table->string('diem_tu_cham_t1')->nullable(); 
            $table->string('diem_phe_duyet_t1')->nullable(); 
            $table->string('kqxl_t1')->nullable();
            $table->string('diem_tu_cham_t2')->nullable(); 
            $table->string('diem_phe_duyet_t2')->nullable(); 
            $table->string('kqxl_t2')->nullable();
            $table->string('diem_tu_cham_t3')->nullable(); 
            $table->string('diem_phe_duyet_t3')->nullable(); 
            $table->string('kqxl_t3')->nullable();
            $table->string('diem_tu_cham_t4')->nullable(); 
            $table->string('diem_phe_duyet_t4')->nullable(); 
            $table->string('kqxl_t4')->nullable();
            $table->string('diem_tu_cham_t5')->nullable(); 
            $table->string('diem_phe_duyet_t5')->nullable(); 
            $table->string('kqxl_t5')->nullable();
            $table->string('diem_tu_cham_t6')->nullable(); 
            $table->string('diem_phe_duyet_t6')->nullable(); 
            $table->string('kqxl_t6')->nullable();
            $table->string('diem_tu_cham_t7')->nullable(); 
            $table->string('diem_phe_duyet_t7')->nullable(); 
            $table->string('kqxl_t7')->nullable();
            $table->string('diem_tu_cham_t8')->nullable(); 
            $table->string('diem_phe_duyet_t8')->nullable(); 
            $table->string('kqxl_t8')->nullable();
            $table->string('diem_tu_cham_t9')->nullable(); 
            $table->string('diem_phe_duyet_t9')->nullable(); 
            $table->string('kqxl_t9')->nullable();
            $table->string('diem_tu_cham_t10')->nullable(); 
            $table->string('diem_phe_duyet_t10')->nullable(); 
            $table->string('kqxl_t10')->nullable();
            $table->string('diem_tu_cham_t11')->nullable(); 
            $table->string('diem_phe_duyet_t11')->nullable(); 
            $table->string('kqxl_t11')->nullable();
            $table->string('diem_tu_cham_t12')->nullable(); 
            $table->string('diem_phe_duyet_t12')->nullable(); 
            $table->string('kqxl_t12')->nullable();
            $table->tinyInteger('ma_trang_thai')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kqxl_thang');
    }
};
