<?php

namespace Database\Seeders;

use App\Models\Applicant;
use Illuminate\Database\Seeder;

class ApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Applicant::firstOrNew([
            'first_name' => 'Salah',
            'last_name' => 'Eddine',
            'email' => 'salah@test.com',
            'password' => bcrypt('password'),
        ]);
        Applicant::factory(10)->create();
    }
}
