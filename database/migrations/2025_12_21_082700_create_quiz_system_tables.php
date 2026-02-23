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
        // 1. Quizzes Table
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->nullable(); // e.g., "Silsilah Ilmiah 1"
            $table->string('color')->default('blue'); // blue, green, etc
            $table->enum('type', ['wajib', 'sunnah'])->default('wajib');
            $table->integer('duration_minutes')->default(10);
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Questions Table
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->string('type')->default('radio'); // radio, checkbox, etc
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 3. Options Table
        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_question_id')->constrained()->cascadeOnDelete();
            $table->string('label')->nullable(); // A, B, C, D
            $table->string('text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        // 4. Attempts Table
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->integer('score')->nullable();
            $table->string('status')->default('started'); // started, completed
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_options');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
