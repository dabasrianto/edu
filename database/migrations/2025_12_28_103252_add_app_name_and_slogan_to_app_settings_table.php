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
        Schema::table('app_settings', function (Blueprint $table) {
            $table->string('app_name')->nullable()->default('Edu HSI')->after('login_header_text');
            $table->string('app_slogan')->nullable()->default('Belajar Kapanpun, Dimanapun')->after('app_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['app_name', 'app_slogan']);
        });
    }
};
