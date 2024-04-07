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
        Schema::create('don_vi', function (Blueprint $table) {
            $table->id();
            $table->string('ma_don_vi')->unique();
            $table->string('ten_don_vi');
            $table->string('ma_don_vi_cap_tren')->nullable();
            $table->string('ma_trang_thai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('don_vi');
    }
};
