<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\Job;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = collect([
            'PHP', 'Laravel', 'ReactJS', 'Node.js', 'Vue.js', 'Docker', 'Python', 'AWS', 'MySQL', 'Redis',
            'JavaScript', 'TypeScript', 'GraphQL', 'MongoDB', 'Kubernetes', 'Jenkins', 'CI/CD', 'Terraform',
            'GoLang', 'Flutter', 'Swift', 'Kotlin', 'Java', 'C', 'C++', 'C#', 'Ruby on Rails', 'Django', 'Flask',
            'PostgreSQL', 'SQLite', 'Firebase', 'ElasticSearch', 'RabbitMQ', 'Kafka', 'GraphQL', 'Spring Boot',
            'HTML', 'CSS', 'SASS', 'Bootstrap', 'Tailwind CSS', 'Material UI', 'Express.js', 'Next.js', 'Nuxt.js',
            'REST API', 'gRPC', 'Microservices', 'Serverless', 'Machine Learning', 'Deep Learning', 'AI/ML',
            'Data Science', 'Cybersecurity', 'Penetration Testing', 'Blockchain', 'Smart Contracts',
        ]);

        foreach (Job::where('id', '>', 1)->get() as $job) {
            $randSkills = $skills->random(rand(3, 8))->map(fn ($skill) => ['title' => $skill])->toArray();
            $job->skills()->createMany($randSkills);
        }

        foreach (Applicant::all() as $applicant) {
            $randSkills = $skills->random(rand(3, 8))->map(fn ($skill) => ['title' => $skill])->toArray();
            $applicant->skills()->createMany($randSkills);
        }

    }
}
