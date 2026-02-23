<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->whereIn('email', [
            'admin@localhost',
            'admin@hsi-edu.local',
        ])->update(['is_admin' => true]);
    }

    public function down(): void
    {
        DB::table('users')->whereIn('email', [
            'admin@localhost',
            'admin@hsi-edu.local',
        ])->update(['is_admin' => false]);
    }
};

