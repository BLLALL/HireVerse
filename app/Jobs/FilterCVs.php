<?php

namespace App\Jobs;

use App\AIServices\CVFiltrationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;

class FilterCVs implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [5, 10];
    
    public function __construct(protected Collection $applications) {}

    public function handle(): void
    {
        CVFiltrationService::handle($this->applications);
    }

}
