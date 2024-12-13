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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['part_time','full_time', 'freelance'])->default('full_time');
            $table->enum('experience_level', ['junior', 'mid-senior','senior'])->default('junior');
            $table->string('summary')->nullable();
            $table->bigInteger('salary');
            $table->string('currency')->default('USD');
            $table->integer('work_hours')->nullable();
            $table->enum('work_location', ['remote', 'hybrid', 'onsite'])->default('onsite');
            $table->text('requirements');
            $table->text('responsibilities');
            $table->boolean('is_available')->default(false);
            $table->foreignId('company_id')->nullable()->constrained('companies');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
