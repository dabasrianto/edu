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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., 'main_settings'
            $table->string('logo_path')->nullable();
            $table->string('theme_color')->default('blue'); // blue, red, emerald, purple
            $table->string('font_family')->default('Inter'); // Inter, Roboto, Poppins
            $table->json('slider_config')->nullable(); // Arrays of {image, title, subtitle}
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
