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
        Schema::table('kqxl_nam_tap_the', function (Blueprint $table) {
            $table->string('ket_qua_chuyen_mon')->after('nam_danh_gia'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kqxl_nam_tap_the', function (Blueprint $table) {
            $table->dropColumn('ket_qua_chuyen_mon');
        });
    }
};
