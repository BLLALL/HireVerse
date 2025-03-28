<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = Job::get();
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
    }
}
