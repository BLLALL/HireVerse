<?php

use App\Enums\ApplicationStatus;
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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('cv');
            $table->enum('status', ApplicationStatus::values())->default(ApplicationStatus::Pending);
            $table->integer('cv_score')->nullable();
            $table->foreignId('applicant_id')->nullable()->constrained('applicants');
            $table->foreignId('job_id')->nullable()->constrained('jobs');
            $table->unique(['job_id', 'applicant_id']);
            $table->date('inteview_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
