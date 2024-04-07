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
        Schema::create('ket_qua_muc_b', function (Blueprint $table) {
            $table->id();
            $table->string('ma_phieu_danh_gia');
            $table->string('noi_dung');
            $table->boolean('nhiem_vu_phat_sinh')->nullable();
            $table->string('hoan_thanh_nhiem_vu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ket_qua_muc_b');
    }
};
