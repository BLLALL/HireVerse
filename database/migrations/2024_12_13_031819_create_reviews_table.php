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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('job_title');
            $table->text('body');
            $table->text('employment_status');
            $table->boolean('is_current_employee')->default(false); 
            $table->enum('rating', [1, 2, 3, 4, 5]); 
            $table->foreignId('applicant_id')->nullable()->constrained('applicants'); 
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
        Schema::dropIfExists('reviews');
    }
};
