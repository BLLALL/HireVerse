<?php

use App\Enums\ExperienceLevel;
use App\Enums\WorkLocation;
use App\Enums\JobType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("jobs", function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->enum("type", JobType::values())->default("full_time");
            $table->enum("experience_level", ExperienceLevel::values())->default("junior");
            $table->string("summary")->nullable();
            $table->bigInteger("salary")->nullable();
            $table->string("currency")->default("USD");
            $table->integer("work_hours")->nullable();
            $table->enum("work_location", WorkLocation::values())->default("onsite");
            $table->text("requirements");
            $table->text("responsibilities");
            $table->boolean("is_available")->default(false);
            $table->dateTime("available_to")->nullable();
            $table->smallInteger("max_applicants")->nullable();
            $table->foreignId("company_id")->nullable()->constrained("companies");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("jobs");
    }
};
