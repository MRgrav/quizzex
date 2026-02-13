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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained('quiz_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('option_id')->nullable()->constrained('options')->onDelete('set null');
            $table->text('answer_text')->nullable(); // For short answer questions
            $table->boolean('is_correct')->default(false)->index();
            $table->decimal('points_earned', 8, 2)->default(0);
            $table->timestamps();

            // Ensure one answer per question per attempt
            $table->unique(['quiz_attempt_id', 'question_id'], 'unique_answer_per_question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
