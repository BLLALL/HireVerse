<?php

use App\Enums\QuestionDifficulty;
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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->string('applicant_answer')->nullable();
            $table->enum('difficulty', QuestionDifficulty::values())->default(QuestionDifficulty::Easy);
            $table->foreignId('interview_id')->nullable()->constrained('interviews');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
