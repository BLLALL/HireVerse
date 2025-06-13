<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = Job::where('id', '>', 1)->get();
        $applicants = Applicant::get();

        foreach ($applicants as $applicant) {
            $n = fake()->numberBetween(1, 12);
            $job_ids = $jobs->pluck('id')->shuffle()->toArray();
            for ($i = 0; $i < $n; $i++) {
                Application::factory()->create([
                    'applicant_id' => $applicant->id,
                    'job_id' => $job_ids[$i],
                ]);
            }
        }

        $testApplications = Application::factory(3)->create([
            'status' => ApplicationStatus::Pending,
            'cv_score' => null,
            'job_id' => 1,
        ]);

        $testApplications[0]->update(['cv' => 'applications/Elsayed.pdf', 'applicant_id' => 1]);
        $testApplications[1]->update(['cv' => 'applications/Belal.pdf', 'applicant_id' => 2]);
        $testApplications[2]->update(['cv' => 'applications/Salma.pdf', 'applicant_id' => 3]);
    }
}
