<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('type')->default('physical')->after('name'); // physical or digital
            $table->string('file_path')->nullable()->after('link'); // for digital product file
            $table->string('download_url')->nullable()->after('file_path'); // external download link
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['type', 'file_path', 'download_url']);
        });
    }
};
