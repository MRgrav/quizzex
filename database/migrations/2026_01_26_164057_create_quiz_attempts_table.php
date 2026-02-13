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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('submitted_at')->nullable();
            $table->decimal('score', 8, 2)->default(0); // Total score earned
            $table->decimal('total_possible_score', 8, 2)->default(0); // Total possible score
            $table->enum('status', ['in_progress', 'submitted', 'abandoned'])->default('in_progress')->index();
            $table->timestamps();

            // Index for faster lookups
            $table->index(['participant_id', 'quiz_id']);
            $table->index(['quiz_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
