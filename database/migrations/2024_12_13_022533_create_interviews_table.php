<?php

use App\Enums\QuestionDifficulty;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id')->references('id')->on('applications')->cascadeOnDelete();
            $table->float('technical_skills_score')->nullable();
            $table->float('soft_skills_score')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->text('feedback')->nullable();
            $table->text('resources')->nullable();
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
