<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use phpDocumentor\Reflection\DocBlock\Tags\See;

use function Pest\Laravel\seed;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ApplicantSeeder::class,
            CompanySeeder::class,
            JobSeeder::class,
        ]);

    }
}
