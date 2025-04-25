<?php

use App\Enums\ExperienceLevel;
use App\Enums\JobPhase;
use App\Enums\JobType;
use App\Enums\WorkingHours;
use App\Enums\WorkLocation;
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
            $table->enum('type', JobType::values())->default(JobType::FullTime);
            $table->enum('experience_level', ExperienceLevel::values())->default(ExperienceLevel::Junior);
            $table->string('summary', 510)->nullable();
            $table->bigInteger('salary')->nullable();
            $table->string('currency')->default('USD');
            $table->enum('work_hours', WorkingHours::values())->default(WorkingHours::FixedShecdule);
            $table->enum('work_location', WorkLocation::values())->default(WorkLocation::Onsite);
            $table->string('job_location')->nullable();
            $table->text('requirements');
            $table->text('responsibilities');
            $table->enum('phase', JobPhase::values())->default(JobPhase::CVFiltration);
            $table->boolean('is_available')->default(true);
            $table->date('available_to')->nullable();
            $table->smallInteger('max_applicants')->nullable();
            $table->smallInteger('required_no_of_hires')->nullable();
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
