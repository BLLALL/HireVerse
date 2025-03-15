<?php
use App\Models\Job;

it('has correct structure for index enpoint', function() {
    $jobs = Job::factory()->count(3)->create();

    $response = $this->getJson(route('jobs.index'));

    $response->assertOk();
    $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'attributes' => [
                    'title',
                    'type',
                    'experienceLevel',
                    'workLocation',
                    'isAvailable',
                    'availableTo',
                    'maxApplicants',
                    'companyLogo',
                    'salary',
                    'currency',
                    'summary',
                    'createdAt',
                    'updatedAt',
                    ],
                    'links' => [
                        'self',
                    ],
                    'relationships' => [
                        'company' => [
                            'data' => [
                                'type',
                                'id',
                            ],
                            'links' => [
                                'self',
                            ],
                        ],
                    ],
                ]
            ]
        ])->assertJsonCount(3, 'data');
});

it('has correct structure for show enpoint', function() {
    $job = Job::factory()->create();

    $response = $this->getJson(route('jobs.show', ['job' => $job->id]));

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            'id',
            'type',
            'attributes' => [
                'title',
                'type',
                'experienceLevel',
                'workLocation',
                'isAvailable',
                'availableTo',
                'maxApplicants',
                'companyLogo',
                'salary',
                'currency',
                'summary',
                'requirements',
                'responsibilities',
                'workHours',
                'skills',
                'createdAt',
                'updatedAt',
            ],
            'links' => [
                'self',
            ],
            'relationships' => [
                'company' => [
                    'data' => [
                        'type',
                        'id',
                    ],
                    'links' => [
                        'self',
                    ],
                ],
            ],
        ]
    ])->assertJsonFragment([
        'id' => $job->id,
        'title' => $job->title,
    ]);
});