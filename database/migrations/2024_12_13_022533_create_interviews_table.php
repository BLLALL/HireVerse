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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->enum('difficulty', QuestionDifficulty::values())->default('easy');
            $table->integer('score')->default(0);
            $table->text('ideal_answer');
            $table->text('applicant_answer');
            $table->foreignId('application_id')->nullable()->constrained('applications');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
