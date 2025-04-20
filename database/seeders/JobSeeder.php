<?php

namespace Database\Seeders;

use App\Enums\ExperienceLevel;
use App\Enums\JobType;
use App\Enums\WorkingHours;
use App\Enums\WorkLocation;
use App\Models\Company;
use App\Models\Job;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::limit(3);

        $job = Job::create([
            'title' => 'Junior Laravel Backend Developer',
            'type' => JobType::FullTime,
            'experience_level' => ExperienceLevel::Junior,
            'summary' => 'We are seeking a Junior Laravel Backend Developer to help build and maintain backend applications. You will work with senior developers to develop APIs, manage databases, and ensure efficient backend performance.',
            'salary' => 8000,
            'currency' => 'USD',
            'work_hours' => WorkingHours::FixedShecdule,
            'work_location' => WorkLocation::Hyprid,
            'job_location' => 'Adelemouth',
            'requirements' => 'Basic experience with PHP and Laravel. Understanding of MVC architecture and APIs. Familiarity with databases and SQL. Knowledge of Git for version control. Problem-solving mindset and eagerness to learn.',
            'responsibilities' => 'Develop and maintain backend features using Laravel. Build and integrate RESTful APIs. Work with databases (MySQL, PostgreSQL). Debug and optimize backend performance. Collaborate with the team to improve development processes.',
            'is_available' => true,
            'available_to' => now()->addDay()->toDateString(),
            'max_applicants' => 4,
            'company_id' => 1,
        ]);

        $job->skills = ['RESTful APIs', 'PHP', 'Laravel', 'MVC', 'MySQL', 'PostgreSQL', 'Git/GitHub'];

        Job::factory(20)->recycle($company)->create();
    }
}
