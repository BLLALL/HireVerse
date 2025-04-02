<?php

namespace App\Jobs;

use App\Models\Applicant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendVerificationMail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Applicant $applicant) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->applicant->sendEmailVerificationNotification();
    }
}
