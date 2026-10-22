<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->nullable()->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->text('text');
            $table->string('type')->default('multiple_choice')->index();
            $table->text('correct_answer')->nullable();
            $table->text('explanation')->nullable();
            $table->string('hint')->nullable();
            $table->string('difficulty')->default('medium')->index();
            $table->integer('time_limit')->default(30);
            $table->integer('points')->default(10);
            $table->string('image_path')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->integer('sort_order')->default(0);
            $table->json('options')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
