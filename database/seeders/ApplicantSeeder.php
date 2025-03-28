<?php

namespace Database\Seeders;

use App\Models\Applicant;
use Database\Factories\ApplicationFactory;
use Illuminate\Database\Seeder;

class ApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Applicant::factory()->create([
            'first_name' => 'Salah',
            'last_name' => 'Eddine',
            'email' => 'salah@test.com',
            'password' => 'password',
        ]);

        Applicant::factory(10)->create();
    }
}
