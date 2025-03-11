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
        Schema::table('kqxl_nam', function (Blueprint $table) {
            $table->string('file_tu_dgxl')->nullable()->after('kqxl'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kqxl_nam', function (Blueprint $table) {
            $table->dropColumn('file_tu_dgxl');
        });
    }
};
