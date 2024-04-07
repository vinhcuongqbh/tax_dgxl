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
        Schema::create('kqxl_quy', function (Blueprint $table) {
            $table->id();
            $table->string('ma_kqxl')->unique();            
            $table->string('so_hieu_cong_chuc');
            $table->string('nam_danh_gia');
            $table->string('kqxl_q1')->nullable(); 
            $table->string('kqxl_q2')->nullable(); 
            $table->string('kqxl_q3')->nullable(); 
            $table->string('kqxl_q4')->nullable(); 
            $table->tinyInteger('ma_trang_thai')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kqxl_quy');
    }
};
