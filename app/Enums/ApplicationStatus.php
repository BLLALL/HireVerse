<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum ApplicationStatus: string
{
    use EnumHelpers;

    case Pending = 'Pending';

    case CVProcessing = 'CV processing';
    case CVProcessed = 'CV processed';
    case CVEligible = 'CV eligible';
    case CVRejected = 'CV rejected';
    
    case InterviewScheduled = 'Interview scheduled';
    case Interviewed = 'Interviewed';

    case Accepted = 'Accepted';
    case Rejected = 'Rejected';
}
