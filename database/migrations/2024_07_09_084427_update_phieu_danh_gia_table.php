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
        Schema::table('phieu_danh_gia', function (Blueprint $table) {          
            $table->tinyInteger('ly_do_khong_tu_danh_gia')->after('ket_qua_xep_loai')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phieu_danh_gia', function (Blueprint $table) {          
            $table->dropColumn('ly_do_khong_tu_danh_gia');
        });
    }
};
