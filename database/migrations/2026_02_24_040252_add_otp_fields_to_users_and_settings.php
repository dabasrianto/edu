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
        Schema::table('users', function (Blueprint $table) {
            $table->string('otp_code')->nullable()->after('password');
            $table->datetime('otp_expires_at')->nullable()->after('otp_code');
        });

        Schema::table('app_settings', function (Blueprint $table) {
            $table->json('otp_config')->nullable()->after('blog_config');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['otp_code', 'otp_expires_at']);
        });

        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn('otp_config');
        });
    }
};
