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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('so_hieu_cong_chuc')->unique();
            $table->string('name');
            $table->date('ngay_sinh');
            $table->boolean('ma_gioi_tinh');
            $table->string('ma_ngach')->nullable();
            $table->string('ma_chuc_vu')->nullable();
            $table->string('ma_phong');
            $table->string('ma_don_vi');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('hoi_dong_phe_duyet')->nullable();
            $table->boolean('isAdmin')->nullable();
            $table->string('ma_trang_thai');
            $table->rememberToken();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
