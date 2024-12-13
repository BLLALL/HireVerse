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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('password');
            $table->string('business_email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('location');
            $table->string('website_url');
            $table->string('ceo');
            $table->text('description')->nullable();
            $table->text('insights')->nullable();
            $table->string('industry')->nullable();
            $table->integer('employee_no')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
