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
            $table->string('diem_tu_cham_t01')->nullable(); 
            $table->string('diem_phe_duyet_t01')->nullable(); 
            $table->string('kqxl_t01')->nullable();
            $table->string('diem_tu_cham_t02')->nullable(); 
            $table->string('diem_phe_duyet_t02')->nullable(); 
            $table->string('kqxl_t02')->nullable();
            $table->string('diem_tu_cham_t03')->nullable(); 
            $table->string('diem_phe_duyet_t03')->nullable(); 
            $table->string('kqxl_t03')->nullable();
            $table->string('diem_tu_cham_t04')->nullable(); 
            $table->string('diem_phe_duyet_t04')->nullable(); 
            $table->string('kqxl_t04')->nullable();
            $table->string('diem_tu_cham_t05')->nullable(); 
            $table->string('diem_phe_duyet_t05')->nullable(); 
            $table->string('kqxl_t05')->nullable();
            $table->string('diem_tu_cham_t06')->nullable(); 
            $table->string('diem_phe_duyet_t06')->nullable(); 
            $table->string('kqxl_t06')->nullable();
            $table->string('diem_tu_cham_t07')->nullable(); 
            $table->string('diem_phe_duyet_t07')->nullable(); 
            $table->string('kqxl_t07')->nullable();
            $table->string('diem_tu_cham_t08')->nullable(); 
            $table->string('diem_phe_duyet_t08')->nullable(); 
            $table->string('kqxl_t08')->nullable();
            $table->string('diem_tu_cham_t09')->nullable(); 
            $table->string('diem_phe_duyet_t09')->nullable(); 
            $table->string('kqxl_t09')->nullable();
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
